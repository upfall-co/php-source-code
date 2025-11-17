<?php
    define("SUB", "sub");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/shop_inquiry_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main class="page_inquiry wrapper">
            <div class="center_sub_title">문의하기</div>

            <form id="frm" method="post" action="/php/inquiry.php" enctype="multipart/form-data" class="inquiry_form">
                <input type="hidden" id="mode" name="mode" value="INS"/>
                <input type="hidden" id="PAGE_TYPE" name="PAGE_TYPE" value="<?=PAGE2?>"/>
                <input type="hidden" id="QUESTION_CD" name="QUESTION_CD" value="01"/>
                <input type="hidden" id="inquiry_name" name="inquiry_name" value="<?=$NAME?>"/>
                <input type="hidden" id="inquiry_tel" name="inquiry_tel" value="<?=$MOBILE?>"/>
                <input type="hidden" id="inquiry_email" name="inquiry_email" value="<?=$EMAIL?>"/>

                <ul>
                    <li>
                        <div class="form_title">문의 유형 *</div>
                        <div class="form_input">
                            <div class="form_label_ani">
                                <select id="TYPE_CD" name="TYPE_CD">
                                    <?php gfn_getComboList("문의타입", "COL002", $TYPE_CD,"문의", "", "", "", PAGE2)?>
                                </select>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="form_title">제목 *</div>
                        <div class="form_input">
                            <div class="form_label_ani">
                                <input id="TITLE" name="TITLE" type="text">
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="form_title">내용 *</div>
                        <div class="form_input">
                            <div class="form_label_ani">
                                <textarea id="CONTENT_TEXT" name="CONTENT_TEXT"></textarea>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="form_title">첨부파일</div>
                        <div class="form_input">
                            <div class="with_btn">
                                <div class="form_label_ani">
                                    <div class="file_container">
                                        <ul class="file_input_list" id="file_input_list"></ul>

                                        <div class="file_input_wrap">
                                            <input type="file" id="INQUIRY_FILE" onchange="javascript:fileupload(this, '#file_input_list', 'jpg|jpeg|png|pdf', 1,'M');" name="ATTACH" multiple>
                                            <label for="INQUIRY_FILE" class="input_btn">파일첨부</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="form_title"></div>
                        <div class="form_input">
                            <p class="grey_info">jpg, jpeg, png, pdf 파일 첨부가 가능합니다. <br>파일용량 1MB 이내 첨부가 가능합니다.</p>
                        </div>
                    </li>
                </ul>
            </form>

            <button type="button" class="black_btn inquiryBtn" onclick="reg()"><span>문의하기</span></button>
        </main>
        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>
</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php" ?>

<script>
    let fileMap = new Map();
    let gfnfile = [];
    let formData_del = [];
    let del_count = "";

    //파일업로드
    function fileupload(obj, id, strExt, limitSize, ues) {
        gfnfile = {
              mode : 'M' // [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
            , obj  : obj // input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
            , id   : id // 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
            , strExt : strExt // 확장자 ex) jpg|gif|jpeg|png|pdf|zip
            , limitSize : limitSize // 파일의 사이즈를 확인
            , fileMap : fileMap // mode가 M인경우 다중파일일 경우 값 저장을 위하여
            , formData_del : formData_del // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
            , del_count : del_count // mode가 M인경우 삭제 pk값보관
            , file_list_row : '.file_list' // mode가 M인경우 다중파일의 pk값을 보관
            , row_val : 3 //mode가 M인경우  다중파일의  max값을 지정해줌
            , ues : ues // 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile(gfnfile);
    }

    function upFileDel(idx) {
        let options = {
              fileMap : fileMap
            , formData_del : formData_del
            , del_count : del_count
            , file_list_row : '#file_list'
        };

        let result = gfn_upFileDel(idx, options);

        if (result != 0) {
            fileMap = result.fileMap;
            formData_del = result.formData_del;
            del_count = result.del_count;
        }
    }

    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("1:1문의를 진행하겠습니까?")) {
            let formData = new FormData($("#frm")[0]);
            let key_value = [];
            let key_count = 0;

            if (gfnfile.fileMap != '' && gfnfile.fileMap != null) {
                for (let key of gfnfile.fileMap.keys()) {
                    key_value[key_count++] = key;
                    formData.append("file_1[]", gfnfile.fileMap.get(key));
                }
            }

            formData.append("key_val", JSON.stringify(key_value));

            $.ajax({
                url: "/php/inquiry.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);

                    alert(json.msg);

                    if (json.code == 200) {
                        location.href = "<?=shopFoldName?>/mypage/inquiry.php";
                    }
                },
                beforeSend: function() {
                    $(".wrap-loading").removeClass("display-none");
                },
                complete: function() {
                    $(".wrap-loading").addClass("display-none");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("#TYPE_CD").val() == "") {
            alert("문의 유형을 선택해주세요.");
            $("#TYPE_CD").focus();
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
  </script>