<?php
  define("SUB", "06");
  include_once $_SERVER['DOCUMENT_ROOT'] . '/php/contact_code.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="cont__sec1 blank blank--t3">

      <div class="cont__s1_container wrapper">

        <div class="subpage__title_container">

          <h3 class="title">contact</h3>

        </div>

        <div class="cont__s1_contents_container">
          <?php getList_HOME_CONTACT(); ?>
        </div>

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>