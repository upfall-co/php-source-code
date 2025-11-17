<?php
/**
 * 파일명 : adm.php
 * 내용 : 계정관리
 * 최초작성날짜 : 2023/07/31
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment      
 * 전상범    2023/03/30     V1.0
 */

//head
define("SUB", "");
include_once __DIR__ .'/../common/head.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/php/adm_details_code.php';
?>
<body class="pace-done">
    <div id="wrapper">
        <?php
            include_once __DIR__ .'/../common/nav.php';
        ?>
            <div class="wrapper wrapper-content animated fadeInRight">
                <form id="frm" method="post" action="../program/adm.php">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox ">
                                <div class="ibox-title">
                                    <h5><strong>회원 정보</strong></h5>

                                    <div class="ibox-tools">
                                        <a class="collapse-link">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="ibox-content custom_detail custom_account">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="M_ID" name="M_ID" value="<?=$M_ID?>"/>
                                    <input type="hidden" id="M_NAME" name="M_NAME" value="<?=$M_NAME?>"/>
                                    <input type="hidden" id="M_MOBILE" name="M_MOBILE" value="<?=$M_MOBILE?>"/>
                                    <input type="hidden" id="R_ID" name="R_ID" value="<?=$_db_id?>"/>
                                    <input type="hidden" id="R_stat" name="R_stat" value="<?=$direct?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">아이디</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="id" name="id" value="<?=$_db_id?>" maxlength="50">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">이름</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="name" name="name" value="<?=$_db_name?>" maxlength="50">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">비밀번호</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="pw" name="pw" placeholder="비밀번호를 입력해 주세요" required="required">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">비밀번호 확인</label>

                                                <div class="col-sm-6">
                                                    <input type="password" class="form-control" id="pw2" name="pw2" placeholder="비밀번호 확인을 입력해 주세요" required="required"/>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">휴대전화</label>

                                                <div class="col-sm-6 custom_phone">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <select class="form-control" id="mobile1" name="mobile1">
                                                                <?= $phone1 ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <input type="text" id="mobile2" name="mobile2" class="form-control" maxlength="4" value="<?=$arrMobile[1]?>" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g,'');">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <input type="text" id="mobile3" name="mobile3" class="form-control" maxlength="4" value="<?=$arrMobile[2]?>" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g,'');">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">이메일</label>

                                                <div class="col-sm-6">
                                                    <input type="email" placeholder="Enter email" class="form-control" id="email" name="email" value="<?=$_db_email?>" placeholder="이메일을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <?php if ($direct) { ?>
                                                <div class="hr-line-solid"></div>

                                                <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!$direct) { ?>
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h4>권한설정</h4>

                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="ibox-content custom_detail">
                                        <div class="row">
                                            <div class="col-sm-12 b-r authority_custom">

                                                <?= $menu_html ?>

                                                <div class="hr-line-solid"></div>
                                                <?php if ($mode == "INS") {?>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:reg();">등록</button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-lg btn-danger float-right w60 ml-1" onclick="javascript:del();">삭제</button>
                                                    <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                                <?php }?>
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='adm_main.php?<?=$query_string?>';">취소</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </form>
            </div>
            <?php
                //footer
                include_once __DIR__ .'/../common/footer.php';
            ?>
        </div>
    </div>

<?php
    //footer
    include_once __DIR__ .'/../common/footer.php';
?>

<script>
    // checkall 체크박스 클릭 시 다른 체크박스들도 체크되도록 처리
    var checkboxes = document.querySelectorAll('input[name="checkall"]');   

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener("change", function (event) {
            var currentCheckbox = event.target;
            var dataType = currentCheckbox.dataset.type;

            var relatedCheckboxes = document.querySelectorAll('.' + dataType + ' input[type="checkbox"]');

            for (var i = 0; i < relatedCheckboxes.length; i++) {
                relatedCheckboxes[i].checked = currentCheckbox.checked;
            }
        });
    });

    // 다른 체크박스를 클릭하여 체크를 해제하면 checkall 체크박스도 해제되도록 처리
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].addEventListener("change", function () {
            var checkall = document.querySelectorAll('input[name="checkall"]');
            var allChecked = true;
            for (var j = 0; j < checkboxes.length; j++) {
                if (checkboxes[j] !== checkall && !checkboxes[j].checked) {
                    allChecked = false;
                    break;
                }
            }

            checkall.checked = allChecked;

            if (this.dataset.depth === "1") {
                var pseq = this.dataset.pseq;
                var relatedCheckboxes = document.querySelectorAll('input[type="checkbox"][data-depth="0"][value="' + pseq + '"]');

                if (this.checked) {
                    relatedCheckboxes[0].checked = true;
                } else {
                    var sub_relatedCheckboxes = document.querySelectorAll('input[type="checkbox"][data-depth="1"][data-pseq="' + pseq + '"]');

                    let sub_chk_count = 0;

                    for (var k = 0; k < sub_relatedCheckboxes.length; k++) {
                        if (sub_relatedCheckboxes[k].checked) {
                            sub_chk_count++;
                        }
                    }

                    if (sub_chk_count > 0) {
                        relatedCheckboxes[0].checked = true;
                    } else {
                        relatedCheckboxes[0].checked = false;
                    }
                }
            } else if (this.dataset.depth === "0") {
                var pseq = this.value;
                var sub_relatedCheckboxes = document.querySelectorAll('input[type="checkbox"][data-depth="1"][data-pseq="' + pseq + '"]');

                if (this.checked) {
                    for (var k = 0; k < sub_relatedCheckboxes.length; k++) {
                        sub_relatedCheckboxes[k].checked = true;
                    }
                } else {
                    for (var k = 0; k < sub_relatedCheckboxes.length; k++) {
                        sub_relatedCheckboxes[k].checked = false;
                    }
                }
            }
        });
    }

    function reg() { // 등록
        //유효성
        if ($("#id").val() == "") {
            alert("ID를 입력해주세요.");
            $("#id").focus();
            return false;
        }

        if ($("#name").val() == "") {
            alert("이름을 입력해주세요.");
            $("#name").focus();
            return false;
        }

        <?php if ($mode == "INS") {?>
            if ($("#pw").val() == "") {
                alert("비밀번호를 입력해주세요.");
                $("#pw").focus();
                return false;
            }
        <?php } ?>

        if ($("#pw").val() != "") {
            if ($("#pw2").val() == "") {
                alert("비밀번호 확인을 입력해주세요.");
                $("#pw2").focus();
                return false;
            }
            if ($("#pw").val() != "" && $("#pw2").val() != "") {
                if ($("#pw").val() != $("#pw2").val()) {
                    alert("비밀번호가 일치하지 않습니다.");
                    $("#pw").focus();
                    return false;
                }
            }
        }
        if (confirm("등록하시겠습니까?")) {
            $("#frm").submit();
        }
    }

    //수정
    function mod() {
        //유효성
        if ($("#id").val() == "") {
            alert("ID를 입력해주세요.");
            $("#id").focus();
            return false;
        }

        if ($("#name").val() == "") {
            alert("이름을 입력해주세요.");
            $("#name").focus();
            return false;
        }

        <?php if ($mode == "INS") {?>
            if ($("#pw").val() == "") {
                alert("비밀번호를 입력해주세요.");
                $("#pw").focus();
                return false;
            }
        <?php } ?>

        if ($("#pw").val() != "") {
            if ($("#pw2").val() == "") {
                alert("비밀번호 확인을 입력해주세요.");
                $("#pw2").focus();
                return false;
            }
            if ($("#pw").val() != "" && $("#pw2").val() != "") {
                if ($("#pw").val() != $("#pw2").val()) {
                    alert("비밀번호가 일치하지 않습니다.");
                    $("#pw").focus();
                    return false;
                }
            }
        }

        if (confirm("수정하시겠습니까?")) {
            //submit
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
</script>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>
</body>