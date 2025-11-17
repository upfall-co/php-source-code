<div id="inquiryPwChk" class="popup sm_popup only_txt">
    <input type="hidden" id="mode" name="mode" value="INQCHK"/>
    <input type="hidden" id="SEQ" name="SEQ" value=""/>

    <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>
    <div class="center_txt">비밀번호를 입력하세요</div>
    <div class="pop_btn_wrap with_input">
        <input id="PASSWORD" name="PASSWORD" type="password" placeholder="비밀번호">
        <div class="black_btn" onclick="inquriyPwChkEnd();">확인</div>
    </div>
</div>

<script>
    $('#PASSWORD').on('keydown', function(event) {
        if (event.keyCode === 13) { // 엔터 클릭시
            event.preventDefault();
            inquriyPwChkEnd(); // 버튼 기능으로 활성화
        }
    });

    function inquriyPwChkEnd() {
        if ($("#PASSWORD").val() == "") {
            alert("비밀번호를 입력해주세요.");
            $("#PASSWORD").focus();
            return false;
        }

        var list = {
              'mode' : $("#mode").val()
            , 'val': $("#SEQ").val()
            , 'PW' : $("#PASSWORD").val()
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
                    location.href = "<?= artFoldName ?>/help/inquiry_view.php?SEQ=" + $("#SEQ").val();
                } else {
                    $("#PASSWORD").focus();
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>