<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;
use Clef\Paging as Paging;

$mysqldb    = new Pdo7();
$clefResult = new ClefResult();

$arrRtn     = array(
    'code'  => 500,
    'msg'   => ''
);

try {
    //파라미터 정리
    $type           = get_request_param('type');
    $seq            = get_request_param('seq');
    $parent_seq     = get_request_param('parent_seq');
    $page_type     = get_request_param('page_type');

    //파라미터 체크
    if (empty($parent_seq)) {
        $parent_seq = 0;
    }
    if (empty($type) || !is_numeric($seq) || !is_numeric($parent_seq)) {
        gfn_isValidation(700);
    }

    //변수 정리
    $table          = 'project_menu';
    $mode           = (empty($seq)) ? 'reg' : 'mod';
    $str_mode       = $config['proc']['mode'][$mode];
    $_db_seq        = 0;
    $_db_parent_seq = 0;
    $_db_sorting    = 0;
    $_db_name       = '';
    $_db_link       = '';
    $_db_use_yn     = 'Y';

    //DB
    $sql = "
        SELECT
            *
        FROM {$table}
        WHERE 1
            AND depth = 0
            AND PAGE_TYPE = '{$page_type}'
        ORDER BY sorting DESC
    ";
    //_p($sql);
    $clefResult = $mysqldb->select($sql);
    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }
    $menu_list  = $clefResult->getResultSet();

    if (!empty($seq)) {
        //DB
        $sql = "
            SELECT
                *
            FROM {$table}
            WHERE 1
              AND seq = :seq
              AND PAGE_TYPE = '{$page_type}'
            LIMIT 1
        ";
        //_p($sql);
        $clefResult = $mysqldb->get($sql, [':seq' => $seq]);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data       = $clefResult->getResultSet();
        if (empty($data)) {
            gfn_isValidation(999, "", "잘못된 접근입니다.");
        }
        //DB 변수 정리
        $_db_seq        = _check_var($data['seq']);
        $_db_parent_seq = _check_var($data['parent_seq']);
        $_db_sorting    = _check_var($data['sorting']);
        $_db_name       = _check_var($data['name']);
        $_db_link       = _check_var($data['link']);
        $_db_use_yn     = _check_var($data['use_yn']);
        $_db_page_type     = _check_var($data['PAGE_TYPE']);
    }

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    dieAndMsgReload($arrRtn['msg']);

}
?>
<!-- 메뉴 모달 -->
<div class="modal fade" id="menu_modal" tabindex="-1" role="dialog" aria-labelledby="regModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-lg modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="regModalLabel">메뉴 <?=$str_mode;?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="menuModalFrm">
                    <input type="hidden" id="mode" name="mode" value="<?=$mode;?>"/>
                    <input type="hidden" id="seq" name="seq" value="<?=$_db_seq;?>"/>
                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>

                    <div class="form-group">
                        <label for="menu_name" class="col-sm-4 col-form-label" style="font-size: 14px;"><b>* 메뉴위치 선택</b></label>
                        <div class="col-sm-4">
                            <select class="form-control" id="parent_seq" name="parent_seq">
                                <option value="0">대메뉴</option>
                                <?php
                                //메뉴
                                if (!empty($menu_list)) {
                                    foreach ($menu_list as $data) {
                                        //DB 변수 정리
                                        $_db_m_seq  = _check_var($data['seq']);
                                        $_db_m_name = _check_var($data['name']);

                                        //selected
                                        $selected   = '';

                                        if ($type == 'child') {
                                            $selected   = ($parent_seq == $_db_m_seq) ? 'selected="selected"' : '';
                                        } else {
                                            $selected   = ($_db_parent_seq == $_db_m_seq) ? 'selected="selected"' : '';
                                        }

                                        echo <<<OPTION
                                <option value="{$_db_m_seq}" {$selected}>{$_db_m_name}</option>
OPTION;
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="menu_name" class="col-sm-4 col-form-label" style="font-size: 14px;"><b>* 메뉴이름</b></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="menu_name" name="menu_name" value="<?=$_db_name;?>" placeholder="메뉴이름을 입력해주세요." maxlength="100"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="menu_link" class="col-sm-4 col-form-label" style="font-size: 14px;"><b>* 링크</b></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="menu_link" name="menu_link" value="<?=$_db_link;?>" placeholder="링크를 입력해주세요." maxlength="255"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="menu_sorting" class="col-sm-4 col-form-label" style="font-size: 14px;"><b>정렬</b></label>
                        <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                            <div class="col-sm-4 m-t-xs no-padding">
                                <input class="touchspin1 form-control" type="text" id="menu_sorting" name="menu_sorting" value="<?=$_db_sorting;?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="menu_use_y" class="col-sm-4 col-form-label" style="font-size: 14px;"><b>* 사용여부</b></label>
                        <div class="col-sm-10 m-t-xs">
                            <div class="i-checks">
                                <label class="">
                                    <div class="iradio_square-green <?php echo ($_db_use_yn == 'Y') ? 'checked' : '';?>" style="position: relative;">
                                        <input id="chk" name="menu_use_yn" type="radio" value="Y" <?php echo ($_db_use_yn == 'Y') ? 'checked="checked"' : '';?> style="position: absolute; opacity: 0;">
                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                    </div>
                                     <i></i> 노출
                                </label>
                                <label class="">
                                    <div class="iradio_square-green <?php echo ($_db_use_yn == 'N') ? 'checked' : '';?>" style="position: relative;">
                                        <input type="radio" name="menu_use_yn" value="N" <?php echo ($_db_use_yn == 'N') ? 'checked="checked"' : '';?> style="position: absolute; opacity: 0;">
                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                    </div>
                                    <i></i> 비 노출
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="javascript:menuProc();"><?=$str_mode;?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">취소</button>
            </div>
        </div>
    </div>
</div>
<!-- //메뉴 모달 -->
<script>
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

    //등록/수정
    function menuProc() {
        //유효성
        if ($("#menu_name").val() == "") {
            alert("메뉴이름을 입력해주세요.");
            $("#menu_name").focus();
            return false;
        }
        
        if ($("#menu_link").val() == "") {
            alert("링크를 입력해주세요.");
            $("#menu_link").focus();
            return false;
        }

        if (confirm("<?=$str_mode;?>하시겠습니까?")) {
            //formData
            var formData = new FormData($("#menuModalFrm")[0]);

            $.ajax({
                url: "../program/menu.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    var json = JSON.parse(data);

                    //성공
                    alert(json.msg);
                    if (json.code == 200) {
                        location.reload();
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
</script>