<?php
/**
 * 파일명 : shop_orderhistory_code.php
 * 내용 : 마이페이지 주문내역 [샵전용]
 * 최초작성날짜 : 2023/11/21
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/11/21    V1.0
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

    $arrValue = array();

    if (!is_numeric($page)) {
        $page = 1;
    }
    
    if (isset($_SESSION['MEMBER'])) {
        if (!empty($_SESSION['MEMBER'])) {
            $ID = $_SESSION['MEMBER']['ID'];
            $where .= " AND M.ID = '{$ID}'";
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
                $where .= " AND M.PAGE_TYPE = :page_type";
                $arrValue[':page_type'] = $page_type;
            }

            $table = 'PURCHASE_ORDER'; //테이블
            $table_OP = 'PRODUCT_OPTION_TEMP'; // 옵션 관리자 테이블

            $sql = "
                 SELECT *
                   FROM {$table} M
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
                 SELECT M.PURCHASE_SEQ
                      , ZCM_COM_NM('COL003', M.TYPE_CD) AS TYPE_CD_NM
                      , DATE_FORMAT(reg_date, '%Y. %m. %d') AS reg_date_nm
                      , (SELECT CONCAT(IF(COUNT(*) > 1, 
                                     CONCAT(MIN(D.CATEGORY3_NAME), ' 외 '), 
                                     GROUP_CONCAT(DISTINCT D.CATEGORY3_NAME SEPARATOR ', ')))
                           FROM PURCHASE_PRODUCT D, PURCHASE_OPTION C
                          WHERE M.PURCHASE_SEQ = D.PURCHASE_SEQ
                            AND D.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND D.CATEGORY3_SEQ = C.CATEGORY3_SEQ) AS CATEGORY3_NAME
                      , (SELECT C.CATEGORY3_SEQ
                           FROM PURCHASE_PRODUCT D, PURCHASE_OPTION C
                          WHERE M.PURCHASE_SEQ = D.PURCHASE_SEQ
                            AND D.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND D.CATEGORY3_SEQ = C.CATEGORY3_SEQ
                            ORDER BY C.CATEGORY3_SEQ DESC
                            LIMIT 1) AS CATEGORY3_SEQ
                      , (SELECT D.ATTACH_FILE_ID
                           FROM PURCHASE_PRODUCT D, PURCHASE_OPTION C
                          WHERE M.PURCHASE_SEQ = D.PURCHASE_SEQ
                            AND D.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND D.CATEGORY3_SEQ = C.CATEGORY3_SEQ
                            ORDER BY C.CATEGORY3_SEQ DESC
                            LIMIT 1) AS ATTACH_FILE_ID
                      , M.STATE_CD
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
                    $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 결제수단
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 작품 시퀀스
                    $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 주문일자
                    $_db_CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 작품명
                    $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 총 주문 금액
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                    $_db_STATE_CD_NM = _check_var($data['STATE_CD_NM']); // 주문상태

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);
            
                        if (!empty($file_list)) {
                            foreach ($file_list as $list) {
                                $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                                $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    if (PAGE == PAGE1) {
                    } else if (PAGE == PAGE2) {
                        $url = shopFoldName. "/product/detail.php?seq=". $_db_CATEGORY3_SEQ;
                        $url2 = shopFoldName. "/order/orderHistory_result.php?SEQ=". $_db_PURCHASE_SEQ;
                        $shopFoldName = shopFoldName;

                        echo <<<LI
                                    <li>
                                        <div class="thead">
                                            <div>
                                                <span>주문일자</span>
                                                <div class="td_order_date">{$_db_reg_date_nm}</div>
                                            </div>
                                            <div>
                                                <span>주문번호</span>
                                                <a href="{$url2}" class="td_order_number">
                                                    <span>{$_db_PURCHASE_SEQ}</span>
                                                    <img src="{$shopFoldName}/img/mypage/order_history_link.png" alt="링크">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="tbody">
                                            <div class="td_order_thumbnail">
                                                <a href="{$url}" class="prdThumbnail"><img src="{$path_File}"></a>
                                            </div>
                                            <div class="td_order_info_wrap">
                                                <div class="td_2">
                                                    <div class="td_order_name">{$_db_CATEGORY3_NAME}</div>
                                                    <div class="price_and_type">
                                                        <div class="td_order_price"><span>{$_db_TOTAL_PRICE_TEXT}</span></div>
                                                        <div class="td_order_pay_type">{$_db_TYPE_CD_NM}</div>
                                                    </div>
                                                </div>
                                                <div class="td_3">
                                                    <div class="td_order_state"><p>{$_db_STATE_CD_NM}</p></div>
                                                    <div class="td_order_tracking">
                                                        <button type="button" onclick="onTrackingPop('{$_db_PURCHASE_SEQ}');" class="border_btn">배송조회</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                LI;
                    }

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