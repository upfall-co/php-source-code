<?php
    define("SUB", "HELP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/notice_view_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="notice">
        <div class="wrapper_1400">
            <div class="sub_title">공지사항</div>

            <div class="notice_view">
                <div class="title_wrap">
                    <div class="td_title"><?=$_db_TITLE?></div>
                    <div class="td_date"><?=$_db_reg_date_nm?></div>
                </div>

                <div class="content">
                    <?=$_db_CONTENT_TEXT?>
                </div>
            </div>

            <a href="javascript:void(0)" onclick="history.back();" class="list_btn border_btn">목록으로</a>

        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>