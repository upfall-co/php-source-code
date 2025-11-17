<div id="findIdPop" class="popup find_popup">
    <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>
    <div class="find_txt">
        <div class="big">아이디 찾기</div>
        <div class="sm">
            가입 시 입력했던 정보를 작성해 주세요. <br>
            회원가입 시 등록하신 이메일 또는 <br class="br_540">휴대폰(SMS)으로 ID가 전송됩니다.
        </div>
    </div>

    <ul class="radio_wrap">
        <li>
            <input type="radio" name="findIdType" id="findIdTypeEmail" value="typeEmail" checked>
            <label for="findIdTypeEmail">이메일</label>
        </li>
        <li>
            <input type="radio" name="findIdType" id="findIdTypePhone" value="typePhone">
            <label for="findIdTypePhone">휴대폰</label>
        </li>
    </ul>

    <ul class="form_wrap">
        <!-- <li>
            <div class="form_label_ani">
                <input id="find_id_member_name" name="member_name" type="text" placeholder="이름">
                <label title="Name">이름</label>
                <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
            </div>
        </li> -->
        <li class="chkType1">
            <div class="form_label_ani">
                <input id="find_id_member_email" name="member_email" type="email" placeholder="이메일">
                <label title="Email">이메일</label>
                <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
            </div>
        </li>
        <li class="chkType2">
            <div class="form_label_ani">
                <input id="find_id_member_tel" name="member_tel" type="tel" maxlength="13" oninput="autoHyphen(this)" placeholder="휴대폰">
                <label title="Tel">휴대폰</label>
                <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
            </div>
        </li>
    </ul>

    <div class="pop_btn_wrap">
        <div class="chkType1 black_btn shadow_btn" onclick="onFindEndEmail();">아이디 이메일 발송</div>
        <div class="chkType2 black_btn shadow_btn" onclick="onFindEndPhone();">아이디 휴대폰 발송</div>
    </div>
</div>

<script>
    $(".chkType2").hide();

    $("input[name='findIdType']:radio").trigger("change");

    $("input[name='findIdType']:radio").change(function() {
        var findIdType = this.value;

        if (findIdType == 'typeEmail') {
            $(".chkType1").show();
            $(".chkType2").hide();
            $(".chkType2 input").val("");
        } else if (findIdType == 'typePhone') {
            $(".chkType2").show();
            $(".chkType1").hide();
            $(".chkType1 input").val("");
        }
    });

    function onFindEndEmail() {
        var list = {
              'mode' : 'FINDEMAIL'
            , 'EMAIL' : $("#find_id_member_email").val()
            , 'TYPE' : 'FIND_ID'
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
                     $(".popup_bg").stop().fadeOut();
                    $("#findIdPop").stop().fadeOut();
                    $('html, body').removeClass("noScroll");
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    function onFindEndPhone() {
        var list = {
              'mode' : 'FINDPHONE'
            , 'MOBILE' : $("#find_id_member_tel").val()
            , 'TYPE' : 'FIND_ID'
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
                    $(".popup_bg").stop().fadeOut();
                    $("#findIdPop").stop().fadeOut();
                    $('html, body').removeClass("noScroll");
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>