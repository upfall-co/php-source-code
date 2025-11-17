<?php
    // 해당 부분은 이니시스 결제 처리 이후 커스텀 페이지 

    
?>

<form id="frm" method="post" action="/php/order.php" enctype="multipart/form-data">
    <input type="hidden" id="mode" name="mode" value="INS"/>
    <input type="hidden" name="SEQ" value="<?= $_SESSION['INIS']['SEQ'] ?>">
    <input type="hidden" name="PRODUCTS" value="<?= $_SESSION['INIS']['PRODUCTS'] ?>">
    <input type="hidden" name="PAGE_TYPE" value="<?= $_SESSION['INIS']['PAGE_TYPE'] ?>">
    <input type="hidden" name="TYPE" value="<?= $_SESSION['INIS']['TYPE'] ?>">
    <input type="hidden" name="TOTAL_COUNT" value="<?= $_SESSION['INIS']['TOTAL_COUNT'] ?>">
    <input type="hidden" name="TOTAL_PRICE" value="<?= $_SESSION['INIS']['TOTAL_PRICE'] ?>">
    <input type="hidden" name="order_name" value="<?= $_SESSION['INIS']['NAME'] ?>">
    <input type="hidden" name="order_tel" value="<?= $_SESSION['INIS']['MOBILE'] ?>">
    <input type="hidden" name="order_email" value="<?= $_SESSION['INIS']['EMAIL'] ?>">
    <input type="hidden" name="DLVY_NAME" value="<?= $_SESSION['INIS']['DLVY_NAME'] ?>">
    <input type="hidden" name="DLVY_MOBILE" value="<?= $_SESSION['INIS']['DLVY_MOBILE'] ?>">
    <input type="hidden" name="DLVY_EMAIL" value="<?= $_SESSION['INIS']['DLVY_EMAIL'] ?>">
    <input type="hidden" name="DLVY_ADDRESS_ZIPCODE" value="<?= $_SESSION['INIS']['DLVY_ADDRESS_ZIPCODE'] ?>">
    <input type="hidden" name="DLVY_ADDRESS" value="<?= $_SESSION['INIS']['DLVY_ADDRESS'] ?>">
    <input type="hidden" name="DLVY_ADDRESSDETAIL" value="<?= $_SESSION['INIS']['DLVY_ADDRESSDETAIL'] ?>">
    <input type="hidden" name="DLVY_MESSAGE" value="<?= $_SESSION['INIS']['DLVY_MESSAGE'] ?>">
    <input type="hidden" name="REAL_DLVY_PRICE" value="<?= $_SESSION['INIS']['REAL_DLVY_PRICE'] ?>">
    <input type="hidden" name="orderPayType" value="<?= $_SESSION['INIS']['orderPayType'] ?>">
    <input type="hidden" name="INICIS_SEQ" value="<?= $INICIS_SEQ ?>">
    <input type="hidden" name="agreeChk1" value="<?= $_SESSION['INIS']['agreeChk1'] ?>">
    <input type="hidden" name="agreeChk2" value="<?= $_SESSION['INIS']['agreeChk2'] ?>">
    <input type="hidden" name="agreeChk3" value="">
</form>

<script>
    if ("<?=$_REQUEST["resultCode"]?>" == "0000") {
        if ("<?=$resultMap_code?>" == "0000") {
            setTimeout(function() {
                document.getElementById("frm").submit();
            }, 1000); // 1000 밀리초 (1초) 후에 submit 함수 실행
        } else {
            alert("<?=$resultMap_msg?>");
            history.back();
        }
    } else {
        alert("<?=$_REQUEST["resultMsg"]?>");
        history.back();
    }
</script>