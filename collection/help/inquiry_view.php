<?php
    define("SUB", "HELP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/inquiry_view_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="inquiry">
        <div class="sub_title">1:1 문의</div>

        <div class="wrapper_1160">
            <ul class="inquiry_view">
                <li class="half">
                    <div class="inquiry_q">이름</div>
                    <div class="inquiry_a">
                        <?=$NAME?>
                    </div>
                </li>
                <li class="half">
                    <div class="inquiry_q">연락처</div>
                    <div class="inquiry_a">
                        <?=$MOBILE?>
                    </div>
                </li>
                <li class="half">
                    <div class="inquiry_q">이메일</div>
                    <div class="inquiry_a">
                        <?=$EMAIL?>
                    </div>
                </li>
                <li class="half">
                    <div class="inquiry_q">주문번호</div>
                    <div class="inquiry_a">
                        <?=$PURCHASE_SEQ?>
                    </div>
                </li>
                <li class="half">
                    <div class="inquiry_q">문의작품</div>
                    <div class="inquiry_a">
                        <?=$PRODUCT_TITLE?>
                    </div>
                </li>
                <li class="half">
                    <div class="inquiry_q">문의분류</div>
                    <div class="inquiry_a">
                        <?=$TYPE_CD_NM?>
                    </div>
                </li>
                <li>
                    <div class="inquiry_q">문의제목</div>
                    <div class="inquiry_a">
                        <?=$TITLE?>
                    </div>
                </li>
                <li>
                    <div class="inquiry_q">문의내용</div>
                    <div class="inquiry_a">
                        <?=$CONTENT_TEXT?>
                    </div>
                </li>
            </ul>
        </div>
        <?php if (!empty($ANSWERS_CONTENT) && $QUESTION_CD == "03") { ?>
            <div class="wrapper_1400 mo_none">
                <div class="inquiry_answer">
                    <div class="title">피크닉 답변</div>
                    <div class="content">
                        <?=$ANSWERS_CONTENT?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <a href="javascript:void(0)" onclick="history.back();" class="list_btn border_btn">목록으로</a>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>