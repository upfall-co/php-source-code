<?php
  define("SUB", "04");
  include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";

  include_once $_SERVER['DOCUMENT_ROOT'] . '/php/space_code.php';
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="spac__sec1 blank blank--t3">

      <div class="spac__s1_container wrapper">
        <div class="subpage__title_container">
            <h3 class="title">SPACE</h3>
            <ul class="category_wrap">

                <li class="active">
                    <a href="/home/sub04/space.php">공간 소개</a>
                </li>

                <li>
                    <a href="/home/sub04/rent.php">대관 문의</a>
                </li>

            </ul>
        </div>

        <div class="spac__s1_contents_container">
          <div class="cap_container">

            <div class="cap_wrap">
              <p>piknic은 일상 속에서 여행하는 방법을 아는 이들에게 도심 속 휴식처가 되어주는 공간입니다.
                깊이 들여다볼 만한 이야기를 찾고 질문을 던지며, 사람들로 하여금 오래 기억에 남을 만한 순간들을 선사합니다.
                남산 자락에 자리한 옛 공간의 흔적들과 자연의 아름다움이 조화를 이루는 이 곳에서 시각 예술, 음악, 문학, 음식 등 다양한 분야의 콘텐츠를 만나보실 수 있습니다.</p>
            </div>

            <div class="inq_wrap">
              <p class="title">공간 문의</p>
              <ul class="info">
                <li><a href="mailto:info@piknic.kr">info@piknic.kr</a></li>
                <li class="separator">|</li>
                <li><a href="tel:02 6245 6372">02 6245 6372</a></li>
              </ul>
            </div>
          </div>

          <picture>
            <source media="(min-width: 768px)" srcset="<?= $MAIN_ATTACH_FILE_ID ?>" id = "plan1" name = "plan1">
            <source media="(min-width: 100px)" srcset="<?= $MAIN_ATTACH_FILE_ID ?>" id = "plan2" name = "plan2">
            <img src="<?= $MAIN_ATTACH_FILE_ID ?>" alt="" id = "plan3" name = "plan3">
          </picture>

        </div>
      </div>
    </section>

    <section class="spac__sec2 blank blank--t4">

      <div class="spac__s2_container wrapper">
        <ul class="spac__s2_tab_wrap">
          <li data-type="SPB1">B1</li>
          <li data-type="SPF1"class="active">1F</li>
          <li data-type="SPF2">2F</li>
          <li data-type="SPF3">3F</li>
          <li data-type="SPF4">4F</li>
          <li data-type="SPAN">별관</li>
        </ul>

        <div class="spac__s2_contents_container">
           <?php getList_SPACE(); ?>
        </div>
      </div>
    </section>

  </main>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>