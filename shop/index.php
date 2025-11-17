<?php
define("SUB", "00");
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/shop_index_code.php';

include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <!-- popup -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . '/include/main_popup.php'; ?>
    <!-- //popup -->

    <div id="content">
        <main>

            <section class="mainSec1"> <!------------ 메인 배너 ------------>
                <div class="swiper mainBannerSwiper">
                    <div class="swiper-wrapper">
                        <?php getList_Image(); ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </section>

            <section class="mainSec2"> <!------------ NEW product ------------>
                <div class="padding_50">
                    <div class="title_flex">
                        <div class="font_36_title">New product</div>

                        <div class="arrow_wrap">
                            <div class="swiper-button-prev"><img src="<?= shopFoldName ?>/img/icon_slide_prev.svg" alt="이전"></div>
                            <div class="swiper-button-next"><img src="<?= shopFoldName ?>/img/icon_slide_next.svg" alt="다음"></div>
                        </div>
                    </div>

                    <div class="slide_btn_wrap">
                        <div class="swiper newPrdSwiper">
                            <div class="swiper-wrapper">
                                <?php getList_New(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mainSec3"> <!------------ 상품 탭 ------------>
                <div class="wrapper">
                    <ul class="prd_cate_tab font_36_title">
                        <?php getFirst_Category1(); ?>
                    </ul>

                    <div class="chg_prd_box">
                        <ul>
                        </ul>
                    </div>
                    <a href="<?= shopFoldName ?>/product/list.php" id="chg_prd_box_a" class="show_all_btn">
                        <span>show all</span><img src="<?= shopFoldName ?>/img/icon_show_all.svg" alt="화살표">
                    </a>
                </div>
            </section>

            <section class="mainSec4"> <!------------ 하단 배너 ------------>
                <div class="wrapper">
                    <ul>
                        <li class="link_banner link_banner1">
                            <div class="img_box">
                                <img src="<?= shopFoldName ?>/img/main/main_bt_banner1.jpg" alt="">
                            </div>
                            <div class="txt">
                                <div class="fff">contact</div>
                                <a href="<?= shopFoldName ?>/contact.php">click here</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer_main.php" ?>
    </div>

</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php" ?>