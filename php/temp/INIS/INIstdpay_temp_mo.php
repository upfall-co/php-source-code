<?php
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/INIS/INIStdPayUtil.php');
    $SignatureUtil = new INIStdPayUtil();

    $P_INI_PAYMENT = "CARD"; // 지불수단 CARD 신용카드, BANK 실시간 계좌이체, VBANK 가상계좌 [이니시스 개발 가이드에 더많이있음]
    $P_MID = INIS_MID; // 상점아이디
    $HashKey = INIS_HASHKEY; // 웹 결제 signkey
    $P_AMT = $_INIS_PRICE; // 상품가격(특수기호 제외, 가맹점에서 직접 설정)
    $P_TIMESTAMP = $SignatureUtil->getTimestamp(); // util에 의해서 자동생성
    $orderNumber = $P_MID . "_" . $P_TIMESTAMP; // 가맹점 주문번호(가맹점에서 직접 설정) oid와 동일한 변수값

    $params = $P_AMT . $orderNumber. $P_TIMESTAMP. $HashKey;
    
    // SHA-512 해싱
    $hash = hash("sha512", $params);
            
    // HEX 문자열을 Base64로 인코딩
    $P_CHKFAKE = base64_encode(hex2bin($hash));

    $P_GOODS = $_INIS_GOODNAME; // 작품명

    $INIS_URL = $_SERVER['DOCUMENT_ROOT'];

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";

    $domain = $_SERVER['HTTP_HOST'];
    $P_NEXT_URL = $protocol . "://" . $domain . "/php/temp/INIS/INIstdpay_temp_mo_return.php"; // 결제가 완료된이후 진행되는 부분

    $P_RESERVED = "centerCd=Y&amt_hash=Y"; // 이니시스 개발 가이드에 추가옵션을 확인 해당 값들은 "below1000=Y&vbank_receipt=Y 이런식으로 작성 centerCd=Y&amt_hash=Y값은 필수
?>

<script type="text/javascript">
    function on_pay() { 
        myform = document.INISTDPAY_FORM_MO; 
        myform.action = "https://mobile.inicis.com/smart/payment/";
        myform.target = "_self";
        myform.submit(); 
    }
</script>

<form name="INISTDPAY_FORM_MO" id="INISTDPAY_FORM_MO" method="post" class="mt-5" accept-charset="euc-kr">
    <input type="hidden" name="P_INI_PAYMENT" value="<?= $P_INI_PAYMENT ?>">
    <input type="hidden" name="P_MID" value="<?= $P_MID ?>">
    <input type="hidden" name="P_OID" value="<?= $orderNumber ?>">
    <input type="hidden" name="P_AMT" value="<?= $P_AMT ?>">
    <input type="hidden" name="P_GOODS" value="<?= $P_GOODS ?>">
    <input type="hidden" name="P_UNAME" value="">
    <input type="hidden" name="P_MOBILE" value="">
    <input type="hidden" name="P_EMAIL" value="">
    <input type="hidden" name="P_RESERVED" value="<?= $P_RESERVED ?>">
    <input type="hidden" name="P_TIMESTAMP" value="<?= $P_TIMESTAMP ?>">
    <input type="hidden" name="P_CHKFAKE" value="<?= $P_CHKFAKE ?>">
    <input type="hidden" name="P_NEXT_URL" value="<?= $P_NEXT_URL ?>">
    <input type="hidden" name="P_CHARSET" value="utf8">
</form>