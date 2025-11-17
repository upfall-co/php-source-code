<?php
    define("SUB", "01");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/category_view_code.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">
    <!-- 상세보기 공통 페이지 -->
    <!-- /sub01/exhibition.php -->
    <!-- /sub02/program.php -->
    <!-- /sub03/collabo.php -->

    <!---------------------------------------- sec1 시작 -->

    <section class="b_view__sec1">

      <div class="b_view__s1_container" data-type="1">

        <div class="visual_slide__container">

          <div class="visual_slide b_view__s1_slide swiper">

            <div class="swiper-wrapper">

              <?= $banner_img ?>

            </div>

          </div>

          <div class="visual_slide__nav_container">

            <button type="button" class="visual_slide__nav visual_slide__nav--prev">
              <img src="<?= homeFoldName ?>/img/slide_prev.svg" alt="">
            </button>

            <button type="button" class="visual_slide__nav visual_slide__nav--next">
              <img src="<?= homeFoldName ?>/img/slide_prev.svg" alt="">
            </button>

          </div>

          <div class="visual_slide__pager_container">
            <div class="visual_slide__pager"></div>
          </div>

        </div>

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->



    <!---------------------------------------- sec2 시작 -->

    <section class="b_view__sec2 blank blank--t2">

      <div class="b_view__s2_container wrapper">

        <div class="info_container">

          <div class="title_container">

            <div class="title_wrap">
              <!-- ↓ 제목 -->
              <h1 class="title"><?= $_db_CONTENT_TITLE ?></h1>
              <!-- ↓ 날짜 -->
              <p class="date"><?= $DATE ?></p>
            </div>


            <?php if (!empty($_db_LINK_URL)) { ?>
                <div class="btn_wrap">
                <!-- ↓ 예매 사이트로 이동 (확실한 정보 필요) -->
                <!-- ※ COLLABO일 시 표기 x -->
                <a href="<?= $_db_LINK_URL ?>" target="_blank">
                    <img src="<?= homeFoldName ?>/img/external.svg" alt="">
                </a>
                </div>
            <?php } ?>

          </div>

          <div class="info_wrap">
            <div class="contents_wrap">
              <!-- ↓ 내용 -->
              <p><?= $_db_CONTENT_TEXT ?></p>
            </div>
          </div>
        </div>

        <!-- ※ COLLABO일 시 표기 x -->
        <div class="relate_container">
            <?php if ($_db_CATEGORY1_SEQ != "COLLABO" && !empty($file_img)) { ?>
                <div class="relate_wrap">

                    <!-- ↓ 해당 상세 페이지가 전시회, 프로그램 등 구분값이 있다면 전달 요청 -->
                    <h2 class="title">related <?=$CATEGORY_NAME?></h2>

                    <ul class="gall__container gall__container--t2 gall__container--hover">

                        <?=$file_img?>

                    </ul>

                </div>
            <?php } ?>

          <div class="back_list_container">

            <a href="<?= $backurl ?>" class="back_btn">
              back to list
            </a>

          </div>

        </div>

      </div>

    </section>

    <!---------------------------------------- sec2 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>