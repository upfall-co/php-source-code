<?php
/**
 * 파일명 : category_details.php
 * 내용 : HOME - 카테고리 관리페이지
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
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/category_details_code.php';
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
                                <h5><strong><?=$title_name?> 관리</strong></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="ibox-content custom_detail">
                                <form id="frm" method="post" action="/php/category.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$CATEGORY3_SEQ?>"/>
                                    <input type="hidden" id="M_CATEGORY1_SEQ" name="M_CATEGORY1_SEQ" value="<?=$M_CATEGORY1_SEQ?>"/>
                                    <input type="hidden" id="M_CATEGORY2_SEQ" name="M_CATEGORY2_SEQ" value="<?=$M_CATEGORY2_SEQ?>"/>
                                    <input type="hidden" id="M_start_date" name="M_start_date" value="<?=$M_start_date?>"/>
                                    <input type="hidden" id="M_end_date" name="M_end_date" value="<?=$M_end_date?>"/>
                                    <input type="hidden" id="M_TITLE" name="M_TITLE" value="<?=$M_TITLE?>"/>
                                    <input type="hidden" id="M_TITLE_YN" name="M_TITLE_YN" value="<?=$M_TITLE_YN?>"/>
                                    <input type="hidden" id="M_MAIN_YN" name="M_MAIN_YN" value="<?=$M_MAIN_YN?>"/>
                                    <input type="hidden" id="R_CATEGORY1_SEQ" name="R_CATEGORY1_SEQ" value="<?=$_db_CATEGORY1_SEQ?>"/>
                                    <input type="hidden" id="R_ORDER_NUMBER" name="R_ORDER_NUMBER" value="<?=$_db_ORDER_NUMBER?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID;?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <?php if ($M_CATEGORY1_SEQ != "COLLABO") { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">* 분류</label>

                                                    <div class="col-sm-7">
                                                        <select class="form-control" id="CATEGORY2_SEQ" name="CATEGORY2_SEQ" style="width:200px;">
                                                            <option value="">선택</option>
                                                            <?php getCATEGORY2ComboList(); ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">메인노출여부</label>

                                                    <div class="col-sm-10 m-t-xs">
                                                        <div class="i-checks">
                                                            <label class=""> 
                                                                <div class="icheckbox_square-green"  style="position: relative;">
                                                                    <input type="checkbox" name="TITLE_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked?>>
                                                                    <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                </div>

                                                                <span class="ml-1">메인노출여부</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">노출여부</label>

                                                <div class="col-sm-10 m-t-xs">
                                                    <div class="i-checks">
                                                        <label class=""> 
                                                            <div class="icheckbox_square-green"  style="position: relative;">
                                                                <input type="checkbox" name="MAIN_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked2?>>
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
                                                    <input class="touchspin1 form-control" type="text" id="ORDER_NUMBER" name="ORDER_NUMBER" value="<?=$_db_ORDER_NUMBER;?>">

                                                    <span class="form-text m-b-none text-navy">중복일시 최대값으로 변경됩니다.</span>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* 제목 첫번째 줄</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="TITLE" name="TITLE" value="<?=$_db_TITLE;?>" placeholder="제목을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 제목 두번째줄</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="SUB_TITLE" name="SUB_TITLE" value="<?=$_db_SUB_TITLE;?>" placeholder="제목 설명을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 기간</label>

                                                <div class="col-sm-10 custom_detail_date">
                                                    <div class="form-group row ml-1">
                                                        <div class="input-group date w140" id="data_1">
                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="SDATE" value="<?= $_db_SDATE?>">
                                                        </div>

                                                        <span class="ml-2 mr-2 mt-2">~</span>

                                                        <div class="input-group date w140" id="data_2">
                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="EDATE" value="<?= $_db_EDATE?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($M_CATEGORY1_SEQ != "COLLABO") { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 링크</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="LINK_URL" name="LINK_URL" value="<?=$_db_LINK_URL;?>" placeholder="링크를 입력해주세요." maxlength="100">
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">관련 제품 리스트</label>

                                                <div class="col-sm-6">
                                                    <select class="select2_demo_3 form-control" id="RELATED_VALUE[]" name="RELATED_VALUE[]" multiple="multiple">
                                                        <option></option>
                                                        <?php getRELATEDComboList(); ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 상세 제목</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="CONTENT_TITLE" name="CONTENT_TITLE" value="<?=$_db_CONTENT_TITLE;?>" placeholder="상세 제목을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 내용</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" id="CONTENT_TEXT" name="CONTENT_TEXT"><?=$_db_CONTENT_TEXT;?></textarea>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label for="ATTACH" class="col-sm-1 text-right col-form-label">* 썸네일 이미지</label>

                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH" name="ATTACH" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview', 'jpg|jpeg|png', 10);">
                                                            <label class="custom-file-label" for="ATTACH">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">10MB 이하 .jpg .jpeg .png</span>
                                                </div>
                                            </div>
                                        
                                            <div class="hr-line-dashed"></div>
                                            
                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >썸네일 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID;?>" class="br_img" id="preview" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 멀티 이미지<br> 최대 10장<br> .jpg .jpeg .png</label>

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
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='<?=$url_name?>_main.php?<?=$query_string?>';">취소</button>
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
    let gfnfile = [];

    //파일업로드
    function fileupload(obj, id, strExt, limitSize) {
        gfnfile = {
          mode : 'I' // [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
        , obj  : obj // input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
        , id   : id // 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
        , strExt : strExt// 확장자 ex) jpg|gif|jpeg|png|pdf|zip
        , limitSize : limitSize // 파일의 사이즈를 확인
        , fileMap : '' // mode가 M인경우 다중파일일 경우 값 저장을 위하여
        , formData_del : '' // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
        , del_count : ''// mode가 M인경우 삭제 pk값보관
        , file_list_row : '' // mode가 M인경우 다중파일의 pk값을 보관
        , row_val : ''//mode가 M인경우  다중파일의  max값을 지정해줌
        , ues : ''// 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile (gfnfile);
    }

    let fileMap = new Map();
    let formData_del = [];
    let del_count = 0;

    let fileInfo_val = "";

    let max_val = 10;
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
        url: "/php/category.php",
        method: "post",
        maxFilesize: limitSize, // MB
        acceptedFiles: ".jpg, .jpeg, .png", // 허용되는 파일 타입
        maxFiles: max_val, // 최대 업로드 가능한 파일 개수
        parallelUploads:5,
        uploadMultiple:true, //멀티파일 업로드
        addRemoveLinks: true, // 업로드된 파일 삭제 링크 표시
        dictRemoveFile: "삭제", // 삭제 버튼 텍스트 설정
        dictDefaultMessage: '<strong>이미지 미리보기</strong><br>(10MB 이하 / 최대 10개 .jpg .jpeg .png)',
    
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
        //달력
        $('#data_1').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        $('#data_2').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

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

        //CKEDITOR
        CKEDITOR.editorConfig = function(config) {
            config.colorButton_foreStyle = {
                element: 'font',
                attributes: {
                    'color': '#(color)'
                }
            };
        }

        CKEDITOR.replace('CONTENT_TEXT', {
            height: 600,
            editorplaceholder: '내용을 입력해주세요.',
            allowedContent: true
        });

        CKEDITOR.instances['CONTENT_TEXT'].setData($("#CONTENT_TEXT").val());

        $(".select2_demo_3").select2({
            theme: 'bootstrap4',
            placeholder: "관련 제품을 선택해주세요.",
            maximumSelectionLength: 3
        });

        let commaSeparatedValues = "<?=$_db_RELATED_VALUE?>";

        if (!gfn_isNull(commaSeparatedValues)) {
            let valuesArray = commaSeparatedValues.split(',');

            // select2 요소에 값 설정
            $(".select2_demo_3").val(valuesArray).trigger('change');
        }
    });

    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("등록하시겠습니까?")) {
            if (CKEDITOR.instances['CONTENT_TEXT']) {
                CKEDITOR.instances['CONTENT_TEXT'].updateElement();
            }

            let formData = new FormData($("#frm")[0]);

            let key_value = [];
            let key_count = 0;

            for (let key of fileMap.keys()) { // 제품사양
                key_value[key_count++] = key;
                formData.append("files[]", fileMap.get(key));
            }
            
            formData.append("key_val", JSON.stringify(key_value));

            $.ajax({
                url: "/php/category.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        location.href = "<?=$back_url?>?<?=$query_string?>";
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
            if (CKEDITOR.instances['CONTENT_TEXT']) {
                CKEDITOR.instances['CONTENT_TEXT'].updateElement();
            }

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
                url: "/php/category.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        location.href = "<?=$back_url?>?<?=$query_string?>";
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
            $("#frm").submit();
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        <?php if ($mode == "INS") {?>
            if ($("#CATEGORY2_SEQ").val() == "") {
                alert("분류을/를 선택해주세요.");
                $("#CATEGORY2_SEQ").focus();
                return false;
            }
        <?php } ?>

        if ($("#TITLE").val() == "") {
            alert("제목을/를 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }

        <?php if ($mode == "INS") {?>
                if ($("#ATTACH").val() == "") {
                    alert("썸네일 이미지를 선택해주세요.");
                    $("#ATTACH").focus();
                    return false;
                }
            /*
            if ($("#ATTACH").val() == "") {
                alert("썸네일 이미지를 선택해주세요.");
                $("#ATTACH").focus();
                return false;
            }
            */
        <?php } ?>

        return true;
    }
</script>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>