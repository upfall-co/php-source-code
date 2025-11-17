<?php
/**
 * 파일명 : secretCodeManagement_main.php
 * 내용 : 시크릿코드관리 페이지
 * 최초작성날짜 : 2023/08/03
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/03    V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';
    
    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/secretCodeManagement_main_code.php';
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
                                <h5><strong>시크릿 코드</strong></h5>
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

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">SecretCode1</label>

                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="CODE01" name="CODE01" value="<?=$CODE01?>" maxlength="10">
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="코드생성" onclick="javascript:randomChange('CODE01', 10);"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-info  dim" type="button" title="복사" onclick="javascript:ScodeCopy('CODE01');"><i class="fa fa-paste"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="코드삭제" onclick="javascript:ScodeDel('CODE01');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">SecretCode2</label>

                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="CODE02" name="CODE02" value="<?=$CODE02?>" maxlength="10">
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="코드생성" onclick="javascript:randomChange('CODE02', 10);"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-info  dim" type="button" title="복사" onclick="javascript:ScodeCopy('CODE02');"><i class="fa fa-paste"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="코드삭제" onclick="javascript:ScodeDel('CODE02');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">SecretCode3</label>

                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="CODE03" name="CODE03" value="<?=$CODE03?>" maxlength="10">
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="코드생성" onclick="javascript:randomChange('CODE03', 10);"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-info  dim" type="button" title="복사" onclick="javascript:ScodeCopy('CODE03');"><i class="fa fa-paste"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="코드삭제" onclick="javascript:ScodeDel('CODE03');"><i class="fa fa-times"></i></button>
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
    function generateRandomString(length) {
        var characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var randomString = '';
        var max = characters.length - 1;

        for (var i = 0; i < length; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * max));
        }

        return randomString;
    }

    function randomChange(obj, max) {
        if (confirm("코드를 생성하시겠습니까?")) {
            //var chageVal = generateRandomString(max);

            //$("#"+ obj).val(chageVal);
            var chageVal = $("#"+ obj).val();

            var list = {
                  'COM_CD': obj.slice(-2)
                , 'TH1_THEM_CD': chageVal
                , 'mode' : 'mod'
                , 'msg' : '생성완료'
            };

            ufn_change_row(list);
        }
    }

    function ScodeCopy(obj) {
        const CopyObj = document.createElement("textarea");
        document.body.appendChild(CopyObj);
        CopyObj.value =  $("#"+ obj).val();
        CopyObj.select();
        document.execCommand('copy');
        document.body.removeChild(CopyObj);

        alert("복사가 완료되었습니다.");
    }

    function ScodeDel(obj) {
        if (confirm("코드를 제거하시겠습니까?")) {
            $("#"+ obj).val("");

            var list = {
                  'COM_CD': obj.slice(-2)
                , 'TH1_THEM_CD': ''
                , 'mode' : 'del'
                , 'msg' : '삭제완료'
            };

            ufn_change_row(list);
        }
    }

    function ufn_change_row(list) {
        $.ajax({
              type: "POST"
            , url: "/php/secretCodeManagement.php"
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

</script>