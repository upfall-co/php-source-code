<?php
/**
 * 파일명 : ajax_module.php
 * 내용 : ajax 모듈 모음
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/07    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    try {
        //파라미터 정리
        $mode = get_request_param('mode');

        $arrRtn['mode'] = $mode;
        $arrRes = array();

        switch ($mode) {
            case 'CHKCODE' : // 시크릿코드 확인
                $arrRes = ufn_Chk_SCode();
                break;
            case 'OVERCHK' : // 중복확인
                $arrRes = ufn_Chk_OVCERID();
                break;
            case 'INQCHK' : // 1:1 문의 비밀번호 체크
                $arrRes = ufn_Chk_INQPW();
                break;
            case 'ORDERADD' : // 작품 주문추가
                $arrRes = ufn_ORDER_ADD();
                break;
            case 'CARTADD' : // 장바구니 추가 
                $arrRes = ufn_CART_ADD();
                break;
            case 'CARTCHK' : // 장바구니 체크값 추가
                $arrRes = ufn_CART_CHK();
                break;
            case 'CARTDEL' : // 장바구니 선택삭제
                $arrRes = ufn_CART_DEL();
                break;
            case 'ORDERCANCEL' : // 주문내역 취소요청
                $arrRes = ufn_ORDER_CANCEL();
                break;
            case 'ORDERCHANGE' : // 주문내역 상태 변경
                $arrRes = ufn_ORDER_CHANGE();
                break;
            case 'ORDERINVOICE' : // 송장번호 등록
                $arrRes = ufn_ORDER_INVOICE();
                break;
            case 'INISTPAY_INFO' : // 이니시스 결제전 세션값들 저장
                $arrRes = ufn_INISTPAY_INFO();
                break;
            case 'FINDEMAIL' : // 아이디, 비밀번호 이메일 찾기
                $arrRes = ufn_MEMBER_FIND_EM();
                break;
            case 'FINDPHONE' : // 아이디, 비밀번호 문자 찾기
                $arrRes = ufn_MEMBER_FIND_PH();
                break;
            case 'MEMBER_TOKEN' : // 카카오, 네이버 로그인
                $arrRes = ufn_MEMBER_TOKEN();
                break;
            case 'MEMBER_DEL' : // 계정삭제
                $arrRes = ufn_Member_DEL();
                break;
            case 'MPRDBOX' : // 샵 메인화면 메인노출 리스트 출력
                $arrRes = ufn_Category1_List();
                break;
            case 'GETCATEG2' : // 카테고리의 분류 첫번째 값
                $arrRes = ufn_Category2_List();
                break;
            case 'PRODUCTINFO' : // 상품 리스트 출력 
                $arrRes = ufn_PRODUCTINFO_List();
                break;
            case 'PRODUCTINFO_ST' : // 상품 리스트 출력 
                $arrRes = ufn_PRODUCTINFO_ST_List();
                break;
            case 'NAVINFO' : // 네비 상품 리스트 출력
                $arrRes = ufn_NAVINFO_List();
                break;
            case 'SPACE' : // SPACE
                $arrRes = ufn_SPACE_List();
                break;
            case 'SPACE_PLAN' : // SPACE 도면 값
                $arrRes = ufn_SPACE_PLAN_FILE();
                break;
            case 'RECRUIT' : // RECRUIT
                $arrRes = ufn_RECRUIT_List();
                break;
            case 'RECRUIT_DETAIL' : // RECRUIT_DETAIL
                $arrRes = ufn_RECRUIT_DETAIL_List();
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }
    
        echo json_encode($arrRes);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();
    }

    /**
     * name :ufn_Chk_SCode
     * comment : 시크릿코드 확인
     */
    function ufn_Chk_SCode() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $TH1_THEM_CD = get_request_param('val');

            gfn_isValidation(302, $TH1_THEM_CD, "시크릿코드");

            $table = 'ZCMCOMMON';

            $sql = "
                SELECT *
                  FROM {$table}
                 WHERE COM_TYPE = 'COL001'
                   AND GETDECRYPT(TH1_THEM_CD, :key) = :TH1_THEM_CD";

            $name_sql = "시크릿코드 확인";
            $clefResult = $mysqldb->count($sql, [':TH1_THEM_CD' => gfn_encrypted($TH1_THEM_CD) , ':key' => $_SESSION['projectkey']], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $CHKCOUNT = $clefResult->getCount();

            if ($CHKCOUNT) {
                $arrRtn['code'] = 200;
                $arrRtn['msg'] = '시크릿코드가 확인되었습니다';

                $_SESSION['SECRETCHK'] = true;
            } else {
                $arrRtn['code'] = 999;
                $arrRtn['msg'] = '시크릿코드를 확인해주세요.';

                unset($_SESSION['SECRETCHK']);
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_Chk_OVCERID
     * comment : 아이디 중복 확인
     */
    function ufn_Chk_OVCERID() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $ID = get_request_param('ID');

            gfn_isValidation(302, $ID, "아이디");

            $table = 'MEMBER_DEL';

            $arrValue = array();
            $arrValue[':ID'] = $ID;

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE ID = :ID";

            $name_sql = "탈퇴 아이디 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 0) {
                gfn_isValidation(999, "", "사용 불가능한 아이디입니다.");
            }

            $table = 'MEMBER';

            $sql = "
                SELECT *
                  FROM {$table}
                 WHERE ID = :ID";

            $name_sql = "중복 확인";
            $clefResult = $mysqldb->count($sql, [':ID' => $ID], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $CHKCOUNT = $clefResult->getCount();

            if (!$CHKCOUNT) {
                $arrRtn['code'] = 200;
                $arrRtn['msg'] = '사용 가능한 아이디입니다.';
            } else {
                $arrRtn['code'] = 999;
                $arrRtn['msg'] = '중복된 아이디가 존재합니다.';
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_Chk_INQPW
     * comment : 문의내역 비밀번호 확인
     */
    function ufn_Chk_INQPW() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $SEQ = get_request_param('val');
            $PASSWORD = get_request_param('PW');

            $PASSWORD = trim($PASSWORD);

            gfn_isValidation(305, $SEQ, "문의내역을 다시 클릭해주세요.");
            gfn_isValidation(302, $PASSWORD, "비밀번호");

            $table = 'INQUIRY';

            $arrValue = array();
            $arrValue[':INQUIRY_SEQ'] = $SEQ;
            $arrValue[':PASSWORD'] = gfn_encrypted($PASSWORD);
            $arrValue[':key'] = $_SESSION['projectkey'];

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE INQUIRY_SEQ = :INQUIRY_SEQ
                    AND GETDECRYPT(PASSWORD, :key) = :PASSWORD ";

            $name_sql = "계정 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total == 0) {
                gfn_isValidation(999, "", "비밀번호가 일치하지 않습니다.");
            }

            $_SESSION['INQ']['CHK'] = 'Y'; // 접근제한 방식 비밀번호 입력시 Y로 하여 사용자와달라도 문의내역에 접속가능 그이후 N 처리하여 재접근 불가

            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '확인되었습니다.';
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_ORDER_ADD
     * comment : 주문 페이지 값 등록
     */
    function ufn_ORDER_ADD() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();
            $page_type = get_request_param('page_type');

            $TYPE_CD = get_request_param('TYPE_CD');

            $PRODUCTS = get_request_param('val');
            $Prices = get_request_param('Prices');
            $Options = get_request_param('Options');
            $Quantitys = get_request_param('Quantitys');

            $BTN_TYPE = get_request_param('BTN_TYPE'); // 로그인 구매 , 비회원 구매

            if (empty($Options)) {
                gfn_isValidation(999, "", "선택된 작품이 없습니다.");
            }

            $PRODUCTArray = explode(',', $PRODUCTS);
            $PricesArray = explode(',', $Prices);
            $optionArray = explode(',', $Options);
            $QuantityArray = explode(',', $Quantitys);

            $PRODUCT_TEMP_SEQ = "";

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $values = array(
                  'TYPE' => "SEQ"
                , 'TYPE_CD' => $TYPE_CD
                , 'CATEGORY3_SEQ' => $PRODUCTArray[0]
                , 'OPTION_SEQ' => ""
                , 'IP' => $ip
                , 'PRODUCT_TEMP_SEQ' => ""
            );

            $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $sql = "
                 SELECT ORDER_TEMP_ADD('$json_str') as temp";

            $name_sql = "작품 구매페이지 시퀀스 등록";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(505);
            }

            $data = $clefResult->getResultSet();

            if ($data['temp'] == 'false') {
                gfn_isValidation(999, "", "잠시 후 다시 시도해주세요.");
            } else {
                $PRODUCT_TEMP_SEQ = $data['temp'];
            }

            $count = 0;

            foreach ($PRODUCTArray as $seq) {
                if ($page_type == PAGE1) {
                    $PRICE = 0;
                } else if ($page_type == PAGE2) {
                    $PRICE =$PricesArray[$count++];
                }

                $values = array(
                      'TYPE' => "PT"
                    , 'TYPE_CD' => $TYPE_CD
                    , 'CATEGORY3_SEQ' => $seq
                    , 'OPTION_SEQ' => ""
                    , 'MPRICE' => $PRICE
                    , 'IP' => $ip
                    , 'PRODUCT_TEMP_SEQ' => $PRODUCT_TEMP_SEQ
                );

                $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $sql = "
                    SELECT ORDER_TEMP_ADD('$json_str') as temp";

                $name_sql = "작품 구매페이지 등록";
                $clefResult = $mysqldb->get($sql, null, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(505);
                }

                $data = $clefResult->getResultSet();

                if ($data['temp'] == 'false') {
                    gfn_isValidation(999, "", "잠시 후 다시 시도해주세요.");
                }
            }

            $count = 0;
            
            foreach ($optionArray as $code) {
                $OPQUANTITY = 0;

                if ($page_type == PAGE1) {
                    $OPQUANTITY = 1;
                } else if ($page_type == PAGE2) {
                    $OPQUANTITY = $QuantityArray[$count++];
                }

                $arrValue = array();
                $arrValue[':OPTION_SEQ'] = $code;

                $sql = "
                     SELECT OPTION_NAME
                       FROM CATEGORY_OPTION
                      WHERE OPTION_SEQ = :OPTION_SEQ
                        AND SOLD_YN = 'Y'";

                $name_sql = "옵션확인 확인";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $OPTION = $clefResult->getResultSet();

                if (!empty($OPTION)) {
                    gfn_isValidation(999, "", "품절된 작품이 존재합니다. [". $OPTION['OPTION_NAME'] . "]");
                }
                
                $values = array(
                      'TYPE' => "POT"
                    , 'TYPE_CD' => $TYPE_CD
                    , 'CATEGORY3_SEQ' => ""
                    , 'OPTION_SEQ' => $code
                    , 'OPQUANTITY' => $OPQUANTITY
                    , 'IP' => $ip
                    , 'PRODUCT_TEMP_SEQ' => $PRODUCT_TEMP_SEQ
                );
    
                $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $sql = "
                     SELECT ORDER_TEMP_ADD('$json_str') as temp";

                $name_sql = "작품 옵션 구매페이지 등록";
                $clefResult = $mysqldb->get($sql, null, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(505);
                }

                $data = $clefResult->getResultSet();

                if ($data['temp'] == 'false') {
                    gfn_isValidation(999, "", "잠시 후 다시 시도해주세요.");
                }
            }
            
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '확인되었습니다.';
            $arrRtn['seq'] = $PRODUCT_TEMP_SEQ;

            unset($_SESSION['ORDER']);
            $_SESSION['ORDER'][$PRODUCT_TEMP_SEQ] = $PRODUCT_TEMP_SEQ;

            if ($BTN_TYPE == 'Login') {
                if ($page_type == PAGE1) {
                    $_SESSION['INFOR']['URL'] = artFoldName. "/order/ordersheet.php?SEQ=" .$PRODUCT_TEMP_SEQ;
                } else if ($page_type == PAGE2) {
                    $_SESSION['INFOR']['URL'] = shopFoldName. "/order/ordersheet.php?SEQ=" .$PRODUCT_TEMP_SEQ;
                }
            }
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_CART_ADD
     * comment : 장바구니 추가
     */
    function ufn_CART_ADD() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $page_type = get_request_param('page_type');

            $CATEGORY3_SEQ = get_request_param('val');
            $MPRICE = get_request_param('MPRICE');
            $Options = get_request_param('Options');
            $Quantitys = get_request_param('Quantitys');

            $optionArray = explode(',', $Options);
            $QuantityArray = explode(',', $Quantitys);

            $ID = "";
            $unique_id = "";
            $arrValue = array();
            $where = "";

            if (isset($_SESSION['MEMBER'])) {
                if (!empty($_SESSION['MEMBER'])) {
                    $ID = $_SESSION['MEMBER']['ID'];
                    $where .= " AND A.ID = :ID";
                    $arrValue[':ID'] = $ID;
                } else {
                    $unique_id = session_id();

                    $where .= " AND A.SESSION = :SESSION";
                    $arrValue[':SESSION'] = $unique_id;
                }
            } else {
                $unique_id = session_id();

                $where .= " AND A.SESSION = :SESSION";
                $arrValue[':SESSION'] = $unique_id;
            }

            $table = "PRODUCT_OPTION_CART";

            // 장바구니 동일 제품 확인
            foreach ($optionArray as $code) {
                $where_data = $where. " AND D.OPTION_SEQ = :OPTION_SEQ";
                $arrValue[':OPTION_SEQ'] = $code;

                $arrValue2 = array();
                $arrValue2[':OPTION_SEQ'] = $code;

                $sql = "
                     SELECT OPTION_NAME
                       FROM CATEGORY_OPTION
                      WHERE OPTION_SEQ = :OPTION_SEQ
                        AND SOLD_YN = 'Y'";

                $name_sql = "옵션확인 확인";
                $clefResult = $mysqldb->get($sql, $arrValue2, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $OPTION = $clefResult->getResultSet();

                if (!empty($OPTION)) {
                    gfn_isValidation(999, "", "품절된 작품이 존재합니다. [". $OPTION['OPTION_NAME'] . "]");
                }

                $sql = "
                     SELECT *
                       FROM PRODUCT_SEQ_CART A ,{$table} D
                      WHERE A.PRODUCT_CART_SEQ = D.PRODUCT_CART_SEQ
                      {$where_data}";

                $name_sql = "중복 확인";
                $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $CHKCOUNT = $clefResult->getCount();

                if ($CHKCOUNT) {
                    gfn_isValidation(999, "", "장바구니에 동일한 작품이 존재합니다.");
                }
            }

            $PRODUCT_TEMP_SEQ = "";

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $values = array(
                  'TYPE' => "SEQ"
                , 'ID' => $ID
                , 'SESSION' => $unique_id
                , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                , 'OPTION_SEQ' => ""
                , 'IP' => $ip
                , 'PRODUCT_TEMP_SEQ' => ""
            );

            $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $sql = "
                    SELECT ORDER_CART_ADD('$json_str') as temp";

            $name_sql = "작품 장바구니 시퀀스 등록";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(505);
            }

            $data = $clefResult->getResultSet();

            if ($data['temp'] == 'false') {
                gfn_isValidation(999, "", "잠시 후 다시 시도해주세요.");
            } else {
                $PRODUCT_TEMP_SEQ = $data['temp'];
            }

            $PRICE = 0;

            if ($page_type == PAGE1) {
                $PRICE = 0;
            } else if ($page_type == PAGE2) {
                $PRICE = $MPRICE;
            }

            $values = array(
                  'TYPE' => "CPT"
                , 'ID' => $ID
                , 'SESSION' => $unique_id
                , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                , 'OPTION_SEQ' => ""
                , 'MPRICE' => $PRICE
                , 'IP' => $ip
                , 'PRODUCT_TEMP_SEQ' => $PRODUCT_TEMP_SEQ
            );

            $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $sql = "
                SELECT ORDER_CART_ADD('$json_str') as temp";

            $name_sql = "작품 장바구니 등록";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(505);
            }

            $data = $clefResult->getResultSet();

            if ($data['temp'] == 'false') {
                gfn_isValidation(999, "", "잠시 후 다시 시도해주세요.");
            }

            $count = 0;

            foreach ($optionArray as $code) {
                $OPQUANTITY = 0;

                if ($page_type == PAGE1) {
                    $OPQUANTITY = 1;
                } else if ($page_type == PAGE2) {
                    $OPQUANTITY = $QuantityArray[$count++];
                }

                $values = array(
                      'TYPE' => "CPOT"
                    , 'ID' => $ID
                    , 'SESSION' => $unique_id
                    , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                    , 'OPTION_SEQ' => $code
                    , 'OPQUANTITY' => $OPQUANTITY
                    , 'IP' => $ip
                    , 'PRODUCT_TEMP_SEQ' => $PRODUCT_TEMP_SEQ
                );
  
                $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $sql = "
                     SELECT ORDER_CART_ADD('$json_str') as temp";

                $name_sql = "작품 옵션 장바구니 등록";
                $clefResult = $mysqldb->get($sql, null, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(505);
                }
                $data = $clefResult->getResultSet();

                if ($data['temp'] == 'false') {
                    gfn_isValidation(999, "", "잠시 후 다시 시도해주세요.");
                }
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '확인되었습니다.';
            $arrRtn['seq'] = $PRODUCT_TEMP_SEQ;
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_CART_CHK
     * comment : 장바구니 체크박스 값 변경
     */
    function ufn_CART_CHK() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $CHEK_YN = get_request_param('CHEK_YN');

            $SEQS = get_request_param('pk');
            $PRODUCTS = get_request_param('val');
            $Options = get_request_param('Options');

            $SEQrray = explode(',', $SEQS);
            $PRODUCTArray = explode(',', $PRODUCTS);
            $optionArray = explode(',', $Options);

            $table = 'PRODUCT_OPTION_CART';

            for ($i = 0; $i < count($optionArray); $i++) {
                $pk = $SEQrray[$i];
                $seq = $PRODUCTArray[$i];
                $code = $optionArray[$i];

                $values = array(
                    'CHEK_YN' => $CHEK_YN
                );

                $pkvalues = array (
                      'PRODUCT_CART_SEQ' => $pk
                    , 'CATEGORY3_SEQ' => $seq
                    , 'OPTION_SEQ' => $code
                );

                $name_sql = "장바구니 상태변경 변경";
                $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '확인되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_CART_DEL
     * comment : 장바구니 선택삭제
     */
    function ufn_CART_DEL() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $SEQS = get_request_param('pk');
            $PRODUCTS = get_request_param('val');
            $Options = get_request_param('Options');

            if (empty($Options)) {
                gfn_isValidation(999, "", "선택된 작품이 없습니다.");
            }

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

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '삭제되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_ORDER_CANCEL
     * comment : 주문 취소
     */
    function ufn_ORDER_CANCEL() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $PURCHASE_SEQ = get_request_param('SEQ'); // 주문번호
            $TYPE_CD = get_request_param('TYPE_CD'); // 결제방식 공통코드 COL003
            $STATE_CD = get_request_param('STATE_CD'); // 주문 - 주문상태 - 전체
            $INICIS_SEQ = get_request_param('INICIS_SEQ'); // 이니시스 시퀀스 [카드 , 계좌이체 경우]
            $MOBILE = get_request_param('MOBILE'); // 사용자 연락처
            $TOTAL_COUNTS = get_request_param('totalcount'); // 이니시스 시퀀스 [카드 , 계좌이체 경우]
            $TOTAL_NOW_PRICE = get_request_param('TOTAL_NOW_PRICE'); // 현금액

            $SEQS = get_request_param('pk'); // 주문번호 - 필요시 사용
            $PRODUCTS = get_request_param('val'); // 작품 시퀀스
            $Options = get_request_param('Options'); // 주문 옵션 시퀀스
            $States = get_request_param('state'); //  주문별 - 개별 주문 상태
            $op_state = get_request_param('change_stage'); // 변경할 상태값
            $prices = get_request_param('price'); //  주문별 - 작품 금액

            $TOTALrray = explode(',', $TOTAL_COUNTS); // 주문번호 - 필요시 사용
            $SEQrray = explode(',', $SEQS); // 주문번호 - 필요시 사용
            $PRODUCTArray = explode(',', $PRODUCTS); // 작품 시퀀스
            $optionArray = explode(',', $Options); // 주문 옵션 시퀀스
            $StatesArray = explode(',', $States); //  주문별 - 개별 주문 상태
            $pricesArray = explode(',', $prices); //  주문별 - 작품 금액

            $TOTAL_COUNT = $TOTALrray[0]; // 토탈

            $CANCEL_PRICE = 0;

            $REAL_DLVY_PRICE = get_request_param('REAL_DLVY_PRICE'); // 실배송비

            if (!empty($MOBILE)) {
                $MOBILE = str_replace('-', '', $MOBILE);
            }

            if (isset($_SESSION['MEMBER'])) {
                if (!empty($_SESSION['MEMBER'])) {
                    $ID = $_SESSION['MEMBER']['ID'];
                } else {
                    $ID = session_id();

                }
            } else {
                $ID = session_id();
            }

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $arrValue = array();
            $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

            $sql = "
                SELECT STATE_CD
                     , ZCM_COM_NM('COL005', STATE_CD) AS STATE_CD_NM
                     , PAGE_TYPE
                  FROM PURCHASE_ORDER
                 WHERE PURCHASE_SEQ = :PURCHASE_SEQ";

            $name_sql = "주문상태 확인";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (empty($data)) {
                dieAndErrorMove("잘못된 접근입니다.");
            }

            $STATE_CD = _check_var($data['STATE_CD']); // 주문상태
            $STATE_CD_NM = _check_var($data['STATE_CD_NM']); // 주문상태
            $PAGE_TYPE = _check_var($data['PAGE_TYPE']); // 페이지구분

            if ($op_state == "41") {
                $STATE_VAL = ["01", "81", "82", "21", "60", "83", "84", "30", "85", "86"];

                if (!in_array($STATE_CD, $STATE_VAL)) {
                    gfn_isValidation(999, "", "주문취소를 진행할 수 없습니다. 확인 후 다시 주문취소를 눌러주세요.");
                }

                $OP_STATE_VAL = ["01", "21", "30"];
            } else if ($op_state == "51") {
                $STATE_VAL = ["31", "62", "87", "88", "32", "63", "89", "90"];

                if (!in_array($STATE_CD, $STATE_VAL)) {
                    gfn_isValidation(999, "", "환불요청을 진행할 수 없습니다. 확인 후 다시 환불요청을 눌러주세요.");
                }

                $OP_STATE_VAL = ["31", "32"];
            } else {
                dieAndErrorMove("잘못된 접근입니다.");
            }

            $TOTAL_CONFIRMPRICE = $TOTAL_NOW_PRICE;

            if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && count($optionArray) != $TOTAL_COUNT) { // 카드결제
                $INIS_TID = gfn_getIinisVal($INICIS_SEQ, "TID");

                if (!empty($INIS_TID)) {
                    $_SESSION['INIS']['SEQ'] = $INICIS_SEQ;
                    $_SESSION['INIS']['TID'] = $INIS_TID;
                    $_SESSION['INIS']['CURRENCY'] = 'WON'; // 통화 (WON, USD)
                    $_SESSION['INIS']['TAX'] = '0'; // 부과세
                    $_SESSION['INIS']['TAXFREE'] = '0'; // 비과세

                    include($_SERVER['DOCUMENT_ROOT']. '/php/temp/INIS/partialRefund.php'); // executePartialRefundCode 를 사용하기 위해
                }
            } else if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && count($optionArray) == $TOTAL_COUNT) {
                $INIS_TID = gfn_getIinisVal($INICIS_SEQ, "TID");

                if (!empty($INIS_TID)) {
                    $_SESSION['INIS']['SEQ'] = $INICIS_SEQ;
                    $_SESSION['INIS']['TID'] = $INIS_TID;
                    $_SESSION['INIS']['CURRENCY'] = 'WON'; // 통화 (WON, USD)
                    $_SESSION['INIS']['TAX'] = '0'; // 부과세
                    $_SESSION['INIS']['TAXFREE'] = '0'; // 비과세

                    include($_SERVER['DOCUMENT_ROOT']. '/php/temp/INIS/refund.php'); // executeRefundCode 를 사용하기 위해
                }
            }

            $table = "PURCHASE_OPTION";
            $table2 = "CATEGORY_OPTION";

            for ($i = 0; $i < count($optionArray); $i++) {
                $CATEGORY3_SEQ = $PRODUCTArray[$i];
                $OPTION_SEQ = $optionArray[$i];
                $OP_STATE_CD = $StatesArray[$i];
                $OP_PRICE = $pricesArray[$i];

                $STATE = "";

                if ($op_state == "41") {
                    if (!in_array($OP_STATE_CD, $OP_STATE_VAL)) {
                        gfn_isValidation(999, "", "주문취소를 진행할 수 없습니다. 확인 후 다시 주문취소를 눌러주세요.");
                    }

                    if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && $OP_STATE_CD == "21") {
                        $STATE = "42";
                    } else if ($TYPE_CD == "NBKB" && $OP_STATE_CD == "01") {
                        $STATE = "42";
                    } else {
                        $STATE = "41";
                    }
                } else if ($op_state == "51") {
                    if (!in_array($OP_STATE_CD, $OP_STATE_VAL)) {
                        gfn_isValidation(999, "", "환불요청을 진행할 수 없습니다. 확인 후 다시 환불요청을 눌러주세요.");
                    }

                    $STATE = $op_state;
                }

                if (empty($STATE)) {
                    dieAndErrorMove("잘못된 접근입니다.");
                }

                $values = array(
                      'STATE_CD' => $STATE
                    , 'mod_user' => $ID // 등록자
                    , 'mod_ip' => $ip // 등록자 아이피
                    , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                );

                $pkvalues = array (
                      'PURCHASE_SEQ' => $PURCHASE_SEQ
                    , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                    , 'OPTION_SEQ' => $OPTION_SEQ
                );

                $name_sql = "작품 옵션 개별 주문상태 상태변경";
                $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }

                if ($PAGE_TYPE == PAGE1) {
                    if ($STATE == "42" || $STATE == "52") {
                        $SOLD_YN = "N";
                    } else {
                        $SOLD_YN = "Y";
                    }

                    $values = array(
                          'SOLD_YN' => $SOLD_YN
                        , 'mod_user' => $ID // 등록자
                        , 'mod_ip' => $ip // 등록자 아이피
                        , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                    );

                    $pkvalues = array (
                        'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                        , 'OPTION_SEQ' => $OPTION_SEQ
                    );

                    $name_sql = "작품 품절 상태변경";
                    $clefResult = $mysqldb->update($table2, $values, $pkvalues, $name_sql);

                    if (!$clefResult->getResult()) {
                        gfn_isValidation(502);
                    }
                } else if ($PAGE_TYPE == PAGE2) {
                    if ($STATE == "42") {
                        $sql = "
                             SELECT GROUP_CONCAT(CATEGORY3_SEQ SEPARATOR ',') as PRODUCTS
                                  , GROUP_CONCAT(OPTION_SEQ SEPARATOR ',') as Options
                               FROM CATEGORY_OPTION
                              WHERE OPTION_SEQ IN (SELECT OPTION_SEQ
                                                     FROM PURCHASE_OPTION
                                                    WHERE PURCHASE_SEQ = '$PURCHASE_SEQ'
                                                      AND CATEGORY3_SEQ = '$CATEGORY3_SEQ'
                                                      AND OPTION_SEQ = '$OPTION_SEQ'
                                                      AND STATE_CD = '42')";
    
                        $name_sql = "카테고리3 옵션 값 추출";
                        $clefResult = $mysqldb->get($sql, null, $name_sql);
    
                        if (!$clefResult->getResult()) {
                            gfn_isValidation(800);
                        }
    
                        $cata3op = $clefResult->getResultSet();
    
                        if (!empty($cata3op)) {
                            $CATA_PRODUCTS = _check_var($cata3op['PRODUCTS']);
                            $CATA_Options = _check_var($cata3op['Options']);
        
                            $CATA_PRODUCTArray = explode(',', $CATA_PRODUCTS);
                            $CATA_optionArray = explode(',', $CATA_Options);
    
                            for ($j = 0; $j < count($CATA_optionArray); $j++) {
                                $CATA_seq = $CATA_PRODUCTArray[$j];
                                $CATA_code = $CATA_optionArray[$j];
                                
                                $BOBY_INFO = array(
                                      'CATEGORY3_SEQ' => $CATA_seq
                                    , 'OPTION_SEQ' => $CATA_code
                                    , 'VAL' => 'QUANTITY'
                                );
        
                                $CATA_QUANTITY = gfn_OPTION_QUANTITY($BOBY_INFO);
    
                                $BOBY_INFO = array(
                                      'CATEGORY3_SEQ' => $CATA_seq
                                    , 'OPTION_SEQ' => $CATA_code
                                    , 'VAL' => 'SOLD_YN'
                                );
        
                                $CATA_SOLD_YN = gfn_OPTION_QUANTITY($BOBY_INFO);
        
                                $BOBY_INFO = array(
                                      'PURCHASE_SEQ' => $PURCHASE_SEQ
                                    , 'CATEGORY3_SEQ' => $CATA_seq
                                    , 'OPTION_SEQ' => $CATA_code
                                    , 'VAL' => 'QUANTITY'
                                );
                                
                                $ORDER_QUANTITY = gfn_ORDER_OPTION_QUANTITY($BOBY_INFO);
    
                                $option_table = 'CATEGORY_OPTION';
    
                                $option_values = array();
                                $option_pkvalues = array();
    
                                $COUNT_QUANTITY = (int)$CATA_QUANTITY + (int)$ORDER_QUANTITY;
                                
                                $option_values['QUANTITY'] = $COUNT_QUANTITY;
                                
                                if ($COUNT_QUANTITY == 0) {
                                    $option_values['SOLD_YN'] = 'Y';
                                } else if ($CATA_QUANTITY == 0 && $CATA_SOLD_YN == 'Y') {
                                    if ($COUNT_QUANTITY != 0) {
                                        $option_values['SOLD_YN'] = 'N';
                                    }
                                }
    
                                $option_values['mod_user'] = $ID;
                                $option_values['mod_ip'] = $ip;
                                $option_values['mod_date'] = date('Y-m-d H:i:s');
    
                                $option_pkvalues['OPTION_SEQ'] = $CATA_code;
                                $option_pkvalues['CATEGORY3_SEQ'] = $CATA_seq;
                                
                                $name_sql = "수량 수정";
                                $clefResult = $mysqldb->update($option_table, $option_values, $option_pkvalues, $name_sql);
    
                                if (!$clefResult->getResult()) {
                                    gfn_isValidation(502);
                                }
                            }
                        }
                    }
                }

                $TOTAL_CONFIRMPRICE = (string)((int)$TOTAL_CONFIRMPRICE - (int)$OP_PRICE);

                if ($TOTAL_CONFIRMPRICE == $REAL_DLVY_PRICE && $REAL_DLVY_PRICE != 0) {
                    $TOTAL_CONFIRMPRICE = (string)((int)$TOTAL_CONFIRMPRICE - (int)$REAL_DLVY_PRICE);
                    $OP_PRICE = (string)((int)$OP_PRICE + (int)$REAL_DLVY_PRICE);
                }

                // 이니시스
                if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && $OP_STATE_CD == "21" && count($optionArray) != $TOTAL_COUNT) {
                    $_SESSION['INIS']['INIS_MSG'] = '주문 취소처리';

                    $_SESSION['INIS']['CANCEL_SEQ'] = $OPTION_SEQ; // 취소 시퀀스
                    $_SESSION['INIS']['PRICE'] = $OP_PRICE;
                    $_SESSION['INIS']['CONFIRMPRICE'] = $TOTAL_CONFIRMPRICE;
                    $_SESSION['INIS']['CANCEL_NAME'] = $ID;

                    executePartialRefundCode();
                } 

                $CANCEL_PRICE = (int)$CANCEL_PRICE + (int)$OP_PRICE;
            }

            if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && $STATE == "42" && count($optionArray) == $TOTAL_COUNT) {
                $_SESSION['INIS']['INIS_MSG'] = '주문 취소처리';
                $_SESSION['INIS']['CANCEL_SEQ'] = $OPTION_SEQ; // 취소 시퀀스
                $_SESSION['INIS']['CANCEL_NAME'] = $ID;

                executeRefundCode();
            }

            $table = "PURCHASE_ORDER";

            $STATE = "";

            $sql = "
                 SELECT (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '01') AS STATE_COUNT01
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '21') AS STATE_COUNT21
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '30') AS STATE_COUNT30
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '31') AS STATE_COUNT31
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '32') AS STATE_COUNT32
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '41') AS STATE_COUNT41
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '42') AS STATE_COUNT42
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '51') AS STATE_COUNT51
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '52') AS STATE_COUNT52
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ) AS TOTAL_STATE_COUNT
                   FROM PURCHASE_ORDER M
                  WHERE PURCHASE_SEQ = :PURCHASE_SEQ";

            $name_sql = "주문상태 카운트값";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (empty($data)) {
                dieAndErrorMove("잘못된 접근입니다.");
            }

            $STATE_COUNT01 = _check_var($data['STATE_COUNT01']); // 주문접수
            $STATE_COUNT21 = _check_var($data['STATE_COUNT21']); // 결제완료
            $STATE_COUNT30 = _check_var($data['STATE_COUNT30']); // 배송준비중
            $STATE_COUNT31 = _check_var($data['STATE_COUNT31']); // 배송중
            $STATE_COUNT32 = _check_var($data['STATE_COUNT32']); // 배송완료
            $STATE_COUNT41 = _check_var($data['STATE_COUNT41']); // 주문취소요청
            $STATE_COUNT42 = _check_var($data['STATE_COUNT42']); // 주문취소
            $STATE_COUNT51 = _check_var($data['STATE_COUNT51']); // 환불요청
            $STATE_COUNT52 = _check_var($data['STATE_COUNT52']); // 환불승인
            $TOTAL_STATE_COUNT = _check_var($data['TOTAL_STATE_COUNT']); // 토탈값

            $STATE_COUNTS = [
                '52' => $STATE_COUNT52,
                '51' => $STATE_COUNT51,
                '42' => $STATE_COUNT42,
                '41' => $STATE_COUNT41,
                '32' => $STATE_COUNT32,
                '31' => $STATE_COUNT31,
                '30' => $STATE_COUNT30,
                '21' => $STATE_COUNT21,
                '01' => $STATE_COUNT01
            ];

            $STATE = determineStateCD($TOTAL_STATE_COUNT, $STATE_COUNTS);

            $values = array(
                  'STATE_CD' => $STATE
                , 'mod_user' => $ID // 등록자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $pkvalues = array (
                  'PURCHASE_SEQ' => $PURCHASE_SEQ
            );

            $name_sql = "작품 옵션 개별 주문상태 상태변경";
            $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            $arr_msg = "";

            if ($op_state == "41") {
                $arr_msg = "주문취소 진행이 완료되었습니다.";
            } else if ($op_state == "51"){
                $arr_msg = "환불요청이 완료되었습니다.";
            }

            $arrValue2 = array();
            $arrValue2[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

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

            if ($op_state == "41") {
                $Info_value = array(
                      'userid' => bizppurio_userid
                    , 'userpw' => bizppurio_password
                    , 'apikey' => bizppurio_apikey
                    , 'senderkey' => bizppurio_senderkey
                    , 'tpl_code' => 'bizp_2023111517430409144108153'
                );
                
                $message_emtitle = array();
    
                $message = array(
                      "주문상품명" => $temp_name['temp']
                    , "주문번호" => $PURCHASE_SEQ
                    , "취소금액" => number_format($CANCEL_PRICE). '원'
                );

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
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = $arr_msg;
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_ORDER_CHANGE
     * comment : 주문 상태변경
     */
    function ufn_ORDER_CHANGE() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $PURCHASE_SEQ = get_request_param('SEQ'); // 주문번호
            $TYPE_CD = get_request_param('TYPE_CD'); // 결제방식 공통코드 COL003
            $STATE_CD = get_request_param('STATE_CD'); // 주문 - 주문상태 - 전체
            $INICIS_SEQ = get_request_param('INICIS_SEQ'); // 이니시스 시퀀스 [카드 , 계좌이체 경우]

            $MOBILE = get_request_param('MOBILE'); // 사용자 연락처

            $TOTAL_COUNTS = get_request_param('totalcount'); // 체크 카운트 값
            $TOTAL_PRICE = get_request_param('TOTAL_PRICE'); // 토탈금액
            $TOTAL_NOW_PRICE = get_request_param('TOTAL_NOW_PRICE'); // 현금액
            $REAL_DLVY_PRICE = get_request_param('REAL_DLVY_PRICE'); // 실배송비
            $op_state = get_request_param('change_stage'); // 변경할 상태값

            $SEQS = get_request_param('pk'); // 주문번호 - 필요시 사용
            $PRODUCTS = get_request_param('val'); // 작품 시퀀스
            $Options = get_request_param('Options'); // 주문 옵션 시퀀스
            $States = get_request_param('state'); //  주문별 - 개별 주문 상태
            $prices = get_request_param('price'); //  주문별 - 작품 금액

            $TOTALrray = explode(',', $TOTAL_COUNTS); // 주문번호 - 필요시 사용
            $SEQrray = explode(',', $SEQS); // 주문번호 - 필요시 사용
            $PRODUCTArray = explode(',', $PRODUCTS); // 작품 시퀀스
            $optionArray = explode(',', $Options); // 주문 옵션 시퀀스
            $StatesArray = explode(',', $States); //  주문별 - 개별 주문 상태
            $pricesArray = explode(',', $prices); //  주문별 - 작품 금액

            $INIS_TID = '';
            $TOTAL_CONFIRMPRICE = ""; 
            $TOTAL_COUNT = $TOTALrray[0]; // 토탈

            $TOTAL_INVOICE = "";

            if (!empty($MOBILE)) {
                $MOBILE = str_replace('-', '', $MOBILE);
            }

            $CANCEL_PRICE = 0;

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $arrValue = array();
            $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

            $sql = "
                SELECT STATE_CD
                     , ZCM_COM_NM('COL005', STATE_CD) AS STATE_CD_NM
                     , PAGE_TYPE
                  FROM PURCHASE_ORDER
                 WHERE PURCHASE_SEQ = :PURCHASE_SEQ";

            $name_sql = "주문상태 확인";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (empty($data)) {
                dieAndErrorMove("잘못된 접근입니다.");
            }

            $STATE_CD = _check_var($data['STATE_CD']); // 주문상태
            $STATE_CD_NM = _check_var($data['STATE_CD_NM']); // 주문상태
            $PAGE_TYPE = _check_var($data['PAGE_TYPE']); // 페이지구분

            if ($STATE_CD == "42" || $STATE_CD == "52") {
                gfn_isValidation(999, "", "이미 주문이 ". $STATE_CD_NM ."된 작품이 존재합니다.");
            }
            
            $table = "PURCHASE_OPTION";
            $table2 = "CATEGORY_OPTION";

            $TOTAL_CONFIRMPRICE = $TOTAL_NOW_PRICE;

            if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && count($optionArray) != $TOTAL_COUNT) { // 카드결제
                $INIS_TID = gfn_getIinisVal($INICIS_SEQ, "TID");

                if (!empty($INIS_TID)) {
                    $_SESSION['INIS']['SEQ'] = $INICIS_SEQ;
                    $_SESSION['INIS']['TID'] = $INIS_TID;
                    $_SESSION['INIS']['CURRENCY'] = 'WON'; // 통화 (WON, USD)
                    $_SESSION['INIS']['TAX'] = '0'; // 부과세
                    $_SESSION['INIS']['TAXFREE'] = '0'; // 비과세

                    include($_SERVER['DOCUMENT_ROOT']. '/php/temp/INIS/partialRefund.php'); // executePartialRefundCode 를 사용하기 위해
                }
            } else if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && count($optionArray) == $TOTAL_COUNT) {
                $INIS_TID = gfn_getIinisVal($INICIS_SEQ, "TID");

                if (!empty($INIS_TID)) {
                    $_SESSION['INIS']['SEQ'] = $INICIS_SEQ;
                    $_SESSION['INIS']['TID'] = $INIS_TID;
                    $_SESSION['INIS']['CURRENCY'] = 'WON'; // 통화 (WON, USD)
                    $_SESSION['INIS']['TAX'] = '0'; // 부과세
                    $_SESSION['INIS']['TAXFREE'] = '0'; // 비과세

                    include($_SERVER['DOCUMENT_ROOT']. '/php/temp/INIS/refund.php'); // executeRefundCode 를 사용하기 위해
                }
            }

            for ($i = 0; $i < count($optionArray); $i++) {
                $CATEGORY3_SEQ = $PRODUCTArray[$i];
                $OPTION_SEQ = $optionArray[$i];
                $OP_STATE_CD = $StatesArray[$i];
                $OP_PRICE = $pricesArray[$i];

                if ($OP_STATE_CD == "42") {
                    gfn_isValidation(999, "", "이미 주문이 취소된 작품이 존재합니다.");
                }

                if ($OP_STATE_CD == "52") {
                    gfn_isValidation(999, "", "이미 주문이 환불승인된 작품이 존재합니다.");
                }

                $values = array(
                      'STATE_CD' => $op_state
                    , 'mod_user' => $_SESSION['adm']['name'] // 등록자
                    , 'mod_ip' => $ip // 등록자 아이피
                    , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                );

                $pkvalues = array (
                      'PURCHASE_SEQ' => $PURCHASE_SEQ
                    , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                    , 'OPTION_SEQ' => $OPTION_SEQ
                );

                $name_sql = "작품 옵션 개별 주문상태 상태변경";
                $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }

                if ($op_state == "31") {
                    $BOBY_INFO = array(
                          'PURCHASE_SEQ' => $PURCHASE_SEQ
                        , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                        , 'OPTION_SEQ' => $OPTION_SEQ
                        , 'VAL' => 'INVOICE_NUMBER'
                    );
                  
                    $ORDER_INVOICE_NUMBER = gfn_ORDER_OPTION_QUANTITY($BOBY_INFO);

                    if (!empty($ORDER_INVOICE_NUMBER)) {
                        if (strpos($TOTAL_INVOICE, $ORDER_INVOICE_NUMBER) === false) {
                            if (empty($TOTAL_INVOICE)) {
                                $TOTAL_INVOICE = $ORDER_INVOICE_NUMBER;
                            } else {
                                $TOTAL_INVOICE .= ','.$ORDER_INVOICE_NUMBER;
                            }
                        }
                    }
                }

                if ($PAGE_TYPE == PAGE1) {
                    if ($op_state == "42" || $op_state == "52") {
                        $SOLD_YN = "N";
                    } else {
                        $SOLD_YN = "Y";
                    }

                    $values = array(
                          'SOLD_YN' => $SOLD_YN
                        , 'mod_user' => $_SESSION['adm']['name'] // 등록자
                        , 'mod_ip' => $ip // 등록자 아이피
                        , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                    );

                    $pkvalues = array (
                          'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                        , 'OPTION_SEQ' => $OPTION_SEQ
                    );

                    $name_sql = "작품 품절 상태변경";
                    $clefResult = $mysqldb->update($table2, $values, $pkvalues, $name_sql);

                    if (!$clefResult->getResult()) {
                        gfn_isValidation(502);
                    }
                } else if ($PAGE_TYPE == PAGE2) {
                    if ($op_state == "42" || $op_state == "52") {
                        $sql = "
                             SELECT GROUP_CONCAT(CATEGORY3_SEQ SEPARATOR ',') as PRODUCTS
                                  , GROUP_CONCAT(OPTION_SEQ SEPARATOR ',') as Options
                               FROM CATEGORY_OPTION
                              WHERE OPTION_SEQ IN (SELECT OPTION_SEQ
                                                     FROM PURCHASE_OPTION
                                                    WHERE PURCHASE_SEQ = '$PURCHASE_SEQ'
                                                      AND CATEGORY3_SEQ = '$CATEGORY3_SEQ'
                                                      AND OPTION_SEQ = '$OPTION_SEQ'
                                                      AND STATE_CD IN ('42', '52'))";
    
                        $name_sql = "카테고리3 옵션 값 추출";
                        $clefResult = $mysqldb->get($sql, null, $name_sql);
    
                        if (!$clefResult->getResult()) {
                            gfn_isValidation(800);
                        }
    
                        $cata3op = $clefResult->getResultSet();
    
                        if (!empty($cata3op)) {
                            $CATA_PRODUCTS = _check_var($cata3op['PRODUCTS']);
                            $CATA_Options = _check_var($cata3op['Options']);

                            if (!empty($CATA_PRODUCTS) && !empty($CATA_Options)) {        
                               $CATA_PRODUCTArray = explode(',', $CATA_PRODUCTS);
                               $CATA_optionArray = explode(',', $CATA_Options);
    
                                for ($j = 0; $j < count($CATA_optionArray); $j++) {
                                    $CATA_seq = $CATA_PRODUCTArray[$j];
                                    $CATA_code = $CATA_optionArray[$j];
                                    
                                    $BOBY_INFO = array(
                                          'CATEGORY3_SEQ' => $CATA_seq
                                        , 'OPTION_SEQ' => $CATA_code
                                        , 'VAL' => 'QUANTITY'
                                    );
        
                                    $CATA_QUANTITY = gfn_OPTION_QUANTITY($BOBY_INFO);
    
                                    $BOBY_INFO = array(
                                          'CATEGORY3_SEQ' => $CATA_seq
                                        , 'OPTION_SEQ' => $CATA_code
                                        , 'VAL' => 'SOLD_YN'
                                    );
        
                                    $CATA_SOLD_YN = gfn_OPTION_QUANTITY($BOBY_INFO);
        
                                    $BOBY_INFO = array(
                                          'PURCHASE_SEQ' => $PURCHASE_SEQ
                                        , 'CATEGORY3_SEQ' => $CATA_seq
                                        , 'OPTION_SEQ' => $CATA_code
                                        , 'VAL' => 'QUANTITY'
                                    );
                                    
                                    $ORDER_QUANTITY = gfn_ORDER_OPTION_QUANTITY($BOBY_INFO);
    
                                    $option_table = 'CATEGORY_OPTION';
    
                                    $option_values = array();
                                    $option_pkvalues = array();
    
                                    $COUNT_QUANTITY = intval($CATA_QUANTITY) + intval($ORDER_QUANTITY);
                                    
                                    $option_values['QUANTITY'] = $COUNT_QUANTITY;
                                    
                                    if ($COUNT_QUANTITY == 0) {
                                        $option_values['SOLD_YN'] = 'Y';
                                    } else if ($CATA_QUANTITY == 0 && $CATA_SOLD_YN == 'Y') {
                                        if ($COUNT_QUANTITY != 0) {
                                            $option_values['SOLD_YN'] = 'N';
                                        }
                                    }
    
                                    $option_values['mod_user'] = $_SESSION['adm']['name']; // 등록자
                                    $option_values['mod_ip'] = $ip;
                                    $option_values['mod_date'] = date('Y-m-d H:i:s');
    
                                    $option_pkvalues['OPTION_SEQ'] = $CATA_code;
                                    $option_pkvalues['CATEGORY3_SEQ'] = $CATA_seq;
                                    
                                    $name_sql = "수량 수정";
                                    $clefResult = $mysqldb->update($option_table, $option_values, $option_pkvalues, $name_sql);
    
                                    if (!$clefResult->getResult()) {
                                        gfn_isValidation(502);
                                    }
                                }
                            }
                        }
                    }
                }

                if ($op_state == "42" || $op_state == "52") {
                    $TOTAL_CONFIRMPRICE = (string)((int)$TOTAL_CONFIRMPRICE - (int)$OP_PRICE);
                    
                    if ($TOTAL_CONFIRMPRICE == $REAL_DLVY_PRICE && $REAL_DLVY_PRICE != 0) {
                        $TOTAL_CONFIRMPRICE = (string)((int)$TOTAL_CONFIRMPRICE - (int)$REAL_DLVY_PRICE);
                        $OP_PRICE = (string)((int)$OP_PRICE + (int)$REAL_DLVY_PRICE);

                    }
                }
                
                // 이니시스
                if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && ($op_state == "42" || $op_state == "52") && count($optionArray) != $TOTAL_COUNT) {
                    if ($op_state == "42") {
                        $_SESSION['INIS']['INIS_MSG'] = '주문 취소처리';
                    } else if ($op_state == "52") {
                        $_SESSION['INIS']['INIS_MSG'] = '주문 환불처리';
                    }

                    $_SESSION['INIS']['CANCEL_SEQ'] = $OPTION_SEQ; // 취소 시퀀스
                    $_SESSION['INIS']['PRICE'] = $OP_PRICE;
                    $_SESSION['INIS']['CONFIRMPRICE'] = $TOTAL_CONFIRMPRICE;
                    $_SESSION['INIS']['CANCEL_NAME'] = $_SESSION['adm']['name'];

                    executePartialRefundCode();
                }

                $CANCEL_PRICE = (int)$CANCEL_PRICE + (int)$OP_PRICE;
            }

            if (($TYPE_CD == "CCARD" || $TYPE_CD == "RTBT") && ($op_state == "42" || $op_state == "52") && count($optionArray) == $TOTAL_COUNT) {
                $_SESSION['INIS']['INIS_MSG'] = '주문 취소처리';
                $_SESSION['INIS']['CANCEL_SEQ'] = $OPTION_SEQ; // 취소 시퀀스
                $_SESSION['INIS']['CANCEL_NAME'] = $_SESSION['adm']['name'];

                executeRefundCode();
            }

            $table = "PURCHASE_ORDER";

            $sql = "
                 SELECT (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '01') AS STATE_COUNT01
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '21') AS STATE_COUNT21
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '30') AS STATE_COUNT30
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '31') AS STATE_COUNT31
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '32') AS STATE_COUNT32
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '41') AS STATE_COUNT41
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '42') AS STATE_COUNT42
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '51') AS STATE_COUNT51
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '52') AS STATE_COUNT52
                      , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ) AS TOTAL_STATE_COUNT
                   FROM PURCHASE_ORDER M
                  WHERE PURCHASE_SEQ = :PURCHASE_SEQ";

            $name_sql = "주문상태 카운트값";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (empty($data)) {
                dieAndErrorMove("잘못된 접근입니다.");
            }

            $STATE_COUNT01 = _check_var($data['STATE_COUNT01']); // 주문접수
            $STATE_COUNT21 = _check_var($data['STATE_COUNT21']); // 결제완료
            $STATE_COUNT30 = _check_var($data['STATE_COUNT30']); // 배송준비중
            $STATE_COUNT31 = _check_var($data['STATE_COUNT31']); // 배송중
            $STATE_COUNT32 = _check_var($data['STATE_COUNT32']); // 배송완료
            $STATE_COUNT41 = _check_var($data['STATE_COUNT41']); // 주문취소요청
            $STATE_COUNT42 = _check_var($data['STATE_COUNT42']); // 주문취소
            $STATE_COUNT51 = _check_var($data['STATE_COUNT51']); // 환불요청
            $STATE_COUNT52 = _check_var($data['STATE_COUNT52']); // 환불승인
            $TOTAL_STATE_COUNT = _check_var($data['TOTAL_STATE_COUNT']); // 토탈값

            $STATE_COUNTS = [
                '52' => $STATE_COUNT52,
                '51' => $STATE_COUNT51,
                '42' => $STATE_COUNT42,
                '41' => $STATE_COUNT41,
                '32' => $STATE_COUNT32,
                '31' => $STATE_COUNT31,
                '30' => $STATE_COUNT30,
                '21' => $STATE_COUNT21,
                '01' => $STATE_COUNT01
            ];

            $STATE_CD = determineStateCD($TOTAL_STATE_COUNT, $STATE_COUNTS);
            
            $values = array(
                  'STATE_CD' => $STATE_CD
                , 'mod_user' => $_SESSION['adm']['name'] // 등록자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            if ($op_state == "31") {
                $values['ORDER_DATE'] = date('Y-m-d H:i:s'); 
            }

            $pkvalues = array (
                  'PURCHASE_SEQ' => $PURCHASE_SEQ
            );

            $name_sql = "작품 주문상태 상태변경";
            $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            $arrValue2 = array();
            $arrValue2[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

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

            if ($op_state == "31") {
                $NVOICEArray = explode(',', $TOTAL_INVOICE);
                $NVOICE_URL = "";

                for ($i = 0; $i < count($NVOICEArray); $i++) {
                    $NVOICE = $NVOICEArray[$i];

                    if (empty($NVOICE_URL)) {
                        $NVOICE_URL = 'https://www.ilogen.com/m/personal/trace/'. $NVOICE;
                    } else {
                        $NVOICE_URL .= ' , https://www.ilogen.com/m/personal/trace/'. $NVOICE;
                    }
                }

                $Info_value = array(
                      'userid' => bizppurio_userid
                    , 'userpw' => bizppurio_password
                    , 'apikey' => bizppurio_apikey
                    , 'senderkey' => bizppurio_senderkey
                    , 'tpl_code' => 'bizp_2023111517380018915835005'
                );

                $message_emtitle = array();
    
                $message = array(
                      '주문상품명' => $temp_name['temp']
                    , "주문번호" => $PURCHASE_SEQ
                    , '송장번호' => $TOTAL_INVOICE
                    , "송장링크" => $NVOICE_URL
                );

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
            } else  if ($op_state == "32") { // 배송완료
                $Info_value = array(
                      'userid' => bizppurio_userid
                    , 'userpw' => bizppurio_password
                    , 'apikey' => bizppurio_apikey
                    , 'senderkey' => bizppurio_senderkey
                    , 'tpl_code' => 'bizp_2023111517500609144100673'
                );
                
                $message_emtitle = array();
    
                $message = array(
                      '주문상품명' => $temp_name['temp']
                    , "주문번호" => $PURCHASE_SEQ
                );

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
            } else  if ($op_state == "42") { // 주문취소
                $Info_value = array(
                      'userid' => bizppurio_userid
                    , 'userpw' => bizppurio_password
                    , 'apikey' => bizppurio_apikey
                    , 'senderkey' => bizppurio_senderkey
                    , 'tpl_code' => 'bizp_2023111517430409144108153'
                );
                
                $message_emtitle = array();
  
                $message = array(
                      "주문상품명" => $temp_name['temp']
                    , "주문번호" => $PURCHASE_SEQ
                    , "취소금액" => number_format($CANCEL_PRICE). '원'
                );

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
            } else  if ($op_state == "52") { // 환불승인
                $Info_value = array(
                      'userid' => bizppurio_userid
                    , 'userpw' => bizppurio_password
                    , 'apikey' => bizppurio_apikey
                    , 'senderkey' => bizppurio_senderkey
                    , 'tpl_code' => 'bizp_2023111517422118915115190'
                );
                
                $message_emtitle = array();

                $message = array(
                      "주문상품명" => $temp_name['temp']
                    , "주문번호" => $PURCHASE_SEQ
                    , "환불금액" => number_format($CANCEL_PRICE). '원'
                );

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
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '주문상태가 변경되었습니다.';

            unset($_SESSION['INIS']);
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_ORDER_INVOICE
     * comment : 송장번호
     */
    function ufn_ORDER_INVOICE() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $PURCHASE_SEQ = get_request_param('SEQ'); // 주문번호
            $TOTAL_COUNTS = get_request_param('totalcount'); // 체크 카운트 값

            $SEQS = get_request_param('pk'); // 주문번호 - 필요시 사용
            $PRODUCTS = get_request_param('val'); // 작품 시퀀스
            $Options = get_request_param('Options'); // 주문 옵션 시퀀스
            $Invoices = get_request_param('Invoices'); // 송장번호 모음

            $TOTALrray = explode(',', $TOTAL_COUNTS); // 주문번호 - 필요시 사용
            $SEQrray = explode(',', $SEQS); // 주문번호 - 필요시 사용
            $PRODUCTArray = explode(',', $PRODUCTS); // 작품 시퀀스
            $optionArray = explode(',', $Options); // 주문 옵션 시퀀스
            $InvoiceArray = explode(',', $Invoices); // 송장번호

            $TOTAL_COUNT = $TOTALrray[0]; // 토탈

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = "PURCHASE_OPTION";

            for ($i = 0; $i < count($optionArray); $i++) {
                $CATEGORY3_SEQ = $PRODUCTArray[$i];
                $OPTION_SEQ = $optionArray[$i];
                $INVOICE_NUMBER = $InvoiceArray[$i];

                if (!empty($INVOICE_NUMBER)) {
                    $INVOICE_NUMBER = str_replace("-", "", $INVOICE_NUMBER);

                    if (strlen($INVOICE_NUMBER) != 11) {
                        gfn_isValidation(999, "", "송장번호는 11자리를 입력해주세요.");
                    }
                }

                $values = array();
                $pkvalues = array();

                $values['INVOICE_NUMBER'] = $INVOICE_NUMBER;
                $values['mod_user'] = $_SESSION['adm']['name']; // 등록자
                $values['mod_ip'] = $ip;
                $values['mod_date'] = date('Y-m-d H:i:s');

                $pkvalues['PURCHASE_SEQ'] = $PURCHASE_SEQ;
                $pkvalues['OPTION_SEQ'] = $OPTION_SEQ;
                $pkvalues['CATEGORY3_SEQ'] = $CATEGORY3_SEQ;

                $name_sql = $PURCHASE_SEQ. " 송장번호 변경";
                $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '송장번호가 등록되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_MEMBER_FIND_EM
     * comment : 계정 찾기 - 이메일
     */
    function ufn_MEMBER_FIND_EM() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $TYPE = get_request_param('TYPE');

            $arrValue = array();
            $table = 'MEMBER';

            $code = "";
            $msg = "";

            if ($TYPE == "FIND_ID") {
                $EMAIL = get_request_param('EMAIL');

                gfn_isValidation(302, $EMAIL, "EMAIL");

                $arrValue[':EMAIL'] = $EMAIL;

                $sql = "
                     SELECT ID
                          , NAME
                          , MOBILE
                          , EMAIL
                       FROM {$table}
                      WHERE EMAIL = :EMAIL";

                $name_sql = "계정 확인";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $data = $clefResult->getResultSet();

                if (!empty($data)) {
                    $subject = "piknic -". $data['NAME']. "님의 아이디 내역입니다.";

                    $email_title = "아이디 찾기";

                    $email_txt_wrap_html = <<<P
                                                <p>안녕하세요. 피크닉입니다.</p>
                                                <p>저희 서비스를 이용해 주시는 고객님들께 감사의 말씀 드립니다.</p>
                                                <br/>
                                                <p>아이디 찾기를 진행하셧습니다.</p>
                                                <p>아래의 내용은 고객님의 아이디정보입니다.</p>
                                            P;

                    $email_table_html = <<<TR
                                            <tr style="theadtr" id="thead">
                                                <th style="theadth" colspan="2">문의자 정보 내역</th>
                                            </tr>
                                            <tr style="bodytr">
                                                <th style="bodyth">문의자 명</th>
                                                <td style="bodytd">{$data['NAME']}</td>
                                            </tr>
                                            <tr style="bodytr">
                                                <th style="bodyth">아이디</th>
                                                <td style="bodytd">{$data['ID']}</td>
                                            </tr>
                                        TR;

                    $MAIL_INFO = [
                          'to' => $EMAIL
                        , 'subject' => $subject
                        , 'email_title' => $email_title
                        , 'email_txt_wrap_html' => $email_txt_wrap_html
                        , 'email_table_html' => $email_table_html
                        , 'path' => ""
                        , 'fileName' => ""
                        , 'EMAIL' => SMTP_EMAIL
                        , 'PW' => SMTP_PW
                        , 'NAME' => 'piknic'
                        , 'TYPE' => 'naver'
                    ];

                    $arrRes = gfn_send_mail($MAIL_INFO);

                    if ($arrRes) {
                        $code = 200;
                        $msg = 'ID 내역이 전송되었습니다.';
                    } else {
                        gfn_isValidation(999, "", "이메일 발송실패 관리자에게 문의필요");
                    }
                } else {
                    gfn_isValidation(999, "", "이메일을 확인해주세요.");
                }
            } else if ($TYPE == "FIND_PW") {
                $ID = get_request_param('ID');
                $EMAIL = get_request_param('EMAIL');

                gfn_isValidation(302, $ID, "아이디");
                gfn_isValidation(302, $EMAIL, "이메일");

                $arrValue[':ID'] = $ID;
                $arrValue[':EMAIL'] = $EMAIL;

                $sql = "
                     SELECT ID
                          , NAME
                          , MOBILE
                          , EMAIL
                       FROM {$table}
                      WHERE ID = :ID
                        AND EMAIL = :EMAIL";

                $name_sql = "계정 확인";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $data = $clefResult->getResultSet();

                if (!empty($data)) {
                    $PASSWORD  = generateRandomPassword();

                    $subject = "piknic -". $data['NAME']. "님의 임시비밀번호 발급 내역입니다.";

                    $email_title = "임시비밀번호 발급";

                    $email_txt_wrap_html = <<<P
                                                <p>안녕하세요. 피크닉입니다.</p>
                                                <p>저희 서비스를 이용해 주시는 고객님들께 감사의 말씀 드립니다.</p>
                                                <br/>
                                                <p>비밀번호 찾기를 진행하셨습니다.</p>
                                                <br/>
                                                <p>로그인 진행 후 꼭 비밀번호를 변경해주세요.</p>
                                                <p>아래의 내용은 고객님의 임시비밀번호 발급 내역입니다.</p>
                                            P;

                    $email_table_html = <<<TR
                                            <tr style="theadtr" id="thead">
                                                <th style="theadth" colspan="2">문의자 정보 내역</th>
                                            </tr>
                                            <tr style="bodytr">
                                                <th style="bodyth">문의자 명</th>
                                                <td style="bodytd">{$data['NAME']}</td>
                                            </tr>
                                            <tr style="bodytr">
                                                <th style="bodyth">아이디</th>
                                                <td style="bodytd">{$data['ID']}</td>
                                            </tr>
                                            <tr style="bodytr">
                                                <th style="bodyth">임시비밀번호</th>
                                                <td style="bodytd">{$PASSWORD}</td>
                                            </tr>
                                        TR;

                    $MAIL_INFO = [
                          'to' => $EMAIL
                        , 'subject' => $subject
                        , 'email_title' => $email_title
                        , 'email_txt_wrap_html' => $email_txt_wrap_html
                        , 'email_table_html' => $email_table_html
                        , 'path' => ""
                        , 'fileName' => ""
                        , 'EMAIL' => SMTP_EMAIL
                        , 'PW' => SMTP_PW
                        , 'NAME' => 'piknic'
                        , 'TYPE' => 'naver'
                    ];

                    $arrRes = gfn_send_mail($MAIL_INFO);

                    if ($arrRes) {
                        $code = 200;
                        $msg = '임시비밀번호가 전송되었습니다.';

                        $arrValue = array();
                        $values['PASSWORD'] = gfn_getEncrypt(gfn_encrypted($PASSWORD), $_SESSION['projectkey']);

                        $name_sql = "비밀번호 수정";
                        $clefResult = $mysqldb->update($table, $values, ['ID' => $ID], $name_sql);

                        if (!$clefResult->getResult()) {
                            gfn_isValidation(502);
                        }
                    } else {
                        gfn_isValidation(999, "", "이메일 발송실패 관리자에게 문의필요");
                    }
                } else {
                    gfn_isValidation(999, "", "아이디 혹은 이메일을 확인해주세요.");
                }
            } else {
                gfn_isValidation(999, "", "이메일 발송실패 관리자에게 문의필요");
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = $code;
            $arrRtn['msg'] = $msg;
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_MEMBER_FIND_PH
     * comment : 계정 찾기 - 휴대폰
     */
    function ufn_MEMBER_FIND_PH() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'seq' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $TYPE = get_request_param('TYPE');

            $arrValue = array();
            $table = 'MEMBER';

            $code = "";
            $msg = "";

            if ($TYPE == "FIND_ID") {
                $MOBILE = get_request_param('MOBILE');

                gfn_isValidation(302, $MOBILE, "연락처");

                $MOBILE = str_replace('-', '', $MOBILE);

                $arrValue[':MOBILE'] = $MOBILE;

                $sql = "
                     SELECT ID
                          , NAME
                          , MOBILE
                          , EMAIL
                       FROM {$table}
                      WHERE MOBILE = :MOBILE";

                $name_sql = "계정 확인";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $data = $clefResult->getResultSet();

                if (!empty($data)) {
                    $Info_value = array(
                          'userid' => bizppurio_userid
                        , 'userpw' => bizppurio_password
                        , 'apikey' => bizppurio_apikey
                        , 'senderkey' => bizppurio_senderkey
                        , 'tpl_code' => 'bizp_2023111517535418915505673'
                    );
                    
                    $message_emtitle = array();

                    $message = array(
                          "고객명" => $data['NAME']
                        , "고객아이디" => $data['ID']
                    );
                    
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

                    if ($arrRes) {
                        $code = 200;
                        $msg = '휴대폰으로 ID가 전송되었습니다.';
                    } else {
                        gfn_isValidation(999, "", "휴대폰 발송실패 관리자에게 문의필요". "[".$arrRes."]");
                    }
                } else {
                    gfn_isValidation(999, "", "회원 아이디 또는 이메일이 일치하지 않습니다.");
                }
            } else if ($TYPE == "FIND_PW") {
                $ID = get_request_param('ID');
                $MOBILE = get_request_param('MOBILE');

                gfn_isValidation(302, $ID, "아이디");
                gfn_isValidation(302, $MOBILE, "연락처");

                $MOBILE = str_replace('-', '', $MOBILE);

                $arrValue[':ID'] = $ID;
                $arrValue[':MOBILE'] = $MOBILE;

                 $sql = "
                      SELECT ID
                           , NAME
                           , MOBILE
                           , EMAIL
                        FROM {$table}
                       WHERE ID = :ID
                         AND MOBILE = :MOBILE";

                $name_sql = "계정 확인";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $data = $clefResult->getResultSet();

                if (!empty($data)) {
                    $PASSWORD  = generateRandomPassword();

                    $Info_value = array(
                          'userid' => bizppurio_userid
                        , 'userpw' => bizppurio_password
                        , 'apikey' => bizppurio_apikey
                        , 'senderkey' => bizppurio_senderkey
                        , 'tpl_code' => 'bizp_2023111517554309144897414'
                    );
                    
                    $message_emtitle = array();

                    $message = array(
                          "고객명" => $data['NAME']
                        , "고객아이디" => $data['ID']
                        , "임시비밀번호" => $PASSWORD
                    );

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

                    if ($arrRes) {
                        $code = 200;
                        $msg = '휴대폰으로 임시비밀번호가 전송되었습니다.';

                        $arrValue = array();
                        $values['PASSWORD'] = gfn_getEncrypt(gfn_encrypted($PASSWORD), $_SESSION['projectkey']);

                        $name_sql = "비밀번호 수정";
                        $clefResult = $mysqldb->update($table, $values, ['ID' => $ID], $name_sql);

                        if (!$clefResult->getResult()) {
                            gfn_isValidation(502);
                        }
                    } else {
                        gfn_isValidation(999, "", "휴대폰 발송실패 관리자에게 문의필요". "[".$arrRes."]");
                    }
                } else {
                    gfn_isValidation(999, "", "회원 아이디 또는 휴대폰 번호가 일치하지 않습니다.");
                }
            } else {
                gfn_isValidation(999, "", "휴대폰 발송실패 관리자에게 문의필요");
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = $code;
            $arrRtn['msg'] = $msg;
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_MEMBER_TOKEN
     * comment : 카카오, 네이버 토큰 연동
     */
    function ufn_MEMBER_TOKEN() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $ID = $_SESSION['SNSIFNO']['ID'];
            $ACCESS_TOKEN_NAVER = "";
            $ACCESS_TOKEN_KAKAO = "";

            gfn_isValidation(305, $ID, "연동 오류 다시 진행해 주시길 바랍니다.");

            if (isset($_SESSION['SNSIFNO'])) {
                if (!empty($_SESSION['SNSIFNO'])) {
                    if (isset($_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'])) {
                        $ACCESS_TOKEN_NAVER = $_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'];
                    }

                    if (isset($_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'])) {
                        $ACCESS_TOKEN_KAKAO = $_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'];
                    }
                }
            }

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $values = array();

            if (!empty($ACCESS_TOKEN_NAVER)) {
                $values['ACCESS_TOKEN_NAVER'] = gfn_getEncrypt(gfn_encrypted($ACCESS_TOKEN_NAVER), $_SESSION['projectkey']);
            }

            if (!empty($ACCESS_TOKEN_KAKAO)) {
                $values['ACCESS_TOKEN_KAKAO'] = gfn_getEncrypt(gfn_encrypted($ACCESS_TOKEN_KAKAO), $_SESSION['projectkey']);
            }

            $values['mod_user'] = $_SESSION['SNSIFNO']['NAME'];
            $values['mod_ip'] = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');

            $table = 'MEMBER';

            $name_sql = "계정 수정";
            $clefResult = $mysqldb->update($table, $values, ['ID' => $ID], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            $_SESSION['MEMBER']['ID'] = $ID;

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '연동되었습니다.';

            $FoldName = $_SESSION['SNSIFNO']['PAGE'];

            if ($_SESSION['INFOR']['LOGIN_CHK']) {
                $arrRtn['url'] = $_SESSION['INFOR']['URL'];
            } else {
                $arrRtn['url'] = $FoldName. '/mypage/orderhistory.php'; // 임시
            }

            unset($_SESSION['SNSIFNO']);
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }
    
    /**
     * name :ufn_Member_DEL
     * comment : 회원 탈퇴
     */
    function ufn_Member_DEL() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $ID = $_SESSION['MEMBER']['ID'];
            $TYPE_CD = get_request_param('TYPE_CD'); // 탈퇴 사유 COL009
            $CONTENT_TEXT = get_request_param('CONTENT_TEXT'); // 탈퇴 사유 기타인경우

            gfn_isValidation(301, $TYPE_CD, "탈퇴사유");

            $table = 'MEMBER';

            $arrValue = array();
            $arrValue[':ID'] = $ID;

            $sql = "
                 SELECT NAME
                      , MOBILE
                   FROM MEMBER
                  WHERE ID = :ID";

            $name_sql = "이름";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            $NAME = _check_var($data['NAME']);
            $MOBILE = _check_var($data['MOBILE']);

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            
            $table = 'MEMBER_DEL';

            $values = array( 
                  'ID' => $ID
                , 'TYPE_CD' => $TYPE_CD
                , 'NAME' => $NAME
                , 'CONTENT_TEXT' => $CONTENT_TEXT
                , 'reg_user' => '회원탈퇴' // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "회원 탈퇴 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            $arrValue = array();
            $arrValue[':pk'] = $ID;

            $sql = "
                 DELETE FROM MEMBER
                  WHERE ID = :pk";
            
            $name_sql = "회원 탈퇴";
            $clefResult = $mysqldb->delete($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $Info_value = array(
                  'userid' => bizppurio_userid
                , 'userpw' => bizppurio_password
                , 'apikey' => bizppurio_apikey
                , 'senderkey' => bizppurio_senderkey
                , 'tpl_code' => 'bizp_2023111513513509144699519'
            );
            
            $message_emtitle = array();
            
            $message = array(
                  "고객명" => $NAME
                , "고객아이디" => $ID
                , "가입일" => date('Y-m-d H:i:s') // 등록날짜
            );
            
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
            
            gfn_Bizalimtalk_send($Info_value, $Body_value);

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '회원탈퇴가 완료되었습니다.';

            unset($_SESSION['MEMBER']);
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }


    /**
     * name :ufn_INISTPAY_INFO
     * comment : 이니시스에 이후 값을 쓰기위해서 세션저장
     */
    function ufn_INISTPAY_INFO() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $_SESSION['INIS']['SEQ'] = get_request_param('SEQ'); // 주문페이지 시퀀스
            $_SESSION['INIS']['PRODUCTS'] = get_request_param('PRODUCTS'); // 주문 작품, 상품 시퀀스 
            $_SESSION['INIS']['OPTIONS'] = get_request_param('OPTIONS'); // 옵션 시퀀스
            $_SESSION['INIS']['PAGE_TYPE'] = get_request_param('PAGE_TYPE'); // 페이지 타입
            $_SESSION['INIS']['TYPE'] = get_request_param('TYPE'); // 페이지 타입 값 10 : collection
            $_SESSION['INIS']['TOTAL_COUNT'] = get_request_param('TOTAL_COUNT'); // 토탈 개수
            $_SESSION['INIS']['TOTAL_PRICE'] = get_request_param('TOTAL_PRICE'); // 토탈금액

            // 주문자 정보 입력
            $_SESSION['INIS']['NAME'] = get_request_param('order_name'); // 주문자 - 이름
            $_SESSION['INIS']['MOBILE'] = get_request_param('order_tel'); // 주문자 - 연락처
            $_SESSION['INIS']['EMAIL'] = get_request_param('order_email'); // 주문자 - 이메일

            // 배송정보 입력
            $_SESSION['INIS']['DLVY_NAME'] = get_request_param('DLVY_NAME'); // 배송정보 - 이름
            $_SESSION['INIS']['DLVY_MOBILE'] = get_request_param('DLVY_MOBILE'); // 배송정보 - 연락처
            $_SESSION['INIS']['DLVY_EMAIL'] = get_request_param('DLVY_EMAIL'); // 배송정보 - 이메일 [필요시 나중에추가]
            $_SESSION['INIS']['DLVY_ADDRESS_ZIPCODE'] = get_request_param('DLVY_ADDRESS_ZIPCODE'); // 배송정보 - 주소 (우편번호)
            $_SESSION['INIS']['DLVY_ADDRESS'] = get_request_param('DLVY_ADDRESS'); // 배송정보 - 기본주소 
            $_SESSION['INIS']['DLVY_ADDRESSDETAIL'] = get_request_param('DLVY_ADDRESSDETAIL'); // 배송정보 - 상세주소
            $_SESSION['INIS']['DLVY_MESSAGE'] = get_request_param('DLVY_MESSAGE'); // 배송정보 - 배송메세지
            $_SESSION['INIS']['REAL_DLVY_PRICE'] = get_request_param('REAL_DLVY_PRICE'); // 배송정보 - 실배송비

            $_SESSION['INIS']['orderPayType'] = get_request_param('orderPayType');  // 결제 수단

            $agreeChk1 = get_request_param('agreeChk1'); // 이용약관
            $agreeChk2 = get_request_param('agreeChk2'); // 취급방침

            if ($agreeChk1 != "Y") {
                $agreeChk1 = "N";
            }

            if ($agreeChk2 != "Y") {
                $agreeChk2 = "N";
            }

            $_SESSION['INIS']['agreeChk1'] = $agreeChk1;
            $_SESSION['INIS']['agreeChk2'] = $agreeChk2;

            $arrRtn['code'] = 200;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    function ufn_Category1_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $PAGE = get_request_param('page_type');
            $SEQ = get_request_param('SEQ');

            $arrValue = array();
            $arrValue[':CATEGORY1_SEQ'] = $SEQ;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT D.CATEGORY3_SEQ
                      , D.TITLE AS CATEGORY3_NAME
                      , D.BADGE_CO
                      , D.SALE_YN
                      , FORMAT(D.OID_PRICE, '') AS OID_PRICE
                      , D.SALE_PERCENT
                      , FORMAT(D.PRICE, '') AS PRICE
                      , D.ORDER_NUMBER
                      , D.reg_date
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '1' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS MAIN_ATTACH_FILE_ID
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '4' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS HOVER_ATTACH_FILE_ID
                   FROM CATEGORY1 M, CATEGORY3 D
                 WHERE 1
                   AND M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                   AND M.MAIN_YN = 'Y'
                   AND D.MAIN_YN = 'Y'
                   AND D.INDEX_YN = 'Y'
                   AND M.PAGE_TYPE = :PAGE_TYPE
                   AND M.CATEGORY1_SEQ = :CATEGORY1_SEQ
                 ORDER BY ORDER_NUMBER DESC, reg_date DESC
                 LIMIT 0,7";

            $name_sql = "카테고리3 값 조회";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $arrRtn = $list;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :getFirst_Category2
     * comment : 첫번째 정렬 카테고리 값 조회
     */
    function ufn_Category2_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();
        
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $PAGE = get_request_param('page_type');
            $SEQ = get_request_param('SEQ');

            $arrValue = array();
            $arrValue[':CATEGORY1_SEQ'] = $SEQ;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT M.CATEGORY1_SEQ
                      , D.CATEGORY2_SEQ
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , D.ORDER_NUMBER AS SUB_ORDER
                      , M.reg_date
                   FROM CATEGORY1 M, CATEGORY2 D
                 WHERE 1
                   AND M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                   AND M.MAIN_YN = 'Y'
                   AND D.MAIN_YN = 'Y'
                   AND M.PAGE_TYPE = :PAGE_TYPE
                   AND M.CATEGORY1_SEQ = :CATEGORY1_SEQ
                 ORDER BY MAIN_ORDER DESC, reg_date DESC, SUB_ORDER DESC
                 LIMIT 1";

            $name_sql = "첫번째 정렬 카테고리 값 조회";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            $arrRtn = $data;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_PRODUCTINFO_List
     * comment : 상품 리스트 출력 
     */
    function ufn_PRODUCTINFO_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $PAGE_TYPE = get_request_param('page_type');
            $SEQ1 = get_request_param('SEQ1');
            $SEQ2 = get_request_param('SEQ2');
            $page = get_request_param('page');
            $limit = get_request_param('limit');

            $arrValue = array();
            $where = '';

            if ($SEQ1 == "NEW" || $SEQ1 == "SALE") {
                    $where .= " AND FIND_IN_SET(:TYPE_CD, D.TYPE_CD) > 0";
                    $arrValue[':TYPE_CD'] = $SEQ1;
            } else {
                if (!empty($SEQ1)) {
                    $arrValue[':CATEGORY1_SEQ'] = $SEQ1;
                    $where = " AND M.CATEGORY1_SEQ = :CATEGORY1_SEQ";
                }

                if (!empty($SEQ2)) {
                    $arrValue[':CATEGORY2_SEQ'] = $SEQ2;
                    $where = " AND M.CATEGORY2_SEQ = :CATEGORY2_SEQ";
                }
            }

            if (empty($page)) {
                $page = 1;
            }

            $where .= " AND M.PAGE_TYPE = :PAGE_TYPE";
            $arrValue[':PAGE_TYPE'] = $PAGE_TYPE;

            $offset = ($page - 1) * $limit;

            $sql = "
                 SELECT D.CATEGORY3_SEQ
                      , D.TITLE AS CATEGORY3_NAME
                      , D.BADGE_CO
                      , D.SALE_YN
                      , FORMAT(D.OID_PRICE, '') AS OID_PRICE
                      , D.SALE_PERCENT
                      , FORMAT(D.PRICE, '') AS PRICE
                      , D.ORDER_NUMBER
                      , D.reg_date
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '1' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS MAIN_ATTACH_FILE_ID
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '4' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS HOVER_ATTACH_FILE_ID
                   FROM CATEGORY2 M, CATEGORY3 D
                 WHERE 1
                   AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ
                   AND M.MAIN_YN = 'Y'
                   AND D.MAIN_YN = 'Y'
                   {$where}
                 ORDER BY ORDER_NUMBER DESC, reg_date DESC
                 LIMIT {$offset}, {$limit}";

            $name_sql = "카테고리3 값 조회";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (empty($list)) {
                gfn_isValidation(999, "", "데이터없음");
            }

            $arrRtn = $list;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_PRODUCTINFO_List
     * comment : 상품 리스트 출력 검색용
     */
    function ufn_PRODUCTINFO_ST_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $PAGE_TYPE = get_request_param('page_type');
            $VAL = get_request_param('search_text');
            $page = get_request_param('page');
            $limit = get_request_param('limit');

            $arrValue = array();
            $where = '';

            if (empty($page)) {
                $page = 1;
            }

            $where .= " AND M.PAGE_TYPE = :PAGE_TYPE";
            $arrValue[':PAGE_TYPE'] = $PAGE_TYPE;

            $VAL = trim($VAL);

            $arrValue[':VAL'] = $VAL;

            $offset = ($page - 1) * $limit;

            $sql = "
                 SELECT D.CATEGORY3_SEQ
                      , D.TITLE AS CATEGORY3_NAME
                      , D.BADGE_CO
                      , D.SALE_YN
                      , FORMAT(D.OID_PRICE, '') AS OID_PRICE
                      , D.SALE_PERCENT
                      , FORMAT(D.PRICE, '') AS PRICE
                      , D.ORDER_NUMBER
                      , D.reg_date
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '1' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS MAIN_ATTACH_FILE_ID
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '4' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS HOVER_ATTACH_FILE_ID
                   FROM CATEGORY2 M, CATEGORY3 D
                 WHERE 1
                   AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ
                   AND M.MAIN_YN = 'Y'
                   AND D.MAIN_YN = 'Y'
                   AND (
                     TRIM(D.TITLE) LIKE CONCAT('%', :VAL, '%')
                     OR FIND_IN_SET(:VAL, REPLACE(SEARCH_TEXT, ' ', ''))
                     OR EXISTS ( SELECT 1
                                   FROM CATEGORY1
                                  WHERE TRIM(CATEGORY1.TITLE) LIKE CONCAT('%', :VAL, '%')
                                    AND CATEGORY1.CATEGORY1_SEQ = M.CATEGORY1_SEQ)
                     OR EXISTS (SELECT 1
                                  FROM CATEGORY2
                                 WHERE TRIM(CATEGORY2.TITLE) LIKE CONCAT('%', :VAL, '%')
                                   AND CATEGORY2.CATEGORY1_SEQ = M.CATEGORY1_SEQ
                                   AND CATEGORY2.CATEGORY2_SEQ = M.CATEGORY2_SEQ))
                   {$where}
                 ORDER BY ORDER_NUMBER DESC, reg_date DESC
                 LIMIT {$offset}, {$limit}";

            $name_sql = "카테고리3 값 조회";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (empty($list)) {
                gfn_isValidation(999, "", "데이터없음");
            }

            $arrRtn = $list;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_NAVINFO_List
     * comment : 네비 상품 리스트 출력 
     */
    function ufn_NAVINFO_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $PAGE = get_request_param('page_type');
            $SEQ = get_request_param('SEQ');
            $TYPE = get_request_param('TYPE');

            $arrValue = array();
            $arrValue[':CATEGORY1_SEQ'] = $SEQ;

            $sql = "
                 SELECT M.CATEGORY1_SEQ
                      , M.CATEGORY2_SEQ
                      , M.TITLE AS MAIN_TITLE
                      , D.CATEGORY3_SEQ
                      , D.BADGE_CO
                      , D.BRAND
                      , D.TITLE AS CATEGORY3_NAME
                      , (SELECT TITLE FROM CATEGORY1 A WHERE A.CATEGORY1_SEQ = M.CATEGORY1_SEQ) AS CATEGORY1_NAME
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , D.ORDER_NUMBER AS SUB_ORDER
                      , D.SALE_YN
                      , FORMAT(D.OID_PRICE, '') AS OID_PRICE
                      , D.SALE_PERCENT
                      , FORMAT(PRICE, '') AS PRICE
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '1' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS MAIN_ATTACH_FILE_ID
                      , M.reg_date
                   FROM CATEGORY2 M, CATEGORY3 D
                  WHERE 1
                    AND M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                    AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ
                    AND M.MAIN_YN = 'Y'
                    AND D.MAIN_YN = 'Y'
                    AND M.PAGE_TYPE = '{$PAGE}'
                    AND D.TYPE_CD LIKE '%{$TYPE}%'
                    AND M.CATEGORY1_SEQ = :CATEGORY1_SEQ
                  ORDER BY MAIN_ORDER DESC, SUB_ORDER DESC
                  LIMIT 0, 3";

            $name_sql = "제품 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue , $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $arrRtn = $list;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    
    /**
     * name :ufn_SPACE_List
     * comment : SPACE 리스트
     */
    function ufn_SPACE_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $TYPE_CD = get_request_param('TYPE_CD');

            $arrValue = array();
            $where = '';
    
            if (!empty($TYPE_CD)) { // 구분
                $where .= " AND TYPE_CD = :TYPE_CD";
                $arrValue[':TYPE_CD'] = $TYPE_CD;
            }

            $sql = "
                 SELECT SPACE_SEQ
                      , TITLE
                      , DATE_TEXT
                      , MOBILE
                      , EMAIL
                      , ATTACH_FILE_ID
                   FROM SPACE 
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = 'home'
                    {$where}
                  ORDER BY ORDER_NUMBER DESC";

            $name_sql = "SPACE 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $html = "";
            $homeFolder = homeFoldName;

            if (!empty($list)) {
                $count = 1;

                foreach ($list as $data) {
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_DATE_TEXT = _check_var($data['DATE_TEXT']); // 기간
                    $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                    $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    $img_html = "";
                    $li_html = "";
                    $li_html2 = "";

                    if (!empty($_db_MOBILE)) {
                        $li_html = '<li class="separator">|</li>';
                    }

                    if (!empty($_db_EMAIL)) {
                        $li_html2 = '<li class="separator">|</li>';
                    }

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);
            
                        if (!empty($file_list)) {
                            foreach ($file_list as $list2) {
                                $_db_attach_file_temp_name = _check_var($list2['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_real_name = _check_var($list2['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                                $_db_attach_file_path = _check_var($list2['ATTACH_FILE_PATH']); // 경로 
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;

                                $img_html .=  <<<HTML
                                                <div class="swiper-slide">
                                                    <figure>
                                                    <img src="{$path_File}" alt="">
                                                    </figure>
                                                </div>
                                                HTML;
                            }
                        }
                    }

                    $html .= <<<HTML
                                    <div class="spac__s2_contents_wrap spac__s2_contents_wrap{$count}">
                                        <div class="spac__s2_slide spac__s2_slide{$count} swiper">
                                            <div class="swiper-wrapper">
                                                {$img_html}
                                            </div>
                                            <div class="spac__s2_nav_container">
                                                <button type="button" class="spac__s2_nav{$count}--prev spac__s2_nav spac__s2_nav--prev">
                                                    <img src="{$homeFolder}/img/slide_prev.svg" alt="">
                                                </button>
                                                <button type="button" class="spac__s2_nav{$count}--next spac__s2_nav spac__s2_nav--next">
                                                    <img src="{$homeFolder}/img/slide_prev.svg" alt="">
                                                </button>
                                            </div>
                                        </div>
                                        <div class="spac__s2_cap">
                                            <p class="title">{$_db_TITLE}</p>
                                            <ul class="info_wrap">
                                                <li>{$_db_DATE_TEXT}</li>
                                                {$li_html}
                                                <li><a href="tel:{$_db_MOBILE}">{$_db_MOBILE}</a></li>
                                                {$li_html2}
                                                <li><a href="mailto:{$_db_EMAIL}">{$_db_EMAIL}</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                HTML;
                    $count++;
                }
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $html;
        }
    }

    /**
     * name :ufn_SPACE_PLAN_FILE
     * comment : SPACE 도면
     */
    function ufn_SPACE_PLAN_FILE() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'file' => ''
        );

        try {
            $TYPE_CD = get_request_param('TYPE_CD');

            $ATTACH_FILE_ID = "ATTACH_SPCPALN";

            $GROUP = gfn_getZcmcommonVal("COL011", $TYPE_CD , "TH1_THEM_CD");

            $file_list = gfn_file_upload("S", "", $ATTACH_FILE_ID, $GROUP);

            $MAIN_ATTACH_FILE_ID = "";

            if (!empty($file_list)) {
                foreach ($file_list as $data) {
                    $MAIN_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                }
            }

            if (!empty($MAIN_ATTACH_FILE_ID)) {
                $arrRtn['code'] = 200;
                $arrRtn['file'] = $MAIN_ATTACH_FILE_ID;
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_RECRUIT_List
     * comment : 업종 리스트
     */
    function ufn_RECRUIT_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $CATEGORY1_SEQ = get_request_param('SEQ');

            $sql = "
                 SELECT CATEGORY2_SEQ
                      , TITLE
                      , SUB_TITLE
                      , CONTENT_TEXT
                   FROM CATEGORY2
                  WHERE CATEGORY1_SEQ = :CATEGORY1_SEQ";

            $name_sql = "업종 상세 리스트";
            $clefResult = $mysqldb->select($sql, [':CATEGORY1_SEQ' => $CATEGORY1_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $arrRtn = $list;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_RECRUIT_DETAIL_List
     * comment : 세부업종 상세정보
     */
    function ufn_RECRUIT_DETAIL_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $CATEGORY2_SEQ = get_request_param('SEQ');

            $sql = "
                 SELECT CATEGORY2_SEQ
                      , CONTENT_TEXT
                   FROM CATEGORY2
                  WHERE CATEGORY2_SEQ = :CATEGORY2_SEQ";

            $name_sql = "업종 상세 정보";
            $clefResult = $mysqldb->get($sql, [':CATEGORY2_SEQ' => $CATEGORY2_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $arrRtn = $list;
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }
 ?>