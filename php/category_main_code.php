<?php
/**
 * 파일명 : category_main_code.php
 * 내용 : home 카테고리 메인 페이지 코드
 * 최초작성날짜 : 2023/11/28
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/11/28     V1.0
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

    try {
        global $query_string;

        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $TITLE = get_request_param('TITLE', 'GET'); // 작품, 상품
        $TITLE_YN = get_request_param('TITLE_YN','GET'); // 메인노출여부
        $MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부
        $start_date = get_request_param('start_date','GET'); // 시작일
        $end_date = get_request_param('end_date','GET'); // 종료일

        if ($CATEGORY1_SEQ != "COLLABO") {
            $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ', 'GET'); // 시리즈, 분류
        }

        $category1_name = $CATEGORY1_SEQ;
        $category1_val = $CATEGORY1_SEQ;
        $category2_name = "분류명";
        $category2_val = "분류";
        $title_name = $CATEGORY1_SEQ;
        $title_val = "제목";

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'CATEGORY3'; // 관리자 테이블
        $type_gb = '';

        $arrValue2 = array();
        $where2 = '';

        //페이지 타입
        if (!empty($page_type)) {
            $where .= " AND M.PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;

            $where2 .= " AND PAGE_TYPE = :page_type";
            $arrValue2[':page_type'] = $page_type;
        }

        //검색
        if (!empty($CATEGORY1_SEQ)) { // 작가, 카테고리
            $where .= " AND D.CATEGORY1_SEQ = :CATEGORY1_SEQ";
            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;

            $where2 .= " AND CATEGORY1_SEQ = :CATEGORY1_SEQ";
            $arrValue2[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;

            global $type_gb3;
            $type_gb3 = $CATEGORY1_SEQ;
        }

        if (!empty($CATEGORY2_SEQ)) { // 시리즈, 분류
            $where .= " AND B.CATEGORY2_SEQ = :CATEGORY2_SEQ";
            $arrValue[':CATEGORY2_SEQ'] = $CATEGORY2_SEQ;

            global $type_gb4;
            $type_gb4 = $CATEGORY2_SEQ;
        }

        if (!empty($TITLE)) { // 작품, 상품
            $where .= " AND M.TITLE LIKE :TITLE";
            $arrValue[':TITLE'] = "%{$TITLE}%";
        }

        if (!empty($TITLE_YN)) { // 메인노출여부
            $where .= " AND M.TITLE_YN = :TITLE_YN";
            $arrValue[':TITLE_YN'] = $TITLE_YN;
        }

        if (!empty($MAIN_YN)) { // 노출여부
            $where .= " AND M.MAIN_YN = :MAIN_YN";
            $arrValue[':MAIN_YN'] = $MAIN_YN;

            $type_gb = $MAIN_YN;
        }

         //날짜 검색
         if (!empty($start_date)) { // 시작일
            $where .= " AND DATE(M.SDATE) >= :sdate";
            $arrValue[':sdate'] = $start_date;
        }

        if (!empty($end_date)) { // 종료일
            $where .= " AND DATE(M.EDATE) <= :edate";
            $arrValue[':edate'] = $end_date;
        }

        $sql = "
             SELECT M.CATEGORY3_SEQ
                  , M.CATEGORY1_SEQ
                  , M.ORDER_NUMBER AS ORDER_NUMBER
                  , D.TITLE AS CATEGORY1_NAME
                  , B.TITLE AS CATEGORY2_NAME
                  , M.TITLE AS CATEGORY3_NAME
                  , ZCM_COM_NM('AD002', M.TITLE_YN) AS TITLE_YN_NM
                  , ZCM_COM_NM('AD002', M.MAIN_YN) AS MAIN_YN_NM
                  , M.SDATE
                  , M.EDATE
                  , M.ATTACH_FILE_ID
                  , M.reg_date
                  , M.reg_user
               FROM {$table} M
               LEFT OUTER JOIN CATEGORY1 D ON M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
               LEFT OUTER JOIN CATEGORY2 B ON M.CATEGORY2_SEQ = B.CATEGORY2_SEQ
              WHERE 1
               {$where}
              ORDER BY M.MAIN_YN DESC, M.ORDER_NUMBER DESC, M.reg_date DESC";

        $name_sql = "카테고리 리스트";
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
            , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ
        );

        $INS_query_string = http_build_query($INS_arrParams);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ
            , 'CATEGORY2_SEQ' => $CATEGORY2_SEQ
            , 'TITLE' => $TITLE
            , 'TITLE_YN' => $TITLE_YN
            , 'MAIN_YN' => $MAIN_YN
            , 'start_date' => $start_date 
            , 'end_date' => $end_date
        );

        $query_string = http_build_query($arrParams);

        $sql = "
            SELECT CATEGORY2_SEQ as COM_CD
                 , TITLE as COM_CD_NM
                 , CATEGORY1_SEQ as TH1_THEM_CD
              FROM CATEGORY2
             WHERE 1
              {$where2}
             ORDER BY ORDER_NUMBER";

        $name_sql = "카테고리2 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue2, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setSERIESComboList($combo_list);

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

        if (!empty($Main_list_arry)) {
            foreach ($Main_list_arry as $data) {
                $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 시퀀스
                $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 카테고리
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_CATEGORY2_NAME = _check_var($data['CATEGORY2_NAME']); // 분류
                $_db_CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 제목
                $_db_SDATE = _check_var($data['SDATE']); // 시작일
                $_db_EDATE = _check_var($data['EDATE']); // 종료일
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                $_db_TITLE_YN_NM = _check_var($data['TITLE_YN_NM']); // 메인노출여부
                $_db_MAIN_YN_NM = _check_var($data['MAIN_YN_NM']); // 노출여부
                $_db_reg_date = _check_var($data['reg_date']); // 등록일
                $_db_reg_user = _check_var($data['reg_user']); // 등록자

                $url = "../board/category_details.php?mode=MOD&{$query_string}";
                $DATE = "";
                $TITLE_HTML = "";

                if ($_db_CATEGORY1_SEQ != "COLLABO") {                    
                    $TITLE_HTML = "<td>{$_db_TITLE_YN_NM}</td>";
                }

                if (!empty($_db_SDATE) && !empty($_db_EDATE)) {
                    $DATE = $_db_SDATE." ~ ".$_db_EDATE;
                }

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                if (!empty($_db_ATTACH_FILE_ID)) {
                    $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                    if (!empty($file_list)) {
                        foreach ($file_list as $list) {
                            $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                            $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                            $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                        }
                    }
                }

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_CATEGORY2_NAME}</td>
                                <td><a href="{$url}&seq={$_db_CATEGORY3_SEQ}">{$_db_CATEGORY3_NAME}</td>
                                <td>
                                    <div class="lightBoxGallery">
                                        <img src="{$path_File}" style="height: 100px;" alt="썸네일 이미지">
                                    </div>
                                </td>
                                {$TITLE_HTML}
                                <td>{$_db_MAIN_YN_NM}</td>
                                <td>{$_db_ORDER_NUMBER}</td>
                                <td>{$_db_reg_user}</td>
                                <td>{$DATE}</td>
                            </tr>
                        TR;
            }
        }
    }

    /**
     * name :setSERIESComboList
     * comment : 콤보박스에 사용하는 값을 전역변수에 저장
     */
    function setSERIESComboList($data) {
        global $combo_list_arry2;
        $combo_list_arry2 = $data;
    }

    /**
     * name :getSERIESComboList
     * comment : 콤보박스에 사용하는 값을 콤보박스 값으로 출력
     */
    function getSERIESComboList() {
        global $combo_list_arry2;
        global $type_gb4;

        $COM_CD = '';
        $COM_CD_NM = '';
        $selected = '';

        foreach($combo_list_arry2 as $array_val) {
            $data_code = '';

            foreach ($array_val as $key => $val) {
                if ($key == 'COM_CD') {
                    $COM_CD = $val;
                } else if ($key == 'COM_CD_NM') {
                    $COM_CD_NM = $val;
                } else if ($key == 'TH1_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code1='$val' ";
                    }
                }
            }

            if (!empty($type_gb4)) {
                $selected = ($type_gb4 == $COM_CD) ? 'selected="selected"' : '';
            }
            
            echo <<<OPTION
                        <option value="{$COM_CD}" $data_code $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }
?>