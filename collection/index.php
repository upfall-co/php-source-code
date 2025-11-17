<?php
    define("SUB", "00");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/collection_index_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";

    if (isset($_SESSION['SECRETCHK'])) {
        if (!empty($_SESSION['SECRETCHK'])) {
            $_url = artFoldName. '/main.php';

            header("Location: {$_url}");
        }
    }
?>

<body id="height100" class="noScroll">

    <main id="main">
        <div id="secretLayer">
            <div class="logo"><img src="<?= artFoldName ?>/img/main/secret_logo.png" alt="로고"></div>
            <div class="secret_code_wrap">
                <input type="password" id='SCODE' name = 'SCODE' placeholder="시크릿 코드를 입력해 주세요.">
                <button type="" onclick="onCodeChk('SCODE');">ENTER</button>
            </div>
        </div>

        <ul class="main_list pc">
            <?php getList_Image_PC(); ?>
        </ul>

        <div class="swiper mainMoSwiper">
            <div class="swiper-wrapper">
                <?php getList_Image_MO(); ?>
            </div>
            <div class="swiper-button-prev"><img src="<?= artFoldName ?>/img/main/main_prev.png" alt="이전"></div>
            <div class="swiper-button-next"><img src="<?= artFoldName ?>/img/main/main_next.png" alt="다음"></div>
            <div class="mo_slide_set">
                <div class="swiper-pagination"></div>
                <?php getFirst_Series(); ?> 
            </div>
        </div>

    </main>
</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php" ?>

<script>
    const setVh = () => {
        document.documentElement.style.setProperty('--vh', `${window.innerHeight}px`)
    };
    window.addEventListener('resize', setVh);
    setVh();

    function onCodeChk(obj) {
        var list = {
              'mode' : 'CHKCODE'
            , 'val': $("#"+ obj).val()
        };

        $.ajax({
              type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function(data) {
                // 처리 성공 시 실행할 코드
                let json = JSON.parse(data);
                alert(json.msg);

                if (json.code == 200) {
                    location.href = "<?= artFoldName ?>/main.php"
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    $('#SCODE').on('keydown', function(event) {
        if (event.keyCode === 13) { // 엔터 클릭시
            event.preventDefault();
            onCodeChk('SCODE'); // 버튼 기능으로 활성화
        }
    });
</script>