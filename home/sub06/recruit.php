<?php
define("SUB", "06");
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";

include_once $_SERVER['DOCUMENT_ROOT'] . '/php/recruit_code.php';
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="recr__sec1 blank blank--t3">

      <div class="recr__s1_container wrapper">

        <div class="subpage__title_container">

          <h3 class="title">recruit</h3>

        </div>

        <!-- ※ 마무리 작업 필요 -->
        <div class="recr__s1_contents_container">

          <!---------- ↓ 조직 카테고리 컴포넌트 시작 -->
          <div class="recr__s1_depth1_container recr__s1_depth_container_style">

            <!---------- ↓ pc 채용 탭 컴포넌트 시작 -->
            <!-- 내용은 둘 다 같음 -->
            <ul class="recr__s1_depth1--pc">
              <?php getList_RECRUIT(); ?>
            </ul>
            <!---------- pc 채용 탭 컴포넌트 끝 -->

            <!---------- ↓ mo 채용 탭 컴포넌트 시작 -->
            <!-- 내용은 둘 다 같음 -->
            <div class="recr__s1_depth1--mo">

              <select name="" id="">
                <option>선택</option>
                <?php getList_MO_RECRUIT(); ?>
              </select>

            </div>
            <!---------- mo 채용 탭 컴포넌트 끝 -->

          </div>
          <!---------- 조직 카테고리 컴포넌트 끝 -->



          <!---------- ↓ 채용 포지션 컴포넌트 시작 -->
          <div class="recr__s1_depth2_container">

            <ul class="recr__s1_depth2">
            </ul>

          </div>
          <!---------- 채용 포지션 컴포넌트 끝 -->



          <!---------- ↓ 채용 상세정보 컴포넌트 시작 -->
          <div class="recr__s1_depth3_container">

            <div class="recr__s1_depth3_wrap">
              <ul class="recr__s1_depth3">
              </ul>

            </div>

          </div>
          <!---------- 채용 상세정보 컴포넌트 끝 -->

        </div>

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>