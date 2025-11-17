<ul class="page_route">
    <li><a href="<?= shopFoldName ?>/index.php">home</a></li>

    <?php if (!empty($_db_CATEGORY1_SEQ)) { ?>
        <li><a href="<?= shopFoldName ?>/product/list.php?cate1=<?= $_db_CATEGORY1_SEQ ?>"><?= $_db_CATEGORY1_NAME ?></a></li>
    <?php } ?>

    <?php if (!empty($_db_CATEGORY2_SEQ)) { ?>
        <li><a href="<?= shopFoldName ?>/product/list.php?cate2=<?= $_db_CATEGORY2_SEQ ?>"><?= $_db_CATEGORY2_TITLE ?></a></li>
    <?php } ?>
</ul>