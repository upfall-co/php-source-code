<?php
    define("SUB", "SHOP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/series_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";

?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="series">
        <div class="series_wrapper">
            <?php getList_ArtistSeries(); ?>
        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>