<?php
        require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
        require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');
        require_once($_SERVER['DOCUMENT_ROOT']. '/lib/INIS/INIStdPayUtil.php');
        require_once($_SERVER['DOCUMENT_ROOT']. '/lib/INIS/HttpClient.php');
        require_once($_SERVER['DOCUMENT_ROOT']. '/lib/INIS/properties.php');
 
        $util = new INIStdPayUtil();
        $prop = new properties();
        use Clef\Pdo7 as Pdo7;
        use Clef\ClefResult as ClefResult;

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $mysqldb->link->beginTransaction();

            //#############################
            // 인증결과 파라미터 수신
            //#############################
            //_p('------- Request-------');
            //_p($_REQUEST);
            //_p('------- Request-------');

            $resultMap_code = "";
            $resultMap_msg = "";
            
            if ($_REQUEST["P_STATUS"] === "00") {
                $P_STATUS = $_REQUEST["P_STATUS"];
                $P_RMESG1 = $_REQUEST["P_RMESG1"];
                $P_TID = $_REQUEST["P_TID"];
                $P_REQ_URL = $_REQUEST["P_REQ_URL"];
                $P_NOTI = $_REQUEST["P_NOTI"];
                $P_AMT = $_REQUEST["P_AMT"];

                $id_merchant = substr($P_TID,'10','10'); // P_TID 내 MID 구분

                $data = array(
                    'P_MID' => $id_merchant, // P_MID
                    'P_TID' => $P_TID // P_TID
                );

                //##########################################################################
                // 승인요청 API url (authUrl) 리스트 는 properties 에 세팅하여 사용합니다.
                // idc_name 으로 수신 받은 센터 네임을 properties 에서 include 하여 승인요청하시면 됩니다.
                //##########################################################################
                $idc_name 	= $_REQUEST["idc_name"];
                $P_REQ_URL  = $prop->getAuthUrl_Mo($idc_name); 

                if (strcmp($P_REQ_URL, $_REQUEST["P_REQ_URL"]) == 0) {
                    // curl 통신 시작 
                
                    $ch = curl_init(); //curl 초기화
                    curl_setopt($ch, CURLOPT_URL, $_REQUEST["P_REQ_URL"]); //URL 지정하기
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //요청 결과를 문자열로 반환 
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //connection timeout 10초 
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //원격 서버의 인증서가 유효한지 검사 안함
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));    //POST 로 $data 를 보냄
                    curl_setopt($ch, CURLOPT_POST, 1); //true시 post 전송 
            
            
                    $response = curl_exec($ch);
                    curl_close($ch);
        
                    parse_str($response, $resultMap);
                    
                    $resultMap_code = $resultMap["P_STATUS"];
                    $resultMap_msg = $resultMap["P_RMESG1"];

                    try {
                        if ($resultMap["P_STATUS"] == "00") {
                            $seq_name = "INIS". array_key_exists("payMethod", $resultMap) ? $resultMap["payMethod"] : (array_key_exists("P_TYPE", $resultMap) ? $resultMap["P_TYPE"] : "");

                            $sql = "
                                SELECT nextval_Order('{$seq_name}') as seq";
                
                            $name_sql = "이니시스 시퀀스";
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
                            
                            $table = 'PURCHASE_INICIS';

                            $INICIS_SEQ = $data['seq']; // 이니시스 시퀀스

                            $TYPE_MONITOR = ''; // pc, mo 구분

                            if (get_is_mobile()) {
                                $TYPE_MONITOR = 'mo'; // 모바일
                            } else {
                                $TYPE_MONITOR = 'pc'; // PC
                            }

                            $TID = array_key_exists("tid", $resultMap) ? $resultMap["tid"] : (array_key_exists("P_TID", $resultMap) ? $resultMap["P_TID"] : ""); // 거래번호 tid, P_TID
                            $OID = array_key_exists("MOID", $resultMap) ? $resultMap["MOID"] : (array_key_exists("P_OID", $resultMap) ? $resultMap["P_OID"] : ""); // 주문번호 MOID, P_OID
                            $PRICE = array_key_exists("TotPrice", $resultMap) ? $resultMap["TotPrice"] : (array_key_exists("P_AMT", $resultMap) ? $resultMap["P_AMT"] : ""); // 결제금액 TotPrice, P_AMT
                            $GOODNAME = array_key_exists("goodName", $resultMap) ? $resultMap["goodName"] : ""; // 상품명 goodName, 모바일 x
                            $PAYMETHOD = array_key_exists("payMethod", $resultMap) ? $resultMap["payMethod"] : (array_key_exists("P_TYPE", $resultMap) ? $resultMap["P_TYPE"] : ""); // 지불수단 payMethod, P_TYPE 공통코드 AD010 값
                            $APPLDATE = array_key_exists("applDate", $resultMap) ? $resultMap["applDate"] : (array_key_exists("P_AUTH_DT", $resultMap) ? $resultMap["P_AUTH_DT"] : ""); // 승인일자 applDate, P_AUTH_DT
                            $APPLTIME = array_key_exists("applTime", $resultMap) ? $resultMap["applTime"] : ""; // 승인시간 applTime, 모바일 x
                            $EVENTCODE = array_key_exists("EventCode", $resultMap) ? $resultMap["EventCode"] : ""; // 이벤트 코드, 카드 할부 및 행사 적용 코드 EventCode, 모바일 x
                            $BUYERNAME = array_key_exists("buyerName", $resultMap) ? $resultMap["buyerName"] : (array_key_exists("P_UNAME", $resultMap) ? $resultMap["P_UNAME"] : ""); // 구매자명 buyerName, P_UNAME
                            $BUYERTEL = array_key_exists("buyerTel", $resultMap) ? $resultMap["buyerTel"] : ""; // 구매자 휴대폰번호 buyerTel, 모바일 x
                            $BUYEREMAIL = array_key_exists("buyerEmail", $resultMap) ? $resultMap["buyerEmail"] : ""; // 구매자 이메일주소 buyerEmail, 모바일 x
                            $P_MNAME = array_key_exists("P_MNAME", $resultMap) ? $resultMap["P_MNAME"] : ""; // 가맹점명 PC x, P_MNAME
                            $P_NOTI = array_key_exists("P_NOTI", $resultMap) ? $resultMap["P_NOTI"] : ""; // 가맹점 임의 데이터 PC x, P_NOTI
                            $P_NOTEURL = array_key_exists("P_NOTEURL", $resultMap) ? $resultMap["P_NOTEURL"] : ""; // 가맹점 전달 P_NOTI_URL PC x, P_NOTEURL
                            $P_NEXT_URL = array_key_exists("P_NEXT_URL", $resultMap) ? $resultMap["P_NEXT_URL"] : ""; // 가맹점 전달 P_NEXT_URL 전달 P_NOTI_URL PC x, P_NEXT_URL
                            $CUSTEMAIL = array_key_exists("custEmail", $resultMap) ? $resultMap["custEmail"] : ""; // 최종 이메일주소 custEmail, 모바일 x
                            $APPLNUM = array_key_exists("applNum", $resultMap) ? $resultMap["applNum"] : (array_key_exists("P_AUTH_NO", $resultMap) ? $resultMap["P_AUTH_NO"] : ""); // 신용카드 - 승인번호 applNum, P_AUTH_NO
                            $CARD_NUM = array_key_exists("CARD_Num", $resultMap) ? $resultMap["CARD_Num"] : (array_key_exists("P_CARD_NUM", $resultMap) ? $resultMap["P_CARD_NUM"] : ""); // 신용카드 - 신용카드번호 CARD_Num, P_CARD_NUM
                            $CARD_INTEREST = array_key_exists("CARD_Interest", $resultMap) ? $resultMap["CARD_Interest"] : (array_key_exists("P_CARD_INTEREST", $resultMap) ? $resultMap["P_CARD_INTEREST"] : ""); // 신용카드 - 상점부담 무이자 할부여부 CARD_Interest, P_CARD_INTEREST
                            $CARD_QUOTA = array_key_exists("CARD_Quota", $resultMap) ? $resultMap["CARD_Quota"] : (array_key_exists("P_RMESG2", $resultMap) ? $resultMap["P_RMESG2"] : ""); // 신용카드 - 카드 할부기간 CARD_Quota, P_RMESG2
                            $CARD_CODE = array_key_exists("CARD_Code", $resultMap) ? $resultMap["CARD_Code"] : (array_key_exists("P_FN_CD1", $resultMap) ? $resultMap["P_FN_CD1"] : ""); // 신용카드 - 카드사코드 CARD_Code, P_FN_CD1
                            $CARD_P_FN_NM = array_key_exists("P_FN_NM", $resultMap) ? $resultMap["P_FN_NM"] : ""; // 신용카드 - 결제카드사 한글명 PC x, P_FN_NM
                            $CARD_CORPFLAG = array_key_exists("CARD_CorpFlag", $resultMap) ? $resultMap["CARD_CorpFlag"] : ""; // 신용카드 - 카드구분 ["0":개인카드, "1":법인카드, "9":구분불가] CARD_CorpFlag, CARD_CorpFlag
                            $CARD_CHECKFLAG = array_key_exists("CARD_CheckFlag", $resultMap) ? $resultMap["CARD_CheckFlag"] : (array_key_exists("P_CARD_CHECKFLAG", $resultMap) ? $resultMap["P_CARD_CHECKFLAG"] : ""); // 신용카드 - 카드종류 ["0":신용카드, "1":체크카드, "2":기프트카드] CARD_CheckFlag, P_CARD_CHECKFLAG
                            $CARD_PRTC_CODE = array_key_exists("CARD_PRTC_CODE", $resultMap) ? $resultMap["CARD_PRTC_CODE"] : (array_key_exists("P_CARD_PRTC_CODE", $resultMap) ? $resultMap["P_CARD_PRTC_CODE"] : ""); // 신용카드 - 부분취소 가능여부 ["1":가능 , "0":불가능] CARD_PRTC_CODE, P_CARD_PRTC_CODE
                            $CARD_BANKCODE = array_key_exists("CARD_BankCode", $resultMap) ? $resultMap["CARD_BankCode"] : (array_key_exists("P_CARD_ISSUER_CODE", $resultMap) ? $resultMap["P_CARD_ISSUER_CODE"] : ""); // 신용카드 - 카드발급사(은행) 코드 CARD_BankCode, P_CARD_ISSUER_CODE
                            $P_ISP_CARDCODE = array_key_exists("P_ISP_CARDCODE", $resultMap) ? $resultMap["P_ISP_CARDCODE"] : ""; // 신용카드 - VP 카드코드 PC x, P_ISP_CARDCODE
                            $CARD_SRCCODE = array_key_exists("CARD_SrcCode", $resultMap) ? $resultMap["CARD_SrcCode"] : (array_key_exists("P_SRC_CODE", $resultMap) ? $resultMap["P_SRC_CODE"] : ""); // 신용카드 - 간편(앱)결제구분 CARD_SrcCode, P_SRC_CODE 공통코드 AD011 값
                            $P_CARD_MEMBER_NUM = array_key_exists("P_CARD_MEMBER_NUM", $resultMap) ? $resultMap["P_CARD_MEMBER_NUM"] : ""; // 신용카드 - 가맹점번호 PC x, P_CARD_MEMBER_NUM
                            $P_CARD_PURCHASE_CODE = array_key_exists("P_CARD_PURCHASE_CODE", $resultMap) ? $resultMap["P_CARD_PURCHASE_CODE"] : ""; // 신용카드 - 매입사코드 PC x, P_CARD_PURCHASE_CODE
                            $CARD_POINT = array_key_exists("CARD_Point", $resultMap) ? $resultMap["CARD_Point"] : ""; // 신용카드 - 카드포인트 사용여부 ["":카드 포인트 사용안함, "1":카드 포인트 사용] CARD_Point, 모바일 x
                            $CARD_USEPOINT = array_key_exists("CARD_UsePoint", $resultMap) ? $resultMap["CARD_UsePoint"] : (array_key_exists("P_CARD_USEPOINT", $resultMap) ? $resultMap["P_CARD_USEPOINT"] : ""); // 신용카드 - 포인트 사용금액 CARD_UsePoint, P_CARD_USEPOINT
                            $P_COUPONFLAG = array_key_exists("P_COUPONFLAG", $resultMap) ? $resultMap["P_COUPONFLAG"] : ""; // 신용카드 - 쿠폰사용 유무 ["1":사용] PC x, P_COUPONFLAG
                            $CARD_COUPONPRICE = array_key_exists("CARD_CouponPrice", $resultMap) ? $resultMap["CARD_CouponPrice"] : (array_key_exists("P_CARD_COUPON_PRICE", $resultMap) ? $resultMap["P_CARD_COUPON_PRICE"] : ""); // 신용카드 - 실제 카드승인 금액 CARD_CouponPrice, P_CARD_COUPON_PRICE
                            $P_CARD_APPLPRICE = array_key_exists("P_CARD_APPLPRICE", $resultMap) ? $resultMap["P_CARD_APPLPRICE"] : ""; // 신용카드 - 승인요청 금액 PC x, P_CARD_APPLPRICE
                            $COUPON_DISCOUNT = array_key_exists("CARD_CouponDiscount", $resultMap) ? $resultMap["CARD_CouponDiscount"] : (array_key_exists("P_COUPON_DISCOUNT", $resultMap) ? $resultMap["P_COUPON_DISCOUNT"] : ""); // 신용카드 - 쿠폰(즉시할인) 금액 CARD_CouponDiscount, P_COUPON_DISCOUNT
                            $NAVERPOINT_USEFREEPOINT = array_key_exists("NAVERPOINT_UseFreePoint", $resultMap) ? $resultMap["NAVERPOINT_UseFreePoint"] : (array_key_exists("NAVERPOINT_UseFreePoint", $resultMap) ? $resultMap["NAVERPOINT_UseFreePoint"] : ""); // 신용카드 - 네이버포인트 무상포인트 NAVERPOINT_UseFreePoint, NAVERPOINT_UseFreePoint
                            $NAVERPOINT_CSHRAPPLYN = array_key_exists("NAVERPOINT_CSHRApplYN", $resultMap) ? $resultMap["NAVERPOINT_CSHRApplYN"] : (array_key_exists("NAVERPOINT_CSHRApplYN", $resultMap) ? $resultMap["NAVERPOINT_CSHRApplYN"] : ""); // 신용카드 - 네이버포인트 현금영수증 발행여부 ["Y":발행, "N":미발행] NAVERPOINT_CSHRApplYN, NAVERPOINT_CSHRApplYN
                            $NAVERPOINT_CSHRAPPLAMT = array_key_exists("NAVERPOINT_CSHRApplAmt", $resultMap) ? $resultMap["NAVERPOINT_CSHRApplAmt"] : (array_key_exists("NAVERPOINT_CSHRApplAmt", $resultMap) ? $resultMap["NAVERPOINT_CSHRApplAmt"] : ""); // 신용카드 - 네이버포인트 현금영수증 발행 금액 NAVERPOINT_CSHRApplAmt, NAVERPOINT_CSHRApplAmt
                            $PCO_ORDERNO = array_key_exists("PCO_OrderNo", $resultMap) ? $resultMap["PCO_OrderNo"] : (array_key_exists("PCO_OrderNo", $resultMap) ? $resultMap["PCO_OrderNo"] : ""); // 신용카드 - 페이코 주문번호 PCO_OrderNo, PCO_OrderNo
                            $CURRENCY = array_key_exists("currency", $resultMap) ? $resultMap["currency"] : ""; // 신용카드 - 통화코드 currency, 모바일 x
                            $ORGPRICE = array_key_exists("OrgPrice", $resultMap) ? $resultMap["OrgPrice"] : ""; // 신용카드 - 달러 환전금액 OrgPrice, 모바일 x
                            $CARD_EMPPRTNCODE = array_key_exists("CARD_EmpPrtnCode", $resultMap) ? $resultMap["CARD_EmpPrtnCode"] : ""; // 신용카드 - 롯데카드 임직원 제휴 구분코드 ["L":임직원] PC x, CARD_EmpPrtnCode
                            $CARD_NOMLMOBPRTNCODE = array_key_exists("CARD_NomlMobPrtnCode", $resultMap) ? $resultMap["CARD_NomlMobPrtnCode"] : ""; // 신용카드 - 카드사 제휴구분코드 ["P":롯데카드일반, "M":롯데카드모바일, "H":현대카드(통합)] PC x, CARD_NomlMobPrtnCode
                            $ACCT_BANKCODE = array_key_exists("ACCT_BankCode", $resultMap) ? $resultMap["ACCT_BankCode"] : (array_key_exists("P_FN_CD1", $resultMap) ? $resultMap["P_FN_CD1"] : ""); // 계좌이체 - 은행코드 ACCT_BankCode, P_FN_CD1
                            $CSHR_RESULTCODE = array_key_exists("CSHR_ResultCode", $resultMap) ? $resultMap["CSHR_ResultCode"] : (array_key_exists("P_CSHR_CODE", $resultMap) ? $resultMap["P_CSHR_CODE"] : ""); // 계좌이체 - 현금영수증 발행 정상여부 CSHR_ResultCode, P_CSHR_CODE
                            $CSHR_APPNUM = array_key_exists("CSHR_ApplNum", $resultMap) ? $resultMap["CSHR_ApplNum"] : (array_key_exists("P_CSHR_AUTH_NO", $resultMap) ? $resultMap["P_CSHR_AUTH_NO"] : ""); // 계좌이체 - 현금영수증 발행 승인번호 CSHR_ApplNum, P_CSHR_AUTH_NO
                            $CSHR_TYPE = array_key_exists("CSHR_Type", $resultMap) ? $resultMap["CSHR_Type"] : (array_key_exists("P_CSHR_TYPE", $resultMap) ? $resultMap["P_CSHR_TYPE"] : ""); // 계좌이체 - 현금영수증구분 ["0":소득공제, "1":지출증빙] CSHR_Type, P_CSHR_TYPE
                            $P_CSHR_AMT = array_key_exists("P_CSHR_AMT", $resultMap) ? $resultMap["P_CSHR_AMT"] : ""; // 계좌이체 - 현금영수증 총 금액 [총금액 = 공급가액+세금+봉사료] PC x, P_CSHR_AMT
                            $P_CSHR_SUP_AMT = array_key_exists("P_CSHR_SUP_AMT", $resultMap) ? $resultMap["P_CSHR_SUP_AMT"] : ""; // 계좌이체 - 공급가액 PC x, P_CSHR_SUP_AMT
                            $P_CSHR_TAX = array_key_exists("P_CSHR_TAX", $resultMap) ? $resultMap["P_CSHR_TAX"] : ""; // 계좌이체 - 부가세 PC x, P_CSHR_TAX
                            $P_CSHR_SRVC_AMT = array_key_exists("P_CSHR_SRVC_AMT", $resultMap) ? $resultMap["P_CSHR_SRVC_AMT"] : ""; // 계좌이체 - 봉사료 PC x, P_CSHR_SRVC_AMT
                            $P_CSHR_DT = array_key_exists("P_CSHR_DT", $resultMap) ? $resultMap["P_CSHR_DT"] : ""; // 계좌이체 - 발행일시 PC x, P_CSHR_DT
                            $CSHR_P_FN_NM = array_key_exists("P_FN_NM", $resultMap) ? $resultMap["P_FN_NM"] : ""; // 계좌이체 - 결제은행 한글명 PC x, P_FN_NM
                            $ACCT_NAME = array_key_exists("ACCT_Name", $resultMap) ? $resultMap["ACCT_Name"] : ""; // 계좌이체 - 계좌주명 한글명 ACCT_Name, 모바일 x
                            $VACT_NUM = array_key_exists("VACT_Num", $resultMap) ? $resultMap["VACT_Num"] : (array_key_exists("P_VACT_NUM", $resultMap) ? $resultMap["P_VACT_NUM"] : ""); // 가상계좌 - 가상계좌번호 VACT_Num, P_VACT_NUM
                            $VACT_BANKCODE = array_key_exists("VACT_BankCode", $resultMap) ? $resultMap["VACT_BankCode"] : (array_key_exists("P_VACT_BANK_CODE", $resultMap) ? $resultMap["P_VACT_BANK_CODE"] : ""); // 가상계좌 - 입금은행코드 VACT_BankCode, P_VACT_BANK_CODE
                            $VACTBANKNAME = array_key_exists("vactBankName", $resultMap) ? $resultMap["vactBankName"] : (array_key_exists("P_FN_NM", $resultMap) ? $resultMap["P_FN_NM"] : ""); // 가상계좌 - 입금은행명 vactBankName, P_FN_NM
                            $VACT_NAME = array_key_exists("VACT_Name", $resultMap) ? $resultMap["VACT_Name"] : (array_key_exists("P_VACT_NAME", $resultMap) ? $resultMap["P_VACT_NAME"] : ""); // 가상계좌 - 예금주명 VACT_Name, P_VACT_NAME
                            $VACT_INPUTNAME = array_key_exists("VACT_InputName", $resultMap) ? $resultMap["VACT_InputName"] : ""; // 가상계좌 - 송금자명 VACT_InputName, 모바일 x
                            $VACT_DATE = array_key_exists("VACT_Date", $resultMap) ? $resultMap["VACT_Date"] : (array_key_exists("P_VACT_DATE", $resultMap) ? $resultMap["P_VACT_DATE"] : ""); // 가상계좌 - 입금기한일자 VACT_Date, P_VACT_DATE
                            $VACT_TIME = array_key_exists("VACT_Time", $resultMap) ? $resultMap["VACT_Time"] : (array_key_exists("P_VACT_TIME", $resultMap) ? $resultMap["P_VACT_TIME"] : ""); // 가상계좌 - 입금기한시각 VACT_Time, P_VACT_TIME
                            $HPP_NUM = array_key_exists("HPP_Num", $resultMap) ? $resultMap["HPP_Num"] : (array_key_exists("P_HPP_NUM", $resultMap) ? $resultMap["P_HPP_NUM"] : ""); // 휴대폰 - 휴대폰번호 HPP_Num, P_HPP_NUM

                            $values = array(
                                'INICIS_SEQ' => $INICIS_SEQ // 이니시스 시퀀스
                                , 'TYPE_MONITOR' => $TYPE_MONITOR // pc, mo 구분
                                , 'TID' => $TID // 거래번호 tid, P_TID
                                , 'OID' => $OID // 주문번호 MOID, P_OID
                                , 'PRICE' => $PRICE // 결제금액 TotPrice, P_AMT
                                , 'GOODNAME' => $GOODNAME // 상품명 goodName, 모바일 x
                                , 'PAYMETHOD' => $PAYMETHOD // 지불수단 payMethod, P_TYPE 공통코드 AD010 값
                                , 'APPLDATE' => $APPLDATE // 승인일자 applDate, P_AUTH_DT
                                , 'APPLTIME' => $APPLTIME // 승인시간 applTime, 모바일 x
                                , 'EVENTCODE' => $EVENTCODE // 이벤트 코드, 카드 할부 및 행사 적용 코드 EventCode, 모바일 x
                                , 'BUYERNAME' => $BUYERNAME // 구매자명 buyerName, P_UNAME
                                , 'BUYERTEL' => $BUYERTEL // 구매자 휴대폰번호 buyerTel, 모바일 x
                                , 'BUYEREMAIL' => $BUYEREMAIL // 구매자 이메일주소 buyerEmail, 모바일 x
                                , 'P_MNAME' => $P_MNAME // 가맹점명 PC x, P_MNAME
                                , 'P_NOTI' => $P_NOTI // 가맹점 임의 데이터 PC x, P_NOTI
                                , 'P_NOTEURL' => $P_NOTEURL // 가맹점 전달 P_NOTI_URL PC x, P_NOTEURL
                                , 'P_NEXT_URL' => $P_NEXT_URL // 가맹점 전달 P_NEXT_URL 전달 P_NOTI_URL PC x, P_NEXT_URL
                                , 'CUSTEMAIL' => $CUSTEMAIL // 최종 이메일주소 custEmail, 모바일 x
                                , 'APPLNUM' => $APPLNUM // 신용카드 - 승인번호 applNum, P_AUTH_NO
                                , 'CARD_NUM' => $CARD_NUM // 신용카드 - 신용카드번호 CARD_Num, P_CARD_NUM
                                , 'CARD_INTEREST' => $CARD_INTEREST // 신용카드 - 상점부담 무이자 할부여부 CARD_Interest, P_CARD_INTEREST
                                , 'CARD_QUOTA' => $CARD_QUOTA // 신용카드 - 카드 할부기간 CARD_Quota, P_RMESG2
                                , 'CARD_CODE' => $CARD_CODE // 신용카드 - 카드사코드 CARD_Code, P_FN_CD1
                                , 'CARD_P_FN_NM' => $CARD_P_FN_NM // 신용카드 - 결제카드사 한글명 PC x, P_FN_NM
                                , 'CARD_CORPFLAG' => $CARD_CORPFLAG // 신용카드 - 카드구분 ["0":개인카드, "1":법인카드, "9":구분불가] CARD_CorpFlag, CARD_CorpFlag
                                , 'CARD_CHECKFLAG' => $CARD_CHECKFLAG // 신용카드 - 카드종류 ["0":신용카드, "1":체크카드, "2":기프트카드] CARD_CheckFlag, P_CARD_CHECKFLAG
                                , 'CARD_PRTC_CODE' => $CARD_PRTC_CODE // 신용카드 - 부분취소 가능여부 ["1":가능 , "0":불가능] CARD_PRTC_CODE, P_CARD_PRTC_CODE
                                , 'CARD_BANKCODE' => $CARD_BANKCODE // 신용카드 - 카드발급사(은행) 코드 CARD_BankCode, P_CARD_ISSUER_CODE
                                , 'P_ISP_CARDCODE' => $P_ISP_CARDCODE // 신용카드 - VP 카드코드 PC x, P_ISP_CARDCODE
                                , 'CARD_SRCCODE' => $CARD_SRCCODE // 신용카드 - 간편(앱)결제구분 CARD_SrcCode, P_SRC_CODE 공통코드 AD011 값
                                , 'P_CARD_MEMBER_NUM' => $P_CARD_MEMBER_NUM // 신용카드 - 가맹점번호 PC x, P_CARD_MEMBER_NUM
                                , 'P_CARD_PURCHASE_CODE' => $P_CARD_PURCHASE_CODE // 신용카드 - 매입사코드 PC x, P_CARD_PURCHASE_CODE
                                , 'CARD_POINT' => $CARD_POINT // 신용카드 - 카드포인트 사용여부 ["":카드 포인트 사용안함, "1":카드 포인트 사용] CARD_Point, 모바일 x
                                , 'CARD_USEPOINT' => $CARD_USEPOINT // 신용카드 - 포인트 사용금액 CARD_UsePoint, P_CARD_USEPOINT
                                , 'P_COUPONFLAG' => $P_COUPONFLAG // 신용카드 - 쿠폰사용 유무 ["1":사용] PC x, P_COUPONFLAG
                                , 'CARD_COUPONPRICE' => $CARD_COUPONPRICE // 신용카드 - 실제 카드승인 금액 CARD_CouponPrice, P_CARD_COUPON_PRICE
                                , 'P_CARD_APPLPRICE' => $P_CARD_APPLPRICE // 신용카드 - 승인요청 금액 PC x, P_CARD_APPLPRICE
                                , 'COUPON_DISCOUNT' => $COUPON_DISCOUNT // 신용카드 - 쿠폰(즉시할인) 금액 CARD_CouponDiscount, P_COUPON_DISCOUNT
                                , 'NAVERPOINT_USEFREEPOINT' => $NAVERPOINT_USEFREEPOINT // 신용카드 - 네이버포인트 무상포인트 NAVERPOINT_UseFreePoint, NAVERPOINT_UseFreePoint
                                , 'NAVERPOINT_CSHRAPPLYN' => $NAVERPOINT_CSHRAPPLYN // 신용카드 - 네이버포인트 현금영수증 발행여부 ["Y":발행, "N":미발행] NAVERPOINT_CSHRApplYN, NAVERPOINT_CSHRApplYN
                                , 'NAVERPOINT_CSHRAPPLAMT' => $NAVERPOINT_CSHRAPPLAMT // 신용카드 - 네이버포인트 현금영수증 발행 금액 NAVERPOINT_CSHRApplAmt, NAVERPOINT_CSHRApplAmt
                                , 'PCO_ORDERNO' => $PCO_ORDERNO // 신용카드 - 페이코 주문번호 PCO_OrderNo, PCO_OrderNo
                                , 'CURRENCY' => $CURRENCY // 신용카드 - 통화코드 currency, 모바일 x
                                , 'ORGPRICE' => $ORGPRICE // 신용카드 - 달러 환전금액 OrgPrice, 모바일 x
                                , 'CARD_EMPPRTNCODE' => $CARD_EMPPRTNCODE // 신용카드 - 롯데카드 임직원 제휴 구분코드 ["L":임직원] PC x, CARD_EmpPrtnCode
                                , 'CARD_NOMLMOBPRTNCODE' => $CARD_NOMLMOBPRTNCODE // 신용카드 - 카드사 제휴구분코드 ["P":롯데카드일반, "M":롯데카드모바일, "H":현대카드(통합)] PC x, CARD_NomlMobPrtnCode
                                , 'ACCT_BANKCODE' => $ACCT_BANKCODE // 계좌이체 - 은행코드 ACCT_BankCode, P_FN_CD1
                                , 'CSHR_RESULTCODE' => $CSHR_RESULTCODE // 계좌이체 - 현금영수증 발행 정상여부 CSHR_ResultCode, P_CSHR_CODE
                                , 'CSHR_APPNUM' => $CSHR_APPNUM // 계좌이체 - 현금영수증 발행 승인번호 CSHR_ApplNum, P_CSHR_AUTH_NO
                                , 'CSHR_TYPE' => $CSHR_TYPE // 계좌이체 - 현금영수증구분 ["0":소득공제, "1":지출증빙] CSHR_Type, P_CSHR_TYPE
                                , 'P_CSHR_AMT' => $P_CSHR_AMT // 계좌이체 - 현금영수증 총 금액 [총금액 = 공급가액+세금+봉사료] PC x, P_CSHR_AMT
                                , 'P_CSHR_SUP_AMT' => $P_CSHR_SUP_AMT // 계좌이체 - 공급가액 PC x, P_CSHR_SUP_AMT
                                , 'P_CSHR_TAX' => $P_CSHR_TAX // 계좌이체 - 부가세 PC x, P_CSHR_TAX
                                , 'P_CSHR_SRVC_AMT' => $P_CSHR_SRVC_AMT // 계좌이체 - 봉사료 PC x, P_CSHR_SRVC_AMT
                                , 'P_CSHR_DT' => $P_CSHR_DT // 계좌이체 - 발행일시 PC x, P_CSHR_DT
                                , 'CSHR_P_FN_NM' => $CSHR_P_FN_NM // 계좌이체 - 결제은행 한글명 PC x, P_FN_NM
                                , 'ACCT_NAME' => $ACCT_NAME // 계좌이체 - 계좌주명 한글명 ACCT_Name, 모바일 x
                                , 'VACT_NUM' => $VACT_NUM // 가상계좌 - 가상계좌번호 VACT_Num, P_VACT_NUM
                                , 'VACT_BANKCODE' => $VACT_BANKCODE // 가상계좌 - 입금은행코드 VACT_BankCode, P_VACT_BANK_CODE
                                , 'VACTBANKNAME' => $VACTBANKNAME // 가상계좌 - 입금은행명 vactBankName, P_FN_NM
                                , 'VACT_NAME' => $VACT_NAME // 가상계좌 - 예금주명 VACT_Name, P_VACT_NAME
                                , 'VACT_INPUTNAME' => $VACT_INPUTNAME // 가상계좌 - 송금자명 VACT_InputName, 모바일 x
                                , 'VACT_DATE' => $VACT_DATE // 가상계좌 - 입금기한일자 VACT_Date, P_VACT_DATE
                                , 'VACT_TIME' => $VACT_TIME // 가상계좌 - 입금기한시각 VACT_Time, P_VACT_TIME
                                , 'HPP_NUM' => $HPP_NUM // 휴대폰 - 휴대폰번호 HPP_Num, P_HPP_NUM
                                , 'reg_user' => $BUYERNAME // 등록자
                                , 'reg_ip' => $ip // 등록자 아이피
                                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
                            );

                            $name_sql = "이니시스 추가";
                            $clefResult = $mysqldb->insert($table, $values, $name_sql);

                            if (!$clefResult->getResult()) {
                                throw new Exception('결제오류 다시 결제해주세요.', 503);
                            }
                            
                            $mysqldb->link->commit();
                        } else {
                            throw new Exception($resultMap['P_RMESG1'], $resultMap['P_STATUS']);
                        } 
                    } catch (Exception $e) {
                        $mysqldb->link->rollBack();
            
                        $P_TIMESTAMP = $util->getTimestamp();
                        $HashKey = INIS_HASHKEY;

                        $params = $P_AMT . $resultMap['P_OID']. $P_TIMESTAMP. $HashKey;
    
                        // SHA-512 해싱
                        $hash = hash("sha512", $params);
                                
                        // HEX 문자열을 Base64로 인코딩
                        $P_CHKFAKE = base64_encode(hex2bin($hash));
                        
                        $cancelUrl = $prop->getNetCancel_Mo($idc_name);

                        $data = array(
                              'P_TID' => $P_TID
                            , 'P_MID' => $resultMap['P_MID']
                            , 'P_AMT' => $resultMap['P_AMT']
                            , 'P_OID' => $resultMap['P_OID']
                            , 'P_TIMESTAMP' => $P_TIMESTAMP
                            , 'P_CHKFAKE' => $P_CHKFAKE
                        );

                        // cURL 초기화
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $cancelUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

                        // 망취소 요청 보내기
                        $response = curl_exec($ch);

                        // cURL 세션 닫기
                        curl_close($ch);
            
                        parse_str($response, $resultMap2);

                        throw new Exception($e->getMessage(), $e->getCode());
                    }
                }
            } else {
                throw new Exception($_REQUEST['P_RMESG1'], $_REQUEST['P_STATUS']);
            }
        } catch (Exception $e) {
            $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
            dieAndErrorMove($s);
            //echo $s;
        }
?>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/php/temp/INIS/INIstdpay_temp_mo_custom.php'; ?>