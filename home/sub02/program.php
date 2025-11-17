<?php
    define("SUB", "02");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";

    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/category_code.php';
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="prog__sec1 blank blank--t3">

      <div class="prog_s1_container wrapper">

        <div class="subpage__title_container">
          <?php if(empty($search_text)) { ?>
            <h3 class="title">PROGRAM</h3>
          <?php } else { ?>
            <h3 class="title"><?=$search_text?></h3>
          <?php } ?>

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