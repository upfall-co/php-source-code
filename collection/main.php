<?php
    define("SUB", "00");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/main_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body id="height100">

    <!-- popup -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . '/include/main_popup.php'; ?>
    <!-- //popup -->

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="main" class="mainFooter">
        <ul class="main_list pc">
            <?php getList_Image_PC(); ?>
        </ul>

        <div class="swiper mainMoSwiper">
          <div class="swiper-wrapper">
              <?php getList_Image_MO(); ?>
            </div>
            <div class="swiper-button-prev"><img src="<?= artFoldName ?>/img/main/main_prev.png" alt="이전"></div>
            <div class="swiper-button-next"><img src="<?= artFoldName ?>/img/main/main_next.png" alt="다음"></div>
            <div class="mo_slide_set">
                <div class="swiper-pagination"></div>
                <?php getFirst_Series(); ?> 
            </div>
        </div>

    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer_main.php" ?>
</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php" ?>

<script>
    const setVh = () => {
        document.documentElement.style.setProperty('--vh', `${window.innerHeight}px`)
    };

    window.addEventListener('resize', setVh);
    setVh();

    $("#footer_toggle").click(function() {
        $("body").toggleClass("noScroll");
    });
</script>