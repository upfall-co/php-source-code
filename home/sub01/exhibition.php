<?php
    define("SUB", "01");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";

    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/category_code.php';
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="exh__sec1 blank blank--t3">

      <div class="exh__s1_container wrapper">

        <div class="subpage__title_container">

          <h3 class="title">exhibition</h3>

          <ul class="category_wrap">

            <!-- ↓ 기획전 카테고리 value 값 전달 요청 -->
            <li data-cate="EXHIBITION_01">
              <a href="<?= homeFoldName ?>/sub01/exhibition.php?cate=EXHIBITION&cate2=EXHIBITION_01">기획전</a>
            </li>

            <!-- ↓ 기획전 카테고리 value 값 전달 요청 -->
            <li data-cate="EXHIBITION_ETC">
              <a href="<?= homeFoldName ?>/sub01/exhibition.php?cate=EXHIBITION&cate2=EXHIBITION_ETC">기타</a>
            </li>

          </ul>

        </div>

         <!-- ↓ 갤러리 게시판 1형 공통 사용 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/_comp/_galleryType1.php" ?>
        
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/pagination.php" ?>

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>