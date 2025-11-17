<?php
/**
 * 파일명 : orderHistory_pop_code.php
 * 내용 : 주문상세정보 
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/08    V1.0
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
        global $_db_TOTAL_COUNT;
        global $_db_TOTAL_PRICE;
        global $_db_TOTAL_PRICE_TEXT;
        global $PRODUCTS;
        global $prd_list_mdoe;

        $NAME = '';
        $MOBILE = '';
        $EMAIL = '';

        $PURCHASE_SEQ = get_request_param('SEQ', 'GET');

        $arrValue = array();
        $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

        $sql = "
             SELECT TYPE_CD
                  , STATE_CD
                  , INICIS_SEQ
                  , DATE_FORMAT(reg_date, '%Y. %m. %d') AS reg_date_nm
                  , DLVY_PRICE
                  , M.MOBILE
                  , (SELECT IFNULL(SUM(C.PRICE), 0)
                       FROM PURCHASE_ORDER B, PURCHASE_OPTION C
                      WHERE 1
                        AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                        AND B.PURCHASE_SEQ = M.PURCHASE_SEQ
                        AND C.STATE_CD NOT IN ('42', '52')) AS TOTAL_NOW_PRICE
               FROM PURCHASE_ORDER M
              WHERE PURCHASE_SEQ = :PURCHASE_SEQ";

        $name_sql = "주문내역";
        $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            //dieAndErrorMove("잘못된 접근입니다.");
        }
        
        $TYPE_CD = _check_var($data['TYPE_CD']); // 결제방식
        $STATE_CD = _check_var($data['STATE_CD']); // 주문상태
        $INICIS_SEQ = _check_var($data['INICIS_SEQ']); // 이니시스 시퀀스
        $_db_MOBILE = _check_var($data['MOBILE']); // 주문자 - 연락처
        $TOTAL_NOW_PRICE = _check_var($data['TOTAL_NOW_PRICE']); // 취소, 환불한 현금액
        $REAL_DLVY_PRICE = _check_var($data['DLVY_PRICE']); // 배송비
        $reg_date_nm = _check_var($data['reg_date_nm']); // 주문일자


        $STATE_VAL = ["01", "81", "82", "21", "60", "83", "84", "30", "61", "85", "86", "41", "42"];

        if (in_array($STATE_CD, $STATE_VAL)) {
            $STATE_MODE = "CANCEL";
        } else {
            $STATE_MODE = "REFUND";
        }

        $prd_list_mdoe = "ORDER";
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

    function getPrdChkList() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            global $_db_TOTAL_COUNT;
            global $_db_TOTAL_PRICE;
            global $_db_TOTAL_PRICE_TEXT;

            $PURCHASE_SEQ = get_request_param('SEQ', 'GET');

            $arrValue = array();
            $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;
    
            $table = 'PURCHASE_PRODUCT'; // 작품 테이블
            $table_OP = 'PURCHASE_OPTION'; // 옵션 관리자 테이블
    
            $sql = "
                 SELECT M.PURCHASE_SEQ
                      , M.CATEGORY3_SEQ
                      , M.ATTACH_FILE_ID
                      , M.CATEGORY3_NAME
                      , D.OPTION_NAME
                      , M.FRAME
                      , D.QUANTITY
                      , D.PRICE
                      , D.OPTION_SEQ
                      , A.STATE_CD
                      , D.STATE_CD AS OP_STATE_CD
                      , ZCM_COM_NM('AD009', D.STATE_CD) AS OP_STATE_CD_NM
                      , (SELECT SUM(C.QUANTITY*C.PRICE)
                           FROM PURCHASE_OPTION C
                          WHERE M.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND D.OPTION_SEQ = C.OPTION_SEQ) AS OPTION_PRICE
                      , FORMAT((SELECT SUM(C.QUANTITY*C.PRICE)
                                  FROM PURCHASE_OPTION C
                                 WHERE M.PURCHASE_SEQ = C.PURCHASE_SEQ
                                   AND D.OPTION_SEQ = C.OPTION_SEQ), 0) AS OPTION_PRICE_TEXT
                      , (SELECT IFNULL(COUNT(*), 0)
                           FROM PURCHASE_ORDER B, PURCHASE_OPTION C
                          WHERE 1
                            AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND B.PURCHASE_SEQ = A.PURCHASE_SEQ
                            AND C.STATE_CD NOT IN ('42', '52')) AS TOTAL_COUNT
                      , (SELECT IFNULL(SUM(C.PRICE), 0)
                           FROM PURCHASE_ORDER B, PURCHASE_OPTION C
                          WHERE 1
                            AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND B.PURCHASE_SEQ = A.PURCHASE_SEQ
                            AND C.STATE_CD NOT IN ('42', '52')) AS TOTAL_PRICE
                      , FORMAT((SELECT IFNULL(SUM(C.QUANTITY*C.PRICE), 0)
                                  FROM PURCHASE_ORDER B, PURCHASE_OPTION C
                                 WHERE 1
                                   AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                                   AND B.PURCHASE_SEQ = A.PURCHASE_SEQ
                                   AND C.STATE_CD NOT IN ('42', '52')), 0) AS TOTAL_PRICE_TEXT
                   FROM PURCHASE_ORDER A, {$table} M, {$table_OP} D
                  WHERE A.PURCHASE_SEQ = M.PURCHASE_SEQ
                    AND A.PURCHASE_SEQ = D.PURCHASE_SEQ
                    AND M.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                    AND A.PURCHASE_SEQ = :PURCHASE_SEQ
                  ORDER BY M.CATEGORY3_NAME, D.OPTION_NAME DESC";

            $name_sql = "주문내역 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);
    
            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                $count = 1;

                foreach ($list as $data) {
                    $_db_PURCHASE_SEQ = _check_var($data['PURCHASE_SEQ']); // 주문번호 시퀀스
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 작품 시퀀스
                    $_db_CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 작품명
                    $_db_OPTION_NAME = _check_var($data['OPTION_NAME']); // 옵션명
                    $_db_FRAME = _check_var($data['FRAME']); // 프레임
                    $_db_QUANTITY = _check_var($data['QUANTITY']); // 수량
                    $_db_OPTION_PRICE = _check_var($data['OPTION_PRICE']); // 옵션별 토탈 금액
                    $_db_OPTION_PRICE_TEXT = _check_var($data['OPTION_PRICE_TEXT']); // 옵션별 토탈 금액
                    $_db_OPTION_SEQ = _check_var($data['OPTION_SEQ']); // 옵션시퀀스
                    $_db_STATE_CD = _check_var($data['STATE_CD']); // 주문상태 - 전체 
                    $_db_OP_STATE_CD = _check_var($data['OP_STATE_CD']); // 주문상태 - 개별
                    $_db_OP_STATE_CD_NM = _check_var($data['OP_STATE_CD_NM']); // 주문상태 -개별
                    $_db_TOTAL_COUNT = _check_var($data['TOTAL_COUNT']); // 토탈 개수
                    $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 토탈 금액 
                    $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 토탈 금액 
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

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

                    $checked = "";

                    if ($_db_OP_STATE_CD == "42" || $_db_OP_STATE_CD == "52") { // 42 : 주문취소
                        $_db_QUANTITY = 0;
                        $_db_OPTION_PRICE = 0;
                        $_db_OPTION_PRICE_TEXT = 0;
                    }

                    echo <<<LI
                                <li class="tbody">
                                    <ul class="table_column">
                                        <li class="td_chk" data-totalcount="{$_db_TOTAL_COUNT}" data-pk="{$_db_PURCHASE_SEQ}" data-seq="{$_db_CATEGORY3_SEQ}" data-code="{$_db_OPTION_SEQ}" data-state="{$_db_OP_STATE_CD}" data-val="{$_db_OPTION_PRICE}" data-count="{$_db_QUANTITY}">
                                            <input type="checkbox" id="prdChk{$count}" name="prdChk" $checked>
                                            <label for="prdChk{$count}"></label>
                                        </li>
                                        <li class="td_img">
                                            <div class="shop_img"><img src="{$path_File}" alt="작품 이미지"></div>
                                        </li>
                                        <li class="td_name">{$_db_CATEGORY3_NAME}</li>
                                        <li class="td_option">{$_db_OPTION_NAME}</li>
                                        <li class="td_frame">{$_db_FRAME}</li>
                                        <li class="td_count">{$_db_QUANTITY}</li>
                                        <li class="td_price"><span>{$_db_OPTION_PRICE_TEXT}</span> 원</li>
                                        <li class="td_op_state">{$_db_OP_STATE_CD_NM}</li>
                                    </ul>
                                </li>
                            LI;

                    $count++;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }