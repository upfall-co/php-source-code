<?php
    define("SUB", "HELP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/faq_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="faq">
        <div class="wrapper_1400">
            <div class="sub_title">구매안내</div>

            <ul class="sub_depth2_tab faq_tab">
                <li class="active">전체</li>
                <li>제품관련</li>
                <li>결제관련</li>
                <li>배송관련</li>
            </ul>

            <ul class="faq_list">
                <!--------------------------------------
                제품관련 : li에 faq_prd 클래스 추가
                결제관련 : li에 faq_pay 클래스 추가
                배송관련 : li에 faq_ship 클래스 추가
                -------------------------------------->
                <?php getList_FAQ(); ?>
            </ul>

            <div class="faq_bt">
                <span>더 궁금하신 점이 있다면?</span>
                <a href="<?= artFoldName ?>/help/inquiry_write.php" class="border_btn inquiry_btn">문의하기</a>
            </div>

        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
    $(document).ready(function() {

        // faq 탭
        $(".faq_tab li").click(function() {
            $(".faq_tab li").removeClass("active");
            $(this).addClass("active");
            $(".faq_list .q_wrap").removeClass("open");
            $(".faq_list .q_wrap").siblings(".a_wrap").hide();

            var tabIdx = $(this).index()

            if (tabIdx == 0) { // 전체
                $(".faq_list li.faq_prd").show();
                $(".faq_list li.faq_pay").show();
                $(".faq_list li.faq_ship").show();
            } else if (tabIdx == 1) { // 제품관련
                $(".faq_list li.faq_prd").show();
                $(".faq_list li.faq_pay").hide();
                $(".faq_list li.faq_ship").hide();
            } else if (tabIdx == 2) { // 결제관련
                $(".faq_list li.faq_prd").hide();
                $(".faq_list li.faq_pay").show();
                $(".faq_list li.faq_ship").hide();
            } else if (tabIdx == 3) { // 배송관련
                $(".faq_list li.faq_prd").hide();
                $(".faq_list li.faq_pay").hide();
                $(".faq_list li.faq_ship").show();
            }
        });

        // faq 토클
        $(".faq_list .q_wrap").click(function() {
            $(this).toggleClass("open");
            $(this).siblings(".a_wrap").stop().slideToggle(250);
        });
    });
</script>