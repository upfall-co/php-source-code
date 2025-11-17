<?php
/**
 * 파일명 : work_main_code.php
 * 내용 : 작품 메인 페이지 코드
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/07     V1.0
 * 김민성    2023/11/13    shop 기능추가
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
        $PROGRAM_CD = get_request_param('PROGRAM_CD', 'GET'); // 중분류 코드
        $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET'); // 작가 , 카테고리
        $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ', 'GET'); // 시리즈, 분류
        $TITLE = get_request_param('TITLE', 'GET'); // 작품, 상품
        $TITLE_YN = get_request_param('TITLE_YN','GET'); // 메인노출여부
        $MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부
        $start_date = get_request_param('start_date','GET'); // 시작일
        $end_date = get_request_param('end_date','GET'); // 종료일
        $sub_type = isset($common_type) ? $common_type : ''; //서브 페이지 분류

        $category1_name = "";
        $category1_val = "";
        $title_name = "";

        if ($page_type == PAGE1) {
            $category1_name = "작가";
            $category1_val = "작가명";
            $category2_name = "시리즈";
            $category2_val = "시리즈명";
            $title_name = "작품"; // Copy, CSV, Excel, Print 제목
            $title_val = "작품명";
        } else if ($page_type == PAGE2) {
            $category1_name = "카테고리";
            $category1_val = "카테고리명";
            $category2_name = "분류";
            $category2_val = "분류명";
            $title_name = "상품"; // Copy, CSV, Excel, Print 제목
            $title_val = "상품명";
        } else if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE2) {
                $category1_name = "카테고리";
                $category1_val = "카테고리명";
                $category2_name = "분류";
                $category2_val = "분류명";
                $title_name = "제목"; // Copy, CSV, Excel, Print 제목
                $title_val = "제목";
            }
        }

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'CATEGORY3'; // 관리자 테이블
        $type_gb = '';

        //페이지 타입
        if (!empty($page_type)) {
            $where .= " AND M.PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        //서브페이지 타입
        if (!empty($sub_type)) {
            $where .= " AND M.SUB_TYPE = :sub_type";
            $arrValue[':sub_type'] = $sub_type;
        }

        //중분류
        if (!empty($PROGRAM_CD)) {
            $where .= " AND B.PROGRAM_CD = :PROGRAM_CD";
            $arrValue[':PROGRAM_CD'] = $PROGRAM_CD;
        }

        //검색
        if (!empty($CATEGORY1_SEQ)) { // 작가, 카테고리
            $where .= " AND D.CATEGORY1_SEQ = :CATEGORY1_SEQ";
            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;

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
            $where .= " AND DATE(M.reg_date) >= :sdate";
            $arrValue[':sdate'] = $start_date;
        }

        if (!empty($end_date)) { // 종료일
            $where .= " AND DATE(M.reg_date) <= :edate";
            $arrValue[':edate'] = $end_date;
        }

        $sql = "
             SELECT M.CATEGORY3_SEQ
                  , M.ORDER_NUMBER AS ORDER_NUMBER
                  , D.TITLE AS CATEGORY1_NAME
                  , B.TITLE AS CATEGORY2_NAME
                  , M.TITLE AS CATEGORY3_NAME
                  , M.PAGE_TYPE
                  , M.SUB_TYPE
                  , ZCM_COM_NM('AD002', M.TITLE_YN) AS TITLE_YN_NM
                  , ZCM_COM_NM('AD002', M.MAIN_YN) AS MAIN_YN_NM
                  , M.reg_date
                  , M.reg_user
               FROM {$table} M
               LEFT OUTER JOIN CATEGORY1 D ON M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
               LEFT OUTER JOIN CATEGORY2 B ON M.CATEGORY2_SEQ = B.CATEGORY2_SEQ
              WHERE 1
               {$where}
              ORDER BY M.MAIN_YN DESC, M.ORDER_NUMBER DESC, M.reg_date DESC";

        $name_sql = "작품 리스트";
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
            , 'PROGRAM_CD' => $PROGRAM_CD
            , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ
            , 'CATEGORY2_SEQ' => $CATEGORY2_SEQ
            , 'TITLE' => $TITLE
            , 'TITLE_YN' => $TITLE_YN
            , 'MAIN_YN' => $MAIN_YN
            , 'start_date' => $start_date 
            , 'end_date' => $end_date
        );

        $query_string = http_build_query($arrParams);
        
        $arrValue2 = array();
        $arrValue2[':PAGE_TYPE'] = $page_type;
        $arrValue2[':SUB_TYPE'] = $sub_type;

        $sql = "
            SELECT TITLE
                 , CATEGORY1_SEQ
                 , PROGRAM_CD
              FROM CATEGORY1
             WHERE PAGE_TYPE = :PAGE_TYPE
               AND SUB_TYPE = :SUB_TYPE
             ORDER BY ORDER_NUMBER DESC";

        $name_sql = "카테고리1 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue2, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setARTISTComboList($combo_list);

        $sql = "
            SELECT CATEGORY2_SEQ as COM_CD
                 , TITLE as COM_CD_NM
                 , CATEGORY1_SEQ as TH1_THEM_CD
              FROM CATEGORY2
             WHERE PAGE_TYPE = :PAGE_TYPE
               AND SUB_TYPE = :SUB_TYPE
             ORDER BY TITLE";

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
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_CATEGORY1_NAME = _check_var($data['CATEGORY1_NAME']); // 작가명, 카테고리명
                $_db_CATEGORY2_NAME = _check_var($data['CATEGORY2_NAME']); // 시리즈명, 분류명
                $_db_CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 작품명, 상품명
                $_db_PAGE_TYPE = _check_var($data['PAGE_TYPE']); // 홈페이지타입
                $_db_SUB_TYPE = _check_var($data['SUB_TYPE']); // 서브페이지타입
                $_db_TITLE_YN_NM = _check_var($data['TITLE_YN_NM']); // 메인노출여부
                $_db_MAIN_YN_NM = _check_var($data['MAIN_YN_NM']); // 노출여부
                $_db_reg_date = _check_var($data['reg_date']); // 등록일
                $_db_reg_user = _check_var($data['reg_user']); // 등록자
                $TITLE_HTML = '';

                $url = "../board/work_details.php?mode=MOD&{$query_string}";

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                if ($_db_PAGE_TYPE == PAGE3 && $_db_SUB_TYPE == SUB_PAGE2) {
                    $TITLE_HTML = <<<TD
                                         <td>{$_db_TITLE_YN_NM}</td>
                                     TD;
                }

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_CATEGORY1_NAME}</td>
                                <td>{$_db_CATEGORY2_NAME}</td>
                                <td><a href="{$url}&seq={$_db_CATEGORY3_SEQ}">{$_db_CATEGORY3_NAME}</td>
                                {$TITLE_HTML}
                                <td>{$_db_MAIN_YN_NM}</td>
                                <td>{$_db_ORDER_NUMBER}</td>
                                <td>{$_db_reg_user}</td>
                                <td>{$_db_reg_date}</td>
                            </tr>
                        TR;
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
        global $type_gb3;

        $COM_CD = '';
        $COM_CD_NM = '';
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

            if (!empty($type_gb3)) {
                $selected = ($type_gb3 == $COM_CD) ? 'selected="selected"' : '';
            }

            echo <<<OPTION
                        <option value="{$COM_CD}" data-code1="{$TH1_THEM_CD}" $selected>{$COM_CD_NM}</option>
                    OPTION;
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

