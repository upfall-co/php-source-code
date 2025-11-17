<?php
    define("SUB", "MYPAGE");
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');
    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";

    use Clef\SiteConfig;

    $terms = SiteConfig::terms_data(PAGE1); //약관

    $_SESSION['SNSIFNO']['PAGE'] = artFoldName;

    include_once $_SERVER['DOCUMENT_ROOT'] . "/php/withdraw_code.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/sns_header.php"
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="mypage" class="withdraw">
        <div class="sub_title">회원탈퇴</div>

        <div class="wrapper_1400">

            <div class="withdraw_wrap">
                <div class="withdraw_notice">
                    <?=$terms['privacy_statement3'];?>
                </div>

                <?=$TYPE_HTML?>
            </div>

            <div class="edit_btn_wrap">
                <?= $DIV_HTML ?>
            </div>

        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
     $(".withdraw_reason select").on("change", function() {
        let val = $(this).val();
        $(".etc_hidden").val("");

        if (val == "ETC") {
            $(".etc_hidden").stop().show();
        } else {
            $(".etc_hidden").stop().hide();
        }
    });
    
    function onWithDrawEnd() {
        if ($("#TYPE_CD").val() === "") {
            alert("탈퇴 사유를 선택해주세요.");
            $("#TYPE_CD").focus();
            return false
        }

        if (confirm("정말로 탈퇴를 진행하시겠습니까?")) {
            var list = {
                  'mode' : "MEMBER_DEL"
                , 'TYPE_CD' : $("#TYPE_CD" ).val()
            }

            $.ajax({
                  type: "POST"
                , url: "/php/ajax_module.php"
                , data: list
                , success: function(data) {
                    // 처리 성공 시 실행할 코드
                    let json = JSON.parse(data);

                    alert(json.msg);

                    if (json.code == 200) {
                        location.href = "<?= artFoldName ?>/main.php";
                    }
                }
                , error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/sns_bottom.php" ?>