<?php
    define("SUB", "MYPAGE");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/Mypage_edit_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main id="mypage" class="edit wrapper">
            <div class="center_sub_title">마이페이지</div>

            <ul class="mypage_tab">
                <li><a href="<?= shopFoldName ?>/mypage/orderhistory.php">주문내역조회</a></li>
                <li><a href="<?= shopFoldName ?>/mypage/inquiry.php">1:1문의 내역</a></li>
                <li class="active"><a href="<?= shopFoldName ?>/mypage/edit.php">개인정보 수정</a></li>
            </ul>

            <form id="frm" method="post" action="/php/member.php" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="MOD"/>
                <input type="hidden" id="NAME" name="NAME" value="<?=$_db_NAME?>"/>
                <input type="hidden" id="type" name="type" value=""/>
                <input type="hidden" id="page_type" name="page_type" value="<?=PAGE2?>"/>

                <div class="half_section">
                    <div class="half">
                        <div class="mypage_title">비밀번호 수정</div>

                        <ul class="form_wrap with_title border_top">
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">이름</div>
                                    <fieldset>
                                        <input id="member_name" name="member_name" type="text" value="<?=$_db_NAME?>" placeholder="이름" readonly>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">아이디</div>
                                    <fieldset>
                                        <input id="member_id" name="member_id" type="text" value="<?=$_db_ID?>" placeholder="아이디" readonly>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">비밀번호 *</div>
                                    <fieldset>
                                        <input id="member_pw" name="member_pw" type="password" placeholder="비밀번호">
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">비밀번호 확인 *</div>
                                    <fieldset>
                                        <input id="member_pw_chk" name="member_pw_chk" type="password" placeholder="비밀번호 확인">
                                    </fieldset>
                                </div>
                                <div class="form_label_ani">
                                    <div class="form_title"></div>
                                    <fieldset>
                                        <p class="bt_info">비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을 포함하여 8자 이상으로 등록해주세요.</p>
                                    </fieldset>
                                </div>
                            </li>
                        </ul>

                        <button type="button" class="border_btn" id="pwChangeBtn" onclick="chageInfo('PW')">비밀번호 수정</button>
                    </div>

                    <div class="half">
                        <div class="mypage_title">기본정보 수정</div>

                        <ul class="form_wrap with_title border_top">

                            <?php if ($_db_TYPE_CD == 'BSM') { ?>
                                <li>
                                    <div class="form_label_ani">
                                        <div class="form_title">사업자명</div>
                                        <fieldset>
                                            <input id="member_business_name" name="member_business_name" type="text" maxlength="100" value="<?=$_db_BUSINESS_NAME?>" placeholder="사업자명">
                                        </fieldset>
                                    </div>
                                </li>
                                <li>
                                    <div class="form_label_ani">
                                        <div class="form_title">사업자등록번호</div>
                                        <fieldset>
                                            <input id="member_business_number" name="member_business_number" type="text" maxlength="100" value="<?=$_db_BUSINESS_NUMBER?>" placeholder="사업자등록번호">
                                        </fieldset>
                                    </div>
                                </li>
                            <?php } ?>

                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">휴대폰 *</div>
                                    <fieldset>
                                        <input id="member_tel" name="member_tel" type="tel" maxlength="13" oninput="autoHyphen(this)" value="<?=$_db_MOBILE?>" placeholder="휴대폰">
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">이메일 *</div>
                                    <fieldset>
                                        <input id="member_email" name="member_email" type="email" value="<?=$_db_EMAIL?>" placeholder="이메일">
                                    </fieldset>
                                </div>
                            </li>
                            <li class="with_btn">
                                <div class="form_label_ani">
                                    <div class="form_title">주소 *</div>
                                    <fieldset>
                                        <input id="ADDRESS_ZIPCODE" name="ADDRESS_ZIPCODE" type="text" value="<?=$_db_ADDRESS_ZIPCODE?>" placeholder="주소" readonly>
                                    </fieldset>
                                </div>
                                <button type="button" id="addressBtn" class="border_btn" onclick="execDaumPostcode()">주소검색</button>
                            </li>
                            <li>
                                <div class="form_label_ani placeholder">
                                    <div class="form_title"></div>
                                    <fieldset>
                                        <input id="ADDRESS" name="ADDRESS" type="text" value="<?=$_db_ADDRESS?>" placeholder="기본주소" readonly>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani placeholder">
                                    <div class="form_title"></div>
                                    <fieldset>
                                        <input id="ADDRESSDETAIL" name="ADDRESSDETAIL" type="text" value="<?=$_db_ADDRESSDETAIL?>" placeholder="상세주소">
                                    </fieldset>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>

            <div class="edit_btn_wrap">
                <div class="black_btn shadow_btn w_500" onclick="chageInfo('INFO')">정보수정</div>
                <a href="<?= shopFoldName ?>/mypage/withdraw.php" type="button" id="withdrawBtn" class="black_btn">회원탈퇴</a>
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

    function chageInfo(mode) {
        let msg = "";

        $('#type').val(mode);

        if (mode == "PW") {
            msg = "비밀번호를 수정하시겠습니까?";
        } else if (mode == "INFO") {
            msg = "정보를 수정하시겠습니까?\n비밀번호 변경은 비밀번호 수정을 눌러주세요."
        }

        if (!ufn_validation(mode)) { //유효성
            return false;
        }

        if (!gfn_isNull(msg)) {
            if (confirm(msg)) {
                $("#frm").submit();
            }
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation(mode) {
        if (mode == "PW") {
            if ($("#member_pw").val() == "") {
                alert("비밀번호를 입력해주세요.");
                $("#member_pw").focus();
                return false;
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
        } else if (mode == "INFO") {
            if ($("#member_tel").val() == "") {
                alert("연락처를 입력해주세요.");
                $("#member_tel").focus();
                return false;
            }

            if (!isValidPhoneNumber($("#member_tel").val())) {
                alert("유효한 휴대폰 번호를 입력해주세요. \n(숫자 11자리를 입력해 주세요)");
                $("#member_tel").focus();
                return false;
            }

            if ($("#member_email").val() == "") {
                alert("이메일을 입력해주세요.");
                $("#member_email").focus();
                return false;
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
        }

        return true;
    }
    
</script>