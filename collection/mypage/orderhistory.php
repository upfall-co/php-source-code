<?php
    define("SUB", "MYPAGE");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/orderhistory_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="mypage">
        <div class="sub_title">마이페이지</div>

        <div class="wrapper_1400">
            <ul class="sub_depth2_tab">
                <li class="active"><a href="<?= artFoldName ?>/mypage/orderhistory.php">주문내역</a></li>
                <li><a href="<?= artFoldName ?>/mypage/inquiry.php">나의 문의내역</a></li>
                <li><a href="<?= artFoldName ?>/mypage/edit.php">개인정보 수정</a></li>
            </ul>

            
                <div class="right_btn_wrap">
                    <form id="frm" method="get" action="<?=$_SERVER['PHP_SELF'];?>">
                        <div class="date_wrap">
                            <input type="date" id="start_date" name="start_date" value="<?=$start_date?>">
                            <span>-</span>
                            <input type="date" id="end_date" name="end_date" value="<?=$end_date?>">
                        </div>

                        <button type="submit" class="black_btn shadow_btn">조회</button>
                    </form>
                </div>
            

            <ul class="order_table table_layout">
                <!------- 테이블 제목 ------->
                <li class="thead">
                    <div class="td_order_date">주문일자</div>
                    <div class="td_order_number">주문번호</div>
                    <div class="td_order_name">작품명</div>
                    <div class="td_order_price">총 주문금액</div>
                    <div class="td_order_state">주문상태</div>
                </li>

                <!------- 테이블 내용 ------->
                <?php getList_Order(); ?>
            </ul>

        </div>

        <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/pagination.php" ?>

    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

    <!-- 주문 상세 팝업 -->
    <div id ="orderHistory">
        
    <div>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
    function history_pop(SEQ) {
        $.ajax({
            type: "GET",
            url: "<?=artFoldName?>/order/orderHistory_pop.php?SEQ="+SEQ, // 실제 경로로 수정해주세요
            success: function(data) {
                document.getElementById("orderHistory").innerHTML = data;

                $(".popup_bg").stop().fadeIn();
                $("#orderHistoryPop").stop().fadeIn();
                $('html, body').addClass("noScroll");

                /* 전체 동의 */
                function allCheckFunc(obj) {
                    $("[name=prdChk]").prop("checked", $(obj).prop("checked"));
                }

                /* 체크박스 체크시 전체선택 체크 여부 */
                function oneCheckFunc(obj) {
                    var allObj = $("[name=prdChkAll]");
                    var objName = $(obj).attr("name");

                    if ($(obj).prop("checked")) {
                        checkBoxLength = $("[name=" + objName + "]").length;
                        checkedLength = $("[name=" + objName + "]:checked").length;

                        if (checkBoxLength == checkedLength) {
                            allObj.prop("checked", true);
                        } else {
                            allObj.prop("checked", false);
                        }
                    } else {
                        allObj.prop("checked", false);
                    }
                }

                $(function() {
                    $("[name=prdChkAll]").click(function() {
                        allCheckFunc(this);
                    });

                    $("[name=prdChk]").each(function() {
                        $(this).click(function() {
                            oneCheckFunc($(this));
                        });
                    });
                });

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>
