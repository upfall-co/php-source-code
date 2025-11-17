<?php
    define("SUB", "03");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";

    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/category_code.php';
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="collab__sec1 blank blank--t3">

      <div class="collab_s1_container wrapper">

        <div class="subpage__title_container">

          <h3 class="title">collaboration</h3>

        </div>

        <!-- ↓ 갤러리 게시판 2형 공통 사용 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/_comp/_galleryType2.php" ?>

        <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/pagination.php" ?>

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>