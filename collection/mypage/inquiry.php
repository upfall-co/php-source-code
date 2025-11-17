<?php
    define("SUB", "MYPAGE");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/inquiry_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="mypage">
        <div class="sub_title">마이페이지</div>

        <div class="wrapper_1400">
            <ul class="sub_depth2_tab">
                <li><a href="<?= artFoldName ?>/mypage/orderhistory.php">주문내역</a></li>
                <li class="active"><a href="<?= artFoldName ?>/mypage/inquiry.php">나의 문의내역</a></li>
                <li><a href="<?= artFoldName ?>/mypage/edit.php">개인정보 수정</a></li>
            </ul>

            <div class="right_btn_wrap">
                <a href="<?= artFoldName ?>/help/inquiry_write.php" class="black_btn shadow_btn">글쓰기</a>
            </div>

            <ul class="inquiry_table table_layout">
                <!------- 테이블 제목 ------->
                <li class="thead">
                    <div class="td_num">번호</div>
                    <div class="td_state">답변상태</div>
                    <div class="td_title">제목</div>
                    <div class="td_writer">작성자</div>
                    <div class="td_date">작성일</div>
                </li>

                <!------- 테이블 내용 ------->
                 <!-------------------------------------- 
                    답변대기 : td_state에 yet 클래스 추가 td_state yet
                    답변완료 : td_state에 end 클래스 추가 td_state end

                    비밀글일 땐 secret 클래스 추가 -> td_title secret
                -------------------------------------->
                <?php getMyList_FAQ(); ?>
            </ul>

            <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/pagination.php" ?>
        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

    <!-- 비밀글 비밀번호 확인 팝업 -->
    <?php include_once "../help/inquiry_pw_chk.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
    function ufn_view_Chk(obj, seq) {
        if ($(obj).hasClass("secret") == true) {
            $("#SEQ").val(seq);

            $(".popup_bg").stop().fadeIn();
            $("#inquiryPwChk").stop().fadeIn();
            $('html, body').addClass("noScroll");
            $("#PASSWORD").focus();
        } else {
            location.href = "<?= artFoldName ?>/help/inquiry_view.php?SEQ="+seq;
        }
    }
</script>