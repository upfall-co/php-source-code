<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/gmenu_code.php';
?>
<header>

    <!-- <div class="header__root_container">
        <ul>
            <li>
                <a href="<?= shopFoldName ?>">
                    <img src="<?= homeFoldName ?>/img/header__root_shop.svg" alt="">
                </a>
            </li>
            <li>
                <a href="<?= artFoldName ?>">
                    <img src="<?= homeFoldName ?>/img/header__root_piknic.svg" alt="">
                </a>
            </li>
        </ul>
    </div> -->

    <div class="header__wrap">

        <div class="header__ham_wrap">
            <button class="header__ham_btn" onclick="headerBtnInter(this)">
                <!-- <span></span>
                <span></span>
                <span></span> -->
                <img src="<?= homeFoldName ?>/img/header__ham.svg" alt="">
            </button>
        </div>


        <a href="<?= homeFoldName ?>/index.php" class="header__logo">
            <img src="<?= homeFoldName ?>/img/logo_sm.svg" alt="">
        </a>

        <nav>
            <ul>
                <li class="depth1">
                    <a href="#">ticket</a>
                </li>
                <li class="depth1">
                    <a href="<?= shopFoldName ?>/">shop</a>
                </li>
                <li class="depth1">
                    <a href="<?= artFoldName ?>/">collection</a>
                </li>
            </ul>
        </nav>

    </div>

</header>

<div class="header__mo">

    <nav>
        <ul>
        </ul>
    </nav>

    <div class="other_depth__container">

        <ul class="other_depth">

            <li>
                <a href="<?= homeFoldName ?>/sub06/contact.php">contact</a>
            </li>

            <li class="pb--30 border--bot_1_E6E6E6">
                <a href="<?= homeFoldName ?>/sub06/recruit.php">recruit</a>
            </li>

            <li class="pb--30 border--bot_1_E6E6E6">
                <?php if (!$login_chk) { ?>
                    <a href="<?= shopFoldName ?>/mypage/login.php">login</a>
                <?php } else { ?>
                    <a href="javascript:void(0);" onclick="member_logout();">logout</a>
                <?php } ?>
            </li>

        </ul>

        <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/searchbar.php" ?>

    </div>

</div>

<script>
    function member_logout() {
		if (confirm("로그아웃 하시겠습니까?")) {
			location.href = '/php/member_logout.php';
		}
	}
    function ufn_change_location(newSearch) {
        window.location.href = '<?= homeFoldName ?>/sub02/program.php?' + newSearch;
        console.log(newSearch);
    }
</script>