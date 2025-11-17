<?php
/**
 * 파일명 : order.php
 * 내용 : 결제 (등록, 수정, 삭제)
 * 최초작성날짜 : 2023/08/22
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/22     V1.0
 */

    // 주문번호 시퀀스 기준
    // 날짜  + 페이지정보 (10 : 시크릿코드 , 이후추가예정) + (카드 : 20 , 실시간 : 21, 무통장 : 23)

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $arrRtn = array(
          'code' => 500
        , 'msg'  => ''
        , 'mode' => ''
        , 'url' => ''
    );

    try {
        //파라미터 정리
        $mode = get_request_param('mode');

        switch ($mode) {
            case 'INS' :
                $arrRes = order_insert();
                break;
            case 'MOD' :
                $arrRes = order_update();
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }

        if ($arrRes['code'] != 200) {
            throw new Exception($arrRes['msg'], $arrRes['code']);
        }

        $m_seq = get_request_param('m_seq');
        $mp_seq = get_request_param('mp_seq');
        $page_type = get_request_param('page_type');
        $M_TITLE = get_request_param('M_TITLE'); // 제목
        $M_MAIN_YN = get_request_param('M_MAIN_YN'); // 노출여부

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];
        $arrRtn['url'] = $arrRes['url'];

        dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
    //성공
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        dieAndErrorMove($arrRtn['msg']);
    }

    /**
     * name :row_insert
     * comment : 등록
     */
    function order_insert() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            //트랜잭션
            $mysqldb->link->beginTransaction();
            
            // hidden 값 모음
            $PRODUCT_TEMP_SEQ = get_request_param('SEQ'); // 주문페이지 시퀀스
            $PRODUCTS = get_request_param('PRODUCTS'); // 주문 작품,상품 시퀀스
            $PAGE_TYPE = get_request_param('PAGE_TYPE'); // 페이지 타입
            $TYPE = get_request_param('TYPE'); // 페이지 타입 값 10 : collection
            $TOTAL_COUNT = get_request_param('TOTAL_COUNT'); // 토탈 개수
            $TOTAL_PRICE = get_request_param('TOTAL_PRICE'); // 토탈금액
            $POINT_PRICE = get_request_param('POINT_PRICE'); // 할인금액

            // 주문자 정보 입력
            $NAME = get_request_param('order_name'); // 주문자 - 이름
            $MOBILE = get_request_param('order_tel'); // 주문자 - 연락처
            $EMAIL = get_request_param('order_email'); // 주문자 - 이메일

            // 배송정보 입력
            $DLVY_NAME = get_request_param('DLVY_NAME'); // 배송정보 - 이름
            $DLVY_MOBILE = get_request_param('DLVY_MOBILE'); // 배송정보 - 연락처
            $DLVY_EMAIL = get_request_param('DLVY_EMAIL'); // 배송정보 - 이메일 [필요시 나중에추가]
            $DLVY_ADDRESS_ZIPCODE = get_request_param('DLVY_ADDRESS_ZIPCODE'); // 배송정보 - 주소 (우편번호)
            $DLVY_ADDRESS = get_request_param('DLVY_ADDRESS'); // 배송정보 - 기본주소 
            $DLVY_ADDRESSDETAIL = get_request_param('DLVY_ADDRESSDETAIL'); // 배송정보 - 상세주소
            $DLVY_MESSAGE = get_request_param('DLVY_MESSAGE'); // 배송정보 - 배송메세지
            $DLVY_PRICE = get_request_param('REAL_DLVY_PRICE'); // 배송비

            $orderPayType = get_request_param('orderPayType'); // 결제 수단

            $agreeChk1 = get_request_param('agreeChk1'); // 이용약관
            $agreeChk2 = get_request_param('agreeChk2'); // 취급방침
            $agreeChk3 = "N"; // 추가시

            if ($agreeChk1 != "Y") {
                $agreeChk1 = "N";
            }

            if ($agreeChk2 != "Y") {
                $agreeChk2 = "N";
            }

            if (empty($POINT_PRICE)) {
                $POINT_PRICE = 0;
            }

            if (empty($DLVY_PRICE)) {
                $DLVY_PRICE = 0;
            }

            $PRICE = 0;

            $PRICE = (int)$TOTAL_PRICE - (int)$DLVY_PRICE - (int)$POINT_PRICE; // 배송비, 포인트를 제외한 금액

            //파라미터 체크
            gfn_isValidation(302, $NAME, "주문자 - 이름");
            gfn_isValidation(302, $MOBILE, "주문자 - 연락처");
            gfn_isValidation(302, $EMAIL, "주문자 - 이메일");
            gfn_isValidation(302, $DLVY_NAME, "배송정보 - 이름");
            gfn_isValidation(302, $DLVY_MOBILE, "배송정보 - 연락처");
            gfn_isValidation(301, $DLVY_ADDRESS_ZIPCODE, "배송정보 - 주소");
            gfn_isValidation(301, $orderPayType, "결제 방식");

            $MOBILE = str_replace('-', '', $MOBILE);
            $DLVY_MOBILE = str_replace('-', '', $DLVY_MOBILE);

            // 무통장
            $NO_BANK_CD = "";  // 은행 코드 AD007
            $NO_BANK_ACCOUNT = ""; // 입금 계좌
            $NO_BANK_NAME = "";  // 입금자
            $NO_BANK_DEPOSITOR = ""; // 입금자명
            $NO_BANK_DATE = NULL; // 입금기한
            $issuance = ""; // 무통장 - 발급정보
            $NO_BANK_CASH_YN = "N"; // 현금영수증 발행 요청 여부

            // 현금영수증
            $cashReceipt = ""; // 현금영수증 - 개인 , 사업자
            $CASH_YN = "N"; // 현금영수증 발행 여부
            $CASH_MOBILE = ""; // 현금영수증 - 연락처
            $CASH_EMAIL = ""; // 현금영수증 - 이메일
            $CASH_BUSINESS = ""; // 현금영수증 - 사업자번호

            // 세금계산서 여부 
            $TAX_BILL_YN = "N"; // 세금계산서 여부
            $TAX_BILL_EMAIL = ""; // 세금계산서 - 이메일

            // 그 외  
            $TYPE_CD = ""; // 결제 구분 COL003
            $STATE_CD = ""; // 주문상태 코드 AD009
            $payType = ""; // 시퀀스 값 val
            $SOILD_OUT_YN = "N"; // 품절 여부
            $CANCEL_YN = "N"; // 상품취소/환불 여부
            $INICIS_SEQ = get_request_param('INICIS_SEQ'); // 이니시스 결제내역 시퀀스
            $POINT_SEQ = ""; // 포인트 시퀀스 이값은 후에 추가
            
            $ATTACH_FILE_ID = "";
            
            $ID = "";
            $unique_id = "";

            $arrValue = array();
            $where = '';

            if (isset($_SESSION['MEMBER'])) {
                if (!empty($_SESSION['MEMBER'])) {
                    $ID = $_SESSION['MEMBER']['ID'];

                    $where .= " AND ID = :ID";
                    $arrValue[':ID'] = $ID;
                } else {
                    $unique_id = session_id();

                    $where .= " AND SESSION = :SESSION";
                    $arrValue[':SESSION'] = $unique_id;
                }
            } else {
                $unique_id = session_id();

                $where .= " AND SESSION = :SESSION";
                $arrValue[':SESSION'] = $unique_id;
            }

            if ($orderPayType == "payCard") { // 결제수단 - 신용카드
                $TYPE_CD = "CCARD"; // 신용카드
                $STATE_CD = "21";
                $payType = "20";
            } else if ($orderPayType == "payAccount") { // 결제수단 - 계좌이체
                $TYPE_CD = "RTBT"; // 실시간계좌이체
                $STATE_CD = "21";
                $payType = "21";
            } else if ($orderPayType == "payNoBankbook") { // 결제수단 - 무통장 입금
                $TYPE_CD = "NBKB"; // 무통장입금
                $STATE_CD = "01"; // 주문상태 코드 AD009
                $payType = "22";

                $NO_BANK_CD = get_request_param('NO_BANK_CD');  // 은행 코드 AD007
                $NO_BANK_ACCOUNT = gfn_getZcmcommonVal("AD007", $NO_BANK_CD, "TH2_THEM_CD"); // 입금 계좌
                $NO_BANK_NAME = gfn_getZcmcommonVal("AD007", $NO_BANK_CD, "TH3_THEM_CD"); // 예금주
                $NO_BANK_DEPOSITOR = get_request_param('NO_BANK_DEPOSITOR');  // 입금자명
                $NO_BANK_DATE = date('Y-m-d', strtotime('+4 days')); // 입금기한 [임시로 3일로 지정]

                gfn_isValidation(301, $NO_BANK_CD, "입금은행");
                gfn_isValidation(302, $NO_BANK_DEPOSITOR, "입금자명");

                $issuance = get_request_param('issuance');

                if ($issuance == "issuanceNo") { // 발급안함
                    $NO_BANK_CASH_YN = "N"; // 현금영수증 발행 요청 여부

                } else if ($issuance == "issuanceCash") { // 현금영수증
                    $NO_BANK_CASH_YN = "Y"; // 현금영수증 발행 요청 여부

                    $cashReceipt = get_request_param('cashReceipt'); // 현금영수증 - 개인 , 사업자

                    if ($cashReceipt == "cashReceipt1") { // 개인 소득공제
                        $CASH_MOBILE = get_request_param('CASH_MOBILE'); // 휴대폰
                        $CASH_EMAIL = get_request_param('CASH_EMAIL'); // 이메일

                        gfn_isValidation(302, $CASH_MOBILE, "개인 소득공제 - 연락처");
                        gfn_isValidation(302, $CASH_EMAIL, "개인 소득공제 - 이메일");

                        $CASH_MOBILE = str_replace('-', '', $CASH_MOBILE);
                    } else if ($cashReceipt == "cashReceipt2") { // 사업자 지출증빙
                        $CASH_BUSINESS = get_request_param('CASH_BUSINESS'); // 사업자 번호
                        $CASH_EMAIL = get_request_param('CASH_EMAIL2'); // 이메일

                        gfn_isValidation(302, $CASH_BUSINESS, "사업자 지출증빙 - 연락처");
                        gfn_isValidation(302, $CASH_EMAIL, "사업자 지출증빙 - 이메일");
                    }

                } else if ($issuance == "issuanceTax") { // 세금계산서
                    $NO_BANK_CASH_YN = "N"; // 현금영수증 발행 요청 여부
                    $TAX_BILL_YN = "Y"; // 세금계산서 여부

                    $ATTACH = get_request_param('ATTACH'); // 세금계산서 - 사업자등록증
                    $TAX_BILL_EMAIL = get_request_param('TAX_BILL_EMAIL'); // 세금계산서 - 이메일

                    gfn_isValidation(302, $TAX_BILL_EMAIL, "세금계산서 - 이메일");
                }
            }

            if (!empty($INICIS_SEQ)) {
                $INIS_NAVERPOINT_CSHRAPPLYN = gfn_getIinisVal($INICIS_SEQ, "NAVERPOINT_CSHRAPPLYN");
                $INIS_NAVERPOINT_CSHRAPPLAMT = gfn_getIinisVal($INICIS_SEQ, "NAVERPOINT_CSHRAPPLAMT");
                $INIS_CSHR_RESULTCODE = gfn_getIinisVal($INICIS_SEQ, "CSHR_RESULTCODE");

                if ($INIS_NAVERPOINT_CSHRAPPLYN == "Y" && $INIS_NAVERPOINT_CSHRAPPLAMT != 0) {
                    $CASH_YN = "Y";
                } else if ($INIS_CSHR_RESULTCODE == "220000" || $INIS_CSHR_RESULTCODE == "0000") {
                    $CASH_YN = "Y";
                }
            }

            $seq_name = $TYPE. $payType;

            $sql = "
                 SELECT nextval_Order('{$seq_name}') as seq";

            $name_sql = "주문번호 시퀀스";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            $data = $clefResult->getResultSet();

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'PURCHASE_ORDER';

            if ($issuance == "issuanceTax") { // 세금계산서
                $dir = UPLOAD_DIR ."/PURCHASE/TAX/". date('Ymd');

                if (is_array($_FILES)) {
                    foreach ($_FILES as $key => $val) {
                        if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 썸네일 이미지
                            $ATTACH_FILE_ID = 'ATTACH_'. $data['seq'];
    
                            $arrRes = json_decode(one_file_upload($dir, $key), true);
    
                            if ($arrRes['code'] != 200) {
                                throw new Exception($arrRes['msg'], $arrRes['code']);
                            }
    
                            if (is_array($arrRes['file'])) {
                                $ATTACH_GROUP = 1;
                                $idx = 1;
    
                                foreach ($arrRes['file'] as $key => $val) {
                                    gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $NAME, $ip);
                                }
                            }
                        }
                    }
                }
            }

            $values = array( 
                  'PURCHASE_SEQ' => $data['seq'] // 주문번호 시퀀스
                , 'PAGE_TYPE' => $PAGE_TYPE // 홈페이지타입
                , 'TYPE_CD' => $TYPE_CD // 결제 구분 COL003 값
                , 'STATE_CD' => $STATE_CD // 주문상태 AD009
                , 'ID' => $ID // 아이디 [로그인경우]
                , 'SESSION' => $unique_id // 세션 [비회원인경우]
                , 'INICIS_SEQ' => $INICIS_SEQ // 이니시스 결제내역 시퀀스 
                , 'POINT_SEQ' => $POINT_SEQ
                , 'NAME' => $NAME // 문의자명
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'DLVY_NAME' => $DLVY_NAME // 배송정보 이름
                , 'DLVY_MOBILE' => $DLVY_MOBILE // 배송정보 연락처
                , 'DLVY_EMAIL' => $DLVY_EMAIL // 배송정보 이메일
                , 'DLVY_ADDRESS_ZIPCODE' => $DLVY_ADDRESS_ZIPCODE // 배송정보 우편번호
                , 'DLVY_ADDRESS' => $DLVY_ADDRESS // 배송정보 주소
                , 'DLVY_ADDRESSDETAIL' => $DLVY_ADDRESSDETAIL // 상세주소
                , 'DLVY_MESSAGE' => $DLVY_MESSAGE // 배송정보 배송메세지
                , 'TOTAL_COUNT' => $TOTAL_COUNT // 토탈 개수
                , 'TOTAL_PRICE' => $TOTAL_PRICE // 토탈 금액
                , 'PRICE' => $PRICE // 배송비, 포인트를 제외한 금액
                , 'POINT_PRICE' => $POINT_PRICE // 할인금액
                , 'DLVY_PRICE' => $DLVY_PRICE // 배송비
                , 'NO_BANK_CD' => $NO_BANK_CD // 은행 공통코드 AD007
                , 'NO_BANK_ACCOUNT' => $NO_BANK_ACCOUNT // 입금 계좌
                , 'NO_BANK_NAME' => $NO_BANK_NAME // 입금자명
                , 'NO_BANK_DEPOSITOR' => $NO_BANK_DEPOSITOR // 입금자명
                , 'NO_BANK_DATE' => $NO_BANK_DATE // 입금기한
                , 'NO_BANK_CASH_YN' => $NO_BANK_CASH_YN // 무통장 현금영수증 발행 요청 여부
                , 'CASH_YN' => $CASH_YN // 현금영수증 발행 여부
                , 'CASH_MOBILE' => $CASH_MOBILE // 현금영수증 - 연락처
                , 'CASH_EMAIL' => $CASH_EMAIL // 현금영수증 - 이메일
                , 'CASH_BUSINESS' => $CASH_BUSINESS // 현금영수증 - 사업자번호
                , 'TAX_BILL_YN' => $TAX_BILL_YN // 세금계산서 여부
                , 'TAX_BILL_EMAIL' => $TAX_BILL_EMAIL // 세금계산서 - 이메일
                , 'SOILD_OUT_YN' => $SOILD_OUT_YN // 품절 여부
                , 'CANCEL_YN' => $CANCEL_YN // 상품취소/환불 여부
                , 'AGREECHK_YN1' => $agreeChk1
                , 'AGREECHK_YN2' => $agreeChk2
                , 'AGREECHK_YN3' => $agreeChk3
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID
                , 'reg_user' => $NAME // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "주문내역 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            $PRODUCTArray = explode(',', $PRODUCTS);

            foreach ($PRODUCTArray as $seq) {
                $values = array(
                      'ID' => $ID
                    , 'SESSION' => $unique_id
                    , 'PURCHASE_SEQ' => $data['seq']
                    , 'PRODUCT_TEMP_SEQ' => $PRODUCT_TEMP_SEQ
                    , 'CATEGORY3_SEQ' => $seq
                    , 'STATE_CD' => $STATE_CD
                    , 'reg_user' => $NAME
                    , 'IP' => $ip
                );

                $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $sql = "
                        SELECT ORDER_ADD('$json_str') as temp";

                $name_sql = "작품 주문리스트 등록";
                $clefResult = $mysqldb->get($sql, null, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(505);
                }

                $val = $clefResult->getResultSet();
            }

            if ($PAGE_TYPE == PAGE1) {
                $SEQ_VAL = $data['seq'];

                $sql = "
                        SELECT OPTION_CHANGE_FN('$SEQ_VAL') as SOLD_YN";

                $name_sql = "작품 옵션 품절처리";
                $clefResult = $mysqldb->get($sql, null, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(505);
                }
            } else if ($PAGE_TYPE == PAGE2) {
                $ORDER_PURCHASE_SEQ = $data['seq'];

                $arrValue_OPTION = array();
                $arrValue_OPTION[':PURCHASE_SEQ'] = $ORDER_PURCHASE_SEQ;

                $sql = "
                     SELECT GROUP_CONCAT(CATEGORY3_SEQ SEPARATOR ',') as PRODUCTS
                          , GROUP_CONCAT(OPTION_SEQ SEPARATOR ',') as Options
                       FROM CATEGORY_OPTION
                      WHERE OPTION_SEQ IN (SELECT OPTION_SEQ
                                             FROM PURCHASE_OPTION
                                            WHERE PURCHASE_SEQ = :PURCHASE_SEQ)";

                $name_sql = "카테고리3 옵션 값 추출";
                $clefResult = $mysqldb->get($sql, $arrValue_OPTION, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $cata3op = $clefResult->getResultSet();
                
                if (!empty($cata3op)) {
                    $PRODUCTS = _check_var($cata3op['PRODUCTS']);
                    $Options = _check_var($cata3op['Options']);

                    $PRODUCTArray = explode(',', $PRODUCTS);
                    $optionArray = explode(',', $Options);

                    for ($i = 0; $i < count($optionArray); $i++) {
                        $seq = $PRODUCTArray[$i];
                        $code = $optionArray[$i];
                        
                        $BOBY_INFO = array(
                              'CATEGORY3_SEQ' => $seq
                            , 'OPTION_SEQ' => $code
                            , 'VAL' => 'QUANTITY'
                        );

                        $QUANTITY = gfn_OPTION_QUANTITY($BOBY_INFO);

                        $BOBY_INFO = array(
                              'CATEGORY3_SEQ' => $seq
                            , 'OPTION_SEQ' => $code
                            , 'VAL' => 'SOLD_YN'
                        );

                        $SOLD_YN = gfn_OPTION_QUANTITY($BOBY_INFO);

                        $BOBY_INFO = array(
                              'CATEGORY3_SEQ' => $seq
                            , 'OPTION_SEQ' => $code
                            , 'VAL' => 'OPTION_NAME'
                        );

                        $OPTION_NAME = gfn_OPTION_QUANTITY($BOBY_INFO);

                        if ($QUANTITY < 0) {
                            gfn_isValidation(999, "", "품절된 상품이 존재합니다. [". $OPTION_NAME . "]");
                        }

                        if ($SOLD_YN == "Y") {
                            gfn_isValidation(999, "", "품절된 상품이 존재합니다. [". $OPTION_NAME . "]");
                        }

                        $BOBY_INFO = array(
                              'PURCHASE_SEQ' => $data['seq']
                            , 'CATEGORY3_SEQ' => $seq
                            , 'OPTION_SEQ' => $code
                            , 'VAL' => 'QUANTITY'
                        );

                        $order_arrValue = array();
                        $order_where = '';

                        $order_where .= " AND PURCHASE_SEQ = :PURCHASE_SEQ";
                        $order_arrValue[':PURCHASE_SEQ'] = $BOBY_INFO['PURCHASE_SEQ'];
                        $order_where .= " AND OPTION_SEQ = :OPTION_SEQ";
                        $order_arrValue[':OPTION_SEQ'] = $BOBY_INFO['OPTION_SEQ'];
                        $order_where .= " AND CATEGORY3_SEQ = :CATEGORY3_SEQ";
                        $order_arrValue[':CATEGORY3_SEQ'] = $BOBY_INFO['CATEGORY3_SEQ'];

                        $VAL = $BOBY_INFO['VAL'];

                        $sql = "
                            SELECT {$VAL} AS TEMP
                              FROM PURCHASE_OPTION
                             WHERE 1
                                {$order_where}";
                       
                        $name_sql = "주문 옵션값 검색";
                        $clefResult = $mysqldb->get($sql, $order_arrValue, $name_sql);
                        
                        if (!$clefResult->getResult()) {
                            gfn_isValidation(800);
                        }

                        $order_data = $clefResult->getResultSet();

                        $ORDER_QUANTITY = $order_data['TEMP'];

                        if ($QUANTITY < $ORDER_QUANTITY) {
                            gfn_isValidation(999, "", "수량이 부족한 상품이 존재합니다. [". $OPTION_NAME . "]");
                        } else {
                            $table = 'CATEGORY_OPTION';

                            $option_values = array();
                            $option_pkvalues = array();

                            $COUNT_QUANTITY = intval($QUANTITY) - intval($ORDER_QUANTITY);
                            
                            $option_values['QUANTITY'] = $COUNT_QUANTITY;
                            
                            if ($COUNT_QUANTITY == 0) {
                                $option_values['SOLD_YN'] = 'Y';
                            }

                            $option_values['mod_user'] = $NAME;
                            $option_values['mod_ip'] = $ip;
                            $option_values['mod_date'] = date('Y-m-d H:i:s');

                            $option_pkvalues['OPTION_SEQ'] = $code;
                            $option_pkvalues['CATEGORY3_SEQ'] = $seq;
                            
                            $name_sql = "수량 수정";
                            $clefResult = $mysqldb->update($table, $option_values, $option_pkvalues, $name_sql);

                            if (!$clefResult->getResult()) {
                                gfn_isValidation(800);
                            }
                        }
                    }
                }
            }

            if ($val['temp'] == 'CART') {
                $sql = "
                     SELECT GROUP_CONCAT(PRODUCT_CART_SEQ SEPARATOR ',') as SEQS
                          , GROUP_CONCAT(CATEGORY3_SEQ SEPARATOR ',') as PRODUCTS
                          , GROUP_CONCAT(OPTION_SEQ SEPARATOR ',') as Options
                       FROM PRODUCT_OPTION_CART
                      WHERE PRODUCT_CART_SEQ IN (SELECT PRODUCT_CART_SEQ
                                                   FROM PRODUCT_SEQ_CART
                                                  WHERE 1
                                                   {$where})
                        AND CHEK_YN = 'Y'";

                $name_sql = "장바구니 리스트 리스트";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $cart = $clefResult->getResultSet();

                if (!empty($cart)) {
                    $SEQS = _check_var($cart['SEQS']);
                    $PRODUCTS = _check_var($cart['PRODUCTS']);
                    $Options = _check_var($cart['Options']);

                    $SEQrray = explode(',', $SEQS);
                    $PRODUCTArray = explode(',', $PRODUCTS);
                    $optionArray = explode(',', $Options);

                    for ($i = 0; $i < count($optionArray); $i++) {
                        $seq = $SEQrray[$i];
                        $seq2 = $PRODUCTArray[$i];
                        $code = $optionArray[$i];

                        $sql = "
                             DELETE FROM PRODUCT_OPTION_CART
                              WHERE PRODUCT_CART_SEQ = :seq
                                AND OPTION_SEQ = :pk";
                    
                        $name_sql = "장바구니 작품 옵션 삭제 ";
                        $clefResult = $mysqldb->delete($sql, [':seq' => $seq, ':pk' => $code], $name_sql);
    
                        if (!$clefResult->getResult()) {
                            gfn_isValidation(503);
                        }
        
                        $sql = "
                             SELECT *
                               FROM PRODUCT_OPTION_CART
                              WHERE PRODUCT_CART_SEQ = :seq
                                AND CATEGORY3_SEQ = :seq2";
        
                        $name_sql = "장바구니 작품 옵션 개수 확인";
                        $clefResult = $mysqldb->count($sql, [':seq' => $seq, ':seq2' => $seq2], $name_sql);
        
                        if (!$clefResult->getResult()) {
                            gfn_isValidation(800);
                        }
        
                        $count = $clefResult->getCount();
        
                        if ($count == 0) {
                            $sql = "
                                 DELETE FROM PRODUCT_CART
                                  WHERE PRODUCT_CART_SEQ = :pk
                                    AND CATEGORY3_SEQ = :pk2";
                        
                            $name_sql = "장바구니 작품 삭제 ";
                            $clefResult = $mysqldb->delete($sql, [':pk' => $seq, ':pk2' => $seq2], $name_sql);
        
                            if (!$clefResult->getResult()) {
                                gfn_isValidation(503);
                            }
                        }
        
                        $sql = "
                                SELECT *
                                FROM PRODUCT_CART
                                WHERE PRODUCT_CART_SEQ = :seq";
        
                        $name_sql = "장바구니 작품 개수 확인";
                        $clefResult = $mysqldb->count($sql, [':seq' => $seq], $name_sql);
        
                        if (!$clefResult->getResult()) {
                            gfn_isValidation(800);
                        }
        
                        $count = $clefResult->getCount();
        
                        if ($count == 0) {
                            $sql = "
                                 DELETE FROM PRODUCT_SEQ_CART
                                  WHERE PRODUCT_CART_SEQ = :pk";
                    
                            $name_sql = "장바구니 작품 시퀀스 삭제 ";
                            $clefResult = $mysqldb->delete($sql, [':pk' => $seq], $name_sql);
        
                            if (!$clefResult->getResult()) {
                                gfn_isValidation(503);
                            }
                        }
                    }
                }
            }

            $values = array(
                  'PRODUCT_TEMP_SEQ' => $PRODUCT_TEMP_SEQ
            );

            $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $sql = "
                    SELECT ORDER_TEMP_DEL('$json_str') as temp";

            $name_sql = "주문 담아둔 데이터 삭제";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(505);
            }

            $arrValue2 = array();
            $arrValue2[':PURCHASE_SEQ'] = $data['seq'];

            $sql = "
                 SELECT CONCAT(IF(COUNT(*) > 1, 
                               CONCAT(MIN(CATEGORY3_NAME), ' 외'), 
                               GROUP_CONCAT(DISTINCT CATEGORY3_NAME SEPARATOR ', '))) as temp
                   FROM PURCHASE_PRODUCT
                  WHERE PURCHASE_SEQ = :PURCHASE_SEQ";

            $name_sql = "상품명 값 추출";
            $clefResult = $mysqldb->get($sql, $arrValue2, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $temp_name = $clefResult->getResultSet();

            if ($STATE_CD == "01") {
                $page_name = "";

                if ($PAGE_TYPE == PAGE1) {
                    $page_name = "piknic";
                } else if ($PAGE_TYPE == PAGE2) {
                    $page_name = "shop piknic";
                }

                $Info_value = array(
                      'userid' => bizppurio_userid
                    , 'userpw' => bizppurio_password
                    , 'apikey' => bizppurio_apikey
                    , 'senderkey' => bizppurio_senderkey
                    , 'tpl_code' => 'bizp_2023111513564918915929376'
                );
                
                $message_emtitle = array();
    
                $message = array(
                      '홈페이지' => $page_name
                    , "주문번호" => $data['seq']
                    , "주문상품명" => $temp_name['temp']
                );
            } else if ($STATE_CD == "21") {
                $Info_value = array(
                      'userid' => bizppurio_userid
                    , 'userpw' => bizppurio_password
                    , 'apikey' => bizppurio_apikey
                    , 'senderkey' => bizppurio_senderkey
                    , 'tpl_code' => 'bizp_2023111517315709144402700'
                );
                
                $message_emtitle = array();
    
                $message = array(
                      "주문상품명" => $temp_name['temp']
                    , "주문번호" => $data['seq']
                    , "주문금액" => number_format($TOTAL_PRICE). '원'
                );
            }
            
            $Body_value = array( 
                  'type' => 'at'
                , 'from' => '023183233'
                , 'to' => $MOBILE
                , 'emtitle' => $message_emtitle
                , 'message' => $message
                , 'button' => array()
                , 'item' => array()
                , 'link' => array()
                , 'resend' => "lms"
                , 'subject' => ""
                , 'file' => array()
            );
            
            $arrRes = gfn_Bizalimtalk_send($Info_value, $Body_value);

            $url = "";

            if ($PAGE_TYPE == PAGE1) {
                if ($orderPayType == "payCard") { // 결제수단 - 신용카드
                    $url = artFoldName. '/order/order_pay_end.php?SEQ='.$data['seq'];
                } else if ($orderPayType == "payAccount") { // 결제수단 - 계좌이체
                    $url = artFoldName. '/order/order_pay_end.php?SEQ='.$data['seq'];
                } else if ($orderPayType == "payNoBankbook") { // 결제수단 - 무통장 입금
                    $url = artFoldName. '/order/order_end.php?SEQ='.$data['seq'];
                }
            } else if ($PAGE_TYPE == PAGE2) {
                if ($orderPayType == "payCard") { // 결제수단 - 신용카드
                    $url = shopFoldName. '/order/order_pay_end.php?SEQ='.$data['seq'];
                } else if ($orderPayType == "payAccount") { // 결제수단 - 계좌이체
                    $url = shopFoldName. '/order/order_pay_end.php?SEQ='.$data['seq'];
                } else if ($orderPayType == "payNoBankbook") { // 결제수단 - 무통장 입금
                    $url = shopFoldName. '/order/order_end.php?SEQ='.$data['seq'];
                }
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '주문이 완료되었습니다.';
            $arrRtn['url'] = $url; // 주문완료

            unset($_SESSION['INIS']);
            unset($_SESSION['ORDER']);
        } catch (Exception $e) {
            if ($orderPayType == "payCard" || $orderPayType == "payAccount") { // 오류 발생시 실제 금액 취소
                $INIS_TID = gfn_getIinisVal($INICIS_SEQ, "TID");

                $_SESSION['INIS']['SEQ'] = $INICIS_SEQ;
                $_SESSION['INIS']['TID'] = $INIS_TID;
                $_SESSION['INIS']['INIS_MSG'] = '주문 취소처리';

                include($_SERVER['DOCUMENT_ROOT']. '/php/temp/INIS/refund.php'); // executeRefundCode 를 사용하기 위해

                executeRefundCode();

                $sql = "
                     DELETE FROM PURCHASE_INICIS
                      WHERE INICIS_SEQ = :pk";

                $name_sql = $CATEGORY3_SEQ." 이니시스 삭제 ";

                $clefResult = $mysqldb->delete($sql, [':pk' => $INICIS_SEQ], $name_sql);
            }

            unset($_SESSION['INIS']);
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    function order_update() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            // 시퀀스
            $PURCHASE_SEQ = get_request_param('SEQ'); // 주문페이지 시퀀스

            // 주문자 정보 입력
            $NAME = get_request_param('NAME'); // 주문자 - 이름
            $MOBILE = get_request_param('MOBILE'); // 주문자 - 연락처
            $EMAIL = get_request_param('EMAIL'); // 주문자 - 이메일

            // 배송정보
            $DLVY_NAME = get_request_param('DLVY_NAME'); // 배송정보 - 이름
            $DLVY_MOBILE = get_request_param('DLVY_MOBILE'); // 배송정보 - 연락처
            $DLVY_EMAIL = get_request_param('DLVY_EMAIL'); // 배송정보 - 이메일 [필요시 나중에추가]
            $DLVY_ADDRESS_ZIPCODE = get_request_param('DLVY_ADDRESS_ZIPCODE'); // 배송정보 - 주소 (우편번호)
            $DLVY_ADDRESS = get_request_param('DLVY_ADDRESS'); // 배송정보 - 기본주소 
            $DLVY_ADDRESSDETAIL = get_request_param('DLVY_ADDRESSDETAIL'); // 배송정보 - 상세주소
            $DLVY_MESSAGE = get_request_param('DLVY_MESSAGE'); // 배송정보 - 배송메세지

            // 무통장
            $NO_BANK_CASH_YN = get_request_param('NO_BANK_CASH_YN');  // 현금영수증 요청
            $NO_BANK_DEPOSITOR = get_request_param('NO_BANK_DEPOSITOR');  // 입금자명

            // 현금영수증
            $CASH_YN = get_request_param('CASH_YN'); // 현금영수증 여부
            $CASH_MOBILE = get_request_param('CASH_MOBILE'); // 휴대폰
            $CASH_EMAIL = get_request_param('CASH_EMAIL'); // 이메일
            $CASH_BUSINESS = get_request_param('CASH_BUSINESS'); // 사업자 번호

            // 세금계산서
            $TAX_BILL_YN = get_request_param('TAX_BILL_YN'); // 세금계산서 - 여부
            $TAX_BILL_EMAIL = get_request_param('TAX_BILL_EMAIL'); // 세금계산서 - 이메일

            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); // 파일아이디
            $NOTE = get_request_param('NOTE'); // 파일아이디

            gfn_isValidation(302, $NAME, "주문자 - 이름");
            gfn_isValidation(302, $MOBILE, "주문자 - 연락처");
            gfn_isValidation(302, $EMAIL, "주문자 - 이메일");
            gfn_isValidation(302, $DLVY_NAME, "배송정보 - 이름");
            gfn_isValidation(302, $DLVY_MOBILE, "배송정보 - 연락처");
            gfn_isValidation(301, $DLVY_ADDRESS_ZIPCODE, "배송정보 - 주소");

            gfn_isValidation(302, $NAME, "주문자 - 이름");
            gfn_isValidation(302, $MOBILE, "주문자 - 연락처");
            gfn_isValidation(302, $EMAIL, "주문자 - 이메일");
            gfn_isValidation(302, $DLVY_NAME, "배송정보 - 이름");
            gfn_isValidation(302, $DLVY_MOBILE, "배송정보 - 연락처");
            gfn_isValidation(301, $DLVY_ADDRESS_ZIPCODE, "배송정보 - 주소");

            $MOBILE = str_replace('-', '', $MOBILE);
            $DLVY_MOBILE = str_replace('-', '', $DLVY_MOBILE);
            $CASH_MOBILE = str_replace('-', '', $CASH_MOBILE);

            if (empty($NO_BANK_CASH_YN)){
                $NO_BANK_CASH_YN = 'N';
            }

            if (empty($CASH_YN)){
                $CASH_YN = 'N';
            }

            if (empty($TAX_BILL_YN)){
                $TAX_BILL_YN = 'N';
            }

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            
            $table = 'PURCHASE_ORDER';

            if ($TAX_BILL_YN == "Y") {
                $dir = UPLOAD_DIR ."/PURCHASE/TAX/". date('Ymd');

                if (is_array($_FILES)) {
                    foreach ($_FILES as $key => $val) {
                        if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 썸네일 이미지
                            if (empty($ATTACH_FILE_ID)) {
                                $ATTACH_FILE_ID = 'ATTACH_'. $PURCHASE_SEQ;
                            }
    
                            $arrRes = json_decode(one_file_upload($dir, $key), true);
    
                            if ($arrRes['code'] != 200) {
                                throw new Exception($arrRes['msg'], $arrRes['code']);
                            }
    
                            if (is_array($arrRes['file'])) {
                                $ATTACH_GROUP = 1;
                                $idx = 1;
    
                                foreach ($arrRes['file'] as $key => $val) {
                                    $FIND_FILE = gfn_file_upload("T", '', $ATTACH_FILE_ID, $ATTACH_GROUP);

                                    if ($FIND_FILE > 0) {
                                        gfn_file_upload("U", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                    } else {
                                        gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $values = array( 
                  'NAME' => $NAME // 문의자명
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'DLVY_NAME' => $DLVY_NAME // 배송정보 이름
                , 'DLVY_MOBILE' => $DLVY_MOBILE // 배송정보 연락처
                , 'DLVY_EMAIL' => $DLVY_EMAIL // 배송정보 이메일
                , 'DLVY_ADDRESS_ZIPCODE' => $DLVY_ADDRESS_ZIPCODE // 배송정보 우편번호
                , 'DLVY_ADDRESS' => $DLVY_ADDRESS // 배송정보 주소
                , 'DLVY_ADDRESSDETAIL' => $DLVY_ADDRESSDETAIL // 상세주소
                , 'DLVY_MESSAGE' => $DLVY_MESSAGE // 배송정보 배송메세지
                , 'NO_BANK_DEPOSITOR' => $NO_BANK_DEPOSITOR // 입금자명
                , 'NO_BANK_CASH_YN' => $NO_BANK_CASH_YN // 무통장 현금영수증 발행 요청 여부
                , 'CASH_YN' => $CASH_YN // 현금영수증 발행 여부
                , 'CASH_MOBILE' => $CASH_MOBILE // 현금영수증 - 연락처
                , 'CASH_EMAIL' => $CASH_EMAIL // 현금영수증 - 이메일
                , 'CASH_BUSINESS' => $CASH_BUSINESS // 현금영수증 - 사업자번호
                , 'TAX_BILL_YN' => $TAX_BILL_YN // 세금계산서 여부
                , 'TAX_BILL_EMAIL' => $TAX_BILL_EMAIL // 세금계산서 - 이메일
                , 'NOTE' => $NOTE //메모
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = "주문내역 수정";
            $clefResult = $mysqldb->update($table, $values, ['PURCHASE_SEQ' => $PURCHASE_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            // 파라미터 설정
            $m_seq = get_request_param('m_seq');
            $mp_seq = get_request_param('mp_seq');
            $page_type = get_request_param('page_type'); // 페이지타입
            $M_start_date = get_request_param('M_start_date'); // 날짜
            $M_end_date = get_request_param('M_end_date'); // 날짜 
            $M_TYPE_CD = get_request_param('M_TYPE_CD'); // 결제수단
            $M_STATE_CD = get_request_param('M_STATE_CD'); // 주문상태
            $M_PUR_NUM = get_request_param('M_PUR_NUM'); // 주문번호
            $M_DLVY_NAME = get_request_param('M_DLVY_NAME'); // 주문자

            $M_CATEGORY1_SEQ = get_request_param('M_CATEGORY1_SEQ'); // 작가
            $M_CATEGORY2_SEQ = get_request_param('M_CATEGORY2_SEQ'); // 시리즈
            $M_CATEGORY3_SEQ = get_request_param('M_CATEGORY3_SEQ'); // 작품

            $M_CATEGORY1_NAME = get_request_param('M_CATEGORY1_NAME'); // 작가명
            $M_CATEGORY2_NAME = get_request_param('M_CATEGORY2_NAME'); // 시리즈명
            $M_CATEGORY3_NAME = get_request_param('M_CATEGORY3_NAME'); // 작품명

            $arrParams = array(
                  'm_seq' => $m_seq
                , 'mp_seq' => $mp_seq
                , 'page_type' => $page_type //페이지 타입
                , 'start_date' => $M_start_date // 시작일
                , 'end_date' => $M_end_date // 종료일
                , 'TYPE_CD' => $M_TYPE_CD // 결제수단
                , 'STATE_CD' => $M_STATE_CD // 주문상태
                , 'PUR_NUM' => $M_PUR_NUM // 주문번호
                , 'DLVY_NAME' => $M_DLVY_NAME // 주문자
                , 'CATEGORY1_SEQ' => $M_CATEGORY1_SEQ // 작가
                , 'CATEGORY2_SEQ' => $M_CATEGORY2_SEQ // 시리즈
                , 'CATEGORY3_SEQ' => $M_CATEGORY3_SEQ // 작룸
                , 'CATEGORY1_NAME' => $M_CATEGORY1_NAME // 작가명
                , 'CATEGORY2_NAME' => $M_CATEGORY2_NAME // 시리즈명
                , 'CATEGORY3_NAME' => $M_CATEGORY3_NAME // 작품명
            );

            $query_string = http_build_query($arrParams);

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '수정되었습니다.';
            $arrRtn['url'] = "../adm/board/orderHistory_main.php?{$query_string}"; 
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

?>