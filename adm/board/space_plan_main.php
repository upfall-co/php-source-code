<?php
/**
 * 파일명 : space_plan_main.php
 * 내용 : space 도면 이미지 내역
 * 최초작성날짜 : 2024/03/14
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2024/03/14     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/space_plan_main_code.php';
?>

<body>
    <div id="wrapper">
        <?php
            include_once __DIR__ .'/../common/nav.php';
        ?>

            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5><strong>도면</strong></h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link" >
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content custom_row">
                                <form id="frm" method="post" action="/php/secretCodeManagement.php">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH" class="col-sm-1 text-right col-form-label">B1</label>

                                                <div class="col-sm-5">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH" name="ATTACH" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview1', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="파일저장" onclick="javascript:ScodeINS('1', 'B1');"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="파일삭제" onclick="javascript:ScodeDel('1', 'B1');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >B1 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID?>" class="br_img" id="preview1" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH2" class="col-sm-1 text-right col-form-label">1F</label>

                                                <div class="col-sm-5">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH2" name="ATTACH2" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview2', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH2">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="파일저장" onclick="javascript:ScodeINS('2', '1F');"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="파일삭제" onclick="javascript:ScodeDel('2', '1F');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >1F 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID2?>" class="br_img" id="preview2" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH3" class="col-sm-1 text-right col-form-label">2F</label>

                                                <div class="col-sm-5">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH3" name="ATTACH3" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview3', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH3">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="파일저장" onclick="javascript:ScodeINS('3', '2F');"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="파일삭제" onclick="javascript:ScodeDel('3', '2F');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >2F 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID3?>" class="br_img" id="preview3" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH4" class="col-sm-1 text-right col-form-label">3F</label>

                                                <div class="col-sm-5">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH4" name="ATTACH4" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview4', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH4">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="파일저장" onclick="javascript:ScodeINS('4', '3F');"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="파일삭제" onclick="javascript:ScodeDel('4', '3F');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >3F 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID4?>" class="br_img" id="preview4" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH5" class="col-sm-1 text-right col-form-label">4F</label>

                                                <div class="col-sm-5">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH5" name="ATTACH5" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview5', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH5">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="파일저장" onclick="javascript:ScodeINS('5', '4F');"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="파일삭제" onclick="javascript:ScodeDel('5', '4F');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >4F 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID5?>" class="br_img" id="preview5" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH6" class="col-sm-1 text-right col-form-label">별관</label>

                                                <div class="col-sm-5">
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input id="ATTACH6" name="ATTACH6" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview6', 'jpg|jpeg|png', 20);">
                                                            <label class="custom-file-label" for="ATTACH6">파일 선택</label>
                                                        </div>
                                                    </div>
                                                    <span class="form-text m-b-none">20MB 이하 .jpg .jpeg .png</span>
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="파일저장" onclick="javascript:ScodeINS('6', '별관');"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="파일삭제" onclick="javascript:ScodeDel('6', '별관');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >별관 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID6?>" class="br_img" id="preview6" alt="" width="200"/>
                                                </div>
                                            </div>
                                            
                                            <div class="hr-line-solid"></div>
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

    function ScodeINS(value, name) {
        if (confirm(name + " 도면을 추가/수정 하시겠습니까?")) {
            let formData = new FormData($("#frm")[0]);

            // FormData에 추가 데이터 삽입
            formData.append('GROUP', value);
            formData.append('mode', 'INS');
            formData.append('msg', name + "도면 변경 완료");

            ufn_change_row(formData);
        }
    }

    function ScodeDel(value, name) {
        if (confirm(name + " 도면을 제거 하시겠습니까?")) {
            let formData = new FormData($("#frm")[0]);
            formData.append('GROUP', value);
            formData.append('mode', 'del');
            formData.append('msg', name + "도면 삭제 완료");

            $("#preview" + value).attr('src', "");

            ufn_change_row(formData);
        }
    }

    function ufn_change_row(formData) {
        $.ajax({
              type: "POST"
            , url: "/php/spaceplan.php"
            , data: formData
            , contentType: false
            , processData: false
            , success: function(data) {
                // 처리 성공 시 실행할 코드
                    let json = JSON.parse(data);
                    alert(json.msg);
              }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>