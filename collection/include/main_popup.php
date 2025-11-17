<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

if (!isset($mysqldb) || $mysqldb === null) {
    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();
}

//변수 정리
$today      = date('Y-m-d');
$isMobile   = (get_is_mobile()) ? 1 : 0;

$PAGE = PAGE;
$arrValue = array();
$arrValue[':PAGE_TYPE'] = $PAGE;
$arrValue[':today'] = $today;

//DB
$sql = "
    SELECT 
       * 
    FROM popup 
    WHERE 1
        AND pop_open_yn = 'Y'
        AND PAGE_TYPE = :PAGE_TYPE
        AND 
            (
                pop_start_date <= :today AND 
                pop_end_date   >= :today
            )
";
$clefResult = $mysqldb->select($sql, $arrValue);

if ($clefResult->getResult()) {
    $popup_list = $clefResult->getResultSet();

    foreach ($popup_list as $data) {

        if (empty($_COOKIE["K{$data['pop_seq']}"])) {
            //DB 변수 정리
            $data['pop_img'] = '/upload/popup/' . $data['pop_img1'];

            //변수 정리
            $pop_x      = $data['pop_x'];
            $pop_y      = $data['pop_y'];
            $z_index    = 100000;
            $z_index    += $data['pop_seq'];

            //모바일에서는 좌표값 무효 및 등록순을 가장 위로 뜨게 구현
            if ($isMobile) {
                $pop_x  = 0;
                $pop_y  = 150;
            }
?>
            <div class="common_popup" id="popup_<?= $data['pop_seq']; ?>" style="display:block !important; min-width: auto; min-height: auto; top: <?= $pop_y; ?>px; left: <?= $pop_x; ?>px; z-index: <?= $z_index; ?>;">
                <input type="hidden" class="popup-location" value="<?= $data['pop_seq']; ?>" data-popx="<?= $pop_x; ?>" data-popy="<?= $pop_y; ?>" />

                <?php if (!empty($data['pop_link'])) : ?>
                    <a href="<?= $data['pop_link']; ?>" target="_blank">
                    <?php endif; ?>

                    <img src="<?= $data['pop_img']; ?>" alt="팝업_<?= $data['pop_seq']; ?>" title="팝업_<?= $data['pop_seq']; ?>" id="popupImg" class="popup_img" />

                    <?php if (!empty($data['pop_link'])) : ?>
                    </a>
                <?php endif; ?>

                <div class="onday_close">
                    <div class="onday_input_wrap">
                        <input type="checkbox" data-seq="<?= $data['pop_seq']; ?>" id="pop_chk_<?= $data['pop_seq']; ?>" name="popups_chk" value="Y" />
                        <label for="pop_chk_<?= $data['pop_seq']; ?>">
                            하루동안 보지 않기
                        </label>
                    </div>
                    <button class="close_pop" data-seq="<?= $data['pop_seq']; ?>">닫기</button>
                </div>
                <!--<div class="p_x_btn p_x_btn2"><i class="zwicon-close"></i></div>-->
            </div>
<?php
        }
    }
}
?>
<script>
    function dragIt(e) {
        this.style.left = initX + e.pageX - firstX + 'px';
        this.style.top = initY + e.pageY - firstY + 'px';
    }

    function swipeIt(e) {
        var contact = e.touches;
        this.style.left = initX + contact[0].pageX - firstX + 'px';
        this.style.top = initY + contact[0].pageY - firstY + 'px';
    }

    var object;
    var initX, initY, firstX, firstY;

    //팝업 리사이즈
    function resizePopup() {
        var media = matchMedia('(max-width:1024px)').matches

        if (media == true) {
            // MOBILE & TABLET
            $(".common_popup").css({
                "top": "150px",
                "left": "0px"
            });
        } else {
            // PC
            $(".popup-location").each(function() {
                var pop_seq = $(this).val();
                var pop_x = $(this).data("popx");
                var pop_y = $(this).data("popy");

                $("#popup_" + pop_seq).css({
                    "top": pop_y + "px",
                    "left": pop_x + "px"
                });
            });
        }
    }

    $(document).ready(function() {

        //resize
        $(window).on("resize", function() {
            //팝업 리사이즈
            resizePopup();
        });

        object = document.querySelector(".common_popup");

        $(object).on('mousedown', function(e) {

            e.preventDefault();
            initX = this.offsetLeft;
            initY = this.offsetTop;
            firstX = e.pageX;
            firstY = e.pageY;

            $(this).on('mousemove', dragIt, false);
            $(window).on('mouseup', function() {
                $(object).off('mousemove', dragIt, false);
            }, false);

        }, false);

        $(this).on('touchstart', function(e) {

            e.preventDefault();
            initX = this.offsetLeft;
            initY = this.offsetTop;
            var touch = e.touches;
            firstX = touch[0].pageX;
            firstY = touch[0].pageY;

            $(this).on('touchmove', swipeIt, false);

            $(window).on('touchend', function(e) {
                e.preventDefault();
                $(object).off('touchmove', swipeIt, false);
            }, false);

        }, false);

        $(".close_pop").on('click', function() {
            var seq = $(this).data('seq');
            var value = $('#pop_chk_' + seq).val();

            if ($('#pop_chk_' + seq).is(':checked')) {
                setCookie("K" + $(this).data('seq'), value, 1);
            }

            $("#popup_" + seq).hide();
        });

        //오늘은 그만 보기 체크 시 팝업 닫기

        $(".onday_input_wrap input[type='checkbox']").on("input", function() {
            var seq = $(this).data('seq');
            var value = $('#pop_chk_' + seq).val();

            if ($('#pop_chk_' + seq).is(':checked')) {
                setCookie("K" + $(this).data('seq'), value, 1);
            }

            $("#popup_" + seq).hide();
        })

    });

    function setCookie(key, value, expiredays) {
        var todayDate = new Date();
        todayDate.setDate(todayDate.getDate() + expiredays);
        document.cookie = key + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";"
    }

    function getCookie(key) {
        var result = null;
        var cookie = document.cookie.split(';');
        cookie.some(function(item) {
            // 공백을 제거
            item = item.replace(' ', '');

            var dic = item.split('=');

            if (key === dic[0]) {
                result = dic[1];
                return true; // break;
            }
        });
        return result;
    }
</script>
<!-- <div class="pop_bg"></div> -->