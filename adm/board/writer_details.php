<?php
/**
 * 파일명 : writer_details.php
 * 내용 : 카테고리1 관리
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
 * 김민성    2023/11/09    shop 기능추가
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/writer_details_code.php';
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
                                <form id="frm" method="post" action="/php/writer.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="sub_type" name="sub_type" value="<?=$sub_type;?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/> 
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$CATEGORY1_SEQ?>"/>
                                    <input type="hidden" id="M_TITLE" name="M_TITLE" value="<?=$M_TITLE?>"/>
                                    <input type="hidden" id="M_MAIN_YN" name="M_MAIN_YN" value="<?=$M_MAIN_YN?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* <?=$title_val?></label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="TITLE" name="TITLE" value="<?=$_db_TITLE;?>" placeholder="<?=$title_val?>을 입력해주세요." maxlength="100">
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

        let msg = "";

        if ("작가" == "<?=$title_name?>") {
            msg = "삭제 시 시리즈, 작품내역도 전부 삭제됩니다. [복구 불가능]";
        } else if ("카테고리" == "<?=$title_name?>") {
            msg = "삭제 시 분류, 상품도 전부 삭제됩니다. [복구 불가능]";
        } else if ("업종" == "<?=$title_name?>") {
            msg = "삭제 시 세부 업종도 전부 삭제됩니다. [복구 불가능]";
        }

        if (confirm("삭제하시겠습니까?\n(" + msg + ")")) { 
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
            alert("<?=$title_val?>을 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }

        return true;
    }
</script>