<?php
define("SUB", "00");
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";
?>

<body>

  <!-- 상단 헤더 -->
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <!-- popup -->
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . '/include/main_popup.php'; ?>
  <!-- //popup -->

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="idx__sec1">

      <div class="idx__s1_container">

        <div class="visual_slide__container">

          <div class="visual_slide idx__s1_slide swiper">

            <div class="swiper-wrapper">
                <?php getHomeList_Image(); ?>
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

    <section class="idx__sec2 blank blank--t1">

      <div class="idx__s2_container wrapper">

        <ul class="idx__s2_contents_container">
          <?php getHomeList_EXHIBITION(); ?>
          <?php getHomeList_PROGRAM(); ?>
          <?php getHomeList_SHOP(); ?>
        </ul>

      </div>

    </section>

    <!---------------------------------------- sec2 끝 -->



    <!---------------------------------------- sec3 시작 -->

    <section class="idx__sec3 blank blank--bottom">

      <div class="idx__s3_container wrapper">

        <div class="idx__s3_contents_container">

          <div class="idx__s3_contents_wrap">

            <a href="<?= homeFoldName ?>/sub05/notice.php" class="title_wrap">
              notice
              <img src="<?= homeFoldName ?>/img/arrow--t1.svg" alt="">
            </a>

            <ul class="info_wrap">
              <?php getHomeList_INFORM(); ?>
            </ul>

          </div>

          <div class="idx__s3_contents_wrap">

            <a href="<?= homeFoldName ?>/sub05/location.php" class="title_wrap">
              location
              <img src="<?= homeFoldName ?>/img/arrow--t1.svg" alt="">
            </a>

            <ul class="info_wrap info_wrap--auto">
              <?php getHomeList_LOCATION() ?>
            </ul>

          </div>

        </div>

      </div>

    </section>

    <!---------------------------------------- sec3 끝 -->

  </main>


  <!-- 하단 푸터 -->
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/modal.php" ?>

</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>