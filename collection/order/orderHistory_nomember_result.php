<?php
    define("SUB", "ORDER");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/orderHistory_nomember_result_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] .artFoldName. "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] .artFoldName. "/include/gmenu.php" ?>

    <main id="orderHistory">
        <input type="hidden" id="SEQ" name="SEQ" value="<?=$PURCHASE_SEQ?>"/> 
        <input type="hidden" id="TYPE_CD" name="TYPE_CD" value="<?=$TYPE_CD?>"/> 
        <input type="hidden" id="STATE_CD" name="STATE_CD" value="<?=$STATE_CD?>"/> 
        <input type="hidden" id="INICIS_SEQ" name="INICIS_SEQ" value="<?=$INICIS_SEQ?>"/>
        <input type="hidden" id="TOTAL_NOW_PRICE" name="TOTAL_NOW_PRICE" value="<?=$TOTAL_NOW_PRICE?>"/> 
        <input type="hidden" id="REAL_DLVY_PRICE" name="REAL_DLVY_PRICE" value="<?=$REAL_DLVY_PRICE?>"/>
        <input type="hidden" id="MOBILE" name="MOBILE" value="<?=$_db_MOBILE?>"/>
        
        <div class="sub_title">비회원 주문조회</div>

        <div class="wrapper_1400">
            <div class="order_history_info">
                <p>주문번호 : <span><?= $PURCHASE_SEQ ?></span></p>
                <p>주문일자 : <span><?= $reg_date_nm ?></span></p>
            </div>

            <!-- 상품 체크 리스트 -->
            <?php include_once "./prd_list_chk.php"?> 

            <!-- 최종 결제 금액 -->
            <?php include_once "./final_pay_price.php"?> 

            <div class="btn_wrap">
            <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="Order_nomember();">비회원 주문 조회</a>
                <?php if ( $STATE_MODE == "CANCEL") {?>
                    <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="Order_Cancel('41');">주문취소</a>
                <?php } else {?> 
                    <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="Order_Cancel('51');">환불요청</a>
                <?php } ?>
            </div>

        </div>

    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] .artFoldName. "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] .artFoldName. "/include/bottom.php"; ?>

<script>
    function Order_nomember() {
        location.href = "<?= artFoldName ?>/order/orderHistory_nomember.php";
    }

  /* 전체 동의 */
    function allCheckFunc(obj) {
        $("[name=prdChk]").prop("checked", $(obj).prop("checked"));
    }

    /* 체크박스 체크시 전체선택 체크 여부 */
    function oneCheckFunc(obj) {
        var allObj = $("[name=prdChkAll]");
        var objName = $(obj).attr("name");

        if ($(obj).prop("checked")) {
            checkBoxLength = $("[name=" + objName + "]").length;
            checkedLength = $("[name=" + objName + "]:checked").length;

            if (checkBoxLength == checkedLength) {
                allObj.prop("checked", true);
            } else {
                allObj.prop("checked", false);
            }
        } else {
            allObj.prop("checked", false);
        }
    }

    $(function() {
        $("[name=prdChkAll]").click(function() {
            allCheckFunc(this);
        });

        $("[name=prdChk]").each(function() {
            $(this).click(function() {
                oneCheckFunc($(this));
            });
        });
    });
</script>