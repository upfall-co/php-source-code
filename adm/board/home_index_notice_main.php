<?php
/**
 * 파일명 : home_index_notice_main.php
 * 내용 : index_notice 내역
 * 최초작성날짜 : 2024/03/15
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2024/03/15     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/home_index_notice_main_code.php';
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
                                <h5><strong>Main Notice</strong></h5>
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
                                            <?=$div_html?>
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
            , url: "/php/home_index_notice.php"
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