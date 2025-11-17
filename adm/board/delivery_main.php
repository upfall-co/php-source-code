<?php
/**
 * 파일명 : delivery_main.php
 * 내용 : 배송비 관리 페이지
 * 최초작성날짜 : 2023/08/03
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/11/14    V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';
    
    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/delivery_main_code.php';
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
                                <h5><strong>배송비</strong></h5>
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
                                                <label class="col-sm-1 col-form-label text-right">배송비</label>

                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="PRICE" name="PRICE" value="<?=$PRICE?>" onkeyup="javascript:formatNumber(this, 20)" maxlength="20">
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="수정" onclick="javascript:randomChange('PRICE', 10);"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="초기화" onclick="javascript:ScodeDel('PRICE');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">조건금액</label>

                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" id="IFPRICE" name="IFPRICE" value="<?=$IFPRICE?>" onkeyup="javascript:formatNumber(this, 20)" maxlength="20">
                                                </div>

                                                <div class="col-sm-3 custom_btn3">
                                                    <button class="btn btn-success dim" type="button" title="수정" onclick="javascript:randomChange('IFPRICE', 10);"><i class="fa fa-upload"></i></button>
                                                    <button class="btn btn-warning dim" type="button" title="초기화" onclick="javascript:ScodeDel('IFPRICE');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right"></label>

                                                <div class="col-sm-5">
                                                    <span class="form-text m-b-none text-navy">조건금액 이상은 무료, 그 미만은 작성된 배송비가 적용됩니다.</span>
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
    function randomChange(obj) {
        if (confirm("금액을 수정하시겠습니까?")) {

            //$("#"+ obj).val(chageVal);
            var chageVal = $("#"+ obj).val();

            var list = {
                  'COM_CD': obj
                , 'TH1_THEM_CD': chageVal
                , 'mode' : 'mod'
                , 'msg' : '수정완료'
            };

            ufn_change_row(list);
        }
    }

    function ScodeDel(obj) {
        if (confirm("금액을 초기화 하시겠습니까?")) {
            $("#"+ obj).val("0");

            var list = {
                  'COM_CD': obj
                , 'TH1_THEM_CD': 0
                , 'mode' : 'del'
                , 'msg' : '초기화완료'
            };

            ufn_change_row(list);
        }
    }

    function ufn_change_row(list) {
        $.ajax({
              type: "POST"
            , url: "/php/delivery.php"
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