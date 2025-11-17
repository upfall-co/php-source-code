<?php
/**
 * 파일명 : Inquiry_details.php
 * 내용 : 1:1문의 상세
 * 최초작성날짜 : 2023/08/09
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/09     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';
    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/Inquiry_details_code.php';
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
                                <h5><strong>1:1문의 관리</strong></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="ibox-content custom_detail">
                                <form id="frm" method="post" action="/php/inquiry.php" enctype="multipart/form-data">
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/> 
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$INQUIRY_SEQ?>"/>
                                    <input type="hidden" id="ANSWERS_SEQ" name="ANSWERS_SEQ" value="<?=$ANSWERS_SEQ?>"/>
                                    <input type="hidden" id="PAGE_TYPE" name="PAGE_TYPE" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="M_TYPE_CD" name="M_TYPE_CD" value="<?=$M_TYPE_CD;?>"/>
                                    <input type="hidden" id="M_NAME" name="M_NAME" value="<?=$M_NAME;?>"/>
                                    <input type="hidden" id="M_TITLE" name="M_TITLE" value="<?=$M_TITLE;?>"/>
                                    <input type="hidden" id="M_QUESTION_CD" name="M_QUESTION_CD" value="<?=$M_QUESTION_CD;?>"/>
                                    <input type="hidden" id="M_start_date" name="M_start_date" value="<?=$M_start_date;?>"/>
                                    <input type="hidden" id="M_end_date" name="M_end_date" value="<?=$M_end_date;?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">회원 ID</label>

                                                <div class="col-sm-4">
                                                    <p type="text" class="form-control"><?=$_db_ID;?></p>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">이름</label>

                                                <div class="col-sm-4">
                                                    <p type="text" class="form-control"><?=$_db_NAME;?></p>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">연락처</label>

                                                <div class="col-sm-4">
                                                    <p type="text" class="form-control"><?=$_db_MOBILE;?></p>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">이메일</label>

                                                <div class="col-sm-4">
                                                    <p type="text" class="form-control"><?=$_db_EMAIL;?></p>
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE1) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">주문번호</label>

                                                    <div class="col-sm-4">
                                                        <p type="text" class="form-control"><?=$_db_PURCHASE_SEQ;?></p>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">문의작품</label>

                                                    <div class="col-sm-4">
                                                        <p type="text" class="form-control"><?=$_db_PRODUCT_TITLE;?></p>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">문의분류</label>

                                                    <div class="col-sm-4">
                                                        <p type="text" class="form-control"><?=$_db_TYPE_CD_NM;?></p>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">문의제목</label>

                                                <div class="col-sm-4">
                                                    <p type="text" class="form-control"><?=$_db_TITLE;?></p>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">문의내용</label>

                                                <div class="col-sm-6" style="height:300px;">
                                                    <textarea class="form-control h-100" style="background-color:white" readonly><?=$_db_CONTENT_TEXT?></textarea>
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE2) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 첨부파일</label>

                                                    <div class="col-sm-11">
                                                        <div class="dropzone" id="dropzoneForm">
                                                            <div class="fallback">
                                                                <input name="file" type="file" multiple />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-solid"></div>

                                            <h5><strong>답변 등록</strong></h5>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* 답변상태</label>
    
                                                <div class="col-sm-7">
                                                    <select class="form-control" id="QUESTION_CD" name="QUESTION_CD" style="width:200px;">
                                                        <?php gfn_getComboList("답변상태","AD008",$type_gb,"답변상태", "", "", "Y"); ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div>
                                                <textarea id="editor" name="editor"><?=$_db_AN_CONTENT_TEXT;?></textarea>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                                <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='Inquiry_main.php?<?=$query_string;?>';">취소</button>
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
     //에디터
     $(function () {
        //CKEDITOR
        CKEDITOR.editorConfig = function(config) {
            config.colorButton_foreStyle = {
                element: 'font',
                attributes: {
                    'color': '#(color)'
                }
            };
        }

        CKEDITOR.replace('editor', {
            height: 600,
            editorplaceholder: '내용을 입력해주세요.',
            allowedContent: true
        });

        CKEDITOR.instances['editor'].setData($("#editor").val());
    });


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

    function mod() { // 수정
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("수정하시겠습니까?")) {
            $("#frm").submit();
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
        if ($("#TITLE").val() == "") {
            alert("제목을 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }

        return true;
    }

    <?php
        if (!empty($file_json)) {
            echo 'const fileData = ' . $file_json . ';';
        } else {
            echo 'const fileData = [];';
        }
    ?>

    Dropzone.options.dropzoneForm = {
        url: "/php/inquiry.php",
        clickable: false,
        dictDefaultMessage:'',
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
                            dropzoneInstance.createThumbnailFromUrl(mockFile, '/img/icon/pdf.png');
                        } else {
                            dropzoneInstance.createThumbnailFromUrl(mockFile, URL.createObjectURL(blob));
                        }

                        mockFile.data_group = fileInfo.ATTACH_GROUP;
                        mockFile.data_group_count = parseInt(fileInfo.ATTACH_GROUP_COUNT);
                        mockFile.data_type = fileInfo.data_type;

                        // 여기에서 클릭 이벤트 리스너를 추가합니다
                        mockFile.previewElement.addEventListener("click", function() {
                            window.open(fileInfo.PATH, '_blank');
                        });
                    }).catch(error => {
                        console.error('Error fetching file:', error);
                    });
                });
            }
        }
    }
</script>