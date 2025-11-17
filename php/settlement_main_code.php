<?php
/**
 * 파일명 : settlement_main_code.php
 * 내용 : 결산관리 메인 페이지 코드
 * 최초작성날짜 : 2023/12/07
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/12/07     V1.0
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

        $title_name = "주문내역"; // Copy, CSV, Excel, Print 제목

        if (empty($start_date) && empty($end_date)) {
            $start_date = date('Y-m-d', strtotime('-90 days'));
            $end_date = date('Y-m-d', strtotime('+1 days'));
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
             SELECT A.TODAY
                  , ZCM_WEEK_KOR_NM(A.TODAY) AS WEEK_NAME
                  , FORMAT(A.TOTAL_NOW_COUNT, 0) AS TOTAL_NOW_COUNT
                  , FORMAT(A.TOTAL_DLVY_PRICE, 0) AS TOTAL_DLVY_PRICE
                  , FORMAT(A.TOTAL_NOW_COUNT + A.TOTAL_DLVY_PRICE, 0) AS TOTAL_PRICE
                  , DATE(A.TODAY) AS start_date
                  , DATE(A.TODAY) AS end_date
                  , (SELECT CONCAT('/adm', link, '?m_seq=',seq,'&mp_seq=',parent_seq,'&page_type=', PAGE_TYPE)
                       FROM project_menu
                      WHERE PAGE_TYPE = :page_type
                        AND FIND_IN_SET(link, '/board/orderHistory_main.php') > 0
                      LIMIT 1) AS URL
               FROM (SELECT DATE(M.reg_date) AS TODAY
                          , IFNULL(SUM(D.QUANTITY*(D.PRICE + B.PRICE)), 0) AS TOTAL_NOW_COUNT
                          , IFNULL(SUM(M.DLVY_PRICE), 0) AS TOTAL_DLVY_PRICE
                       FROM {$table} M, PURCHASE_OPTION D, PURCHASE_PRODUCT B
                      WHERE 1
                        AND M.PURCHASE_SEQ = D.PURCHASE_SEQ
                        AND M.PURCHASE_SEQ = B.PURCHASE_SEQ
                        AND B.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                        AND D.STATE_CD NOT IN ('42', '52')
                        {$where}
                        GROUP BY TODAY) A
              ORDER BY A.TODAY DESC";

        $name_sql = "일별 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();
        setMain_List($list);

        $sql = "
             SELECT A.FIRST_WEEK
                  , A.WEEKDAY
                  , A.YEAR_DAY
                  , A.MONTH_NAME
                  , FIRST_WEEK AS WEEK_COUNT
                  , FORMAT(A.TOTAL_NOW_COUNT, 0) AS TOTAL_NOW_COUNT
                  , FORMAT(A.TOTAL_DLVY_PRICE, 0) AS TOTAL_DLVY_PRICE
                  , FORMAT(A.TOTAL_NOW_COUNT + A.TOTAL_DLVY_PRICE, 0) AS TOTAL_PRICE
                  , ZCM_WEEK_SCOPE('S', A.YEAR_DAY, A.MONTH_NAME, A.TODAY_VAL) AS start_date
                  , ZCM_WEEK_SCOPE('E', A.YEAR_DAY, A.MONTH_NAME, A.TODAY_VAL) AS end_date
                  , (SELECT CONCAT('/adm', link, '?m_seq=',seq,'&mp_seq=',parent_seq,'&page_type=', PAGE_TYPE)
                       FROM project_menu
                      WHERE PAGE_TYPE = :page_type
                        AND FIND_IN_SET(link, '/board/orderHistory_main.php') > 0
                      LIMIT 1) AS URL
               FROM (SELECT WEEK(M.reg_date, 5) AS WEEKDAY
                          , YEAR(M.reg_date) AS YEAR_DAY
                          , MONTH(M.reg_date) AS MONTH_NAME
                          , DAY(M.reg_date) AS TODAY_VAL
                          , WEEK(M.reg_date, 5) - WEEK(DATE_SUB(M.reg_date,INTERVAL DAYOFMONTH(M.reg_date)-1 DAY),5) + 1 AS FIRST_WEEK
                          , IFNULL(SUM(D.QUANTITY*(D.PRICE + B.PRICE)), 0) AS TOTAL_NOW_COUNT
                          , IFNULL(SUM(M.DLVY_PRICE), 0) AS TOTAL_DLVY_PRICE
                       FROM {$table} M, PURCHASE_OPTION D, PURCHASE_PRODUCT B
                      WHERE 1
                        AND M.PURCHASE_SEQ = D.PURCHASE_SEQ
                        AND M.PURCHASE_SEQ = B.PURCHASE_SEQ
                        AND B.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                        AND D.STATE_CD NOT IN ('42', '52')
                        {$where}
                        GROUP BY YEAR_DAY, WEEKDAY, FIRST_WEEK) A
              ORDER BY A.MONTH_NAME DESC, A.FIRST_WEEK DESC";

        $name_sql = "주간 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();
        setMain_List2($list);

        $sql = "
             SELECT A.YEAR_DAY
                  , A.MONTHKDAY
                  , FORMAT(A.TOTAL_NOW_COUNT, 0) AS TOTAL_NOW_COUNT
                  , FORMAT(A.TOTAL_DLVY_PRICE, 0) AS TOTAL_DLVY_PRICE
                  , FORMAT(A.TOTAL_NOW_COUNT + A.TOTAL_DLVY_PRICE, 0) AS TOTAL_PRICE
                  , CONCAT(CONCAT(A.YEAR_DAY, '-', A.MONTHKDAY, '-01')) AS start_date
                  , LAST_DAY(CONCAT(A.YEAR_DAY, '-', A.MONTHKDAY, '-01')) AS end_date
                  , (SELECT CONCAT('/adm', link, '?m_seq=',seq,'&mp_seq=',parent_seq,'&page_type=', PAGE_TYPE)
                       FROM project_menu
                      WHERE PAGE_TYPE = :page_type
                        AND FIND_IN_SET(link, '/board/orderHistory_main.php') > 0
                      LIMIT 1) AS URL
               FROM (SELECT MONTH(M.reg_date) AS MONTHKDAY
                          , YEAR(M.reg_date) AS YEAR_DAY
                          , MONTH(M.reg_date) AS MONTH_NAME
                          , IFNULL(SUM(D.QUANTITY*(D.PRICE + B.PRICE)), 0) AS TOTAL_NOW_COUNT
                          , IFNULL(SUM(M.DLVY_PRICE), 0) AS TOTAL_DLVY_PRICE
                       FROM {$table} M, PURCHASE_OPTION D, PURCHASE_PRODUCT B
                      WHERE 1
                        AND M.PURCHASE_SEQ = D.PURCHASE_SEQ
                        AND M.PURCHASE_SEQ = B.PURCHASE_SEQ
                        AND B.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                        AND D.STATE_CD NOT IN ('42', '52')
                        {$where}
                        GROUP BY MONTHKDAY) A
              ORDER BY A.MONTHKDAY DESC";

        $name_sql = "월간 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();
        setMain_List3($list);


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
        );

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

    /**
     * name :setMain_List
     * comment : 메인 데이터 저장 [일별]
     */
    function setMain_List($data) {
        global $Main_list_arry;
        $Main_list_arry = $data;
    }

    /**
     * name :setMain_List2
     * comment : 메인 데이터 저장 [주간]
     */
    function setMain_List2($data) {
        global $Main_list_arry2;
        $Main_list_arry2 = $data;
    }

    /**
     * name :setMain_List
     * comment : 메인 데이터 저장 [월별]
     */
    function setMain_List3($data) {
        global $Main_list_arry3;
        $Main_list_arry3 = $data;
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
                $_db_TODAY = _check_var($data['TODAY']); // 일자
                $_db_WEEK_NAME = _check_var($data['WEEK_NAME']); // 요일명
                $_db_TOTAL_NOW_COUNT = _check_var($data['TOTAL_NOW_COUNT']); // 작품/상품 금액
                $_db_TOTAL_DLVY_PRICE = _check_var($data['TOTAL_DLVY_PRICE']); // 배송비
                $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 토탈금액
                $_db_start_date = _check_var($data['start_date']); // 시작일
                $_db_end_date = _check_var($data['end_date']); // 종료일
                $_db_URL = _check_var($data['URL']); // URL

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                $URL = $_db_URL.'&start_date='. $_db_start_date. '&end_date='. $_db_end_date;

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_WEEK_NAME}</td>
                                <td><a href="{$URL}">{$_db_TODAY}</td>
                                <td style="text-align:right">{$_db_TOTAL_NOW_COUNT}원</td>
                                <td style="text-align:right">{$_db_TOTAL_DLVY_PRICE}원</td>
                                <td style="text-align:right">{$_db_TOTAL_PRICE}원</td>
                            </tr>
                        TR;
            }
        } 
    }

    function getMain_List2() {
        global $Main_list_arry2;
        global $query_string;

        if (!empty($Main_list_arry2)) {
            foreach ($Main_list_arry2 as $data) {
                $_db_YEAR_DAY = _check_var($data['YEAR_DAY']); // 년도
                $_db_MONTH_NAME = _check_var($data['MONTH_NAME']); // 월
                $_db_WEEK_COUNT = _check_var($data['WEEK_COUNT']); // 주차
                $_db_TOTAL_NOW_COUNT = _check_var($data['TOTAL_NOW_COUNT']); // 작품/상품 금액
                $_db_TOTAL_DLVY_PRICE = _check_var($data['TOTAL_DLVY_PRICE']); // 배송비
                $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 토탈금액
                $_db_start_date = _check_var($data['start_date']); // 시작일
                $_db_end_date = _check_var($data['end_date']); // 종료일
                $_db_URL = _check_var($data['URL']); // URL

                $URL = $_db_URL.'&start_date='. $_db_start_date. '&end_date='. $_db_end_date;

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_YEAR_DAY}</td>
                                <td>{$_db_MONTH_NAME}월</td>
                                <td><a href="{$URL}">{$_db_WEEK_COUNT}주차</td>
                                <td style="text-align:right">{$_db_TOTAL_NOW_COUNT}원</td>
                                <td style="text-align:right">{$_db_TOTAL_DLVY_PRICE}원</td>
                                <td style="text-align:right">{$_db_TOTAL_PRICE}원</td>
                            </tr>
                        TR;
            }
        } 
    }

    function getMain_List3() {
        global $Main_list_arry3;
        global $query_string;

        if (!empty($Main_list_arry3)) {
            foreach ($Main_list_arry3 as $data) {
                $_db_YEAR_DAY = _check_var($data['YEAR_DAY']); // 년도
                $_db_MONTHKDAY = _check_var($data['MONTHKDAY']); // 월
                $_db_TOTAL_NOW_COUNT = _check_var($data['TOTAL_NOW_COUNT']); // 작품/상품 금액
                $_db_TOTAL_DLVY_PRICE = _check_var($data['TOTAL_DLVY_PRICE']); // 배송비
                $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 토탈금액
                $_db_start_date = _check_var($data['start_date']); // 시작일
                $_db_end_date = _check_var($data['end_date']); // 종료일
                $_db_URL = _check_var($data['URL']); // URL

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                $URL = $_db_URL.'&start_date='. $_db_start_date. '&end_date='. $_db_end_date;

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_YEAR_DAY}</td>
                                <td><a href="{$URL}">{$_db_MONTHKDAY}월</td>
                                <td style="text-align:right">{$_db_TOTAL_NOW_COUNT}원</td>
                                <td style="text-align:right">{$_db_TOTAL_DLVY_PRICE}원</td>
                                <td style="text-align:right">{$_db_TOTAL_PRICE}원</td>
                            </tr>
                        TR;
            }
        } 
    }

    
?>

