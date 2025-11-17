<?php
/**
 * 파일명 : home_index_location_main.php
 * 내용 : index_location 내역
 * 최초작성날짜 : 2024/04/25
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준    2024/04/25     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/home_index_location_main_code.php';
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
                                <h5><strong>ABOUT</strong></h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link" >
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content custom_row">
                                <form id="frm" method="post" action="/php/location.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/>
                                    <input type="hidden" id="seq" name="seq" value="<?=$_db_LOCATION_SEQ?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* 제1 주소</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="M_ADDRESS" name="M_ADDRESS" value="<?=$_db_M_ADDRESS;?>" placeholder="주소를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="p_link" class="col-sm-1 text-right col-form-label">제1 네이버 맵 링크</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="M_NAVER_LINK" name="M_NAVER_LINK" value="<?=$_db_M_NAVER_LINK;?>" placeholder="네이버 맵 링크를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="p_link" class="col-sm-1 text-right col-form-label">제1 카카오 맵 링크</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="M_KAKAO_LINK" name="M_KAKAO_LINK" value="<?=$_db_M_KAKAO_LINK;?>" placeholder="카카오 맵 링크를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* 제2 주소</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="D_ADDRESS" name="D_ADDRESS" value="<?=$_db_D_ADDRESS;?>" placeholder="주소를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="p_link" class="col-sm-1 text-right col-form-label">제2 네이버 맵 링크</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="D_NAVER_LINK" name="D_NAVER_LINK" value="<?=$_db_D_NAVER_LINK;?>" placeholder="네이버 맵 링크를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="p_link" class="col-sm-1 text-right col-form-label">제2 카카오 맵 링크</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="D_KAKAO_LINK" name="D_KAKAO_LINK" value="<?=$_db_D_KAKAO_LINK;?>" placeholder="카카오 맵 링크를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label for="p_link" class="col-sm-1 text-right col-form-label">운영</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="OPERATE" name="OPERATE" value="<?=$_db_OPERATE;?>" placeholder="운영시간을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label for="p_link" class="col-sm-1 text-right col-form-label">주차</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="PARKKING" name="PARKKING" value="<?=$_db_PARKKING;?>" placeholder="주차를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label for="p_link" class="col-sm-1 text-right col-form-label">시설</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="FACILITIES" name="FACILITIES" value="<?=$_db_FACILITIES;?>" placeholder="시설을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label for="ATTACH" class="col-sm-1 text-right col-form-label">이미지</label>

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
                                                <label for="" class="col-sm-1 text-right col-form-label" >이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID;?>" class="br_img" id="preview" alt="" width="200"/>
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
            , ues : 'A'// 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile (gfnfile);
    }

    // 등록
    function reg() {
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
        if ($("#M_ADDRESS").val() == "") {
            alert("제1 주소를 입력해주세요.");
            $("#M_ADDRESS").focus();
            return false;
        }

        if ($("#D_ADDRESS").val() == "") {
            alert("제2 주소를 입력해주세요.");
            $("#D_ADDRESS").focus();
            return false;
        }

        return true;
    }
    /*
     function ScodeINS(value, name) {
        if (confirm(name + "번 내용을 추가/수정 하시겠습니까?")) {
            
            var list = {
                  'COM_CD': value
                , 'COM_CD_NM': $("#COM_NM"+ name).val()
                , 'TH1_THEM_CD': $("#TH1_NM"+ name).val()
                , 'mode' : 'ins'
                , 'msg' : '저장완료'
            };

            ufn_change_row(list);
        }
    }

    function ScodeDel(value, name) {
        if (confirm(name + " 번 내용을 제거 하시겠습니까?")) {
            $("#COM_NM"+ name).val("");
            $("#TH1_NM"+ name).val("");
            
            var list = {
                  'COM_CD': value
                , 'COM_CD_NM': ""
                , 'TH1_THEM_CD': ""
                , 'mode' : 'del'
                , 'msg' : '저장완료'
            };

            ufn_change_row(list);
        }
    }

    function ufn_change_row(list) {
        $.ajax({
              type: "POST"
            , url: "/php/home_index_location.php"
            , data: list
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
    */
</script>