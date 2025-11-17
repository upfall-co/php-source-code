<?php
    define("SUB", "MYPAGE");

    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');
    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";

    if (isset($_SESSION['MEMBER'])) {
        if (!empty($_SESSION['MEMBER'])) {
            $_url = artFoldName. '/main.php';

            header("Location: {$_url}");
        }
    }

    $member_name = "";
    $member_tel = "";
    $member_email = "";

    if (isset($_SESSION['SNSIFNO'])) {
        if (!empty($_SESSION['SNSIFNO'])) {
            if (isset($_SESSION['SNSIFNO']['NAME'])) {
                $member_name = $_SESSION['SNSIFNO']['NAME'];
                $member_tel = $_SESSION['SNSIFNO']['MOBILE'];
                $member_email = $_SESSION['SNSIFNO']['EMAIL'];
            }
        }
    }
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="join">
        <div class="sub_title eng">Join</div>

        <div class="wrapper_500">
            <form id="frm" method="post" action="/php/member.php" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="JOIN"/>
                <input type="hidden" id="overchk" name="overchk" value="false"/>
                <input type="hidden" id="TYPE" name="TYPE" value="10"/>

                <ul class="form_wrap">
                    <li class="with_btn">
                        <div class="form_label_ani">
                            <input id="member_id" name="member_id" type="text" maxlength="255" placeholder="아이디">
                            <label title="Id">아이디 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                        <button type="button" id="overlapBtn" class="border_btn" onclick="overlapChk('member_id')">중복확인</button>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="member_pw" name="member_pw" type="password" maxlength="255" placeholder="비밀번호">
                            <label title="Pw">비밀번호 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>

                        <p class="bt_info">비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을 포함하여 8자 이상으로 등록해주세요.</p>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="member_pw_chk" name="member_pw_chk" type="password" maxlength="255" placeholder="비밀번호 확인">
                            <label title="Pw">비밀번호 확인 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="member_name" name="member_name" type="text" value ="<?=$member_name?>" maxlength="100" placeholder="이름">
                            <label title="Name">이름 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="member_tel" name="member_tel" type="tel" value ="<?=$member_tel?>" maxlength="13" oninput="autoHyphen(this)"  placeholder="휴대폰">
                            <label title="Tel">휴대폰 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="member_email" name="member_email" type="email" value ="<?=$member_email?>" maxlength="255" placeholder="이메일">
                            <label title="Email">이메일 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                </ul>

                <div id="joinBtn" class="black_btn shadow_btn" onclick="reg()">회원가입</div>

            </div>
        </form>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>

    function overlapChk(obj) {
        if ($("#" + obj).val() === "") {
            alert("아이디를 입력해 주세요.");
            $("#" + obj).focus();
            return false
        }
        
        let formData = new FormData();

        formData.append("mode", "OVERCHK");
        formData.append("ID", $("#" + obj).val());

        $.ajax({
            url: '/php/ajax_module.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (data) {
                alert(data.msg);

                if (data.code == 200) {
                    $("#overchk").val("true");
                } else {
                    $("#overchk").val("false");
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("회원가입을 진행하겠습니까?")) {
            $("#frm").submit();
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("#member_id" ).val() === "") {
            alert("아이디를 입력해주세요.");
            $("#member_id").focus();
            return false
        } else {
            if ($("#overchk").val() == "false") {
                alert("아이디 중복확인을 해주세요.");
                $("#member_id").focus();
                return false;
            }
        }

        if ($("#member_pw" ).val() === "") {
            alert("비밀번호를 입력해주세요.");
            $("#member_pw").focus();
            return false
        } else {
            var password = $("#member_pw" ).val();

            if (!validatePassword(password, 2, 8)) {
                alert("비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을\n포함하여 8자 이상으로 등록해야 합니다.");
                $("#member_pw").focus();
                return false
            } 
        }

        if ($("#member_pw_chk").val() == "") {
            alert("비밀번호를 입력해주세요.");
            $("#member_pw_chk").focus();
            return false;
        } else {
            var password = $("#member_pw" ).val();
            var password2 = $("#member_pw_chk").val();

            if (password != password2) {
                alert("비밀번호가 일치하지 않습니다.");
                $("#member_pw_chk").focus();
                return false
            }
        }

        if ($("#member_name").val() === "") {
            alert("이름을 입력해주세요.");
            $("#member_name").focus();
            return false
        }

        if ($("#member_tel").val() === "") {
            alert("연락처를 입력해주세요.");
            $("#member_tel").focus();
            return false
        }

        if (!isValidPhoneNumber($("#member_tel").val())) {
            alert("유효한 휴대폰 번호를 입력해주세요. \n(숫자 11자리를 입력해 주세요)");
            $("#member_tel").focus();
            return false;
        }

        if ($("#member_email").val() === "") {
            alert("이메일을 입력해주세요.");
            $("#member_email").focus();
            return false
        }

        if (!isValidEmail($("#member_email").val())) {
            alert("유효한 이메일 주소를 입력해주세요.");
            $("#member_email").focus();
            return false;
        }

        return true;
    }

</script>