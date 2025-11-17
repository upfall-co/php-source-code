<?php
    define("SUB", "MYPAGE");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/inquiry_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main id="mypage" class="page_inquiry wrapper">
            <div class="center_sub_title">마이페이지</div>

            <ul class="mypage_tab">
                <li><a href="<?= shopFoldName ?>/mypage/orderhistory.php">주문내역조회</a></li>
                <li class="active"><a href="<?= shopFoldName ?>/mypage/inquiry.php">1:1문의 내역</a></li>
                <li><a href="<?= shopFoldName ?>/mypage/edit.php">개인정보 수정</a></li>
            </ul>
        
            <div class="mypage_title">1:1 문의</div>

            <ul class="inquiry_table">
                <!------- 테이블 제목 ------->
                <li class="thead">
                    <div class="td_date">문의날짜</div>
                    <div class="td_type">문의유형</div>
                    <div class="td_title">제목</div>
                    <div class="td_state">답변상태</div>
                </li>

                <!-------------------------------------- 
                    답변대기 : td_state에 yet 클래스 추가 td_state yet
                    답변완료 : td_state에 end 클래스 추가 td_state end
                -------------------------------------->
                <?php getMyList_FAQ(); ?> 
            </ul>

            <div class="paging_btn_with">
                <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/pagination.php" ?>
                <a href="<?= shopFoldName ?>/inquiry.php" class="border_btn">문의하기</a>
            </div>
        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>

        <!-- 문의 상세 팝업 -->
        <div id ="inquiry_popup">
        
        <div>
    </div>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>

<script>
    // 문의 내역
    function inquiryPop(SEQ) {
        $.ajax({
            type: "GET",
            url: "<?=shopFoldName?>/mypage/inquiry_popup.php?SEQ="+SEQ + "&page_type=<?=PAGE?>", // 실제 경로로 수정해주세요
            success: function(data) {
                document.getElementById("inquiry_popup").innerHTML = data;

                $(".popup_bg").stop().fadeIn();
                $("#inquiryPop").stop().fadeIn();  
                $('html, body').addClass("noScroll");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>