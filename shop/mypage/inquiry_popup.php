
<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/inquiry_popup_code.php';
?>

<div id="inquiryPop" class="popup find_popup">
    <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>

    <div class="inquiry_top">
        <div class="find_txt">
            <div class="big">
                <?=$TITLE?>
            </div>
            
            <ul>
                <li><span class="td_type"><?=$TYPE_CD_NM?></span></li>
                <li>문의날짜&nbsp;&nbsp;<span class="td_date"><?=$_db_reg_date_nm?></span></li>
            </ul>
        </div>
    </div>

    <ul class="inquiry_table border_top">
        <li class="inquiry_q">
            <div class="lt">문의내용</div>
            <div class="rt">
                <?=$CONTENT_TEXT?>
            </div>
        </li>
        <?php if (!empty($ANSWERS_CONTENT) && $QUESTION_CD == "03") { ?>
            <li class="inquiry_a">
                <div class="lt">답변</div>
                <div class="rt">
                    <?=$ANSWERS_CONTENT?>
                </div>
            </li>
        <?php } ?>
    </ul>


    <div class="pop_btn_wrap two_btn">
        <div class="black_btn shadow_btn" onclick="location.href='<?= shopFoldName ?>/inquiry.php?TYPE_CD=<?=$TYPE_CD?>'">추가 문의하기</div>
        <div class="border_btn" onclick="popClose();">확인</div>
    </div>
</div>