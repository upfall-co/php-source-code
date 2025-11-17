<?php
    define("SUB", "MYPAGE");

    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');
    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";

    if (isset($_SESSION['MEMBER'])) {
        if (!empty($_SESSION['MEMBER'])) {
            $_url = shopFoldName. '/index.php';

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
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content">
        <main id="join" class="padding170">

            <div class="wrapper_500">
                <div class="center_sub_title">Join</div>
                <div class="join_form_wrap border_top">
                    <form id="frm" method="post" action="/php/member.php" enctype="multipart/form-data">
                        <input type="hidden" id="mode" name="mode" value="JOIN"/>
                        <input type="hidden" id="overchk" name="overchk" value="false"/>
                        <input type="hidden" id="TYPE" name="TYPE" value="20"/>
    
                        <ul class="form_wrap with_title form_type">
                            <li class="with_radio">
                                <div class="form_label_ani">
                                    <div class="form_title">회원유형</div>
                                    <fieldset>
                                        <ul class="radio_wrap">
                                            <li class="input_radio">
                                                <input type="radio" name="TYPE_CD" id="MBR" value="MBR" checked>
                                                <label for="MBR">개인</label>
                                            </li>
                                            <li class="input_radio">
                                                <input type="radio" name="TYPE_CD" id="BSM" value="BSM">
                                                <label for="BSM">사업자</label>
                                            </li>
                                        </ul>
                                    </fieldset>
                                </div>
                            </li>
                        </ul>

                        <ul class="form_wrap with_title">
                            <li class="with_btn">
                                <div class="form_label_ani">
                                    <div class="form_title">아이디 *</div>
                                    <fieldset>
                                        <input id="member_id" name="member_id" type="text" maxlength="255" placeholder="아이디">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                                <button type="button" id="overlapBtn" class="border_btn" onclick="overlapChk('member_id')">중복확인</button>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">비밀번호 *</div>
                                    <fieldset>
                                        <input id="member_pw" name="member_pw" type="password" maxlength="255" placeholder="비밀번호">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">비밀번호 확인 *</div>
                                    <fieldset>
                                        <input id="member_pw_chk" name="member_pw_chk" type="password" maxlength="255" placeholder="비밀번호 확인">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                                <div class="form_label_ani">
                                    <div class="form_title"></div>
                                    <fieldset>
                                        <p class="bt_info">비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을 포함하여 8자 이상으로 등록해주세요.</p>
                                    </fieldset>
                                </div>
                            </li>
    
                            <!---------------- 사업자 선택 시에만 노출 ---------------->
                            <li class="type_bsm">
                                <div class="form_label_ani">
                                    <div class="form_title">사업자명</div>
                                    <fieldset>
                                        <input id="member_business_name" name="member_business_name" type="text" maxlength="100" placeholder="사업자명">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                            <li class="type_bsm">
                                <div class="form_label_ani">
                                    <div class="form_title">사업자등록번호</div>
                                    <fieldset>
                                        <input id="member_business_number" name="member_business_number" type="text" maxlength="100" placeholder="사업자등록번호">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                            <!---------------- 사업자 선택 시에만 노출 ---------------->
    
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">이름 *</div>
                                    <fieldset>
                                        <input id="member_name" name="member_name" type="text" value ="<?=$member_name?>" maxlength="100" placeholder="이름">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">휴대폰 *</div>
                                    <fieldset>
                                        <input id="member_tel" name="member_tel" type="tel" value ="<?=$member_tel?>" maxlength="13" oninput="autoHyphen(this)"  placeholder="휴대폰">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">이메일 *</div>
                                    <fieldset>
                                        <input id="member_email" name="member_email" type="email" value ="<?=$member_email?>" maxlength="255" placeholder="이메일">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>

                            <li class="with_btn">
                                <div class="form_label_ani">
                                    <div class="form_title">배송주소 *</div>
                                    <fieldset>
                                        <input id="ADDRESS_ZIPCODE" name="ADDRESS_ZIPCODE" type="text" placeholder="주소" readonly>
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                                <button type="button" id="addressBtn" class="border_btn" onclick="execDaumPostcode()">주소검색</button>
                            </li>
                            <li>
                                <div class="form_label_ani placeholder">
                                    <div class="form_title"></div>
                                    <fieldset>
                                        <input id="ADDRESS" name="ADDRESS" type="text" placeholder="기본주소" readonly>
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani placeholder">
                                    <div class="form_title"></div>
                                    <fieldset>
                                        <input id="ADDRESSDETAIL" name="ADDRESSDETAIL" type="text" placeholder="상세주소">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                        </ul>
    
                        <div id="joinBtn" class="black_btn shadow_btn" onclick="reg()">가입완료</div>
    
                    </form>
                </div>
            </div>
        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.
                let addr = ''; // 주소 변수
    
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                $('#ADDRESS_ZIPCODE').val(data.zonecode);
                $('#ADDRESS').val(addr);
            }
        }).open();
    }
    
    // 회원유형 개인 / 사업자 구분
    $(".type_bsm").hide();
    $("input[name='TYPE_CD']:radio").change(function() {
        var joinType = this.value;

        if (joinType == 'MBR') { // 개인
            $(".type_bsm").hide();
            hiddenInput.value = 'Card';
        } else if (joinType == 'BSM') { // 사업자
            $(".type_bsm").show();
        }
    });

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

        if ($("#member_name" ).val() === "") {
            alert("이름을 입력해주세요.");
            $("#member_name").focus();
            return false
        }

        if ($("#member_tel" ).val() === "") {
            alert("연락처를 입력해주세요.");
            $("#member_tel").focus();
            return false
        }

        if (!isValidPhoneNumber($("#member_tel").val())) {
            alert("유효한 휴대폰 번호를 입력해주세요. \n(숫자 11자리를 입력해 주세요)");
            $("#member_tel").focus();
            return false;
        }

        if ($("#member_email" ).val() === "") {
            alert("이메일을 입력해주세요.");
            $("#member_email").focus();
            return false
        }

        if (!isValidEmail($("#member_email").val())) {
            alert("유효한 이메일 주소를 입력해주세요.");
            $("#member_email").focus();
            return false;
        }

        if ($("#ADDRESS_ZIPCODE").val() == "") {
            alert("주소를 선택해주세요.");
            $("#ADDRESS_ZIPCODE").focus();
            return false;
        }

        return true;
    }

</script>