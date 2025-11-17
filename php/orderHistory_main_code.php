<?php
/**
 * 파일명 : notice_main_code.php
 * 내용 : 주문내역 메인 페이지 코드
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 이보경 (writer_main_code.php 복붙해 수정)
 * ------------------------------------
 * name       date        comment
 * 이보경    2023/08/08     V1.0
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
        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET'); // 페이지타입
        $start_date = get_request_param('start_date','GET'); // 날짜
        $end_date = get_request_param('end_date','GET'); // 날짜 
        $TYPE_CD = get_request_param('TYPE_CD','GET'); // 결제수단
        $STATE_CD = get_request_param('STATE_CD','GET'); // 주문상태
        $PUR_NUM = get_request_param('PUR_NUM','GET'); // 주문번호
        $DLVY_NAME = get_request_param('DLVY_NAME','GET'); // 주문자

        $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET'); // 작가, 카테고리
        $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ', 'GET'); // 시리즈, 분류
        $CATEGORY3_SEQ = get_request_param('CATEGORY3_SEQ', 'GET'); // 작품, 상품

        $CATEGORY1_NAME = get_request_param('CATEGORY1_NAME', 'GET'); // 작가명, 카테고리명
        $CATEGORY2_NAME = get_request_param('CATEGORY2_NAME', 'GET'); // 시리즈명, 분류명
        $CATEGORY3_NAME = get_request_param('CATEGORY3_NAME','GET'); // 작품명, 상품명

        $title_name = "주문내역"; // Copy, CSV, Excel, Print 제목

        if (empty($start_date) && empty($end_date)) {
            $start_date = date('Y-m-d', strtotime('-90 days'));
            $end_date = date('Y-m-d', strtotime('+1 days'));
        }

        $category1_name = "";
        $category1_val = "";
        $category2_name = "";
        $category2_val = "";
        $category3_name = "";
        $category3_val = "";

        if ($page_type == PAGE1) {
            $category1_name = "작가";
            $category1_val = "작가명";
            $category2_name = "시리즈";
            $category2_val = "시리즈명";
            $category3_name = "작품";
            $category3_val = "작품명";
        } else if ($page_type == PAGE2) {
            $category1_name = "카테고리";
            $category1_val = "카테고리명";
            $category2_name = "분류";
            $category2_val = "분류명";
            $category3_name = "상품";
            $category3_val = "상품명";
        }

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'PURCHASE_ORDER'; // 관리자 테이블
        $type_gb = '';

        //페이지 타입
        if (!empty($page_type)) {
            $where .= " AND M.PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        if (!empty($CATEGORY1_SEQ)) { // 작가
            $where .= " AND D.CATEGORY1_SEQ = :CATEGORY1_SEQ";
            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
        }

        if (!empty($CATEGORY2_SEQ)) { // 시리즈
            $where .= " AND D.CATEGORY2_SEQ = :CATEGORY2_SEQ";
            $arrValue[':CATEGORY2_SEQ'] = $CATEGORY2_SEQ;
        }

        if (!empty($CATEGORY3_SEQ)) { // 작품
            $where .= " AND D.CATEGORY3_SEQ = :CATEGORY3_SEQ";
            $arrValue[':CATEGORY3_SEQ'] = $CATEGORY3_SEQ;
        }

        if (!empty($CATEGORY1_NAME)) { // 작가명
            $where .= " AND D.CATEGORY1_NAME LIKE :CATEGORY1_NAME";
            $arrValue[':CATEGORY1_NAME'] = "%{$CATEGORY1_NAME}%";
        }

        if (!empty($CATEGORY2_NAME)) { // 시리즈명
            $where .= " AND D.CATEGORY2_NAME LIKE :CATEGORY2_NAME";
            $arrValue[':CATEGORY2_NAME'] = "%{$CATEGORY2_NAME}%";
        }

        if (!empty($CATEGORY3_NAME)) { // 작품명
            $where .= " AND D.CATEGORY3_NAME LIKE :CATEGORY3_NAME";
            $arrValue[':CATEGORY3_NAME'] = "%{$CATEGORY3_NAME}%";
        }

        if (!empty($STATE_CD)) { // 주문상태
            $where .= " AND M.STATE_CD = :STATE_CD";
            $arrValue[':STATE_CD'] = $STATE_CD;

            $STATE_CD = $STATE_CD;
        }

        if (!empty($TYPE_CD)) { // 결제수단
            $where .= " AND M.TYPE_CD = :TYPE_CD";
            $arrValue[':TYPE_CD'] = $TYPE_CD;

            $TYPE_CD = $TYPE_CD;
        }

        if (!empty($PUR_NUM)) { // 주문번호
            $where .= " AND M.PURCHASE_SEQ LIKE :PURCHASE_SEQ";
            $arrValue[':PURCHASE_SEQ'] = "%{$PUR_NUM}%";
        }

        if (!empty($DLVY_NAME)) { // 주문자
            $where .= " AND M.NAME LIKE :NAME";
            $arrValue[':NAME'] = "%{$DLVY_NAME}%";
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
             SELECT M.PURCHASE_SEQ
                  , ZCM_COM_NM('COL003', M.TYPE_CD) AS TYPE_CD_NM
                  , DATE_FORMAT(M.reg_date, '%Y. %m. %d') AS reg_date_nm
                  , (SELECT CONCAT(IF(COUNT(*) > 1, 
                                     CONCAT(MIN(CATEGORY3_NAME), ' 외 '), 
                                     GROUP_CONCAT(DISTINCT CATEGORY3_NAME SEPARATOR ', ')))
                       FROM PURCHASE_PRODUCT
                      WHERE M.PURCHASE_SEQ = PURCHASE_SEQ) AS CATEGORY3_NAME
                  , IF(CASH_YN = 'Y', '발급', '미발급') AS CASH_YN_NM
                  , FORMAT(M.TOTAL_PRICE, 0) AS TOTAL_PRICE_TEXT
                  , M.reg_user
                  , IF(ID IS NOT NULL AND ID <> '', '회원', '비회원') AS STATE_TYPE_NM
                  , ZCM_COM_NM('COL005', M.STATE_CD) AS STATE_CD_NM
               FROM {$table} M, PURCHASE_PRODUCT D
              WHERE 1
                AND M.PURCHASE_SEQ = D.PURCHASE_SEQ
                {$where}
              GROUP BY M.PURCHASE_SEQ
              ORDER BY M.reg_date DESC";

        $name_sql = "주문내역 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();
        setMain_List($list);

        $arrValue2 = array();
        $arrValue2[':PAGE_TYPE'] = $page_type;

        $sql = "
            SELECT TITLE
                 , CATEGORY1_SEQ
              FROM CATEGORY1
             WHERE PAGE_TYPE = :PAGE_TYPE
             ORDER BY TITLE";

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
              ORDER BY TITLE";

        $name_sql = "카테고리2 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue2, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setSERIESComboList($combo_list);

        $sql = "
             SELECT DISTINCT CATEGORY3_SEQ as COM_CD
                  , TITLE as COM_CD_NM
                  , CATEGORY2_SEQ as TH1_THEM_CD
               FROM CATEGORY3
              WHERE PAGE_TYPE = :PAGE_TYPE
              ORDER BY TITLE";

        $name_sql = "카테고리3 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue2, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setPRODUCTComboList($combo_list);


        $INS_arrParams = array( // 초기화 및 등록
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
        );

        $INS_query_string = http_build_query($INS_arrParams);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type //페이지 타입
            , 'start_date' => $start_date // 시작일
            , 'end_date' => $end_date // 종료일
            , 'TYPE_CD' => $TYPE_CD // 결제수단
            , 'STATE_CD' => $STATE_CD // 주문상태
            , 'PUR_NUM' => $PUR_NUM // 주문번호
            , 'DLVY_NAME' => $DLVY_NAME // 주문자
            , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ // 작가
            , 'CATEGORY2_SEQ' => $CATEGORY2_SEQ // 시리즈
            , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ // 작룸
            , 'CATEGORY1_NAME' => $CATEGORY1_NAME // 작가명
            , 'CATEGORY2_NAME' => $CATEGORY2_NAME // 시리즈명
            , 'CATEGORY3_NAME' => $CATEGORY3_NAME // 작품명
        );

        $query_string = http_build_query($arrParams);
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
                $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 결제수단
                $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 주문일자
                $_db_PURCHASE_SEQ = _check_var($data['PURCHASE_SEQ']); // 주문번호 [시퀀스]
                $_db_CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 작품명
                $_db_CASH_YN_NM = _check_var($data['CASH_YN_NM']); // 현금영수증 발급여부
                $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 총 주문 금액
                $_db_reg_user = _check_var($data['reg_user']); // 주문자
                $_db_STATE_TYPE_NM = _check_var($data['STATE_TYPE_NM']); // 주문구분 (회원/ 비회원)
                $_db_STATE_CD_NM = _check_var($data['STATE_CD_NM']); // 주문상태

                $url = "../board/orderHistory_details.php?mode=MOD&{$query_string}";

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }


                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_TYPE_CD_NM}</td>
                                <td>{$_db_reg_date_nm}</td>
                                <td><a href="{$url}&seq={$_db_PURCHASE_SEQ}">{$_db_PURCHASE_SEQ}</a></td>
                                <td>{$_db_CATEGORY3_NAME}</td>
                                <td style="text-align:right">{$_db_TOTAL_PRICE_TEXT}원</td>
                                <td>{$_db_CASH_YN_NM}</td>
                                <td>{$_db_reg_user}</td>
                                <td>{$_db_STATE_TYPE_NM}</td>
                                <td>{$_db_STATE_CD_NM}</td>
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
     * comment : 작가 리스트
     */
    function getARTISTComboList() {
        global $combo_list_arry;

        $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET');

        $COM_CD = '';
        $COM_CD_NM = '';
        $selected = '';

        foreach($combo_list_arry as $array_val) {
            foreach($array_val as $key => $val) {
                if ($key == 'CATEGORY1_SEQ') {
                    $COM_CD = $val;
                } else {
                    $COM_CD_NM = $val;
                }
            }

            if (!empty($CATEGORY1_SEQ)) {
                $selected = ($CATEGORY1_SEQ == $COM_CD) ? 'selected="selected"' : '';
            }

            echo <<<OPTION
                        <option value="{$COM_CD}" $selected>{$COM_CD_NM}</option>
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
     * comment : 시리즈 리스트
     */
    function getSERIESComboList() {
        global $combo_list_arry2;

        $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ', 'GET');

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

            if (!empty($CATEGORY2_SEQ)) {
                $selected = ($CATEGORY2_SEQ == $COM_CD) ? 'selected="selected"' : '';
            }
            
            echo <<<OPTION
                        <option value="{$COM_CD}" $data_code $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }

    

      /**
     * name :setPRODUCTComboList
     * comment : 콤보박스에 사용하는 값을 전역변수에 저장
     */
    function setPRODUCTComboList($data) {
        global $combo_list_arry3;
        $combo_list_arry3 = $data;
    }

    /**
     * name :getPRODUCTComboList
     * comment : 작품 리스트
     */
    function getPRODUCTComboList() {
        global $combo_list_arry3;

        $CATEGORY3_SEQ = get_request_param('CATEGORY3_SEQ', 'GET');

        $COM_CD = '';
        $COM_CD_NM = '';
        $selected = '';

        foreach($combo_list_arry3 as $array_val) {
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

            if (!empty($CATEGORY3_SEQ)) {
                $selected = ($CATEGORY3_SEQ == $COM_CD) ? 'selected="selected"' : '';
            }
            
            echo <<<OPTION
                        <option value="{$COM_CD}" $data_code $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }
?>

