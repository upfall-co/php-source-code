<?php
/**
 * 파일명 : work_details.php
 * 내용 : 작품 관리
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/work_details_code.php';
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
                                <form id="frm" method="post" enctype="multipart/form-data">
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/> 
                                    <input type="hidden" id="MULTI" name="MULTI" value="<?=$MULTI?>"/> 
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$CATEGORY3_SEQ?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="sub_type" name="sub_type" value="<?=$sub_type;?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID;?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">* 중분류</label>

                                                    <div class="col-sm-7">
                                                        <select class="form-control" id="PROGRAM_CD" name="PROGRAM_CD" style="width:200px;" <?=$disabled?>>
                                                            <?php gfn_getComboList("중분류", "COL014", $_db_PROGRAM_CD,"S")?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php }  ?>
                                            <div class="hr-line-dashed"></div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* <?=$category1_val?></label>

                                                <?php if ($page_type == PAGE2 && $MULTI == "Y") { ?>
                                                    <div class="col-sm-10">
                                                        <div class="row ml-0">
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY1_SEQ1" name="CATEGORY1_SEQ1" style="width:200px;">
                                                                <option value="">1번 카테고리</option>
                                                                <?php getARTISTComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY1_SEQ2" name="CATEGORY1_SEQ2" style="width:200px;">
                                                                <option value="">2번 카테고리</option>
                                                                <?php getARTISTComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY1_SEQ3" name="CATEGORY1_SEQ3" style="width:200px;">
                                                                <option value="">3번 카테고리</option>
                                                                <?php getARTISTComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY1_SEQ4" name="CATEGORY1_SEQ4" style="width:200px;">
                                                                <option value="">4번 카테고리</option>
                                                                <?php getARTISTComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY1_SEQ5" name="CATEGORY1_SEQ5" style="width:200px;">
                                                                <option value="">5번 카테고리</option>
                                                                <?php getARTISTComboList(); ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="col-sm-7">
                                                        <select class="form-control" id="CATEGORY1_SEQ" name="CATEGORY1_SEQ" style="width:200px;" <?=$disabled?>>
                                                            <option value="">선택</option>
                                                            <?php getARTISTComboList(); ?>
                                                        </select>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* <?=$category2_val?></label>
                                                <?php if ($page_type == PAGE2 && $MULTI == "Y") { ?>
                                                    <div class="col-sm-10">
                                                        <div class="row ml-0">
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY2_SEQ1" name="CATEGORY2_SEQ1" style="width:200px;">
                                                                <option value="">1번 분류</option>
                                                                <?php getSERIESComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY2_SEQ2" name="CATEGORY2_SEQ2" style="width:200px;">
                                                                <option value="">2번 분류</option>
                                                                <?php getSERIESComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY2_SEQ3" name="CATEGORY2_SEQ3" style="width:200px;">
                                                                <option value="">3번 분류</option>
                                                                <?php getSERIESComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY2_SEQ4" name="CATEGORY2_SEQ4" style="width:200px;">
                                                                <option value="">4번 분류</option>
                                                                <?php getSERIESComboList(); ?>
                                                            </select>
                                                            <select class="form-control mr-1 mb-2" id="CATEGORY2_SEQ5" name="CATEGORY2_SEQ5" style="width:200px;">
                                                                <option value="">5번 분류</option>
                                                                <?php getSERIESComboList(); ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="col-sm-7">
                                                        <select class="form-control" id="CATEGORY2_SEQ" name="CATEGORY2_SEQ" style="width:200px;" <?=$disabled?>>
                                                            <option value="">선택</option>
                                                            <?php getSERIESComboList(); ?>
                                                        </select>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">메인노출여부</label>

                                                    <div class="col-sm-10 m-t-xs">
                                                        <div class="i-checks">
                                                            <label class=""> 
                                                                <div class="icheckbox_square-green"  style="position: relative;">
                                                                    <input type="checkbox" id="TITLE_YN" name="TITLE_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked?>>
                                                                    <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                </div>

                                                                <span class="ml-1">메인노출여부</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">노출여부</label>

                                                <div class="col-sm-10 m-t-xs">
                                                    <div class="i-checks">
                                                        <label class=""> 
                                                            <div class="icheckbox_square-green"  style="position: relative;">
                                                                <input type="checkbox" id="MAIN_YN" name="MAIN_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked2?>>
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
                                                    <input class="touchspin1 form-control" type="number" id="ORDER_NUMBER" name="ORDER_NUMBER" value="<?=$_db_ORDER_NUMBER;?>">
                                                </div>
                                            </div>

                                            <?php if ($page_type != PAGE3) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 검색 내용</label>

                                                    <div class="col-sm-6">
                                                        <textarea type="text" class="form-control" id="SEARCH_TEXT" name="SEARCH_TEXT" placeholder="검색내용을 입력해주세요."><?=$_db_SEARCH_TEXT;?></textarea>
                                                        <span class="form-text m-b-none text-navy">공백은 상관없이 ',' (콤마)로 구분됩니다.</span>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label">* <?=$input_title_name?></label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="TITLE" name="TITLE" value="<?=$_db_TITLE;?>" placeholder="<?=$title_val?>을/를 입력해주세요." maxlength="100">
                                                </div>
                                            </div>

                                            <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 제목 두 번째 줄</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="SUB_TITLE" name="SUB_TITLE" value="<?=$_db_SUB_TITLE;?>" placeholder="제목 설명을 입력해주세요." maxlength="100">
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 기간</label>

                                                    <div class="col-sm-10 custom_detail_date">
                                                        <div class="form-group row ml-1">
                                                            <div class="input-group date w140" id="data_1">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="SDATE" value="<?= $_db_SDATE?>">
                                                            </div>

                                                            <span class="ml-2 mr-2 mt-2">~</span>

                                                            <div class="input-group date w140" id="data_2">
                                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="EDATE" value="<?= $_db_EDATE?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 링크</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="LINK_URL" name="LINK_URL" value="<?=$_db_LINK_URL;?>" placeholder="링크를 입력해주세요." maxlength="100">
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 상세 제목</label>

                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="CONTENT_TITLE" name="CONTENT_TITLE" value="<?=$_db_CONTENT_TITLE;?>" placeholder="상세 제목을 입력해주세요." maxlength="100">
                                                </div>
                                            </div>
                                            <?php } ?>

                                            <?php if ($page_type == PAGE1) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 수량(Quantity)</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="QUANTITY" name="QUANTITY" oninput="allowOnlyNumbers(this)" maxlength="19" value="<?=$_db_QUANTITY;?>" placeholder="수량을 입력해주세요." >
                                                    </div>
                                                </div>
                                            
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 프레임 (Frame)</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="FRAME" name="FRAME" value="<?=$_db_FRAME;?>" placeholder="프레임을 입력해주세요." maxlength="100">
                                                    </div>
                                                </div>
                                            <?php } else if ($page_type == PAGE2) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 상품 내용</label>

                                                    <div class="col-sm-6">
                                                        <textarea type="text" class="form-control" id="SUB_TITLE" name="SUB_TITLE" placeholder="내용을 입력해주세요."><?=$_db_SUB_TITLE;?></textarea>
                                                        <span class="form-text m-b-none text-navy">상세페이지 상품 내용 </span>
                                                    </div>
                                                </div>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">메인화면 노출</label>

                                                    <div class="col-sm-10 m-t-xs">
                                                        <div class="i-checks">
                                                            <label class=""> 
                                                                <div class="icheckbox_square-green"  style="position: relative;">
                                                                    <input type="checkbox" name="INDEX_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked3?>>
                                                                    <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                </div>

                                                                <span class="ml-1">메인화면 노출</span>
                                                            </label>
                                                        </div>

                                                        <span class="form-text m-b-none text-navy">정렬기준, 등록일순으로 7개만 노출됩니다.</span>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">브랜드</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="BRAND" name="BRAND" value="<?=$_db_BRAND?>" placeholder="브랜드명을 입력해주세요." maxlength="100">
                                                    </div>
                                                </div>
                                                
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 구분</label>

                                                    <div class="col-sm-10">
                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class="icheckbox_square-green"  style="position: relative;">
                                                                        <input type="checkbox" name="TYPE_CD[]" value="NEW" style="position: absolute; opacity: 0;" <?=$TYPE_CHK1?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">Piknic Edition</span>
                                                                </label>
                                                            </div>
                                                        </label>

                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class="icheckbox_square-green"  style="position: relative;">
                                                                        <input type="checkbox" name="TYPE_CD[]" value="BEST" style="position: absolute; opacity: 0;" <?=$TYPE_CHK2?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">Best</span>
                                                                </label>
                                                            </div>
                                                        </label>

                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class="icheckbox_square-green"  style="position: relative;">
                                                                        <input type="checkbox" name="TYPE_CD[]" value="RECOMMENDED" style="position: absolute; opacity: 0;" <?=$TYPE_CHK3?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">Recommended</span>
                                                                </label>
                                                            </div>
                                                        </label>

                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class="icheckbox_square-green"  style="position: relative;">
                                                                        <input type="checkbox" name="TYPE_CD[]" value="SALE" style="position: absolute; opacity: 0;" <?=$TYPE_CHK4?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">Sale</span>
                                                                </label>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 뱃지</label>

                                                    <div class="col-sm-10">
                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class=""  style="position: relative;">
                                                                        <input type="radio" name="BADGE_CO" value="NEW" style="position: absolute; opacity: 0;" <?=$TYPE_CHK5?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">new</span>
                                                                </label>
                                                            </div>
                                                        </label>

                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class=""  style="position: relative;">
                                                                        <input type="radio" name="BADGE_CO" value="SALE" style="position: absolute; opacity: 0;" <?=$TYPE_CHK6?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">sale</span>
                                                                </label>
                                                            </div>
                                                        </label>

                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class=""  style="position: relative;">
                                                                        <input type="radio" name="BADGE_CO" value="BEST" style="position: absolute; opacity: 0;" <?=$TYPE_CHK7?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">best</span>
                                                                </label>
                                                            </div>
                                                        </label>

                                                        <label>
                                                            <div class="i-checks">
                                                                <label class=""> 
                                                                    <div class=""  style="position: relative;">
                                                                        <input type="radio" name="BADGE_CO" value="SOLDOUT" style="position: absolute; opacity: 0;" <?=$TYPE_CHK8?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>

                                                                    <span class="ml-1">sold out</span>
                                                                </label>
                                                            </div>
                                                        </label>

                                                        <button type="button" class="btn btn-lg btn-danger w80 ml-1" onclick="javascript:resetBtn();">Reset</button>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">할인여부</label>

                                                    <div class="col-sm-10 m-t-xs">
                                                        <div class="i-checks">
                                                            <label class=""> 
                                                                <div class="icheckbox_square-green"  style="position: relative;">
                                                                    <input type="checkbox" id="SALE_YN" name="SALE_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked4?>>
                                                                    <ins class="iCheck-helper" onclick="Sale_Change()" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;" ></ins>
                                                                </div>

                                                                <span class="ml-1" >할인여부</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 할인 전 금액 (Price)</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="OID_PRICE" name="OID_PRICE" value="<?=$_db_OID_PRICE;?>" onchange="chage_price(this)" onkeyup="javascript:formatNumber(this, 20)" placeholder="할인 전 금액을 입력해주세요." maxlength="20">
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 할인율</label>

                                                    <div class="col-sm-6">
                                                        <input type="number" class="form-control" id="SALE_PERCENT" name="SALE_PERCENT" value="<?=$_db_SALE_PERCENT;?>" onchange="chage_price(this)" onkeyup="javascript:formatNumber(this, 3)" placeholder="할인율을 입력해주세요." maxlength="20">
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label"> 금액 (Price)</label>

                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control" id="M_PRICE" name="M_PRICE" value="<?=$_db_M_PRICE;?>" onchange="chage_price(this)" onkeyup="javascript:formatNumber(this, 20)" placeholder="금액을 입력해주세요." maxlength="20">
                                                    </div>
                                                </div>

                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label for="ATTACH4" class="col-sm-1 text-right col-form-label">* 호버 이미지</label>

                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input id="ATTACH4" name="ATTACH4" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#preview4', 'jpg|jpeg|png', 10);">
                                                                <label class="custom-file-label" for="ATTACH4">파일 선택</label>
                                                            </div>
                                                        </div>
                                                        <span class="form-text m-b-none">10MB 이하 .jpg .jpeg .png</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row" >
                                                    <label for="" class="col-sm-1 text-right col-form-label" >호버 이미지</br> 미리보기</label>

                                                    <div class="col-sm-10">
                                                        <img src="<?=$_db_MAIN_ATTACH4_FILE_ID;?>" class="br_img" id="preview4" alt="" width="200"/>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label for="ATTACH" class="col-sm-1 text-right col-form-label">* 대표 이미지</label>

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
                                            <div class="form-group row" >
                                                <label for="" class="col-sm-1 text-right col-form-label" >대표 이미지</br> 미리보기</label>

                                                <div class="col-sm-10">
                                                    <img src="<?=$_db_MAIN_ATTACH_FILE_ID;?>" class="br_img" id="preview" alt="" width="200"/>
                                                </div>
                                            </div>

                                            <?php if ($page_type != PAGE3) { ?>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group row">
                                                    <label class="col-sm-1 text-right col-form-label">* 옵션 <br>(Edition / Size)</label>

                                                    <div class="col-sm-7">
                                                        <div id="option_list">
                                                            <?php if ($mode == 'MOD') { ?>
                                                                <?= $OP_html ?>
                                                            <?php } ?>
                                                        </div>

                                                        <button type="button" class="btn btn-lg btn-primary w60 " onclick="javascript:optionAdd();">추가</button>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 text-right col-form-label"> 상세보기<br> 멀티 이미지</label>

                                                <div class="col-sm-11">
                                                    <div class="dropzone" id="dropzoneForm">
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple />
                                                        </div>
                                                        <?php if ($mode == 'MOD') {?>
                                                            <?= $file_html3 ?>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div>
                                                <textarea id="editor" name="editor"><?=$_db_CONTENT_TEXT;?></textarea>
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
    if ('<?=$sub_type?>' == '<?=SUB_PAGE2?>') {
        let PROGRAM_CD_value = '<?=$_db_PROGRAM_CD?>'; // 분류 값

        gfn_setupSelectBox('PROGRAM_CD','CATEGORY1_SEQ',PROGRAM_CD_value);
    }

    $(function () { //에디터
        //CKEDITOR
        CKEDITOR.editorConfig = function(config) {
            config.colorButton_foreStyle = {
                element: 'font',
                attributes: {
                    'color': '#(color)'
                }
            };
        }
        CKEDITOR.replace('editor', {
            height: 600,
            editorplaceholder: '내용을 입력해주세요.',
            allowedContent: true
        });
        CKEDITOR.instances['editor'].setData($("#editor").val());

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
    });

    let gfnfile = [];

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
            , ues : ''// 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile (gfnfile);
    }

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

    function resetBtn() {
        $('input[name="BADGE_CO"]').prop('checked', false);
        $('input[name="BADGE_CO"]').parent().removeClass('checked');
    }

    let option_del = [];

    let fileMap = new Map();
    let formData_del = [];
    let del_count = 0;

    let fileInfo_val = "";

    let max_val = 10;
    let limitSize = 10;
    let str_allowed_ext = ".jpg, .jpeg, .png";

    let Ndel_chk = "N"; // 삭제 취소시 Y 값 적용
    let dfDel_chk ="N"; // 에러 삭제시
    let data_type = "I";

    let max_alert_chk = "N"; // max시 alert 한번만
    let limitSize_alert_chk = "N"; // 사이즈 초과시 alert 한번만
    let str_allowed_alert_chk = "N"; // 확장자가 다를시 alert 한번만
    
    <?php
        if (!empty($file_json)) {
            echo 'const fileData = ' . $file_json . ';';
        } else {
            echo 'const fileData = [];';
        }
    ?>

    function convertFileToDataURL(file, callback) {
        var reader = new FileReader();
        
        reader.onload = function(event) {
            callback(event.target.result);
        };

        reader.readAsDataURL(file);
    }

    //드롭 이미지
    Dropzone.options.dropzoneForm = { 
        url: "/php/work.php",
        method: "post",
        maxFilesize: limitSize, // MB
        acceptedFiles: ".jpg, .jpeg, .png", // 허용되는 파일 타입
        maxFiles: max_val, // 최대 업로드 가능한 파일 개수
        parallelUploads:5,
        uploadMultiple:true, //멀티파일 업로드
        addRemoveLinks: true, // 업로드된 파일 삭제 링크 표시
        dictRemoveFile: "삭제", // 삭제 버튼 텍스트 설정
        dictDefaultMessage: '<strong>이미지 미리보기</strong><br>(10MB 이하 / 최대 10개 .jpg .jpeg .png)',
    
        init: function() {
            const dropzoneInstance = this;

            if (fileData.length > 0) {
                fileData.forEach(function(fileInfo) {
                    fileInfo_val = fileInfo;
                    
                    fetch(fileInfo.PATH).then(response => response.blob()).then(blob => {
                        // Blob 데이터를 사용하여 File 객체를 생성합니다
                        let mockFile = new File([blob], fileInfo.ATTACH_FILE_REAL_NAME, {
                            type: fileInfo.ATTACH_FILE_TYPE,
                            size: blob.size // blob의 실제 크기를 사용합니다
                        });

                        // Dropzone에 파일 추가
                        data_type = "N";
                        dropzoneInstance.addFile(mockFile);

                        // 파일 타입에 따라 썸네일을 설정합니다
                        if (fileInfo.ATTACH_FILE_TYPE === 'pdf') {
                            // PDF 파일의 경우 대체 이미지 사용
                            dropzoneInstance.createThumbnailFromUrl(mockFile, '/img/icon/pdf.png');
                        } else {
                            // 이미지 파일의 경우 원래 URL 사용
                            dropzoneInstance.createThumbnailFromUrl(mockFile, URL.createObjectURL(blob));
                        }

                        mockFile.data_group = fileInfo.ATTACH_GROUP;
                        mockFile.data_group_count = parseInt(fileInfo.ATTACH_GROUP_COUNT);
                        mockFile.data_type = fileInfo.data_type;

                        // 여기에서 클릭 이벤트 리스너를 추가합니다
                        mockFile.previewElement.addEventListener("click", function() {
                            window.open(fileInfo.PATH, '_blank');
                        });

                        fileMap.set(parseInt(fileInfo.ATTACH_GROUP_COUNT), mockFile);
                    }).catch(error => {
                        console.error('Error fetching file:', error);
                    });
                });

                
            } else {
                // fileData가 비어있는 경우에 대한 처리
                // 예: 어떤 메시지를 화면에 표시하거나 아무 작업도 하지 않는다.
            }

            // 파일이 서버로 업로드되기 전에 실행되는 함수
            dropzoneInstance.on("sending", function(file, xhr, formData) {
                
            });

            // 파일 추가시
            dropzoneInstance.on("addedfile", function(file) {
                if (file.data_type != "N") {
                    data_type = "I";
                }
            });

            // 이미지 업로드 성공 후 실행되는 함수
            dropzoneInstance.on("success", function(file, response) {
                if (Ndel_chk == "N" && file.data_type != "N")  {
                    let maxDataGroupCount = getMaxDataGroupCount(dropzoneInstance); // 새로 추가된 파일 객체에 추가 정보를 설정
                    let newDataGroupCount = maxDataGroupCount + 1; // 새 파일의 data_group_count 값 설정

                    file.data_group  = 3;
                    file.data_group_count = parseInt(newDataGroupCount);
                    file.data_type = "I";

                    fileMap.set(newDataGroupCount, file);
                } 

                Ndel_chk = "N";
                max_alert_chk = "N";
                limitSize_alert_chk = "N";
                str_allowed_alert_chk = "N";
            });

            // 추가 정보를 이용해 파일 삭제 등의 작업 수행
            dropzoneInstance.on("removedfile", function(file) {
                if (dfDel_chk == "N") {
                    let data_group = file.data_group;
                    let data_group_count = parseInt(file.data_group_count);

                    let chk = 'N';

                    fileMap.delete(data_group_count);

                    for (let i = 0; i < formData_del.length; i++) {
                        if (data_group_count == formData_del[i]) {
                            chk = 'Y';
                            break;
                        }
                    }

                    if (chk == 'N') {
                        formData_del[del_count++] = data_group_count;
                    }
                }

                dfDel_chk = "N";
            });

            dropzoneInstance.on("error", function(file, errorMessage, xhr) {
                if (errorMessage == "You can not upload any more files.") { //maxfilesreached
                    if (max_alert_chk == "N") {
                        alert("첨부파일은 " + max_val + "개까지 업로드 가능합니다.");
                        max_alert_chk = "Y";
                    }

                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                } else if (errorMessage == "You can't upload files of this type.") {
                    if (str_allowed_alert_chk == "N") {
                        alert("첨부 파일은 " + str_allowed_ext + " 확장자만 가능합니다.");
                        str_allowed_alert_chk = "Y";
                    }

                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                } else if (errorMessage.includes("File is too big")) {
                    if (limitSize_alert_chk == "N") {
                        alert("파일용량은 " + limitSize + "MB 까지 가능합니다.");
                        limitSize_alert_chk = "Y";
                    }

                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                } else { // 에러는 점차 추가
                    dfDel_chk = "Y";
                    dropzoneInstance.removeFile(file);
                }
            });
        }
    };

    // data_group_count값 확인
    function getMaxDataGroupCount(obj) {
        var maxDataGroupCount = 0;

        for (var i = 0; i < obj.files.length; i++) {
            var file = obj.files[i];

            if (file.data_group_count > maxDataGroupCount) {
                maxDataGroupCount = file.data_group_count;
            }
        }
        
        return maxDataGroupCount;
    }

    //옵션 삭제
    function optionDel(obj) {
        if (confirm("옵션 삭제하시겠습니까?")) {
            let dataType = obj.parent().parent().parent("#OPTION_CK").attr('data-type');
            let dataSeq = obj.parent().parent().parent("#OPTION_CK").attr('data-seq');

            if (dataType == "U") {
                option_del.push ({optionSeq : dataSeq});
            }

            obj.parent().parent().parent("#OPTION_CK").remove();
        }
    }

    //옵션 정보 추가
    function optionAdd() {
        let inputElems = document.querySelectorAll('input[name="OPTION_NAME[]"]');

        let dataGroup = 2;
        let dataCount = 0;
        let count = '';

        if (inputElems.length > 0) {
            let parentElem = inputElems[inputElems.length - 1].parentElement.parentElement.parentElement;
            dataGroup = parentElem.getAttribute('data-group');
            dataCount = parentElem.getAttribute('data-count');

            count = Number(Number(dataCount) + 1);
        }  else {
            count = dataCount;
        }

        let html = '' +
            '<div id="OPTION_CK" data-type="I">' +
            '   <div class="form-group row">' +
            '        <label class="col-sm-1 text-right col-form-label">* 옵션</label>' +
            '        <div class="col-sm-4">' +
            '            <input type="text" class="form-control" name="OPTION_NAME[]" value="<?=$_db_OPTION_NAME?>" placeholder="옵션명을 입력해주세요." maxlength="100">' +
            '        </div>' +
            '        <label class="col-sm-1 text-right col-form-label">노출</label>' +
            '        <div class="col-sm-2 m-t-xs">' +
            '            <div class="i-checks">' +
            '                <label class=""> ' +
            '                    <div class="icheckbox_square-green"  style="position: relative;">' +
            '                        <input type="checkbox" name="OP_MAIN_YN[]" value="Y" style="position: absolute; opacity: 0;" <?=$_db_OP_MAIN_YN?>>' +
            '                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>' +
            '                    </div> ' +
            '                    <i></i><font _mstmutation="1" _msttexthash="9770748" _msthash="240"> 노출여부 </font>' +
            '                </label>' +
            '            </div>' +
            '        </div>' +
            '        <label class="col-sm-1 text-right col-form-label">품절</label>' +
            '        <div class="col-sm-2 m-t-xs">' +
            '            <div class="i-checks">' +
            '                <label class=""> ' +
            '                    <div class="icheckbox_square-green"  style="position: relative;">' +
            '                        <input type="checkbox" name="OP_SOLD_YN[]" value="Y" style="position: absolute; opacity: 0;" <?=$_db_OP_SOLD_YN?>>' +
            '                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>' +
            '                    </div> ' +
            '                    <i></i><font _mstmutation="1" _msttexthash="9770748" _msthash="240"> 품절여부 </font>' +
            '                </label>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '    <div class="form-group row">' +
            '        <label class="col-sm-1 text-right col-form-label">* 수량</label>' +
            '        <div class="col-sm-3">' +
            '            <input type="number" class="form-control" name="OP_QUANTITY[]" value="<?=$_db_OP_QUANTITY?>" placeholder="작품 수량을 입력해주세요." maxlength="20" <?=$op_price_readonly?>>' +
            '        </div>' +
            '        <label class="col-sm-1 text-right col-form-label">* 가격</label>' +
            '        <div class="col-sm-3">' +
            '            <input type="text" class="form-control" name="PRICE[]" onkeyup="javascript:formatNumber(this, 20)" value="<?=$_db_PRICE?>" placeholder="금액을 입력해주세요." maxlength="20">' +
            '        </div>' +
            '        <label class="col-sm-1 text-right col-form-label">정렬</label>' +
            '        <div class="col-sm-2">' +
            '            <input class="touchspin1 form-control" type="text" name="OP_ORDER_NUMBER[]" value="<?=$_db_OP_ORDER_NUMBER?>">' +
            '        </div>' +
            '       <div class="dv_Button" id="dv_Button" name="dv_Button">' +
            '          <button type="button" class="btn btn-danger float-right" onclick="javascript:optionDel($(this));">삭제</button>' +
            '       </div>' +
            '    </div>' +
            '   <div class="hr-line-solid"></div>' ;
            '</div>' +

        $("#option_list").append(html);

          //정렬 버튼(+/-)
        $(".touchspin1").TouchSpin({
            buttondown_class: 'btn btn-white',
            buttonup_class: 'btn btn-white'
        });

       // 라디오 버튼 클릭 이벤트 처리 (새로 추가된 옵션 정보에 대해서만 처리)
        $(`input[name="OP_MAIN_YN[]"]`).on('ifChecked', function(event) {
            // 해당 라디오 버튼이 속한 부모 요소에서만 checked 클래스를 제거
            $(event.target).closest('.i-checks').removeClass('checked');
        });

        // 라디오 버튼 클릭 이벤트 처리 (새로 추가된 옵션 정보에 대해서만 처리)
        $(`input[name="OP_SOLD_YN[]"]`).on('ifChecked', function(event) {
            // 해당 라디오 버튼이 속한 부모 요소에서만 checked 클래스를 제거
            $(event.target).closest('.i-checks').removeClass('checked');
        });

        // iCheck 플러그인 적용 (새로 추가된 옵션 정보에 대해서만 처리)
        $(`input[name="OP_MAIN_YN[]"]`).iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });

        // iCheck 플러그인 적용 (새로 추가된 옵션 정보에 대해서만 처리)
        $(`input[name="OP_SOLD_YN[]"]`).iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
    }

    function reg() { // 등록
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("등록하시겠습니까?")) {
            //let myDropzone = Dropzone.forElement("#dropzoneForm");
            let formData = new FormData($("#frm")[0]);

            let key_value = [];
            let key_count = 0;

            formData.append('editor', CKEDITOR.instances['editor'].getData());

            /*myDropzone.files.forEach(function(file) {
                formData.append("files[]", file); // Use "files[]" as the key to send multiple files
            });*/

            for (let key of fileMap.keys()) { // 제품사양
                key_value[key_count++] = key;
                formData.append("files[]", fileMap.get(key));
            }

            formData.append("key_val", JSON.stringify(key_value));

            const data = [];

            const optionInputs = document.querySelectorAll('#OPTION_CK'); // 옵션 링크 데이터

            optionInputs.forEach((input, index) => { // 옵션
                const OP_MAIN_YN = input.querySelector('input[name="OP_MAIN_YN[]"]'); // 노출여부
                const OP_SOLD_YN = input.querySelector('input[name="OP_SOLD_YN[]"]'); // 품절여부

                if (OP_MAIN_YN.checked) {
                    OP_MAIN_YN.value = "Y";
                } else {
                    OP_MAIN_YN.value = "N";
                }

                if (OP_SOLD_YN.checked) {
                    OP_SOLD_YN.value = "Y";
                } else {
                    OP_SOLD_YN.value = "N";
                }

                data.push({
                      OP_MAIN_YN: OP_MAIN_YN.value
                    , OP_SOLD_YN: OP_SOLD_YN.value
                });
            });

            formData.append("option_YN", JSON.stringify(data)); 

            $.ajax({
                url: "/php/work.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        if ("<?=$page_type?>" == "home") {
                            location.href = "../board/program2_main.php?<?=$query_string?>";
                        } else {
                            location.href = "../board/work_main.php?<?=$query_string?>";
                        }
                    }
                },
                beforeSend: function() {
                    $(".wrap-loading").removeClass("display-none");
                },
                complete: function() {
                    $(".wrap-loading").addClass("display-none");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }
    }

    function mod() { // 수정
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("수정하시겠습니까?")) {
            let myDropzone = Dropzone.forElement("#dropzoneForm");
            let formData = new FormData($("#frm")[0]);

            let key_value = [];
            let key_count = 0;

            formData.append('editor', CKEDITOR.instances['editor'].getData());

            /*myDropzone.files.forEach(function(file) {
                formData.append("files[]", file); // Use "files[]" as the key to send multiple files
            });*/

            for (let key of fileMap.keys()) { // 제품사양
                if (fileMap.get(key).data_type == "I") {
                    key_value[key_count++] = key;
                    formData.append("files[]", fileMap.get(key));
                }
            }

            const data = [];

            const optionInputs = document.querySelectorAll('#OPTION_CK'); // 옵션 링크 데이터

            optionInputs.forEach((input, index) => { // 옵션
                const dataType = input.getAttribute('data-type');
                const OPTION_NAME = input.querySelector('input[name="OPTION_NAME[]"]'); // 제목
                const OP_MAIN_YN = input.querySelector('input[name="OP_MAIN_YN[]"]'); // 노출여부
                const OP_SOLD_YN = input.querySelector('input[name="OP_SOLD_YN[]"]'); // 품절여부
                const OP_QUANTITY = input.querySelector('input[name="OP_QUANTITY[]"]'); // 수량
                const PRICE = input.querySelector('input[name="PRICE[]"]'); // 가격
                const OP_ORDER_NUMBER = input.querySelector('input[name="OP_ORDER_NUMBER[]"]'); // 정렬

                if (OP_MAIN_YN.checked) {
                    OP_MAIN_YN.value = "Y";
                } else {
                    OP_MAIN_YN.value = "N";
                }

                if (OP_SOLD_YN.checked) {
                    OP_SOLD_YN.value = "Y";
                } else {
                    OP_SOLD_YN.value = "N";
                }

                if (dataType === "U") {
                    // 데이터 추가
                    data.push({
                          OPTION_NAME: OPTION_NAME.value
                        , OP_MAIN_YN: OP_MAIN_YN.value
                        , OP_SOLD_YN: OP_SOLD_YN.value
                        , OP_QUANTITY: OP_QUANTITY.value
                        , PRICE: PRICE.value
                        , OP_ORDER_NUMBER: OP_ORDER_NUMBER.value
                        , dataType: dataType
                        , optionSeq :input.getAttribute('data-seq')
                    });
                } else {
                    data.push({
                          OPTION_NAME: OPTION_NAME.value
                        , OP_MAIN_YN: OP_MAIN_YN.value
                        , OP_SOLD_YN: OP_SOLD_YN.value
                        , OP_QUANTITY: OP_QUANTITY.value
                        , PRICE: PRICE.value
                        , OP_ORDER_NUMBER: OP_ORDER_NUMBER.value
                        , dataType: dataType
                    });
                }
            });

            formData.append("option", JSON.stringify(data));
            formData.append("key_val", JSON.stringify(key_value));
            formData.append("formData_del", JSON.stringify(formData_del));
            formData.append("option_del", JSON.stringify(option_del));

            $.ajax({
                url: "/php/work.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        if ("<?=$page_type?>" == "home") {
                            location.href = "../board/program2_main.php?<?=$query_string?>";
                        } else {
                            location.href = "../board/work_main.php?<?=$query_string?>";
                        }
                    }
                },
                beforeSend: function() {
                    $(".wrap-loading").removeClass("display-none");
                },
                complete: function() {
                    $(".wrap-loading").addClass("display-none");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }
    }

    function del() { // 삭제
        if (confirm("삭제하시겠습니까?")) { 
            var mode = "DEL";

            // mode input 요소의 값을 변경합니다.
            $("#mode").val(mode);
            let formData = new FormData($("#frm")[0]);

            $.ajax({
                url: "/php/work.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);
                    alert(json.msg);

                    if (json.code == 200) {
                        if ("<?=$page_type?>" == "home") {
                            location.href = "../board/program2_main.php?<?=$query_string?>";
                        } else {
                            location.href = "../board/work_main.php?<?=$query_string?>";
                        }
                    }
                },
                beforeSend: function() {
                    $(".wrap-loading").removeClass("display-none");
                },
                complete: function() {
                    $(".wrap-loading").addClass("display-none");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }
    }
    
    function chage_price(obj) {
        if ($("#SALE_YN").is(":checked")) {
            let priceWithoutComma = 0; 
            let priceAmountWithoutComma = 0; 
            let originalPrice = 0;
            let discountPercent = 0;
            let discountAmount = 0; 
            let finalPrice = 0;
            let adjustedPrice = 0;

            if (obj.id == "OID_PRICE") {
                if (!gfn_isNull(obj.value) && !gfn_isNull($("#SALE_PERCENT").val()) &&
                    obj.value != 0 && $("#SALE_PERCENT").val() != 0) {
                    priceWithoutComma = parseFloat(obj.value.replace(/,/g, ''));

                    // 할인된 금액 계산
                    discountAmount = priceWithoutComma * ($("#SALE_PERCENT").val() / 100);

                    // 할인된 금액을 OID_PRICE에서 차감
                    finalPrice = priceWithoutComma - discountAmount;
                    adjustedPrice = Math.round(finalPrice / 10) * 10;

                    $("#M_PRICE").val(parseInt(adjustedPrice).toLocaleString());
                }
            } else if (obj.id == "SALE_PERCENT") {
                if (!gfn_isNull(obj.value) && !gfn_isNull($("#OID_PRICE").val()) &&
                    obj.value != 0 && $("#OID_PRICE").val() != 0) {
                    priceWithoutComma = parseFloat($("#OID_PRICE").val().replace(/,/g, ''));

                    // 할인된 금액 계산
                    discountAmount = priceWithoutComma * (obj.value / 100);

                    // 할인된 금액을 OID_PRICE에서 차감
                    finalPrice = priceWithoutComma - discountAmount;
                    adjustedPrice = Math.round(finalPrice / 10) * 10;

                    $("#M_PRICE").val(parseInt(adjustedPrice).toLocaleString());
                } else if (!gfn_isNull(obj.value) && !gfn_isNull($("#M_PRICE").val()) &&
                    obj.value != 0 && $("#M_PRICE").val() != 0) {
                    
                    priceAmountWithoutComma = parseFloat($("#M_PRICE").val().replace(/,/g, ''));
                    originalPrice = priceAmountWithoutComma / (1 - obj.value / 100);

                    adjustedPrice = Math.round(originalPrice / 10) * 10;
                    
                    $("#OID_PRICE").val(parseInt(adjustedPrice).toLocaleString());
                }
            } else if (obj.id == "M_PRICE") {
                if (!gfn_isNull(obj.value) && !gfn_isNull($("#OID_PRICE").val()) &&
                    obj.value != 0 && $("#OID_PRICE").val() != 0) {
                    priceWithoutComma = parseFloat($("#OID_PRICE").val().replace(/,/g, ''));
                    priceAmountWithoutComma = parseFloat(obj.value.replace(/,/g, ''));

                    discountAmount = priceWithoutComma - priceAmountWithoutComma; // 할인액 계산
                    discountPercent = (discountAmount / priceWithoutComma) * 100; // 할인율 계산

                    if (discountPercent <= 100) {
                        const discountPercentWithoutDecimal = parseFloat(discountPercent.toFixed(2));
                        $("#SALE_PERCENT").val(discountPercentWithoutDecimal);
                    } else {
                        $("#SALE_PERCENT").val(0);
                    }
                } else if (!gfn_isNull(obj.value) && !gfn_isNull($("#SALE_PERCENT").val()) &&
                          obj.value != 0 && $("#SALE_PERCENT").val() != 0) {
                    priceAmountWithoutComma = parseFloat(obj.value.replace(/,/g, ''));
                    originalPrice = priceAmountWithoutComma / (1 - $("#SALE_PERCENT").val() / 100);

                    adjustedPrice = Math.round(originalPrice / 10) * 10;
                    
                    $("#OID_PRICE").val(parseInt(adjustedPrice).toLocaleString());
                }
            }
        } 
    }

    function Sale_Change() {
        setTimeout(function() {
            Sale_Change_Price();
        }, 100);
    }

    function Sale_Change_Price() {
        if ($("#SALE_YN").is(":checked")) {
            let obj = "";

            if ($("#OID_PRICE").val() != 0 && $("#SALE_PERCENT").val() != 0) {
                obj = document.querySelector("#OID_PRICE");
                chage_price(obj);
            } else if ($("#M_PRICE").val() != 0 && $("#OID_PRICE").val() != 0) {
                obj = document.querySelector("#M_PRICE");
                chage_price(obj);
            } else if ($("#M_PRICE").val() != 0 && $("#SALE_PERCENT").val() != 0) {
                obj = document.querySelector("#M_PRICE");
                chage_price(obj);
            } 

        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        console.log($("#TITLE_YN").prop("checked"));
        console.log($("#MAIN_YN").prop("checked"));

        <?php if ($page_type == PAGE2 && $MULTI == "Y") { ?>
            if ($("#CATEGORY1_SEQ1").val() == "") {
                alert("<?=$category1_val?>을/를 선택해주세요.");
                $("#CATEGORY1_SEQ1").focus();
                return false;
            }

            if ($("#CATEGORY2_SEQ1").val() == "") {
                alert("<?=$category2_val?>을/를 선택해주세요.");
                $("#CATEGORY2_SEQ1").focus();
                return false;
            }
        <?php } else  { ?>
            <?php   if ($page_type==PAGE3 && $sub_type==SUB_PAGE2) { ?>
                if ($("#PROGRAM_CD").val() == "") {
                    alert("중분류을/를 선택해주세요.");
                    $("#PROGRAM_CD").focus();
                    return false;
                }
            <?php } ?>

            if ($("#CATEGORY1_SEQ").val() == "") {
                alert("<?=$category1_val?>을/를 선택해주세요.");
                $("#CATEGORY1_SEQ").focus();
                return false;
            }

            if ($("#CATEGORY2_SEQ").val() == "") {
                alert("<?=$category2_val?>을/를 선택해주세요.");
                $("#CATEGORY2_SEQ").focus();
                return false;
            }

            <?php if ($page_type==PAGE3 && $sub_type==SUB_PAGE2) { ?>
                if ($("#TITLE_YN").prop("checked") && !$("#MAIN_YN").prop("checked")) {
                    alert("메인노출여부를 체크한 경우 노출여부도 체크해주셔야 합니다.");
                    $("#MAIN_YN").focus();
                    return false;
                }
            <?php } ?>
        <?php } ?>

        if ($("#TITLE").val() == "") {
            alert("<?=$title_val?>을/를 입력해주세요.");
            $("#TITLE").focus();
            return false;
        }
      
        if ('<?=$mode?>' == "INS") {
            <?php if ($page_type == PAGE2) { ?>
                if ($("#ATTACH4").val() == "") {
                    alert("호버 이미지를 선택해주세요.");
                    $("#ATTACH4").focus();
                    return false;
                }
            <?php } ?>

            if ($("#ATTACH").val() == "") {
                alert("메인 이미지를 선택해주세요.");
                $("#ATTACH").focus();
                return false;
            }
        }

        <?php if($page_type == PAGE2) { ?>
            if ($("input[name='OPTION_NAME[]']").length === 0) {
                alert("옵션을 하나 이상 추가해주세요.");
                return false;
            }
        <?php } ?>

         // 옵션명 유효성 검사
        var optionValid = true;

        $('input[name="OPTION_NAME[]"]').each(function(index, element) {
            if ($(element).val() === "") {
                alert("옵션명을 입력해주세요.");
                $(element).focus();
                optionValid = false;
                return false; // 반복을 중단하려면 false를 반환합니다.
            }
        });

         if (!optionValid) {
            return false; // 옵션명 유효성 검사 실패 시 바로 false 반환
        }

        $('input[name="OP_QUANTITY[]"]').each(function(index, element) {
            if ($(element).val() === "") {
                alert("수량을 입력해주세요.");
                $(element).focus();
                optionValid = false;
                return false; // 반복을 중단하려면 false를 반환합니다.
            }
        });

         if (!optionValid) {
            return false; // 옵션명 유효성 검사 실패 시 바로 false 반환
        }

        $('input[name="PRICE[]"]').each(function(index, element) {
            if ($(element).val() === "") {
                alert("가격을 입력해주세요.");
                $(element).focus();
                optionValid = false;
                return false; // 반복을 중단하려면 false를 반환합니다.
            }
        });

        return optionValid;
    }

    let COUNTRY_value = '';

    <?php if ($page_type == PAGE2 && $MULTI == "Y") { ?>
        COUNTRY_value = ''; // 시리즈 코드 값

        for (let i = 1; i <= 5; i++) {
            const category1ID = 'CATEGORY1_SEQ' + i;
            const category2ID = 'CATEGORY2_SEQ' + i;

            const selectBox = document.getElementById(category1ID);

            selectBox.addEventListener('change', () => {
                const selectedValue = selectBox.value;
                const selectBox2 = document.getElementById(category2ID);
                const options = selectBox2.options;

                Array.from(options).forEach(option => option.hidden = true);

                if (selectedValue === '') {
                    selectBox2.selectedIndex = -1;
                    Array.from(options).forEach(option => option.hidden = true);
                    selectBox2.options[0].hidden = false;
                    selectBox2.selectedIndex = 0;
                } else {
                    const filteredOptions = Array.from(options).filter(option => {
                        const dataCode = option.getAttribute('data-code1');
                        return dataCode === selectedValue;
                    });

                    filteredOptions.forEach(option => option.hidden = false);
                    selectBox2.options[0].hidden = false;

                    if (COUNTRY_value !== '') {
                        for (let i = 0; i < selectBox2.options.length; i++) {
                            if (selectBox2.options[i].value === COUNTRY_value) {
                                selectBox2.selectedIndex = i;
                                COUNTRY_value = '';
                                break;
                            }
                        }
                    } else {
                        selectBox2.selectedIndex = 0;
                    }
                }
            });

            selectBox.dispatchEvent(new Event('change'));
        }
    <?php } else  { ?>
        COUNTRY_value = '<?=$_db_CATEGORY2_SEQ?>'; // 시리즈 코드 값

        const selectBox = document.getElementById('CATEGORY1_SEQ'); // 작가

        selectBox.addEventListener('change', () => {
            const selectedValue = selectBox.value;
            const selectBox2 = document.getElementById('CATEGORY2_SEQ'); // 시리즈
            const options = selectBox2.options;

            Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

            if (selectedValue === '') { // 전체 옵션 선택한 경우
                selectBox2.selectedIndex = -1; // 선택된 option 요소 초기화
                Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

                selectBox2.options[0].hidden = false;
                selectBox2.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '시리즈'
            } else { // 일반적인 경우
                const filteredOptions = Array.from(options).filter(option => {
                    const dataCode = option.getAttribute('data-code1');
                    return dataCode === selectedValue; // 선택한 값과 일치하는 option 요소만 필터링
                });

                filteredOptions.forEach(option => option.hidden = false); // 필터링된 option 요소만 보이기

                selectBox2.options[0].hidden = false;

                if (COUNTRY_value != '') {
                    for (let i = 0; i < selectBox2.options.length; i++) {
                        if (selectBox2.options[i].value === COUNTRY_value) {
                            selectBox2.selectedIndex = i;
                            COUNTRY_value = '';
                            break;
                        }
                    }
                } else {
                    selectBox2.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '도시'
                }
            }
        });

        selectBox.dispatchEvent(new Event('change')); // change 이벤트 강제 발생
    <?php } ?>
</script>
