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
    $m_seq = get_request_param('m_seq', 'GET');
    $mp_seq = get_request_param('mp_seq', 'GET');
    $page_type      = get_request_param('page_type', 'GET');    
    $search_type    = get_request_param('search_type', 'GET');
    $search_text    = get_request_param('search_text', 'GET');
    $page           = get_request_param('page', 'GET');
    $seq            = get_request_param('seq', 'GET');
    $M_pop_title = get_request_param('pop_title', 'GET');

    //파라미터 체크
    if (!is_numeric($seq)) {
        dieAndErrorMove('잘못된 접근입니다.');
    }
    if (!is_numeric($page)) {
        $page       = 1;
    }

    //변수 정리
    $table  = 'popup';
    $checked = '';

    //DB
    $sql = "
        SELECT 
           * 
        FROM {$table}  
        WHERE 1 
            AND pop_seq = :seq
        LIMIT 1
    ";

    $clefResult = $mysqldb->get($sql, [':seq' => $seq]);
    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $data       = $clefResult->getResultSet();

    if (empty($data)) {
        dieAndErrorMove("잘못된 접근입니다.");
    }

    //DB 변수 정리
    $_db_pop_seq        = _check_var($data['pop_seq']);
    $_db_title      = _check_var($data['pop_title']);
    $_db_img        = _check_var($data['pop_img1']);
    $_db_link       = _check_var($data['pop_link']);
    $_db_start_date = _check_var($data['pop_start_date']);
    $_db_end_date   = _check_var($data['pop_end_date']);
    $_db_open_yn    = _check_var($data['pop_open_yn']);
    $_db_pop_x      = _check_var($data['pop_x']);
    $_db_pop_y      = _check_var($data['pop_y']);

    if ($_db_open_yn == "Y") {
        $checked = 'checked';
    }

    //경로 정리
    $img_path       = "/upload/{$table}/{$_db_img}";

    //페이징
    $arrParams      = array(
        'm_seq' => $m_seq,
        'mp_seq' => $mp_seq,
        'page_type'   => $page_type,
        'page'          => $page,
        'search_type'   => $search_type,
        'search_text'   => $search_text,
        'pop_title'   => $M_pop_title
    );
    $query_string   = http_build_query($arrParams);

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);

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
                            <h5><strong>팝업 수정</strong></h5>

                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content custom_detail">
                            <form id="frm" method="post" action="../program/<?=$table;?>.php" enctype="multipart/form-data">
                                <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                <input type="hidden" id="mode" name="mode" value="mod"/>
                                <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                <input type="hidden" id="seq" name="seq" value="<?= $_db_pop_seq;?>"/>
                                <input type="hidden" id="M_pop_title" name="M_pop_title" value="<?=$M_pop_title?>"/>

                                <div class="row">
                                    <div class="col-sm-12 b-r">
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">* 제목</label>

                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="p_title" name="p_title" value="<?=$_db_title;?>" placeholder="제목을 입력해주세요." maxlength="255">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="p_img" class="col-sm-1 text-right col-form-label">* 이미지</label>
                                            
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
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row" >
                                            <label for="" class="col-sm-1 text-right col-form-label" >이미지 미리보기</label>

                                            <div class="col-sm-10">
                                                <img src="<?=$img_path;?>" class="br_img" id="preview" alt="" width="200"/>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">링크</label>

                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="p_link" name="p_link" value="<?=$_db_link;?>" placeholder="링크를 입력해주세요." maxlength="255">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">* 노출위치-X좌표</label>

                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" id="p_x" name="p_x" value="<?=$_db_pop_x;?>" placeholder="메인에 노출될 위치(x좌표)를 입력해주세요." required="required">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">* 노출위치-Y좌표</label>

                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" id="p_y" name="p_y" value="<?=$_db_pop_y;?>" placeholder="메인에 노출될 위치(Y좌표)를 입력해주세요." required="required">
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">* 노출기간</label>

                                            <div class="col-sm-10 custom_detail_date">
                                                <div class="form-group row ml-1">
                                                    <div class="input-group date w140" id="data_1">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="start" value="<?= $_db_start_date?>">
                                                    </div>

                                                    <span class="ml-2 mr-2 mt-2">~</span>

                                                    <div class="input-group date w140" id="data_2">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="end" value="<?= $_db_end_date?>">
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
                                            <button type="button" class="btn btn-lg btn-danger float-right w60 ml-1" onclick="javascript:del();">삭제</button>
                                            <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
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

        // 라디오 버튼 클릭 이벤트 처리
        $('input[type="radio"]').on('ifChecked', function(event) {
            // 모든 라디오 버튼의 checked 클래스를 제거
            $('input[type="radio"]').parent().parent().removeClass('checked');
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

    //삭제
    function del() {
        if (confirm("삭제하시겠습니까?")) {
            $("#mode").val("del");
            $("#frm").submit();
        }
    }

    //등록
    function mod() {
        //유효성
        if ($("#p_title").val() == "") {
            alert("제목을 입력해주세요.");
            $("#p_title").focus();
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

        if (confirm("수정하시겠습니까?")) {
            //submit
            $("#frm").submit();
        }
    }
</script>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>