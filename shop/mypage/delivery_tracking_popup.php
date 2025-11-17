
<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/delivery_pop_code.php';
?>

<div id="trackingPop" class="popup find_popup">
    <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>
    <div class="find_txt">
        <div class="big">택배 정보 조회</div>
    </div>

    <!-- 배송정보 입력 후(배송중 ~ 배송완료 단계)  -->
    <ul class="tracking_table border_top">
        <li>
            <div class="lt">택배사</div>
            <div class="rt">로젠택배</div>
        </li>
        <li class="stretch">
            <div class="lt">배송 제품</div>
            <div class="rt">
                <?=getPrdChkList()?>
            </div>
        </li>
    </ul>

    <!-- 배송정보 입력 전(주문접수 ~ 배송준비중 단계)  -->
    <!-- <div class="no_tracking_data">아직 택배 정보가 등록되지 않았습니다.</div> -->

    <div class="pop_btn_wrap">
        <div class="chkType1 black_btn shadow_btn" onclick="popClose();">확인</div>
    </div>
</div>