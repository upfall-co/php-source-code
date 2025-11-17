<?php
    define("SUB", "MYPAGE");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/shop_orderhistory_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main id="mypage" class="page_orderhistory wrapper">
            <div class="center_sub_title">마이페이지</div>

            <ul class="mypage_tab">
                <li class="active"><a href="<?= shopFoldName ?>/mypage/orderhistory.php">주문내역조회</a></li>
                <li><a href="<?= shopFoldName ?>/mypage/inquiry.php">1:1문의 내역</a></li>
                <li><a href="<?= shopFoldName ?>/mypage/edit.php">개인정보 수정</a></li>
            </ul>
        
            <div class="mypage_title">주문내역조회</div>

            <ul class="order_table list_table">
                <?php getList_Order(); ?>
            </ul>

            <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/pagination.php" ?>

        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>

        <!-- 택배 정보 조회 팝업 -->
        <div id ="tracking_popup">
        
        <div>
    <div>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>

<script>
    // 택배 정보 조회 팝업
    function onTrackingPop(SEQ) {
        $.ajax({
            type: "GET",
            url: "<?=shopFoldName?>/mypage/delivery_tracking_popup.php?SEQ="+SEQ + "&page_type=<?=PAGE?>", // 실제 경로로 수정해주세요
            success: function(data) {
                document.getElementById("tracking_popup").innerHTML = data;

                $(".popup_bg").stop().fadeIn();
                $("#trackingPop").stop().fadeIn();  
                $('html, body').addClass("noScroll");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>

