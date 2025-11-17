<?php
/**
 * 파일명 : index_image_details.php
 * 내용 : 이미지 관리
 * 최초작성날짜 : 2023/08/30
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/30     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/index_image_details_code.php';
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
                                <h5><strong>메인이미지 관리</strong></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="ibox-content custom_detail">
                                <form id="frm" method="post" action="/php/index_image.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$IMAGE_SEQ?>"/>
                                    <input type="hidden" id="M_TITLE" name="M_TITLE" value="<?=$M_TITLE?>"/>
                                    <input type="hidden" id="M_MAIN_YN" name="M_MAIN_YN" value="<?=$M_MAIN_YN?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID;?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <?php if ($page_type == PAGE2) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">서브명</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="SUB_TITLE" name="SUB_TITLE" value="<?=$_db_SUB_TITLE;?>" placeholder="서브명 입력해주세요." maxlength="100">
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"><?=$validation?>제목</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="TITLE" name="TITLE" value="<?=$_db_TITLE;?>" placeholder="제목을 입력해주세요." maxlength="100">
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
                                                    <input class="touchspin1 form-control" type="text" id="ORDER_NUMBER" name="ORDER_NUMBER" value="<?=$_db_ORDER_NUMBER;?>">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"><?=$validation?>링크</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="LINK_URL" name="LINK_URL" value="<?=$_db_LINK_URL;?>" placeholder="링크를 입력해주세요." maxlength="255">
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE3) { ?>
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
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH2" class="col-sm-1 text-right col-form-label">* 썸네일 이미지</label>

                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH" name="ATTACH" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >썸네일 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID;?>" class="br_img" id="preview" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE1) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label for="ATTACH2" class="col-sm-1 text-right col-form-label">* 호버 이미지</label>

                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input id="ATTACH2" name="ATTACH2" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview2', 'jpg|jpeg|png', 20);">
                                                                <label class="custom-file-label" for="ATTACH2">파일 선택</label>
                                                            </div>
                                                        </div>
                                                        <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row" >
                                                    <label for="" class="col-sm-1 text-right col-form-label" >호버 이미지</br> 미리보기</label>

                                                    <div class="col-sm-10">
                                                        <img src="<?=$_db_MAIN_ATTACH_FILE_ID2;?>" class="br_img" id="preview2" alt="" width="200"/>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH3" class="col-sm-1 text-right col-form-label">* 모바일 이미지</label>

                                                <div class="col-sm-6">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH3" name="ATTACH3" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview3', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH3">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >모바일 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID3;?>" class="br_img" id="preview3" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE3) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">영상여부</label>

                                                    <div class="col-sm-10 m-t-xs">
                                                        <div class="i-checks">
                                                            <label class=""> 
                                                                <div class="icheckbox_square-green"  style="position: relative;">
                                                                    <input type="checkbox" name="VIDEO_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked2?>>
                                                                    <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                </div> 

                                                                <span class="ml-1">영상여부</span>
                                                            </label>
                                                        </div>

                                                        <span class="form-text m-b-none text-navy">영상노출 체크시 영상으로 노출됩니다.</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group row">
                                                    <label for="ATTACH7" class="col-sm-1 text-right col-form-label">영상</label>

                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input id="ATTACH7" name="ATTACH7" type="file" data-idx="0" class="custom-file-input" accept=".mp4" onchange="javascript:fileupload2(this, '#file_list', 'mp4', 20);">
                                                                <label class="custom-file-label" for="ATTACH7">파일 선택</label>
                                                            </div>
                                                        </div>

                                                        <div id="file_list">
                                                            <?php if ($mode == 'MOD') { ?>
                                                                <?= $file_html ?>
                                                            <?php } ?>
                                                        </div>
                                                        <span class="form-text m-b-none">20MB 이하</span>
                                                    </div>
                                                </div>
                                            <?php }?>

                                            <div class="hr-line-solid"></div>

                                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                                <?php if ($mode == "INS") {?>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:reg();">등록</button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-lg btn-danger float-right w60 ml-1" onclick="javascript:del();">삭제</button>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                                <?php }?>
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='index_image_main.php?<?=$query_string?>';">취소</button>
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
    });

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

    function fileupload2(obj, id, strExt, limitSize) {
        gfnfile = {
              mode : 'O' // [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
            , obj  : obj // input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
            , id   : id // 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
            , strExt : strExt// 확장자 ex) jpg|gif|jpeg|png|pdf|zip
            , limitSize : limitSize // 파일의 사이즈를 확인
            , fileMap : '' // mode가 M인경우 다중파일일 경우 값 저장을 위하여
            , formData_del : '' // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
            , del_count : ''// mode가 M인경우 삭제 pk값보관
            , file_list_row : '' // mode가 M인경우 다중파일의 pk값을 보관
            , row_val : ''//mode가 M인경우  다중파일의  max값을 지정해줌
            , ues : 'A'// 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile (gfnfile);
    }

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
        <?php if ($page_type == PAGE1) { ?>
            if ($("#TITLE").val() == "") {
                alert("제목을 입력해주세요.");
                $("#TITLE").focus();
                return false;
            }

            if ($("#LINK_URL").val() == "") {
                alert("링크를 입력해주세요.");
                $("#LINK_URL").focus();
                return false;
            }
        <?php } ?>

        <?php if ($mode == "INS") {?>
            if ($("#ATTACH").val() == "") {
                alert("썸네일 이미지를 선택해주세요.");
                $("#ATTACH").focus();
                return false;
            }

            <?php if ($page_type == PAGE1) { ?>
                if ($("#ATTACH2").val() == "") {
                    alert("호버 이미지를 선택해주세요.");
                    $("#ATTACH2").focus();
                    return false;
                }
            <?php } ?>
            if ($("#ATTACH3").val() == "") {
                alert("모바일 이미지를 선택해주세요.");
                $("#ATTACH3").focus();
                return false;
            }
        <?php } ?>

        return true;
    }
</script>