<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/gmenu_code.php';
?>

<div id="header" class="header_lt">
    <div class="hamburger" id="hamburger-6">
        <img src="<?= artFoldName ?>/img/menu_bar.svg" alt="햄버거메뉴">
    </div>

    <a href="<?= artFoldName ?>/main.php" class="logo"><img src="<?= artFoldName ?>/img/header_logo.svg" alt="piknic"></a>
</div>

<!----- 로그인 전 ----->
<?php if (!$login_chk) {?>
    <div id="header" class="header_rt">
        <a href="<?= artFoldName ?>/order/cart.php">Cart</a>
        <a href="<?= artFoldName ?>/mypage/login.php">Login</a>
        <a href="<?= artFoldName ?>/mypage/join.php">Join</a>
    </div>
<?php } else {?>
    <!----- 로그인 후 ----->
    <div id="header" class="header_rt">
        <a href="<?= artFoldName ?>/order/cart.php">Cart</a>
        <a href="<?= artFoldName ?>/mypage/orderhistory.php">Mypage</a>
        <a href="javascript:void(0);" onclick="member_logout();">Logout</a>
    </div>
<?php }?>


<div id="hamBg"></div>
<div id="hamWrap">

    <nav class="hamNav">
        <?php getList_Gmenu(); ?>
        
        <ul class="help_cate">
            <li class="depth2">
                <ul>
                    <li><a href="<?= artFoldName ?>/help/faq.php">구매안내</a></li>
                    <li><a href="<?= artFoldName ?>/help/notice.php">공지사항</a></li>
                    <li><a href="<?= artFoldName ?>/help/inquiry.php">1:1 문의</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- 검색바 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/searchbar.php" ?>
</div>

<div class="popup_bg" onclick="popClose();"></div>

<script>
    function member_logout() {
        if (confirm("로그아웃 하시겠습니까?")) {
            location.href = '/php/member_logout.php';
        }
    }

    function ufn_change_location(newSearch) {
        window.location.href = '<?= artFoldName ?>/shop/search_result.php?'+ newSearch;
    }
</script>