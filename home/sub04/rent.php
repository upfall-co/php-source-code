<?php
  define("SUB", "04");
  include_once $_SERVER['DOCUMENT_ROOT'] . '/php/home_index_code.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/header.php";

  include_once $_SERVER['DOCUMENT_ROOT'] . '/php/space_code.php';
?>

<body>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/gmenu.php" ?>

  <main id="rent" class="main_conatiner">

    <!---------------------------------------- sec1 시작 -->

    <section class="spac__sec1 blank blank--t3">

      <div class="spac__s1_container wrapper">
        <div class="subpage__title_container">
            <h3 class="title">SPACE</h3>
            <ul class="category_wrap">

                <li>
                    <a href="/home/sub04/space.php">공간 소개</a>
                </li>

                <li class="active">
                    <a href="/home/sub04/rent.php">대관 문의</a>
                </li>

            </ul>
        </div>

        <div class="spac__s1_contents_container">
            <form id="frm" action="/php/rent.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="INS">
                <input type="hidden" id="page_type" name="page_type" value="<?=PAGE3?>">

                <ul class="form_wrap with_title form_type">
                    <li class="with_radio">
                        <div class="form_label_ani">
                            <div class="form_title bold">문의 구분</div>
                            <fieldset>
                                <ul class="radio_wrap">
                                    <li class="input_radio">
                                        <input type="radio" name="TYPE_CD" id="apply" value="RINQ">
                                        <label for="apply">대관 문의</label>
                                    </li>
                                    <li class="input_radio">
                                        <input type="radio" name="TYPE_CD" id="biz" value="CINQ">
                                        <label for="biz">제휴 문의</label>
                                    </li>
                                </ul>
                            </fieldset>
                        </div>
                    </li>
                </ul>
                <ul class="form_wrap with_title">
                    <li>
                        <div class="form_label_ani placeholder">
                            <div class="form_title bold">회사(단체)명</div>
                            <fieldset>
                                <input id="COMPANY" name="COMPANY" type="text" maxlength="150" placeholder="회사(단체)명을 입력해 주세요.">
                            </fieldset>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani placeholder">
                            <div class="form_title bold">대행사명</div>
                            <fieldset>
                                <input id="AGENCY" name="AGENCY" type="text" maxlength="150" placeholder="대행사명을 입력해 주세요.">
                            </fieldset>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani placeholder">
                            <div class="form_title bold">행사명</div>
                            <fieldset>
                                <input id="TITLE" name="TITLE" type="text" maxlength="255" placeholder="행사명을 입력해 주세요.">
                            </fieldset>
                        </div>
                    </li>
                </ul>
                <ul class="form_wrap with_title">
                    <li>
                        <div class="form_label_ani placeholder multiple">
                            <div class="title_wrap">
                                <div class="form_title bold">희망기간</div>
                                <div><span class="red">*</span> <span class="appendix">설치 및 철거 기간 포함</span></div>
                            </div>
                            <fieldset>
                                <input type="date" id="HSDATE" name="HSDATE" class="date_picker">
                                <span class="dashed">-</span>
                                <input type="date" id="HEDATE" name="HEDATE" class="date_picker">
                            </fieldset>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani placeholder">
                            <div class="form_title bold">내용</div>
                            <fieldset>
                                <input id="CONTENT_TEXT" name="CONTENT_TEXT" type="text" placeholder="희망 장소, 행사 대상, 인원수, 시간 등을 구체적으로 입력해 주세요.">
                            </fieldset>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani placeholder">
                            <div class="form_title bold">담당자명 (직함)</div>
                            <fieldset>
                                <input id="NAME" name="NAME" type="text" maxlength="430" placeholder="담당자명과 직함을 입력해 주세요.">
                            </fieldset>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani placeholder multiple">
                            <div class="form_title bold">연락처</div>
                            <fieldset>
                                <select name="PHONE1" id="PHONE1">
                                    <?php gfn_getComboList("휴대폰 앞자리", "AD014", "","선택")?>
                                </select>
                                <span class="dashed">-</span>
                                <input id="PHONE2" name="PHONE2" type="tel" maxlength="4" required>
                                <span class="dashed">-</span>
                                <input id="PHONE3" name="PHONE3" type="tel" maxlength="4" required>
                            </fieldset>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani placeholder multiple email">
                            <div class="form_title bold">이메일</div>
                            <fieldset>
                                <input id="EMAIL_ID" name="EMAIL_ID" type="text" required>
                                <span class="dashed">@</span>
                                <input id="EMAIL_TEXT" name="EMAIL_TEXT" type="text" required>
                                <span class="dashed" style="color: transparent;">@</span>
                                <select name="EMAIL_CD" id="EMAIL_CD">
                                    <?php gfn_getComboList("이메일구분", "AD005", "","선택해 주세요")?>
                                </select>
                            </fieldset>
                        </div>
                    </li>
                    <li class="with_btn">
                        <div class="form_label_ani placeholder multiple">
                            <div class="form_title bold">파일 첨부</div>
                            <fieldset>
                                <input id="attachFile" name="attachFile" type="file" accept=".jpg,.jpeg,.png" onchange="fileupload(this, '', 'jpg|jpeg|png|pdf', 10);">
                                <label for="attachFile" class="black_btn">파일 선택</label>
                            </fieldset>
                        </div>
                        <div class="appendix">
                            <p>+ 파일은 최대 1개, 최대 10MB까지 첨부 가능합니다.</p>
                            <p>+ 파일 종류는 이미지파일(jpg, png) 혹은 pdf로 첨부 가능합니다.</p>
                        </div>
                    </li>
                </ul>
                <ul class="agree_chk_wrap">
                    <li class="input_chk">
                        <input type="checkbox" id="agreeChk2" name="agreeChk2" value = "Y">
                        <label for="agreeChk2">개인정보 수집에 대한 동의</label>
                        <button type="button" class="agree_pop_btn" onclick="onAgreePop2();">
                            전문보기
                            <span class="icon"><img src="/home/img/rent_popup_cursor.svg" alt="커서이미지"></span>
                        </button>
                    </li>
                </ul>
                <!-- 개인정보처리방침 팝업 -->
                <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/agree_popup2.php" ?>

                <button type="button" class="black_btn shadow_btn" onclick="javascript:reg()">문의하기</button>
            </form>
        </div>
      </div>
    </section>

  </main>
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . homeFoldName . "/include/bottom.php" ?>

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(document).ready(function() {
        $("#startDate").datepicker(
            {dateFormat: 'yy-mm-dd'}
        );
        $("#endDate").datepicker(
            {dateFormat: 'yy-mm-dd'}
        );

        $("#EMAIL_CD").on("change", function () {
            const selected = $(this).val(); // 선택된 값

            if (selected) {
                let domain = selected.toLowerCase();
                let fullDomain = domain === "hanmail" ? domain + ".net" : domain + ".com";
                $("#EMAIL_TEXT").val(fullDomain);
            } else {
                $("#EMAIL_TEXT").val("");
            }
        });
    });

    function fileupload(obj, id, strExt, limitSize, ues) {
        gfnfile = {
            mode: 'O',
            obj: obj,
            id: id,
            strExt: strExt,
            limitSize: limitSize,
            fileMap: '',
            formData_del: '',
            del_count: '',
            file_list_row: '',
            row_val: '',
            ues: 'A'
        };

        gfn_changeFile(gfnfile);
    }

    // 등록
    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("등록하시겠습니까?")) {
            let formData = new FormData($("#frm")[0]);

            $.ajax({
                url: "/php/rent.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        location.reload();
                    }
                },
                beforeSend: function() {
                    $(".wrap-loading").removeClass("display-none");
                },
                complete: function() {
                    $(".wrap-loading").addClass("display-none");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("input[name='TYPE_CD']:checked").length === 0) {
            alert("문의 구분을 선택해주세요.");
            $("input[name='TYPE_CD']").first().focus();
            return false;
        }

        if ($("#COMPANY").val() == "") {
            alert("회사(단체)명을 입력해주세요.");
            $("#COMPANY").focus();
            return false;
        }

        if ($("#HSDATE").val() == "" || $("#HEDATE").val() == "") {
            alert("희망기간을 입력해주세요.");
            $("#HSDATE").focus();
            return false;
        }

        if ($("#PHONE1").val() == "" || $("#PHONE2").val() == "" || $("#PHONE3").val() == "") {
            alert("연락처를 입력해주세요.");
            $("#PHONE1").focus();
            return false;
        }

        if ($("#EMAIL_ID").val() == "" || $("#EMAIL_TEXT").val() == "") {
            alert("이메일을 입력해주세요.");
            $("#EMAIL_ID").focus();
            return false;
        }

        if (!$("#agreeChk2").is(":checked")) {
            alert("개인정보 수집에 동의해 주세요.");
            $("#agreeChk2").focus();
            return false;
        }

        return true;
    }
</script>