<?php
/**
 * 파일명 : space_details.php
 * 내용 : 공간 관리페이지
 * 최초작성날짜 : 2023/11/29
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/11/29     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/space_details_code.php';
?>

<body class="pace-done">
    <div id="wrapper">
        <?php
            include_once __DIR__ .'/../common/nav.php';
        ?>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5><strong>공간 관리</strong></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="ibox-content custom_detail">
                                <form id="frm" method="post" action="/php/space.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$SPACE_SEQ?>"/>
                                    <input type="hidden" id="M_TYPE_CD" name="M_TYPE_CD" value="<?=$M_TYPE_CD?>"/>
                                    <input type="hidden" id="M_TITLE" name="M_TITLE" value="<?=$M_TITLE?>"/>
                                    <input type="hidden" id="M_MAIN_YN" name="M_MAIN_YN" value="<?=$M_MAIN_YN?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID;?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* 층수</label>

                                                <div class="col-sm-2">
                                                    <select class="form-control" id="TYPE_CD" name="TYPE_CD" style="width:200px;">
                                                        <?php gfn_getComboList("층수","COL011", $_db_TYPE_CD , "층수"); ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">노출여부</label>

                                                <div class="col-sm-10 m-t-xs">
                                                    <div class="i-checks">
                                                        <label class=""> 
                                                            <div class="icheckbox_square-green"  style="position: relative;">
                                                                <input type="checkbox" name="MAIN_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked?>>
                                                                <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                            </div>

                                                            <span class="ml-1">노출여부</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">정렬</label>

                                                <div class="col-sm-2">
                                                    <input class="touchspin1 form-control" type="text" id="ORDER_NUMBER" name="ORDER_NUMBER" value="<?=$_db_ORDER_NUMBER;?>" maxlength="20">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* 제목</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="TITLE" name="TITLE" value="<?=$_db_TITLE;?>" placeholder="제목을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 기간</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="DATE_TEXT" name="DATE_TEXT" value="<?=$_db_DATE_TEXT;?>" placeholder="기간을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 연락처</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="MOBILE" name="MOBILE" value="<?=$_db_MOBILE;?>" placeholder="연락처을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 이메일</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="EMAIL" name="EMAIL" value="<?=$_db_EMAIL;?>" placeholder="이메일을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 썸네일이미지<br> 최대 5장<br> .jpg .jpeg .png</label>

                                                <div class="col-sm-11">
                                                    <div class="dropzone" id="dropzoneForm">
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple />
                                                        </div>
                                                        <?php if ($mode == 'MOD') {?>
                                                            <?= $file_html ?>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="hr-line-solid"></div>

                                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                                <?php if ($mode == "INS") {?>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:reg();">등록</button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-lg btn-danger float-right w60 ml-1" onclick="javascript:del();">삭제</button>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                                <?php }?>
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='space_main.php?<?=$query_string?>';">취소</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            //footer
            include_once __DIR__ .'/../common/footer.php';
            ?>
        </div>
    </div>

<?php
    //top_btn
    include_once __DIR__ .'/../common/bottom.php';
?>
<script>
    let fileMap = new Map();
    let formData_del = [];
    let del_count = 0;

    let fileInfo_val = "";

    let max_val = 5;
    let limitSize = 10;
    let str_allowed_ext = ".jpg, .jpeg, .png";

    let Ndel_chk = "N"; // 삭제 취소시 Y 값 적용
    let dfDel_chk ="N"; // 에러 삭제시
    let data_type = "I";

    let max_alert_chk = "N"; // max시 alert 한번만
    let limitSize_alert_chk = "N"; // 사이즈 초과시 alert 한번만
    let str_allowed_alert_chk = "N"; // 확장자가 다를시 alert 한번만
    
    <?php
        if (!empty($file_json)) {
            echo 'const fileData = ' . $file_json . ';';
        } else {
            echo 'const fileData = [];';
        }
    ?>

    function convertFileToDataURL(file, callback) {
        var reader = new FileReader();
        
        reader.onload = function(event) {
            callback(event.target.result);
        };

        reader.readAsDataURL(file);
    }

    //드롭 이미지
    Dropzone.options.dropzoneForm = { 
        url: "/php/space.php",
        method: "post",
        maxFilesize: limitSize, // MB
        acceptedFiles: ".jpg, .jpeg, .png", // 허용되는 파일 타입
        maxFiles: max_val, // 최대 업로드 가능한 파일 개수
        parallelUploads:5,
        uploadMultiple:true, //멀티파일 업로드
        addRemoveLinks: true, // 업로드된 파일 삭제 링크 표시
        dictRemoveFile: "삭제", // 삭제 버튼 텍스트 설정
        dictDefaultMessage: '<strong>이미지 미리보기</strong><br>(10MB 이하 / 최대 5개 .jpg .jpeg .png)',
    
        init: function() {
            const dropzoneInstance = this;

            if (fileData.length > 0) {
                fileData.forEach(function(fileInfo) {
                    fileInfo_val = fileInfo;
                    
                    fetch(fileInfo.PATH).then(response => response.blob()).then(blob => {
                        // Blob 데이터를 사용하여 File 객체를 생성합니다
                        let mockFile = new File([blob], fileInfo.ATTACH_FILE_REAL_NAME, {
                            type: fileInfo.ATTACH_FILE_TYPE,
                            size: blob.size // blob의 실제 크기를 사용합니다
                        });

                        // Dropzone에 파일 추가
                        data_type = "N";
                        dropzoneInstance.addFile(mockFile);

                        // 파일 타입에 따라 썸네일을 설정합니다
                        if (fileInfo.ATTACH_FILE_TYPE === 'pdf') {
                            // PDF 파일의 경우 대체 이미지 사용
                            dropzoneInstance.createThumbnailFromUrl(mockFile, '/img/icon/pdf.png');
                        } else {
                            // 이미지 파일의 경우 원래 URL 사용
                            dropzoneInstance.createThumbnailFromUrl(mockFile, URL.createObjectURL(blob));
                        }

                        mockFile.data_group = fileInfo.ATTACH_GROUP;
                        mockFile.data_group_count = parseInt(fileInfo.ATTACH_GROUP_COUNT);
                        mockFile.data_type = fileInfo.data_type;

                        // 여기에서 클릭 이벤트 리스너를 추가합니다
                        mockFile.previewElement.addEventListener("click", function() {
                            window.open(fileInfo.PATH, '_blank');
                        });

                        fileMap.set(parseInt(fileInfo.ATTACH_GROUP_COUNT), mockFile);
                    }).catch(error => {
                        console.error('Error fetching file:', error);
                    });
                });

                
            } else {
                // fileData가 비어있는 경우에 대한 처리
                // 예: 어떤 메시지를 화면에 표시하거나 아무 작업도 하지 않는다.
            }

            // 파일이 서버로 업로드되기 전에 실행되는 함수
            dropzoneInstance.on("sending", function(file, xhr, formData) {
                
            });

            // 파일 추가시
            dropzoneInstance.on("addedfile", function(file) {
                if (file.data_type != "N") {
                    data_type = "I";
                }
            });

            // 이미지 업로드 성공 후 실행되는 함수
            dropzoneInstance.on("success", function(file, response) {
                if (Ndel_chk == "N" && file.data_type != "N")  {
                    let maxDataGroupCount = getMaxDataGroupCount(dropzoneInstance); // 새로 추가된 파일 객체에 추가 정보를 설정
                    let newDataGroupCount = maxDataGroupCount + 1; // 새 파일의 data_group_count 값 설정

                    file.data_group  = 3;
                    file.data_group_count = parseInt(newDataGroupCount);
                    file.data_type = "I";

                    fileMap.set(newDataGroupCount, file);
                } 

                Ndel_chk = "N";
                max_alert_chk = "N";
                limitSize_alert_chk = "N";
                str_allowed_alert_chk = "N";
            });

            // 추가 정보를 이용해 파일 삭제 등의 작업 수행
            dropzoneInstance.on("removedfile", function(file) {
                if (dfDel_chk == "N") {
                    let data_group = file.data_group;
                    let data_group_count = parseInt(file.data_group_count);

                    let chk = 'N';

                    fileMap.delete(data_group_count);

                    for (let i = 0; i < formData_del.length; i++) {
                        if (data_group_count == formData_del[i]) {
                            chk = 'Y';
                            break;
                        }
                    }

                    if (chk == 'N') {
                        formData_del[del_count++] = data_group_count;
                    }
                }

                dfDel_chk = "N";
            });

            dropzoneInstance.on("error", function(file, errorMessage, xhr) {
                if (errorMessage == "You can not upload any more files.") { //maxfilesreached
                    if (max_alert_chk == "N") {
                        alert("첨부파일은 " + max_val + "개까지 업로드 가능합니다.");
                        max_alert_chk = "Y";
                    }

                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                } else if (errorMessage == "You can't upload files of this type.") {
                    if (str_allowed_alert_chk == "N") {
                        alert("첨부 파일은 " + str_allowed_ext + " 확장자만 가능합니다.");
                        str_allowed_alert_chk = "Y";
                    }

                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                } else if (errorMessage.includes("File is too big")) {
                    if (limitSize_alert_chk == "N") {
                        alert("파일용량은 " + limitSize + "MB 까지 가능합니다.");
                        limitSize_alert_chk = "Y";
                    }

                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                } else { // 에러는 점차 추가
                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                }
            });
        }
    };

    // data_group_count값 확인
    function getMaxDataGroupCount(obj) {
        var maxDataGroupCount = 0;

        for (var i = 0; i < obj.files.length; i++) {
            var file = obj.files[i];

            if (file.data_group_count > maxDataGroupCount) {
                maxDataGroupCount = file.data_group_count;
            }
        }
        
        return maxDataGroupCount;
    }

     $(document).ready(function() {
        //정렬 버튼(+/-)
        $(".touchspin1").TouchSpin({
            buttondown_class: 'btn btn-white',
            buttonup_class: 'btn btn-white'
        });

        //라디오 버튼
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        // 라디오 버튼 클릭 이벤트 처리
        $('input[type="radio"]').on('ifChecked', function(event) {
            // 모든 라디오 버튼의 checked 클래스를 제거
            $('input[type="radio"]').parent().parent().removeClass('checked');
        });
    });

    function reg() { // 등록

        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("등록하시겠습니까?")) {
            let formData = new FormData($("#frm")[0]);

            let key_value = [];
            let key_count = 0;

            for (let key of fileMap.keys()) { // 제품사양
                key_value[key_count++] = key;
                formData.append("files[]", fileMap.get(key));
            }
            
            formData.append("key_val", JSON.stringify(key_value));

            $.ajax({
                url: "/php/space.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        location.href = "../board/space_main.php?<?=$query_string?>";
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

    function mod() { // 수정
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("수정하시겠습니까?")) {
            let myDropzone = Dropzone.forElement("#dropzoneForm");
            let formData = new FormData($("#frm")[0]);

            let key_value = [];
            let key_count = 0;

            for (let key of fileMap.keys()) { // 제품사양
                if (fileMap.get(key).data_type == "I") {
                    key_value[key_count++] = key;
                    formData.append("files[]", fileMap.get(key));
                }
            }

            formData.append("key_val", JSON.stringify(key_value));
            formData.append("formData_del", JSON.stringify(formData_del));

            $.ajax({
                url: "/php/space.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        location.href = "../board/space_main.php?<?=$query_string?>";
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

    function del() { // 삭제
        if (confirm("삭제하시겠습니까?")) { 
            var mode = "DEL";

            // mode input 요소의 값을 변경합니다.
            $("#mode").val(mode);
            let formData = new FormData($("#frm")[0]);

            $.ajax({
                url: "/php/space.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        location.href = "../board/space_main.php?<?=$query_string?>";
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
        if ($("#TYPE_CD").val() == "") {
            alert("층수를 선택해주세요.");
            $("#TYPE_CD").focus();
            return false;
        }

        if ($("#TITLE").val() == "") {
            alert("제목을 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }

        if (0 >= fileMap.size ) {
            alert("썸네일 이미지를 하나이상 선택해주세요.");
            $('[name="file"]').focus();
            return false;
        }

        return true;
    }
</script>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>