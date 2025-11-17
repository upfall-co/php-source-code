<?php
    define("SUB", "MYPAGE");
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="login">
        <div class="sub_title">비회원 주문조회</div>

        <div class="wrapper_500">
            <ul class="form_wrap">
                <li>
                    <div class="form_label_ani">
                        <input id="orderer_name" name="orderer_name" type="text" placeholder="주문자명">
                        <label title="Orderer">주문자명</label>
                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                    </div>
                </li>
                <li>
                    <div class="form_label_ani">
                        <input id="order_number" name="order_number" type="text" placeholder="주문번호">
                        <label title="OrderNumber">주문번호</label>
                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                    </div>
                </li>
            </ul>

            <div id="goOrderHistoryBtn" class="black_btn shadow_btn" onclick="onNomemberOrderHistory();">주문조회</div>

        </div>

    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
    function onNomemberOrderHistory() {
        if (!ufn_validation()) { //유효성
            return false;
        }

        let NAME = $("#orderer_name").val();
        let SEQ = $("#order_number").val();

        location.href = "<?= artFoldName ?>/order/orderHistory_nomember_result.php?SEQ=" + SEQ + "&NAME=" +NAME;
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