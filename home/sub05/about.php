<?php
define("SUB", "05");
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="about__sec1 blank blank--t5">

      <div class="about__s1_container wrapper">

        <div class="about__s1_cap_container">

          <p class="main_cap">서울로와 남산을 잇는 회현동 언덕에 위치한 피크닉 piknic은 도심에서 소풍 같은 휴식을 누릴 수 있는 공간입니다.
            전시를 관람하고, 좋은 음식을 먹고, 나무 그늘에서 책을 읽거나 편안한 음악이 흐르는 곳에서 담소를 나눌 수 있는, 모두를 위한 휴식처이자 예술과 문화가 있는 지적 경험을 제공합니다.
          </p>

        </div>

        <div class="about__s1_slide_container">

          <div class="about__s1_slide swiper">

            <div class="swiper-wrapper">

              <div class="swiper-slide">
                <figure>
                  <img src="<?= homeFoldName ?>/img/sub05/about__s1_img03.jpeg" alt="">
                </figure>
              </div>

              <div class="swiper-slide">
                <figure>
                  <img src="<?= homeFoldName ?>/img/sub05/about__s1_img03.jpeg" alt="">
                </figure>
              </div>

            </div>

            <div class="about__s1_nav_container">

              <button type="button" class="about__s1_nav about__s1_nav--prev">
                <img src="<?= homeFoldName ?>/img/slide_prev.svg" alt="">
              </button>

              <button type="button" class="about__s1_nav about__s1_nav--next">
                <img src="<?= homeFoldName ?>/img/slide_prev.svg" alt="">
              </button>

            </div>

          </div>

        </div>

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>