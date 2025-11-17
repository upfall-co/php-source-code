<?php
/**
 * 파일명 : delivery_pop_code.php
 * 내용 : 배송정보 
 * 최초작성날짜 : 2023/11/20
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/11/20    V1.0
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

    function getPrdChkList() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            global $_db_TOTAL_COUNT;
            global $_db_TOTAL_PRICE;
            global $_db_TOTAL_PRICE_TEXT;

            $PURCHASE_SEQ = get_request_param('SEQ', 'GET');
            $PAGE = get_request_param('page_type', 'GET');

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
                      , D.INVOICE_NUMBER
                      , ZCM_COM_NM('AD009', D.STATE_CD) AS OP_STATE_CD_NM
                      , (SELECT SUM(C.QUANTITY*(M.PRICE + C.PRICE))
                           FROM PURCHASE_OPTION C
                          WHERE M.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND D.OPTION_SEQ = C.OPTION_SEQ) AS OPTION_PRICE
                      , FORMAT((SELECT SUM(C.QUANTITY*(M.PRICE + C.PRICE))
                                  FROM PURCHASE_OPTION C
                                 WHERE M.PURCHASE_SEQ = C.PURCHASE_SEQ
                                   AND D.OPTION_SEQ = C.OPTION_SEQ), 0) AS OPTION_PRICE_TEXT
                      , (SELECT IFNULL(COUNT(*), 0)
                           FROM PURCHASE_ORDER B, PURCHASE_OPTION C
                          WHERE 1
                            AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND B.PURCHASE_SEQ = A.PURCHASE_SEQ) AS TOTAL_COUNT
                      , (SELECT IFNULL(SUM(C.QUANTITY*(C.PRICE + E.PRICE)), 0)
                           FROM PURCHASE_ORDER B, PURCHASE_OPTION C, PURCHASE_PRODUCT E
                          WHERE 1
                            AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                            AND B.PURCHASE_SEQ = E.PURCHASE_SEQ
                            AND B.PURCHASE_SEQ = A.PURCHASE_SEQ) AS TOTAL_PRICE
                      , FORMAT((SELECT IFNULL(SUM(C.QUANTITY*(C.PRICE + E.PRICE)), 0)
                                  FROM PURCHASE_ORDER B, PURCHASE_OPTION C, PURCHASE_PRODUCT E
                                 WHERE 1
                                   AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                                   AND B.PURCHASE_SEQ = E.PURCHASE_SEQ
                                   AND B.PURCHASE_SEQ = A.PURCHASE_SEQ), 0) AS TOTAL_PRICE_TEXT
                   FROM PURCHASE_ORDER A, {$table} M, {$table_OP} D
                  WHERE A.PURCHASE_SEQ = M.PURCHASE_SEQ
                    AND A.PURCHASE_SEQ = D.PURCHASE_SEQ
                    AND M.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                    AND A.PURCHASE_SEQ = :PURCHASE_SEQ
                    AND A.PAGE_TYPE = '{$PAGE}'
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
                    $_db_INVOICE_NUMBER = _check_var($data['INVOICE_NUMBER']); // 송장번호

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

                    $URL_html = "";

                    if (!empty($_db_INVOICE_NUMBER)) {
                        $_db_INVOICE_NUMBER_HREF = str_replace('-', '', $_db_INVOICE_NUMBER);
                        
                        $URL_html = '<a href="https://www.ilogen.com/m/personal/trace/'. $_db_INVOICE_NUMBER_HREF .'" target="_blank">'.$_db_INVOICE_NUMBER.'</a>';
                    } else {
                        $URL_html = "미등록";
                    }
                    

                    echo <<<DIV
                                <div>
                                    <div class="prdName">{$_db_CATEGORY3_NAME}</div>
                                    <div class="delivery_state">
                                        <div class="state">{$_db_OP_STATE_CD_NM}</div>
                                        <div class="invoice">
                                            <span>송장번호</span>
                                            {$URL_html}
                                        </div>
                                    </div>
                                </div>
                            DIV;

                    $count++;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>