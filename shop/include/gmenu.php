<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/gmenu_code.php';
?>

<header id="moHeader">
	<a href="<?= shopFoldName ?>/index.php" class="header_logo">
		<img src="<?= shopFoldName ?>/img/header_logo.png" alt="piknic shop">
	</a>

	<div class="hamberger" onclick="onMoNavToggle(this);"><span></span></div>
</header>

<header id="header" class="height_100vh">
	<div class="height_100">

		<a href="<?= shopFoldName ?>/index.php" class="header_logo">
			<img src="<?= shopFoldName ?>/img/header_logo.png" alt="piknic shop">
		</a>

		<section class="nav_wrap">
			<nav>
				<ul>
					<li class="depth1">
						<a href="<?= shopFoldName ?>/product/list.php?cate1=NEW">piknic edition</a>
					</li>

					<?php getList_Gmenu(); ?>

					<li class="depth1">
						<a href="<?= shopFoldName ?>/product/list.php?cate1=SALE">sale</a>
					</li>
				</ul>
			</nav>
		</section>

		<section class="sub_wrap">
			<a href="<?= shopFoldName ?>/contact.php">contact</a>
			<a href="<?= shopFoldName ?>/faq.php">faq</a>
		</section>

		<section class="cart_login_wrap">
			<ul>
				<li class="loginChk">
					<?php if (!$login_chk) { ?>	
						<!-- 비로그인 시  -->
						<a href="<?= shopFoldName ?>/mypage/login.php" class="chk_noLogin">
							<img src="<?= shopFoldName ?>/img/icon_member.svg" alt="login">
							<p><span>login</span></p>
						</a>
					<?php } else { ?>
						<!-- 로그인 시  -->
						<a href="<?= shopFoldName ?>/mypage/orderhistory.php" class="chk_login">
							<img src="<?= shopFoldName ?>/img/icon_member.svg" alt="my account">
							<p><span>my account</span></p>
						</a>

						<a href="javascript:void(0);" onclick="member_logout();" class="chk_login">
							<img src="<?= shopFoldName ?>/img/icon_logout.svg" alt="logout">
							<p><span>logout</span></p>
						</a>
					<?php } ?>
				</li>
				<li>
					<a href="<?= shopFoldName ?>/order/cart.php">
						<img src="<?= shopFoldName ?>/img/icon_cart.svg" alt="cart">
						<p><span>cart</span> <?php getCart_Count(); ?></p>
					</a>
				</li>
			</ul>
		</section>

		<section class="search_wrap">
				<!-- 검색바 -->
				<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/searchbar.php" ?>
		</section>
	</div>
</header>

<!-- piknic collection -->
<nav id="fixed_nav">
	<a href="<?= homeFoldName ?>/">piknic</a>
	<a href="<?= artFoldName ?>/">collection</a>
</nav>

<!------- 우측으로 나오는 베스트셀러 & 추천상품 ------->
<div id="navAddPrdBox">
	<div class="inner">
		<section class="prdBox prdBox_best">
			
		</section>
		<section class="prdBox prdBox_recommend">
			
		</section>
	</div>
</div>

<div id="navAddBg" onclick="sideNavClose();"></div>

<div class="popup_bg" onclick="popClose();"></div>

<script>
	function member_logout() {
		if (confirm("로그아웃 하시겠습니까?")) {
			location.href = '/php/member_logout.php';
		}
	}

	function ufn_change_location(newSearch) {
		window.location.href = '<?= shopFoldName ?>/search_result.php?'+ newSearch;
	}
</script>