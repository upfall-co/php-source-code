<?php
    define("SUB", "ORDER");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/order_end_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main id="orderEnd">
            <div class="wrapper">

                <!-- 상단 현재 위치 / 서브 타이틀 영역 -->
                <div class="sub_title_wrap wrapper">
                    <ul class="page_route">
                        <li><a href="<?= shopFoldName ?>/index.php">home</a></li>
                        <li><p>cart</p></li>
                        <li><p>order</p></li>
                        <li><p>order confirmed</p></li>
                    </ul>
                    <div class="page_title">구매가 완료되었습니다.</div>
                </div>

                <div class="order_title">결제정보</div>
                <ul class="order_end_table border_top">
                    <li class="half">
                        <div class="lt">결제수단</div>
                        <div class="rt"><?=$TYPE_NM?></div>
                    </li>
                    <li class="half">
                        <div class="lt">예금주</div>
                        <div class="rt"><?=$NO_BANK_NAME?></div>
                    </li>
                    <li class="half">
                        <div class="lt">주문제품</div>
                        <div class="rt"><?=$CATEGORY3_NAME?></div>
                    </li>
                    <li class="half">
                        <div class="lt">입금은행</div>
                        <div class="rt"><?=$NO_BANK_CD_NM?></div>
                    </li>
                    <li class="half">
                        <div class="lt">결제요청금액</div>
                        <div class="rt"><?=$TOTAL_PRICE_TEXT?>원</div>
                    </li>
                    <li class="half">
                        <div class="lt">입금계좌</div>
                        <div class="rt"><?=$NO_BANK_ACCOUNT?></div>
                    </li>
                    <li class="half">
                        <div class="lt">주문번호</div>
                        <div class="rt"><?=$PURCHASE_SEQ?></div>
                    </li>
                    <li class="half">
                        <div class="lt">입금기한</div>
                        <div class="rt"><?=$NO_BANK_DATE_NM?></div>
                    </li>
                </ul>

                <div class="btn_wrap">
                    <a href="<?= shopFoldName ?>/index.php" class="border_btn w_300">home</a>
                    <!-- 회원 구매 시 (비회원 주문 시 my account 버튼 삭제) -->
                    <?php if ($login_chk) {?>
                        <a href="<?= shopFoldName ?>/mypage/orderhistory.php" class="border_btn w_300">my account</a>
                    <?php }?>
                </div>

            </div>
        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>
    
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>