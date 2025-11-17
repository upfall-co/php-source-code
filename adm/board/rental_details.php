<?php
/**
 * 파일명 : rental_details.php
 * 내용 : CONTACT 상세 페이지
 * 최초작성날짜 : 2025/04/09
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준     2025/04/09    V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';
    
    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/rental_details_code.php';
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
                            <h5><strong>대관문의 관리</strong></h5>

                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content custom_detail">
                            <form id="frm" method="post" action="/php/rent.php" enctype="multipart/form-data">
                                <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/> 
                                <input type="hidden" id="SEQ" name="SEQ" value="<?=$RENTAL_SEQ?>"/>
                                <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                <input type="hidden" id="M_COMPANY" name="M_COMPANY" value="<?=$M_COMPANY?>"/>
                                <input type="hidden" id="M_AGENCY" name="M_AGENCY" value="<?=$M_AGENCY?>"/>
                                <input type="hidden" id="M_TYPE_CD" name="M_TYPE_CD" value="<?=$M_TYPE_CD?>"/>
                                <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID?>"/>

                                <div class="row">
                                    <div class="col-sm-12 b-r">
                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">문의 구분</label>

                                            <div class="col-sm-4">
                                                <p type="text" class="form-control"><?=$_db_TYPE_CD_NM;?></p>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">회사(단체)명</label>

                                            <div class="col-sm-4">
                                                <p type="text" class="form-control"><?=$_db_COMPANY;?></p>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">대행사명</label>

                                            <div class="col-sm-4">
                                                <p type="text" class="form-control"><?=$_db_AGENCY;?></p>
                                            </div>
                                        </div>


                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">행사명</label>

                                            <div class="col-sm-6">
                                                <p type="text" class="form-control"><?=$_db_TITLE;?></p>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">희망기간</label>

                                            <div class="col-sm-6">
                                                <p type="text" class="form-control"><?=$HOPE_DATE;?></p>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">담당자명 (직함)</label>

                                            <div class="col-sm-6">
                                                <p type="text" class="form-control"><?=$_db_NAME;?></p>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">문의내용</label>

                                            <div class="col-sm-6" style="height:300px;">
                                                <textarea class="form-control h-100" style="background-color:white" disabled><?=$_db_CONTENT_TEXT?></textarea>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">연락처</label>

                                            <div class="col-sm-6">
                                                <p type="text" class="form-control"><?=$_db_MOBILE;?></p>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">이메일</label>

                                            <div class="col-sm-6">
                                                <p type="text" class="form-control"><?=$_db_EMAIL;?></p>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label"> 첨부파일</label>

                                            <div class="col-sm-2">
                                                <div id="file_list">
                                                    <?= $file_html ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hr-line-solid"></div>

                                        <div class="dv_Button" id="dv_Button" name="dv_Button">
                                            <button type="button" class="btn btn-lg btn-danger float-right w60 ml-1" onclick="javascript:del();">삭제</button>
                                            <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='rental_main.php?<?=$query_string;?>';">취소</button>
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

<script>
    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("등록하시겠습니까?")) {
            $("#frm").submit();
        }
    }

    function del() { // 삭제
        if (confirm("삭제하시겠습니까?")) { 
            var mode = "AD_DEL";

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
            alert("제목을 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }

        return true;
    }
</script>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>