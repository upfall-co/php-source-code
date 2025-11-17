<?php
/**
 * 파일명 : series_details.php
 * 내용 : 시리즈 관리
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
 * 김민성    2023/11/10    shop 기능추가
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/series_details_code.php';
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
                                <form id="frm" method="post" action="/php/series.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="sub_type" name="sub_type" value="<?=$sub_type;?>"/>
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$CATEGORY2_SEQ?>"/>
                                    <input type="hidden" id="M_PROGRAM_CD" name="M_PROGRAM_CD" value="<?=$M_PROGRAM_CD?>"/>
                                    <input type="hidden" id="M_CATEGORY1_SEQ" name="M_CATEGORY1_SEQ" value="<?=$M_CATEGORY1_SEQ?>"/>
                                    <input type="hidden" id="M_TITLE" name="M_TITLE" value="<?=$M_TITLE?>"/>
                                    <input type="hidden" id="M_MAIN_YN" name="M_MAIN_YN" value="<?=$M_MAIN_YN?>"/>
                                    <input type="hidden" id="R_CATEGORY1_SEQ" name="R_CATEGORY1_SEQ" value="<?=$_db_CATEGORY1_SEQ?>"/>
                                    <input type="hidden" id="R_ORDER_NUMBER" name="R_ORDER_NUMBER" value="<?=$_db_ORDER_NUMBER?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID;?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">* 중분류</label>

                                                    <div class="col-sm-7">
                                                        <select class="form-control" id="PROGRAM_CD" name="PROGRAM_CD" style="width:200px;" <?=$disabled?>>
                                                            <?php gfn_getComboList("중분류", "COL014", $_db_PROGRAM_CD,"S")?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* <?=$category1_val?></label>

                                                <div class="col-sm-7">
                                                    <select class="form-control" id="CATEGORY1_SEQ" name="CATEGORY1_SEQ" style="width:200px;" <?=$disabled?>>
                                                        <option value="">선택</option>
                                                        <?php getARTISTComboList(); ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* <?=$title_name?></label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="TITLE" name="TITLE" value="<?=$_db_TITLE;?>" placeholder="<?=$title_val?>을/를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE3) { 
                                                if ($sub_type == SUB_PAGE1) {?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">세부 업종(상세)</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="SUB_TITLE" name="SUB_TITLE" value="<?=$_db_SUB_TITLE;?>" placeholder="세부 업종(상세)을/를 입력해주세요." maxlength="100">
                                                    </div>
                                                </div>
                                                <?php }
                                            }?>

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
                                                    <input class="touchspin1 form-control" type="number" id="ORDER_NUMBER" name="ORDER_NUMBER" value="<?=$_db_ORDER_NUMBER;?>">

                                                    <span class="form-text m-b-none text-navy">중복일시 최대값으로 변경됩니다.</span>
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE1) { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label for="ATTACH2" class="col-sm-1 text-right col-form-label">썸네일 이미지</label>

                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input id="ATTACH2" name="ATTACH2" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview2', 'jpg|jpeg|png', 10);">
                                                                <label class="custom-file-label" for="ATTACH2">파일 선택</label>
                                                            </div>
                                                        </div>
                                                        <span class="form-text m-b-none">10MB 이하 .jpg .jpeg .png</span>
                                                    </div>
                                                </div>
                                            
                                                <div class="hr-line-dashed"></div>
                                                
                                                <div class="form-group row" >
                                                    <label for="" class="col-sm-1 text-right col-form-label" >썸네일 이미지</br> 미리보기</label>

                                                    <div class="col-sm-10">
                                                        <img src="<?=$_db_MAIN_ATTACH2_FILE_ID;?>" class="br_img" id="preview2" alt="" width="200"/>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label for="ATTACH" class="col-sm-1 text-right col-form-label">* 호버 이미지</label>

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
                                                    <label for="" class="col-sm-1 text-right col-form-label" >호버 이미지</br> 미리보기</label>

                                                    <div class="col-sm-10">
                                                        <img src="<?=$_db_MAIN_ATTACH_FILE_ID;?>" class="br_img" id="preview" alt="" width="200"/>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if ($page_type == PAGE3) { 
                                                if ($sub_type == SUB_PAGE1) {?>
                                                <div class="hr-line-dashed"></div>

                                                <div>
                                                    <textarea id="editor" name="editor"><?=$_db_CONTENT_TEXT;?></textarea>
                                                </div>
                                                <?php }
                                            }?>

                                            <div class="hr-line-solid"></div>

                                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                                <?php if ($mode == "INS") {?>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:reg();">등록</button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-lg btn-danger float-right w60 ml-1" onclick="javascript:del();">삭제</button>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                                <?php }?>
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='<?=$back_url?>';">취소</button>
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
    if ('<?=$sub_type?>' == '<?=SUB_PAGE2?>') {
        let PROGRAM_CD_value = '<?=$_db_PROGRAM_CD?>'; // 분류 값

        gfn_setupSelectBox('PROGRAM_CD','CATEGORY1_SEQ',PROGRAM_CD_value);
    }
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

    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("등록하시겠습니까?")) {
            $("#frm").submit();
        }
    }

    function mod() { // 수정
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("수정하시겠습니까?")) {
            $("#frm").submit();
        }
    }

    function del() { // 삭제
        if ("시리즈" == "<?=$title_name?>") {
            msg = "(삭제 시 작품내역도 전부 삭제됩니다. [복구 불가능])";
        } else if ("분류" == "<?=$title_name?>") {
            msg = "(삭제 시 상품도 전부 삭제됩니다. [복구 불가능])";

            if ("<?=$sub_type?>" == "program") {
                msg = "(삭제 시 프로그램도 전부 삭제됩니다. [복구 불가능])";
            }
        } else if ("세부 업종" == "<?=$title_name?>") {
            msg = "";
        }

        if (confirm("삭제하시겠습니까?\n" + msg + "")) { 
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
        <?php if ($mode == "INS") { ?>
            <?php   if ($page_type==PAGE3 && $sub_type==SUB_PAGE2) { ?>
                if ($("#PROGRAM_CD").val() == "") {
                    alert("중분류을/를 선택해주세요.");
                    $("#PROGRAM_CD").focus();
                    return false;
                }    
            <?php } ?>
            if ($("#CATEGORY1_SEQ").val() == "") {
                alert("<?=$category1_val?>을/를 선택해주세요.");
                $("#CATEGORY1_SEQ").focus();
                return false;
            }
        <?php } ?>

        if ($("#TITLE").val() == "") {
            alert("<?=$title_name?>을/를 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }

        <?php if ($mode == "INS") {?>
            <?php if ($page_type == PAGE1) { ?>
                if ($("#ATTACH").val() == "") {
                    alert("호버 이미지를 선택해주세요.");
                    $("#ATTACH").focus();
                    return false;
                }
            /*
            if ($("#ATTACH2").val() == "") {
                alert("썸네일 이미지를 선택해주세요.");
                $("#ATTACH2").focus();
                return false;
            }
            */
            <?php } ?>
        <?php } ?>

        return true;
    }
</script>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>