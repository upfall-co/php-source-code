<?php
define("SUB", "05");
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/location_code.php';
include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="loca__sec1 blank">

      <div class="loca__s1_container wrapper">

        <?php getList_LOCATION(); ?>
        <!-- <div class="loca__s1_map" id="loca__s1_map"></div>

        <ul class="loca__s1_info_container">

          <li>
            <p class="name">주소</p>
            <div class="contents_wrap">
              <div class="contents">
                서울시 중구 퇴계로6가길 32 (정문/주차장)
                <a href="https://naver.me/57rc0cAl" target="blank">naver</a>
                <a href="https://kko.to/Ik3sgdDJmw" target="blank">kakao</a>
              </div>

              <div class="contents">
                서울시 중구 퇴계로6가길 30 (후문)
                <a href="https://map.naver.com/p/entry/place/1017034682?lng=126.9780778&lat=37.5570119&placePath=%2F&entry=pll&searchType=place&c=15.00,0,0,0,dh" target="blank">naver</a>
                <a href="https://place.map.kakao.com/225473638" target="blank">kakao</a>
              </div>
            </div>
          </li>

          <li>
            <p class="name">운영</p>
            <div class="contents_wrap">
              <div class="contents">
                11:00⎯19:00, 화⎯일 (월요일 휴관)
              </div>
            </div>
          </li>

          <li>
            <p class="name">주차</p>
            <div class="contents_wrap">
              <div class="contents">
                피크닉 정문 발렛 부스 | 최초 90분 3,000원 이후 10분 1,000원
              </div>
            </div>
          </li>

          <li>
            <p class="name">시설</p>
            <div class="contents_wrap">
              <div class="contents">
                물품보관함, 우산보관함, 수유실 | 본관 1층
              </div>
            </div>
          </li>

        </ul> -->

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>