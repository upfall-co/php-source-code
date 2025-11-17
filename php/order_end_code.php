<?php
/**
 * 파일명 : inquiry_code.php
 * 내용 : 1:1 문의 
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
        $PURCHASE_SEQ = get_request_param('SEQ', 'GET');

        $login_chk = false;

        if (isset($_SESSION['MEMBER'])) {
            if (!empty($_SESSION['MEMBER'])) {
                $login_chk = true;
            }
        }

        $arrValue = array();
        $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

        $sql = "
             SELECT ZCM_COM_NM('COL003', TYPE_CD) AS TYPE_CD_NM
                  , TYPE_CD
                  , NO_BANK_CASH_YN
                  , CASH_YN
                  , NO_BANK_NAME
                  , (SELECT CONCAT(IF(COUNT(*) > 1, 
                                     CONCAT(MIN(D.CATEGORY3_NAME), ' 외 '), 
                                     GROUP_CONCAT(DISTINCT D.CATEGORY3_NAME SEPARATOR ', ')))
                       FROM PURCHASE_PRODUCT D, PURCHASE_OPTION C
                      WHERE M.PURCHASE_SEQ = D.PURCHASE_SEQ
                        AND D.PURCHASE_SEQ = C.PURCHASE_SEQ
                        AND D.CATEGORY3_SEQ = C.CATEGORY3_SEQ) AS CATEGORY3_NAME
                  , ZCM_COM_NM('AD007', NO_BANK_CD) AS NO_BANK_CD_NM
                  , FORMAT(M.TOTAL_PRICE, 0) AS TOTAL_PRICE_TEXT
                  , NO_BANK_ACCOUNT
                  , PURCHASE_SEQ
                  , DATE_FORMAT(NO_BANK_DATE, '%Y. %m. %d') AS NO_BANK_DATE_NM
               FROM PURCHASE_ORDER M
              WHERE PURCHASE_SEQ = :PURCHASE_SEQ";

        $name_sql = "주문내역";
        $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove("잘못된 접근입니다.");
        } 

        $TYPE_CD = _check_var($data['TYPE_CD']); // 결제수단
        $TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 결제수단
        $CASH_YN = _check_var($data['CASH_YN']); // 현금영수증 발행여부
        $NO_BANK_CASH_YN = _check_var($data['NO_BANK_CASH_YN']); // 현금영수증 발행여부
        $NO_BANK_NAME = _check_var($data['NO_BANK_NAME']); // 예금주
        $CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 주문작품
        $NO_BANK_CD_NM = _check_var($data['NO_BANK_CD_NM']); // 입금은행
        $TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 결제요청금액
        $NO_BANK_ACCOUNT = _check_var($data['NO_BANK_ACCOUNT']); // 입금계좌
        $PURCHASE_SEQ = _check_var($data['PURCHASE_SEQ']); // 주문번호
        $NO_BANK_DATE_NM = _check_var($data['NO_BANK_DATE_NM']); // 입금기한

        $TYPE_NM = "";
        $CASH_NM = "";

        if ($TYPE_CD != "CCARD") {
            if ($NO_BANK_CASH_YN == "Y") {
                $CASH_NM = " (현금영수증 요청)";
            } else {
                if ($CASH_YN == "Y") {
                    $CASH_NM = " (현금영수증 발행)";
                } else {
                    $CASH_NM = " (현금영수증 미발행)";
                }
            }
        }

        $TYPE_NM = $TYPE_CD_NM. $CASH_NM;

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

?>