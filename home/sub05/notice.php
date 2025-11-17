<?php
define("SUB", "05");
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/notice_code.php';
include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="noti__sec1 blank">

      <div class="noti__s1_container wrapper">

        <div class="subpage__title_container">
          <h3 class="title">notice</h3>
        </div>

        <div class="noti__s1_contents_container">

          <div class="noti__s1_contents_wrap">
            <?php getList_HOME_NOTICE("NOL");?>
          </div>



          <!---------- ↓ 기타 안내 컴포넌트 시작 -->
          <div class="noti__s1_etc_wrap">

            <?php getList_HOME_NOTICE("ETC");?>

          </div>
          <!---------- 기타 안내 컴포넌트 끝 -->

        </div>

      </div>

    </section>

    <!---------------------------------------- sec1 끝 -->

  </main>


  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>