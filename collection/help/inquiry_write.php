<?php
    define("SUB", "HELP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/inquiry_write_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="inquiry">
        <div class="wrapper_1160">
            <div class="sub_title">1:1 문의</div>

            <form id="frm" method="post" action="/php/inquiry.php" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="INS"/>
                <input type="hidden" id="PAGE_TYPE" name="PAGE_TYPE" value="<?=PAGE1?>"/>
                <input type="hidden" id="QUESTION_CD" name="QUESTION_CD" value="01"/>

                <ul class="form_wrap">
                    <li>
                        <div class="form_label_ani">
                            <input id="inquiry_name" name="inquiry_name" type="text" value="<?=$NAME?>" placeholder="이름">
                            <label title="inquiry_name">이름 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="inquiry_tel" name="inquiry_tel" type="tel" value="<?=$MOBILE?>" maxlength="13" oninput="autoHyphen(this)" placeholder="연락처">
                            <label title="inquiry_tel">연락처</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="inquiry_email" name="inquiry_email" type="email" value="<?=$EMAIL?>" placeholder="이메일">
                            <label title="inquiry_email">이메일</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="PURCHASE" name="PURCHASE" type="text" placeholder="주문번호">
                            <label title="PURCHASE">주문번호</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="PRODUCT_TITLE" name="PRODUCT_TITLE" type="text" placeholder="문의작품">
                            <label title="PRODUCT_TITLE">문의작품</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li class="with_radio">
                        <p class="">문의분류</p>
                        <ul class="radio_wrap">
                            <li class="input_radio">
                                <input type="radio" name="TYPE_CD" id="inquiryType1" value="PRD" checked>
                                <label for="inquiryType1">제품관련</label>
                            </li>
                            <li class="input_radio">
                                <input type="radio" name="TYPE_CD" id="inquiryType2" value="PAY">
                                <label for="inquiryType2">결제관련</label>
                            </li>
                            <li class="input_radio">
                                <input type="radio" name="TYPE_CD" id="inquiryType3" value="DVY">
                                <label for="inquiryType3">배송관련</label>
                            </li>
                            <li class="input_radio">
                                <input type="radio" name="TYPE_CD" id="inquiryType4" value="ETC">
                                <label for="inquiryType4">기타</label>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="TITLE" name="TITLE" type="text" placeholder="문의제목">
                            <label title="TITLE">문의제목 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <!-- <input id="CONTENT_TEXT" name="CONTENT_TEXT" type="text" placeholder="문의내용"> -->
                            <textarea id="CONTENT_TEXT" name="CONTENT_TEXT" placeholder="문의내용"></textarea>
                            <label title="CONTENT_TEXT">문의내용 *</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="PASSWORD" name="PASSWORD" type="password" oninput="handlePasswordInput(this);" onblur="checkPasswordLength(this);" placeholder="열람 비밀번호">
                            <label title="PASSWORD">열람 비밀번호</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                </ul>
            </form>

            <div class="btn_wrap bt_80">
                <button type="button" id="inquiryRegistBtn" class="black_btn shadow_btn w_500" onclick="reg()">문의하기</button>
            </div>

        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("1:1문의를 진행하겠습니까?")) {
            $("#frm").submit();
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("#inquiry_name").val() == "") {
            alert("이름을 입력해주세요.");
            $("#inquiry_name").focus();
            return false;
        }

        if ($("#TITLE").val() == "") {
            alert("문의 제목을 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }

        if ($("#CONTENT_TEXT").val() == "") {
            alert("문의 내용을 입력해주세요.");
            $("#CONTENT_TEXT").focus();
            return false;
        }

        return true;
    }

    function handlePasswordInput(input) {
        var trimmedValue = input.value.trim();
        var sanitizedValue = trimmedValue.replace(/[^0-9]/g, '').slice(0, 7);
        input.value = sanitizedValue;
    }

    function checkPasswordLength(input) {
        var passwordValue = input.value;

        if (passwordValue.length >= 1 && passwordValue.length < 4 ) {
            alert('비밀번호는 4자리 이상이어야 합니다.');
            input.value = '';
        }
    }
</script>