<?php
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/INIS/INIStdPayUtil.php');
    $SignatureUtil = new INIStdPayUtil();

    $gopaymethod = "Card"; // 지불수단 Card 신용카드, DirectBank 실시간 계좌이체, VBank 가상계좌 [이니시스 개발 가이드에 더많이있음]
    $mid = INIS_MID; // 상점아이디
    $signKey = INIS_SIGNKEY; // 웹 결제 signkey
    $price = $_INIS_PRICE; // 상품가격(특수기호 제외, 가맹점에서 직접 설정)
    $timestamp = $SignatureUtil->getTimestamp(); // util에 의해서 자동생성
    $orderNumber = $mid . "_" . $timestamp; // 가맹점 주문번호(가맹점에서 직접 설정) oid와 동일한 변수값
    $use_chkfake = "Y"; // PC결제 보안강화 사용 ["Y" 고정]

    $params = array(
          "oid" => $orderNumber
        , "price" => $price
        , "timestamp" => $timestamp
    );
    
    $sign = $SignatureUtil->makeSignature($params); // signature
    
    $params = array(
          "oid" => $orderNumber
        , "price" => $price
        , "signKey" => $signKey
        , "timestamp" => $timestamp
    );
    
    $sign2 = $SignatureUtil->makeSignature($params); // sign2

    $mKey = $SignatureUtil->makeHash($signKey, "sha256");
    $currency = "WON"; // 통화구분 WON 한화, USD 달러

    $goodname = $_INIS_GOODNAME; // 작품명

    $INIS_URL = $_SERVER['DOCUMENT_ROOT'];

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";

    $domain = $_SERVER['HTTP_HOST'];
    $returnUrl = $protocol . "://" . $domain . "/php/temp/INIS/INIstdpay_temp_return.php"; // 결제가 완료된이후 진행되는 부분
    $closeUrl = $protocol . "://" . $domain . "/php/temp/INIS/INIstdpay_temp_close.php"; // 창닫기 기능

    $acceptmethod = "centerCd(Y)"; // 이니시스 개발 가이드에 추가옵션을 확인 해당 값들은 HPP(1):below1000:centerCd(Y) 이런식으로 작성 centerCd(Y)값은 필수

    if ($config['isLocal'] || $config['isTest']) { 
        echo '<script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>';
    } else {
        echo '<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>';
    }
?>

<script type="text/javascript">
    function paybtn() {
        INIStdPay.pay('INISTDPAY_FORM');
    }
</script>

<form name="INISTDPAY_FORM" id="INISTDPAY_FORM" method="post" class="mt-5">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="gopaymethod" value="<?= $gopaymethod ?>">
    <input type="hidden" name="mid" value="<?= $mid ?>">
    <input type="hidden" name="oid" value="<?= $orderNumber ?>">
    <input type="hidden" name="price" value="<?= $price ?>">
    <input type="hidden" name="timestamp" value="<?= $timestamp ?>">
    <input type="hidden" name="use_chkfake" value="<?= $use_chkfake ?>">
    <input type="hidden" name="signature" value="<?= $sign ?>">
    <input type="hidden" name="verification" value="<?= $sign2 ?>">
    <input type="hidden" name="mKey" value="<?= $mKey ?>">
    <input type="hidden" name="currency" value="<?= $currency ?>">
    <input type="hidden" name="goodname" value="<?= $goodname ?>">
    <input type="hidden" name="buyername" value="">
    <input type="hidden" name="buyertel" value="">
    <input type="hidden" name="buyeremail" value="">
    <input type="hidden" name="returnUrl" value="<?= $returnUrl ?>">
    <input type="hidden" name="closeUrl" value="<?= $closeUrl ?>">
    <input type="hidden" name="acceptmethod" value="<?= $acceptmethod ?>">
</form>