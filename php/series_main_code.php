<?php
/**
 * 파일명 : series_main_code.php
 * 내용 : 시리즈 메인 페이지 코드
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
 * 김민성    2023/11/10    shop 기능추가
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    $combo_list_arry = array();

    try {
        global $query_string;
        global $sub_type;

        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET'); // 작가명
        $PROGRAM_CD = get_request_param('PROGRAM_CD', 'GET'); // 중분류 코드
        $TITLE = get_request_param('TITLE', 'GET'); // 시리즈
        $MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부
        $sub_type = isset($common_type) ? $common_type : '';  // home 페이지 구분

        $category1_name = "";
        $category1_val = "";
        $title_name = "";

        $arrValue = array();
        $limit = 10;
        $where = '';
        $where2 = '';
        $table = 'CATEGORY2'; // 카테고리2 테이블
        $type_gb = '';

        if ($page_type == PAGE1) {
            $category1_name = "작가";
            $category1_val = "작가명";
            $title_name = "시리즈"; // Copy, CSV, Excel, Print 제목
            $title_val = "시리즈명";
        } else if ($page_type == PAGE2) {
            $category1_name = "카테고리";
            $category1_val = "카테고리명";
            $title_name = "분류"; // Copy, CSV, Excel, Print 제목
            $title_val = "분류명";
        } else if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE1) {
                $category1_name = "업종";
                $category1_val = "업종명";
                $title_name = "세부 업종"; // Copy, CSV, Excel, Print 제목
                $title_val = "세부 업종명";
                $where2 .= " AND M.CATEGORY1_SEQ NOT IN ('SHOP', 'COLLABO', 'EXHIBITION', 'PROGRAM')";
                $where2 .= " AND M.CATEGORY1_SEQ NOT LIKE '%PROGRAM%'";
            } else if ($sub_type == SUB_PAGE2)  {
                $category1_name = "카테고리";
                $category1_val = "카테고리명";
                $title_name = "분류"; // Copy, CSV, Excel, Print 제목
                $title_val = "분류명";
                $where2 .= " AND M.CATEGORY1_SEQ NOT IN ('SHOP', 'COLLABO', 'EXHIBITION')";
                $where2 .= " AND M.CATEGORY1_SEQ NOT LIKE '%RECRU%'";
            }
        }

        //페이지타입
        if (!empty($page_type)) {
            $where .= " AND M.PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        //검색
        if (!empty($CATEGORY1_SEQ)) { // 작가명
            $where .= " AND D.CATEGORY1_SEQ = :CATEGORY1_SEQ";
            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;

            global $type_gb2;
            $type_gb2 = $CATEGORY1_SEQ;
        }

        //검색
        if (!empty($PROGRAM_CD)) {
            $where .= " AND D.PROGRAM_CD = :PROGRAM_CD";
            $arrValue[':PROGRAM_CD'] = $PROGRAM_CD;
        }

        if (!empty($TITLE)) { // 시리즈
            $where .= " AND M.TITLE LIKE :TITLE";
            $arrValue[':TITLE'] = "%{$TITLE}%";
        }

        if (!empty($MAIN_YN)) { // 노출여부
            $where .= " AND M.MAIN_YN = :MAIN_YN";
            $arrValue[':MAIN_YN'] = $MAIN_YN;

            $type_gb = $MAIN_YN;
        }

        if (!empty($sub_type)) { // 서브 페이지 타입
            $where .= " AND M.SUB_TYPE = :SUB_TYPE";
            $arrValue[':SUB_TYPE'] = $sub_type;
        }

        $sql = "
             SELECT CATEGORY2_SEQ
                  , ZCM_COM_NM('COL014', M.PROGRAM_CD) AS PROGRAM_NM
                  , M.ORDER_NUMBER
                  , D.TITLE AS TITLE2
                  , M.TITLE
                  , M.ATTACH_FILE_ID
                  , ZCM_COM_NM('AD002', M.MAIN_YN) AS MAIN_YN_NM
                  , M.reg_date
                  , M.reg_user
               FROM {$table} M
               LEFT OUTER JOIN CATEGORY1 D ON M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
              WHERE 1
               {$where}
               {$where2}
               ORDER BY M.ORDER_NUMBER DESC, D.reg_date DESC, D.TITLE DESC";

        $name_sql = "카테고리2 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();
        setMain_List($list);

        $INS_arrParams = array( // 초기화 및 등록
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'sub_type' => $sub_type
        );

        $INS_query_string = http_build_query($INS_arrParams);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'sub_type' => $sub_type
            , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ
            , 'PROGRAM_CD' => $PROGRAM_CD
            , 'TITLE' => $TITLE
            , 'MAIN_YN' => $MAIN_YN
        );

        $query_string = http_build_query($arrParams);

        $sql = "
            SELECT TITLE
                 , CATEGORY1_SEQ
                 , PROGRAM_CD
              FROM CATEGORY1 M
             WHERE PAGE_TYPE = '{$page_type}'
               AND MAIN_YN = 'Y'
               {$where2}
             ORDER BY ORDER_NUMBER DESC, TITLE, reg_date DESC";

        $name_sql = "카테고리1 리스트";
        $clefResult = $mysqldb->select($sql, null, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setARTISTComboList($combo_list);

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

    /**
     * name :setMain_List
     * comment : 메인 데이터 저장
     */
    function setMain_List($data) {
        global $Main_list_arry;
        $Main_list_arry = $data;
    }

    /**
     * name :getMain_List
     * comment : 메인 총 데이터
     */
    function getMain_List() {
        global $Main_list_arry;
        global $query_string;
        global $sub_type;

        $page_type = get_request_param('page_type', 'GET');

        if (!empty($Main_list_arry)) {
            foreach ($Main_list_arry as $data) {
                $_db_PROGRAM_NM = _check_var($data['PROGRAM_NM']); // 중분류 구분 코드 값
                $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 시퀀스
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_TITLE2 = _check_var($data['TITLE2']); //작가명, 카테고리1명
                $_db_TITLE = _check_var($data['TITLE']); // 시리즈명, 카테고리2명
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                $_db_MAIN_YN_NM = _check_var($data['MAIN_YN_NM']); // 노출여부
                $_db_reg_date = _check_var($data['reg_date']); // 등록일
                $_db_reg_user = _check_var($data['reg_user']); // 등록자

                $path_File = "";

                $url = "../board/series_details.php?mode=MOD&{$query_string}";

                if ($page_type == PAGE1) {
                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 2);
    
                        if (!empty($file_list)) {
                            foreach ($file_list as $list) {
                                $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                                $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_TITLE2}</td>
                                <td><a href="{$url}&seq={$_db_CATEGORY2_SEQ}">{$_db_TITLE}</td>
                                <td>
                                    <div class="lightBoxGallery">
                                        <img src="{$path_File}" style="height: 100px;" alt="작품 이미지">
                                    </div>
                                </td>
                                <td>{$_db_ORDER_NUMBER}</td>
                                <td>{$_db_MAIN_YN_NM}</td>
                                <td>{$_db_reg_user}</td>
                                <td>{$_db_reg_date}</td>
                            </tr>
                        TR;
                } else if ($page_type == PAGE2 || $page_type == PAGE3) {
                    if ($sub_type == SUB_PAGE2) {
                        echo <<<TR
                                    <tr>
                                        <td class="simple_numbers"></td>
                                        <td>{$_db_PROGRAM_NM}</td>
                                        <td>{$_db_TITLE2}</td>
                                        <td><a href="{$url}&seq={$_db_CATEGORY2_SEQ}">{$_db_TITLE}</td>
                                        <td>{$_db_ORDER_NUMBER}</td>
                                        <td>{$_db_MAIN_YN_NM}</td>
                                        <td>{$_db_reg_user}</td>
                                        <td>{$_db_reg_date}</td>
                                    </tr>
                                TR;
                    } else {     
                        echo <<<TR
                                    <tr>
                                        <td class="simple_numbers"></td>
                                        <td>{$_db_TITLE2}</td>
                                        <td><a href="{$url}&seq={$_db_CATEGORY2_SEQ}">{$_db_TITLE}</td>
                                        <td>{$_db_ORDER_NUMBER}</td>
                                        <td>{$_db_MAIN_YN_NM}</td>
                                        <td>{$_db_reg_user}</td>
                                        <td>{$_db_reg_date}</td>
                                    </tr>
                                TR;
                    }
                }
            }
        }
    }

    /**
     * name :setARTISTComboList
     * comment : 콤보박스에 사용하는 값을 전역변수에 저장
     */
    function setARTISTComboList($data) {
        global $combo_list_arry;
        $combo_list_arry = $data;
    }

    /**
     * name :getARTISTComboList
     * comment : 콤보박스에 사용하는 값을 콤보박스 값으로 출력
     */
    function getARTISTComboList() {
        global $combo_list_arry;
        global $type_gb2;

        $COM_CD = '';
        $COM_CD_NM = '';
        $TH1_THEM_CD = '';
        $selected = '';

        foreach($combo_list_arry as $array_val) {
            foreach($array_val as $key => $val) {
                if ($key == 'CATEGORY1_SEQ') {
                    $COM_CD = $val;
                } else if ($key == 'TITLE') {
                    $COM_CD_NM = $val;
                } else if ($key == 'PROGRAM_CD') {
                    $TH1_THEM_CD = $val;
                }
            }

            if (!empty($type_gb2)) {
                $selected = ($type_gb2 == $COM_CD) ? 'selected="selected"' : '';
            }

            echo <<<OPTION
                        <option value="{$COM_CD}" data-code1="{$TH1_THEM_CD}" $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }
?>

