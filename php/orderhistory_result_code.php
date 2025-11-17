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
        global $DELIVERY_PRICE;
        global $DELIVERY_IF_PRICE;
        global $DELIVERY;

        $PURCHASE_SEQ = get_request_param('SEQ', 'GET');
        $NAME = get_request_param('NAME', 'GET');

        $DELIVERY_PRICE = gfn_getZcmcommonVal("COL010", "PRICE", "TH1_THEM_CD"); // 배송비
        $DELIVERY_IF_PRICE = gfn_getZcmcommonVal("COL010", "IFPRICE", "TH1_THEM_CD"); // 조건 금액
        $DELIVERY = "무료";

        $login_chk = false; // 로그인확인

        $where = "";

        $arrValue = array();
        $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

        if (isset($_SESSION['MEMBER'])) {
            if (!empty($_SESSION['MEMBER'])) {
                $arrValue[':ID'] = $_SESSION['MEMBER']['ID'];
                $where .= " AND M.ID = :ID";

                $login_chk = true;
            }
        } else {
            $where .= " AND M.NAME = :NAME";
            $arrValue[':NAME'] = $NAME;
        }

        $sql = "
              SELECT M.PURCHASE_SEQ
                   , M.TYPE_CD
                   , M.STATE_CD
                   , M.INICIS_SEQ
                   , (SELECT CONCAT(IF(COUNT(*) > 1, 
                                     CONCAT(MIN(CATEGORY3_NAME), ' 외 '), 
                                     GROUP_CONCAT(DISTINCT CATEGORY3_NAME SEPARATOR ', ')))
                       FROM PURCHASE_PRODUCT
                      WHERE M.PURCHASE_SEQ = PURCHASE_SEQ) AS M_CATEGORY3_NAME
                   , DATE_FORMAT(M.reg_date, '%Y. %m. %d') AS reg_date_nm
                   , ZCM_COM_NM('COL003', M.TYPE_CD) AS TYPE_CD_NM
                   , ZCM_COM_NM('COL005', M.STATE_CD) AS STATE_CD_NM
                   , M.ID
                   , IF(M.ID IS NOT NULL AND M.ID <> '', '회원', '비회원') AS STATE_TYPE_NM
                   , M.TOTAL_COUNT
                   , TOTAL_PRICE
                   , FORMAT(M.TOTAL_PRICE, 0) AS TOTAL_PRICE_TEXT
                   , (SELECT IFNULL(COUNT(*), 0)
                            FROM PURCHASE_ORDER B, PURCHASE_OPTION C
                           WHERE 1
                             AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                             AND B.PURCHASE_SEQ = M.PURCHASE_SEQ
                             AND C.STATE_CD NOT IN ('42', '52')) AS TOTAL_NOW_COUNT
                   , (SELECT IFNULL(SUM(C.QUANTITY*(C.PRICE + E.PRICE)), 0)
                        FROM PURCHASE_ORDER B, PURCHASE_OPTION C, PURCHASE_PRODUCT E
                       WHERE 1
                         AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                         AND B.PURCHASE_SEQ = E.PURCHASE_SEQ
                         AND B.PURCHASE_SEQ = M.PURCHASE_SEQ
                         AND E.CATEGORY3_SEQ = C.CATEGORY3_SEQ
                         AND C.STATE_CD NOT IN ('42', '52')) AS TOTAL_NOW_PRICE
                   , FORMAT((SELECT IFNULL(SUM(C.QUANTITY*(C.PRICE + E.PRICE)), 0)
                               FROM PURCHASE_ORDER B, PURCHASE_OPTION C, PURCHASE_PRODUCT E
                              WHERE 1
                                AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                                AND B.PURCHASE_SEQ = E.PURCHASE_SEQ
                                AND B.PURCHASE_SEQ = M.PURCHASE_SEQ
                                AND E.CATEGORY3_SEQ = C.CATEGORY3_SEQ
                                AND C.STATE_CD NOT IN ('42', '52')), 0) AS TOTAL_NOW_PRICE_TEXT
                   , M.NAME
                   , M.MOBILE
                   , M.EMAIL
                   , M.PRICE
                   , M.DLVY_NAME
                   , M.DLVY_MOBILE
                   , M.DLVY_EMAIL
                   , M.DLVY_ADDRESS_ZIPCODE
                   , M.DLVY_ADDRESS
                   , M.DLVY_ADDRESSDETAIL
                   , M.DLVY_MESSAGE
                   , M.DLVY_PRICE
                   , M.NO_BANK_CD
                   , ZCM_COM_NM('AD007', M.NO_BANK_CD) AS NO_BANK_CD_NM
                   , M.NO_BANK_ACCOUNT
                   , M.NO_BANK_NAME
                   , M.NO_BANK_DEPOSITOR
                   , DATE_FORMAT(M.NO_BANK_DATE, '%Y. %m. %d') AS NO_BANK_DATE_NM
                   , NO_BANK_CASH_YN
                   , CASH_YN
                   , CASH_MOBILE
                   , CASH_EMAIL
                   , CASH_BUSINESS
                   , TAX_BILL_YN
                   , TAX_BILL_EMAIL
                   , NOTE
                   , ATTACH_FILE_ID
                FROM PURCHASE_ORDER M
               WHERE PURCHASE_SEQ = :PURCHASE_SEQ
                 {$where}";

        $name_sql = "주문내역";
        $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove("입력하신 주문자명, 주문번호에 일치하는 주문이 없습니다.");
        }
        
        $TYPE_CD = _check_var($data['TYPE_CD']); // 결제방식
        $STATE_CD = _check_var($data['STATE_CD']); // 주문상태
        $INICIS_SEQ = _check_var($data['INICIS_SEQ']); // 이니시스 시퀀스
        $REAL_PRCIE = _check_var($data['PRICE']); // 제품만비용

        //주문자정보
        $_db_NAME = _check_var($data['NAME']); // 주문자
        $_db_MOBILE = _check_var($data['MOBILE']); // 주문자 - 연락처
        $_db_EMAIL = _check_var($data['EMAIL']); // 주문자 - 이메일

        // 주문정보
        $_db_PURCHASE_SEQ = _check_var($data['PURCHASE_SEQ']); // 주문번호
        $_db_M_CATEGORY3_NAME = _check_var($data['M_CATEGORY3_NAME']); // 토탈명
        $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 결제수단
        $_db_STATE_CD_NM = _check_var($data['STATE_CD_NM']); // 주문상태
        $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 주문일자
        $_db_STATE_TYPE_NM = _check_var($data['STATE_TYPE_NM']); // 회원, 비회원
        $_db_TOTAL_COUNT = _check_var($data['TOTAL_COUNT']); // 총 주문 개수
        $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 총 주문 금액
        $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 총 주문 금액
        $_db_TOTAL_NOW_COUNT = _check_var($data['TOTAL_NOW_COUNT']); // 취소, 환불한 현 개수
        $_db_TOTAL_NOW_PRICE = _check_var($data['TOTAL_NOW_PRICE']); // 취소, 환불한 현금액
        $_db_TOTAL_NOW_PRICE_TEXT = _check_var($data['TOTAL_NOW_PRICE_TEXT']); // 취소, 환불한 현금액

        //배송정보
        $_db_DLVY_NAME = _check_var($data['DLVY_NAME']); // 배송정보 - 이름
        $_db_DLVY_MOBILE = _check_var($data['DLVY_MOBILE']); // 배송정보 - 연락처
        $_db_DLVY_ADDRESS_ZIPCODE = _check_var($data['DLVY_ADDRESS_ZIPCODE']); // 배송정보 - 우편번호
        $_db_DLVY_ADDRESS = _check_var($data['DLVY_ADDRESS']); // 배송정보 - 주소
        $_db_DLVY_ADDRESSDETAIL = _check_var($data['DLVY_ADDRESSDETAIL']); // 배송정보 - 상세주소
        $_db_DLVY_MESSAGE = _check_var($data['DLVY_MESSAGE']); // 배송정보 - 배송메세지
        $REAL_DLVY_PRICE = _check_var($data['DLVY_PRICE']); // 배송비

        if ($_db_TOTAL_PRICE != $_db_TOTAL_NOW_PRICE && $_db_TOTAL_NOW_PRICE != 0) {
            $_db_TOTAL_NOW_PRICE = (string)((int)$_db_TOTAL_NOW_PRICE + (int)$REAL_DLVY_PRICE);

            $_db_TOTAL_NOW_PRICE_TEXT = number_format($_db_TOTAL_NOW_PRICE);
        }

        $frist_del = gfn_getDELIVERY($REAL_PRCIE);

        if ($frist_del > 0) {
            $_db_TOTAL_PRICE = $REAL_PRCIE +  $frist_del;
            $_db_TOTAL_PRICE_TEXT = number_format($_db_TOTAL_PRICE);
            $DELIVERY = number_format($frist_del). '원';

            $REAL_DLVY_PRICE = $frist_del;
        } else {
            $DELIVERY = "무료";
        }

        // 무통장
        $_db_NO_BANK_CD = _check_var($data['NO_BANK_CD']); // 무통장 은행코드 AD007
        $_db_NO_BANK_CD_NM = _check_var($data['NO_BANK_CD_NM']); // 무통장 은행코드 AD007
        $_db_NO_BANK_NAME = _check_var($data['NO_BANK_NAME']); // 무통장 - 예금주
        $_db_NO_BANK_ACCOUNT = _check_var($data['NO_BANK_ACCOUNT']); // 무통장 - 입금계좌
        $_db_NO_BANK_DEPOSITOR = _check_var($data['NO_BANK_DEPOSITOR']); // 무통장 - 입금자
        $_db_NO_BANK_DATE_NM = _check_var($data['NO_BANK_DATE_NM']); // 무통장 - 입금 기한일
        $_db_NO_BANK_CASH_YN = _check_var($data['NO_BANK_CASH_YN']); // 무통장- 현금영수증 발행 요청 여부

        if (!empty($_db_DLVY_MOBILE)) {
            $_db_DLVY_MOBILE = formatPhoneNumber($_db_DLVY_MOBILE);
        }

        // 현금영수증
        $_db_CASH_YN = _check_var($data['CASH_YN']); // 현금영수증 - 여부
        $_db_CASH_MOBILE = _check_var($data['CASH_MOBILE']); // 현금영수증 - 연락처
        $_db_CASH_EMAIL = _check_var($data['CASH_EMAIL']); // 현금영수증 - 이메일
        $_db_CASH_BUSINESS = _check_var($data['CASH_BUSINESS']); // 현금영수증 - 사업자번호

        // 세금계산서
        $_db_TAX_BILL_YN = _check_var($data['TAX_BILL_YN']); // 세금계산서 - 여부
        $_db_TAX_BILL_EMAIL = _check_var($data['TAX_BILL_EMAIL']); // 세금계산서 - 이메일

        // 이니시스 결제 정보
        $INIS_CARD_P_FN_NM = ""; // 결제카드사
        $INIS_CARD_NUM = ""; // 카드번호

        if (!empty($INICIS_SEQ)) {
            if ($TYPE_CD == 'CCARD') { // 신용카드
                $INIS_CARD_P_FN_NM = gfn_getIinisVal($INICIS_SEQ, "CARD_P_FN_NM");
                $INIS_CARD_NUM = gfn_getIinisVal($INICIS_SEQ, "CARD_NUM");
            } else  if ($TYPE_CD == 'RTBT') { // 계좌이체

            }
        }
        

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

            $PAGE = PAGE;

            $PURCHASE_SEQ = get_request_param('SEQ', 'GET');

            $arrValue = array();
            $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;
            $arrValue[':PAGE_TYPE'] = $PAGE;
    
            $table = 'PURCHASE_PRODUCT'; // 작품 테이블
            $table_OP = 'PURCHASE_OPTION'; // 옵션 관리자 테이블
    
            $sql = "
                 SELECT M.PURCHASE_SEQ
                      , M.CATEGORY3_SEQ
                      , M.ATTACH_FILE_ID
                      , M.CATEGORY3_NAME
                      , D.OPTION_NAME
                      , M.FRAME
                      , M.PRICE AS MPRICE
                      , D.QUANTITY
                      , D.PRICE
                      , D.OPTION_SEQ
                      , A.STATE_CD
                      , D.STATE_CD AS OP_STATE_CD
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
                            AND B.PURCHASE_SEQ = A.PURCHASE_SEQ
                            AND E.CATEGORY3_SEQ = C.CATEGORY3_SEQ) AS TOTAL_PRICE
                      , FORMAT((SELECT IFNULL(SUM(C.QUANTITY*(C.PRICE + E.PRICE)), 0)
                                  FROM PURCHASE_ORDER B, PURCHASE_OPTION C, PURCHASE_PRODUCT E
                                 WHERE 1
                                   AND B.PURCHASE_SEQ = C.PURCHASE_SEQ
                                   AND B.PURCHASE_SEQ = E.PURCHASE_SEQ
                                   AND B.PURCHASE_SEQ = A.PURCHASE_SEQ
                                   AND E.CATEGORY3_SEQ = C.CATEGORY3_SEQ), 0) AS TOTAL_PRICE_TEXT
                   FROM PURCHASE_ORDER A, {$table} M, {$table_OP} D
                  WHERE A.PURCHASE_SEQ = M.PURCHASE_SEQ
                    AND A.PURCHASE_SEQ = D.PURCHASE_SEQ
                    AND M.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                    AND A.PURCHASE_SEQ = :PURCHASE_SEQ
                    AND A.PAGE_TYPE = :PAGE_TYPE
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
                    $_db_MPRICE = _check_var($data['MPRICE']); // 금액
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

                    /*if ($_db_OP_STATE_CD == "42" || $_db_OP_STATE_CD == "52") { // 42 : 주문취소
                        $_db_QUANTITY = 0;
                        $_db_OPTION_PRICE = 0;
                        $_db_OPTION_PRICE_TEXT = 0;
                    }*/

                    $url = shopFoldName. "/product/detail.php?seq=". $_db_CATEGORY3_SEQ;

                    echo <<<LI
                                <li class="tbody">
                                    <ul class="table_column">
                                        <li class="td_chk" data-totalcount="{$_db_TOTAL_COUNT}" data-pk="{$_db_PURCHASE_SEQ}" data-seq="{$_db_CATEGORY3_SEQ}" data-code="{$_db_OPTION_SEQ}" data-state="{$_db_OP_STATE_CD}" data-val="{$_db_OPTION_PRICE}" data-count="{$_db_QUANTITY}" data-mval="{$_db_MPRICE}" data-mval="{$_db_MPRICE}">
                                            <input type="checkbox" id="prdChk{$count}" name="prdChk" $checked>
                                            <label for="prdChk{$count}"></label>
                                        </li>
                                        <li class="td_img">
                                            <a href="{$url}" class="prdThumbnail"><img src="{$path_File}" alt="상품 이미지"></a>
                                        </li>
                                        <li class="td_prd_info">
                                            <a href="{$url}" class="td_name"><p class="prdName">{$_db_CATEGORY3_NAME}</p></a>
                                            <div class="td_row">
                                            <div class="td_option"><div class="prd_option">{$_db_OPTION_NAME}</div></div>
                                            <div class="td_count">수량 <span class="option_count">{$_db_QUANTITY}</span>개</div>
                                            </div>
                                        </li>
                                        <li class="td_price"><span class="prdPrice">{$_db_OPTION_PRICE_TEXT}</span></li>
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

?>