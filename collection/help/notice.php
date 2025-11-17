<?php
    define("SUB", "HELP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/notice_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="notice">
        <div class="wrapper_1400">
            <div class="sub_title">공지사항</div>

            <ul class="notice_table table_layout">
                <li class="thead">
                    <div class="td_num">번호</div>
                    <div class="td_title">제목</div>
                    <div class="td_date">작성일</div>
                </li>

                <?php getList_INFORM(); ?>
            </ul>


            <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/pagination.php" ?>
        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>