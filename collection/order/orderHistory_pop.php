<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/orderHistory_pop_code.php';
?>
<div id="orderHistoryPop" class="popup find_popup order_popup">
    <input type="hidden" id="SEQ" name="SEQ" value="<?=$PURCHASE_SEQ?>"/> 
    <input type="hidden" id="TYPE_CD" name="TYPE_CD" value="<?=$TYPE_CD?>"/> 
    <input type="hidden" id="STATE_CD" name="STATE_CD" value="<?=$STATE_CD?>"/> 
    <input type="hidden" id="INICIS_SEQ" name="INICIS_SEQ" value="<?=$INICIS_SEQ?>"/> 
    <input type="hidden" id="TOTAL_NOW_PRICE" name="TOTAL_NOW_PRICE" value="<?=$TOTAL_NOW_PRICE?>"/> 
    <input type="hidden" id="REAL_DLVY_PRICE" name="REAL_DLVY_PRICE" value="<?=$REAL_DLVY_PRICE?>"/>
    <input type="hidden" id="MOBILE" name="MOBILE" value="<?=$_db_MOBILE?>"/>

    <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>
    <div class="find_txt">
        <div class="big">상세주문내역</div>
    </div>

    <div class="wrapper_1400">
        <div class="order_history_info">
            <p>주문번호 : <span><?= $PURCHASE_SEQ ?></span></p>
            <p>주문일자 : <span><?= $reg_date_nm ?></span></p>
        </div>

        <!-- 상품 체크 리스트 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/order/prd_list_chk.php" ?>

        <!-- 최종 결제 금액 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/order/final_pay_price.php" ?>

        <div class="btn_wrap">
            <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="popClose();">확인</a>
            <?php if ( $STATE_MODE == "CANCEL") {?>
                <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="Order_Cancel('41');">주문취소</a>
            <?php } else {?> 
                <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="Order_Cancel('51');">환불요청</a>
            <?php } ?>
        </div>

    </div>
</div>