<div id="findPwPop" class="popup find_popup">
    <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>
    <div class="find_txt">
        <div class="big">비밀번호 찾기</div>
        <div class="sm">
            가입 시 입력했던 정보를 작성해 주세요. <br>
            회원가입 시 등록하신 이메일 또는 <br class="br_540">휴대폰(SMS)으로 임시 비밀번호가 전송됩니다.
        </div>
    </div>

    <ul class="radio_wrap">
        <li>
            <input type="radio" name="findPwType" id="findPwtypeEmail" value="typeEmail" checked>
            <label for="findPwtypeEmail">이메일</label>
        </li>
        <li>
            <input type="radio" name="findPwType" id="findPwtypePhone" value="typePhone">
            <label for="findPwtypePhone">휴대폰</label>
        </li>
    </ul>

    <ul class="form_wrap with_title">
        <li>
            <div class="form_label_ani">
                <div class="form_title">아이디</div>
                <fieldset>
                    <input id="find_pw_member_id" name="member_id" type="text" placeholder="아이디">
                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                </fieldset>
            </div>
        </li>
        <li class="chkType1">
            <div class="form_label_ani">
                <div class="form_title">이메일</div>
                <fieldset>
                    <input id="find_pw_member_email" name="member_email" type="email" placeholder="이메일">
                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                </fieldset>
            </div>
        </li>
        <li class="chkType2">
            <div class="form_label_ani">
                <div class="form_title">휴대폰</div>
                <fieldset>
                    <input id="find_pw_member_tel" name="member_tel" type="tel" maxlength="13" oninput="autoHyphen(this)" placeholder="휴대폰">
                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                </fieldset>
            </div>
        </li>
    </ul>

    <div class="pop_btn_wrap">
        <div class="black_btn shadow_btn" onclick="onFindPwEnd();">임시 비밀번호 발급</div>
    </div>
</div>

<script>
    $(".chkType2").hide();

    $("input[name='findPwType']:radio").change(function() {
        var findPwType = this.value;

        if (findPwType == 'typeEmail') {
            $(".chkType1").show();
            $(".chkType2").hide();
            $(".chkType2 input").val("");
        } else if (findPwType == 'typePhone') {
            $(".chkType2").show();
            $(".chkType1").hide();
            $(".chkType1 input").val("");
        }
    });

    function onFindPwEnd() {
        var list = "";

        if ($("input[name='findPwType']:radio:checked").val() == "typeEmail") {
            list = {
                  'mode' : 'FINDEMAIL'
                , 'ID' : $("#find_pw_member_id").val()
                , 'EMAIL' : $("#find_pw_member_email").val()
                , 'TYPE' : 'FIND_PW'
            };
        } else if ($("input[name='findPwType']:radio:checked").val() == 'typePhone') {
            list = {
                  'mode' : 'FINDPHONE'
                , 'ID' : $("#find_pw_member_id").val()
                , 'MOBILE' : $("#find_pw_member_tel").val()
                , 'TYPE' : 'FIND_PW'
            };
        }
        
        if (!gfn_isNull(list)) {
            $.ajax({
                  type: "POST"
                , url: "/php/ajax_module.php"
                , data: list
                , success: function(data) {
                    // 처리 성공 시 실행할 코드
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        $(".popup_bg").stop().fadeOut();
                        $("#findPwPop").stop().fadeOut();
                        $('html, body').removeClass("noScroll");
                    }
                }
                , error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }
</script>