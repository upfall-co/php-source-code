<?php
    define("SUB", "ORDER");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/order_end_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="orderEnd">
        <div class="wrapper_1400">

            <div class="sub_title">주문이 완료되었습니다.</div>

            <div class="order_title">결제정보</div>
            <ul class="order_end_table">
                <li class="half">
                    <div class="lt">결제수단</div>
                    <div class="rt"><?=$TYPE_NM?></div>
                </li>
                <li class="half">
                    <div class="lt">예금주</div>
                    <div class="rt"><?=$NO_BANK_NAME?></div>
                </li>
                <li class="half">
                    <div class="lt">주문작품</div>
                    <div class="rt"><?=$CATEGORY3_NAME?></div>
                </li>
                <li class="half">
                    <div class="lt">입금은행</div>
                    <div class="rt"><?=$NO_BANK_CD_NM?></div>
                </li>
                <li class="half">
                    <div class="lt">결제요청금액</div>
                    <div class="rt"><?=$TOTAL_PRICE_TEXT?>원</div>
                </li>
                <li class="half">
                    <div class="lt">입금계좌</div>
                    <div class="rt"><?=$NO_BANK_ACCOUNT?></div>
                </li>
                <li class="half">
                    <div class="lt">주문번호</div>
                    <div class="rt"><?=$PURCHASE_SEQ?></div>
                </li>
                <li class="half">
                    <div class="lt">입금기한</div>
                    <div class="rt"><?=$NO_BANK_DATE_NM?></div>
                </li>
            </ul>

            <div class="btn_wrap">
                <a href="<?= artFoldName ?>/main.php" class="border_btn w_300">메인페이지</a>
                <button type="button" onclick="onOrderHistoryPop();" class="border_btn w_300">주문상세보기</button>
            </div>

        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

    <!-- 주문 상세 팝업 -->
    <?php include_once "./orderHistory_pop.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
    function onOrderHistoryPop() {
        $(".popup_bg").stop().fadeIn();
        $("#orderHistoryPop").stop().fadeIn();
        $('html, body').addClass("noScroll");
    }


    /* 아래의 스크립트는 상세주문내역과 관련된 내역 */

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