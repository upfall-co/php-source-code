<?php
/**
 * 파일명 : orderHistory_details_code.php
 * 내용 : 주문상세정보 
 * 최초작성날짜 : 2023/08/28
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/28    V1.0
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

        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET'); // 페이지타입
        $mode = get_request_param('mode', 'GET'); // 모드
        $M_start_date = get_request_param('start_date','GET'); // 날짜
        $M_end_date = get_request_param('end_date','GET'); // 날짜 
        $M_TYPE_CD = get_request_param('TYPE_CD','GET'); // 결제수단
        $M_STATE_CD = get_request_param('STATE_CD','GET'); // 주문상태
        $M_PUR_NUM = get_request_param('PUR_NUM','GET'); // 주문번호
        $M_DLVY_NAME = get_request_param('DLVY_NAME','GET'); // 주문자

        $M_CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET'); // 작가
        $M_CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ', 'GET'); // 시리즈
        $M_CATEGORY3_SEQ = get_request_param('CATEGORY3_SEQ', 'GET'); // 작품

        $M_CATEGORY1_NAME = get_request_param('CATEGORY1_NAME', 'GET'); // 작가명
        $M_CATEGORY2_NAME = get_request_param('CATEGORY2_NAME', 'GET'); // 시리즈명
        $M_CATEGORY3_NAME = get_request_param('CATEGORY3_NAME','GET'); // 작품명

        $title_name = "주문내역";
        $limit = 10;

        $_db_ATTACH_FILE_ID = "";

        $checked = '';
        $checked2 = '';
        $checked3 = '';

        $PURCHASE_SEQ = get_request_param('seq', 'GET');

        $arrValue = array();
        $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;

        $sql = "
             SELECT M.TYPE_CD
                  , M.STATE_CD
                  , M.INICIS_SEQ
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
        
        $TYPE_CD = _check_var($data['TYPE_CD']); // 결제방식
        $STATE_CD = _check_var($data['STATE_CD']); // 주문상태
        $INICIS_SEQ = _check_var($data['INICIS_SEQ']); // 이니시스 시퀀스
        $REAL_PRCIE = _check_var($data['PRICE']); // 제품만비용

        // 주문정보
        $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 결제수단
        $_db_STATE_CD_NM = _check_var($data['STATE_CD_NM']); // 주문상태
        $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 주문일자
        $_db_ID = _check_var($data['ID']); // ID [회원일경우]
        $_db_STATE_TYPE_NM = _check_var($data['STATE_TYPE_NM']); // 회원, 비회원
        $_db_TOTAL_COUNT = _check_var($data['TOTAL_COUNT']); // 총 주문 개수
        $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 총 주문 금액
        $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 총 주문 금액
        $_db_TOTAL_NOW_COUNT = _check_var($data['TOTAL_NOW_COUNT']); // 취소, 환불한 현 개수
        $_db_TOTAL_NOW_PRICE = _check_var($data['TOTAL_NOW_PRICE']); // 취소, 환불한 현금액
        $_db_TOTAL_NOW_PRICE_TEXT = _check_var($data['TOTAL_NOW_PRICE_TEXT']); // 취소, 환불한 현금액

        //주문자정보
        $_db_NAME = _check_var($data['NAME']); // 주문자
        $_db_MOBILE = _check_var($data['MOBILE']); // 주문자 - 연락처
        $_db_EMAIL = _check_var($data['EMAIL']); // 주문자 - 이메일

        if (!empty($_db_MOBILE)) {
            $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
        }

        //배송정보
        $_db_DLVY_NAME = _check_var($data['DLVY_NAME']); // 배송정보 - 이름
        $_db_DLVY_MOBILE = _check_var($data['DLVY_MOBILE']); // 배송정보 - 연락처
        $_db_DLVY_ADDRESS_ZIPCODE = _check_var($data['DLVY_ADDRESS_ZIPCODE']); // 배송정보 - 우편번호
        $_db_DLVY_ADDRESS = _check_var($data['DLVY_ADDRESS']); // 배송정보 - 주소
        $_db_DLVY_ADDRESSDETAIL = _check_var($data['DLVY_ADDRESSDETAIL']); // 배송정보 - 상세주소
        $_db_DLVY_MESSAGE = _check_var($data['DLVY_MESSAGE']); // 배송정보 - 배송메세지
        $REAL_DLVY_PRICE = _check_var($data['DLVY_PRICE']); // 배송비

        if ($_db_TOTAL_PRICE != $_db_TOTAL_NOW_PRICE && $_db_TOTAL_NOW_PRICE != 0) {
            $_db_TOTAL_NOW_PRICE =  (string)((int)$_db_TOTAL_NOW_PRICE + (int)$REAL_DLVY_PRICE);
            $_db_TOTAL_NOW_PRICE_TEXT = number_format($_db_TOTAL_NOW_PRICE);
        }

        if (!empty($_db_DLVY_MOBILE)) {
            $_db_DLVY_MOBILE = formatPhoneNumber($_db_DLVY_MOBILE);
        }

        // 무통장
        $_db_NO_BANK_CD = _check_var($data['NO_BANK_CD']); // 무통장 은행코드 AD007
        $_db_NO_BANK_NAME = _check_var($data['NO_BANK_NAME']); // 무통장 - 예금주
        $_db_NO_BANK_ACCOUNT = _check_var($data['NO_BANK_ACCOUNT']); // 무통장 - 입금계좌
        $_db_NO_BANK_DEPOSITOR = _check_var($data['NO_BANK_DEPOSITOR']); // 무통장 - 입금자
        $_db_NO_BANK_DATE_NM = _check_var($data['NO_BANK_DATE_NM']); // 무통장 - 입금 기한일
        $_db_NO_BANK_CASH_YN = _check_var($data['NO_BANK_CASH_YN']); // 무통장- 현금영수증 발행 요청 여부

        if ($_db_NO_BANK_CASH_YN == "Y") {
            $checked = 'checked';
        }

        // 현금영수증
        $_db_CASH_YN = _check_var($data['CASH_YN']); // 현금영수증 - 여부
        $_db_CASH_MOBILE = _check_var($data['CASH_MOBILE']); // 현금영수증 - 연락처
        $_db_CASH_EMAIL = _check_var($data['CASH_EMAIL']); // 현금영수증 - 이메일
        $_db_CASH_BUSINESS = _check_var($data['CASH_BUSINESS']); // 현금영수증 - 사업자번호

        if ($_db_CASH_YN == "Y") {
            $checked2 = 'checked';
        }

        if (!empty($_db_CASH_MOBILE)) {
            $_db_CASH_MOBILE = formatPhoneNumber($_db_CASH_MOBILE);
        }

        // 세금계산서
        $_db_TAX_BILL_YN = _check_var($data['TAX_BILL_YN']); // 세금계산서 - 여부
        $_db_TAX_BILL_EMAIL = _check_var($data['TAX_BILL_EMAIL']); // 세금계산서 - 이메일

        if ($_db_TAX_BILL_YN == "Y") {
            $checked3 = 'checked';
        }

        $_db_NOTE = _check_var($data['NOTE']); // 메모
        $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

        $file_html = "";

        if (!empty($_db_ATTACH_FILE_ID)) {
            $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

            if (!empty($file_list)) { // 메인 이미지
                foreach ($file_list as $list) {
                    $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                    $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                    $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                    $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;

                    $file_html .= <<<DIV
                                        <div style = 'margin: 5px 0 0 0;'>
                                            <img style='height:16px;' src='/adm/img/paper-clip.svg' alt='paper clip'>
                                            <a style='display:inline-block' href="{$path_File}" download="{$_db_attach_file_real_name}">{$_db_attach_file_real_name}</a>
                                        </div>
                                    DIV;
                }
            }
        }

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
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

    function getOrderChkList() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $PURCHASE_SEQ = get_request_param('seq', 'GET');

            $page_type = get_request_param('page_type', 'GET'); // 페이지타입

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
                    $_db_INVOICE_NUMBER = _check_var($data['INVOICE_NUMBER']); // 송장번호
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

                    /*if ($_db_OP_STATE_CD == "42") { // 42 : 주문취소
                        $_db_QUANTITY = 0;
                        $_db_OPTION_PRICE = 0;
                        $_db_OPTION_PRICE_TEXT = 0;
                    }*/

                    $FRAME_html = "";
                    $NVOICE_html = "";

                    if ($page_type == PAGE1) {
                        $FRAME_html = "<td>".$_db_FRAME."</td>";
                    } else if ($page_type == PAGE2) {
                        $NVOICE_html = '<td><input type="text" id="INVOICE'. $count. '" name="INVOICE" value="'. $_db_INVOICE_NUMBER .'"></td>';
                    }

                    echo <<<TR
                            <tr>
                                <td data-totalcount="{$_db_TOTAL_COUNT}" data-pk="{$_db_PURCHASE_SEQ}" data-seq="{$_db_CATEGORY3_SEQ}" data-code="{$_db_OPTION_SEQ}" data-state="{$_db_OP_STATE_CD}" data-val="{$_db_OPTION_PRICE}" data-count="{$_db_QUANTITY}">
                                    <input type="checkbox" id="prdChk{$count}" name="prdChk" $checked>
                                </td>
                                <td>
                                    <div class="lightBoxGallery"><img src="{$path_File}" style="height: 100px;" alt="작품 이미지">
                                    </div>
                                </td>
                                <td>{$_db_CATEGORY3_NAME}</td>
                                <td>{$_db_OPTION_NAME}</td>
                                {$FRAME_html}
                                <td>{$_db_QUANTITY}</td>
                                <td><span>{$_db_OPTION_PRICE_TEXT}</span> 원</td>
                                {$NVOICE_html}
                                <td>{$_db_OP_STATE_CD_NM}</td>
                            </tr>
                        TR;

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