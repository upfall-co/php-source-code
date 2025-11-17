<?php
//head
define("SUB", "");
include_once __DIR__ .'/../common/head.php';

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

$mysqldb    = new Pdo7();
$clefResult = new ClefResult();

try {
    //파라미터 정리
    $search_type = get_request_param('search_type', 'GET');
    $search_text = get_request_param('search_text', 'GET');
    $page = get_request_param('page', 'GET');
    $page_type      = get_request_param('page_type', 'GET');
    $m_seq = get_request_param('m_seq', 'GET');
    $mp_seq = get_request_param('mp_seq', 'GET');

    //파라미터 체크
    if (!is_numeric($page)) {
        $page = 1;
    }

    //변수 정리
    $yyyymmdd= date('Y-m-d');
    $table = 'popup';
    $checked = 'checked'; // 노출여부

    //페이징
    $arrParams = array(
        'page' => $page,
        'search_type' => $search_type,
        'search_text' => $search_text,
        'page_type' => $page_type,
        'm_seq' => $m_seq,
        'mp_seq' => $mp_seq
    );
    
    $query_string = http_build_query($arrParams);

} catch (Exception $e) {

}
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
                            <h5><strong>팝업 등록</strong></h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="ibox-content">
                            <form id="frm" method="post" action="../program/<?=$table;?>.php" enctype="multipart/form-data">
                                <input type="hidden" id="mode" name="mode" value="reg"/>
                                <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>

                                <div class="row">
                                    <div class="col-sm-12 b-r">
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label text-right">* 제목</label>

                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="p_title" name="p_title" placeholder="제목을 입력해주세요." maxlength="255">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="p_img" class="col-sm-1 col-form-label text-right">* 이미지</label>

                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input id="p_img" name="p_img" type="file" multiple="" class="custom-file-input" accept=".jpg,.jpeg,.png">
                                                        <label class="custom-file-label" for="p_img">파일 선택</label>
                                                    </div>
                                                </div>
                                                <span class="form-text m-b-none">10MB 이하 .jpg .jpeg .png</span>
                                            </div>
                                        </div>
                                        <div class="form-group row" >
                                            <label for="" class="col-sm-1 col-form-label text-right" >이미지 미리보기</label>

                                            <div class="col-sm-10">
                                                <img src="" class="br_img" id="preview" alt="" width="200"/>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label text-right">링크</label>

                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="p_link" name="p_link" placeholder="링크를 입력해주세요." maxlength="255">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label text-right">* 노출위치-X좌표</label>

                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" id="p_x" name="p_x" placeholder="메인에 노출될 위치(x좌표)를 입력해주세요." required="required">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label text-right">* 노출위치-Y좌표</label>

                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" id="p_y" name="p_y" placeholder="메인에 노출될 위치(Y좌표)를 입력해주세요." required="required">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label text-right">* 노출기간</label>

                                            <div class="col-sm-10">
                                                <div class="form-group row ml-1">
                                                    <div class="input-group date w140" id="data_1">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="start" value="<?= $yyyymmdd?>">
                                                    </div>

                                                    <span class="ml-2 mr-2 mt-2">~</span>

                                                    <div class="input-group date w140" id="data_2">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="end" value="<?= $yyyymmdd?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">노출여부</label>

                                            <div class="col-sm-10 m-t-xs">
                                                <div class="i-checks">
                                                    <label class=""> 
                                                        <div class="icheckbox_square-green"  style="position: relative;">
                                                            <input type="checkbox" name="p_open_yn" value="Y" style="position: absolute; opacity: 0;" <?=$checked?>>
                                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                        </div> 

                                                        <span class="ml-1">노출여부</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hr-line-solid"></div>
                                        <div class="dv_Button" id="dv_Button" name="dv_Button">
                                            <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:reg();">등록</button>
                                            <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='<?=$table;?>.php?<?=$query_string;?>';">취소</button>
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
</div>

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

        //라디오 버튼
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    });

    $(function () {
        //썸네일 이미지 미리보기
        $("#p_img").on("change", function () {
            var allowed_ext     = new Array("jpg", "jpeg", "png");
            var str_allowed_ext = allowed_ext.join(", ");

            if (this.files && this.files[0]) {
                var ext         = $(this).val().split(".").pop().toLowerCase();
                var max_size    = 10 * 1024 * 1024;
                var file_size   = this.files[0].size;

                //확장자
                if ($.inArray(ext, allowed_ext) == -1) {
                    alert("첨부파일은 " + str_allowed_ext + " 확장자만 가능합니다.");
                    $(this).focus();
                    $(this).val("");
                    $("#preview").attr("src", "");
                    $(".custom-file-label").html("파일선택");
                    return false;
                }

                //용량 체크
                if (file_size > max_size) {
                    alert("파일용량은 10MB 이하로만 가능합니다.");
                    $(this).focus();
                    $(this).val("");
                    $("#preview").attr("src", "");
                    $(".custom-file-label").html("파일선택");
                    return false;
                }

                if (this.id === "p_img") {
                    $("#preview").attr("src", URL.createObjectURL(this.files[0]));
                }
            }
        });
    });

    //등록
    function reg() {
        //유효성
        if ($("#p_title").val() == "") {
            alert("제목을 입력해주세요.");
            $("#p_title").focus();
            return false;
        }

        if ($("#p_img").val() == "") {
            alert("이미지를 선택해주세요.");
            $("#p_img").focus();
            return false;
        }

        if ($("#p_x").val() == "") {
            alert("X 좌표를 입력해주세요.");
            $("#p_x").focus();
            return false;
        }
        if ($("#p_y").val() == "") {
            alert("Y 좌표를 입력해주세요.");
            $("#p_y").focus();
            return false;
        }

        if (confirm("등록하시겠습니까?")) {
            //submit
            $("#frm").submit();
        }
    }
</script>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>