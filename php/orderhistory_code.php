<?php
/**
 * 파일명 : orderhistory_code.php
 * 내용 : 마이페이지 주문내역
 * 최초작성날짜 : 2023/08/22
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/22    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    global $request_list;
    global $limit;
    global $offset;
    global $scale;
    global $total;
    global $page;
    global $where;
    global $arrValue;

    $page = get_request_param('page', 'GET');
    $start_date = get_request_param('start_date','GET');
    $end_date = get_request_param('end_date','GET');


    $arrValue = array();

    if (!is_numeric($page)) {
        $page = 1;
    }
    
    //날짜 검색
    if (!empty($start_date)) { // 시작일
        $where .= " AND DATE(reg_date) >= :sdate";
        $arrValue[':sdate'] = $start_date;
    }

    if (!empty($end_date)) { // 종료일
        $where .= " AND DATE(reg_date) <= :edate";
        $arrValue[':edate'] = $end_date;
    }

    if (isset($_SESSION['MEMBER'])) {
        if (!empty($_SESSION['MEMBER'])) {
            $ID = $_SESSION['MEMBER']['ID'];
            $where .= " AND ID = '{$ID}'";
        }
    }

    $limit = 10;
    $scale = 10;
    $total = 0;

    $tpye = "Mypage";

    /**
     * name :getList_Order
     * comment : 주문내역
     */
    function getList_Order() {
        global $limit;
        global $offset;
        global $total;
        global $request_list;
        global $page;
        global $where;
        global $arrValue;

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $page_type = PAGE;
            
            //페이지타입
            if (!empty($page_type)) {
                $where .= " AND PAGE_TYPE = :page_type";
                $arrValue[':page_type'] = $page_type;
            }

            $table = 'PURCHASE_ORDER'; //테이블

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE 1
                   {$where}";

            $name_sql = "주문 개수 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total <= $limit) {
                $page = 1;
            }

            $request_list .= "page=". $page;

            $offset = ($page - 1) * $limit;

            $sql = "
                 SELECT PURCHASE_SEQ
                      , DATE_FORMAT(reg_date, '%Y. %m. %d') AS reg_date_nm
                      , (SELECT CONCAT(IF(COUNT(*) > 1, 
                                     CONCAT(MIN(D.CATEGORY3_NAME), ' 외 '), 
                                     GROUP_CONCAT(DISTINCT D.CATEGORY3_NAME SEPARATOR ', ')))
                           FROM PURCHASE_PRODUCT D, PURCHASE_OPTION C
                          WHERE M.PURCHASE_SEQ = D.PURCHASE_SEQ
                            AND D.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND D.CATEGORY3_SEQ = C.CATEGORY3_SEQ) AS CATEGORY3_NAME
                      , FORMAT(TOTAL_PRICE, 0) AS TOTAL_PRICE_TEXT
                      , ZCM_COM_NM('COL005',STATE_CD) AS STATE_CD_NM
                   FROM {$table} M
                  WHERE 1
                    {$where} 
                  ORDER BY reg_date DESC
                  LIMIT {$offset}, {$limit}";

            $name_sql = "주문내역 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $no = $total - $offset;

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_PURCHASE_SEQ = _check_var($data['PURCHASE_SEQ']); // 주문번호 [시퀀스]
                    $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 주문일자
                    $_db_CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 작품명
                    $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 총 주문 금액
                    $_db_STATE_CD_NM = _check_var($data['STATE_CD_NM']); // 주문상태

                    echo <<<LI
                                <li class="tbody">
                                    <div class="td_order_date">{$_db_reg_date_nm}</div>
                                    <div class="td_order_number">
                                        <div class="order_history_pop_btn" onclick="history_pop('{$_db_PURCHASE_SEQ}')">{$_db_PURCHASE_SEQ}</div>
                                    </div>
                                    <div class="td_order_name">{$_db_CATEGORY3_NAME}</div>
                                    <div class="td_order_price"><span>{$_db_TOTAL_PRICE_TEXT}</span>원</div>
                                    <div class="td_order_state"><p>{$_db_STATE_CD_NM}</p></div>
                                </li>
                            LI;

                    $no--;
                }
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

?>