<?php
    define("SUB", "MYPAGE");
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content">

        <main id="nomember" class="page_nomember_history">
            
            <div class="wrapper_500">
                <div class="grey_border">

                    <div class="sub_title font36_bold">비회원 주문조회</div>
                    <ul class="form_wrap">
                        <li>
                            <div class="form_title">주문자명</div>
                            <div class="form_input">
                                <input id="orderer_name" name="orderer_name" type="text">
                            </div>
                        </li>
                        <li>
                            <div class="form_title">주문번호</div>
                            <div class="form_input">
                                <input id="order_number" name="order_number" type="text">
                            </div>
                        </li>
                    </ul>

                    <div id="goOrderHistoryBtn" class="black_btn shadow_btn" onclick="onNomemberOrderHistory();">주문조회</div>
                    <p class="nomember_info">주문번호를 잊으셨다면 <a href="tel:02-318-3233">02-318-3233</a>로 문의하여 주십시오.</p>
                    
                </div>
            </div>

        </main>
        
        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>

<script>
    function onNomemberOrderHistory() {
        if (!ufn_validation()) { //유효성
            return false;
        }

        let NAME = $("#orderer_name").val();
        let SEQ = $("#order_number").val();

        location.href = "<?= shopFoldName ?>/order/orderHistory_result.php?SEQ=" + SEQ + "&NAME=" +NAME;
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("#orderer_name").val() === "") {
            alert("주문자명을 입력해주세요.");
            $("#orderer_name").focus();
            return false
        }

        if ($("#order_number").val() === "") {
            alert("주문번호를 입력해주세요.");
            $("#order_number").focus();
            return false
        }

        return true;
    }
</script>