<?php
/**
 * 파일명 : orderHistory_details.php
 * 내용 : 주문 상세페이지
 * 최초작성날짜 : 2023/08/28
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/28     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/orderHistory_details_code.php';
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
                                <h5><strong>구매 정보 (작품)</strong></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    <div id="DataTables_Table_0_wrapper" class="dt-bootstrap4">
                                        <table class="table table-striped table-bordered table-hover dataTables-example dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" role="grid">
                                            <thead>
                                                <tr role="row">
                                                    <th><input type="checkbox"  id="prdChkAll" name="prdChkAll"></th>
                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">작품이미지</th>
                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">작품명</th>
                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">주문옵션</th>
                                                    <?php if ($page_type == PAGE1) { ?>
                                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">프레임</th>
                                                    <?php } ?>
                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">수량</th>
                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">금액</th>
                                                    <?php if ($page_type == PAGE2) { ?>
                                                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 200px;">송장번호</th>
                                                    <?php } ?>
                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">주문상태</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php getOrderChkList(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12 pr-2">
                                        <div class="text-left">
                                            <button type="button" class="btn btn-outline btn-info" onclick="Order_Type('01');">주문접수</button>
                                            <button type="button" class="btn btn-outline btn-success" onclick="Order_Type('21');">결제완료</button>
                                            <button type="button" class="btn btn-outline btn-primary" onclick="Order_Type('30');">배송준비중</button>
                                            <button type="button" class="btn btn-outline btn-primary" onclick="Order_Type('31');">배송중</button>
                                            <button type="button" class="btn btn-outline btn-success" onclick="Order_Type('32');">배송완료</button>
                                            <button type="button" class="btn btn-outline btn-warning" onclick="Order_Type('41');">주문취소요청</button>
                                            <button type="button" class="btn btn-outline btn-danger" onclick="Order_Type('42');">주문취소</button>
                                            <button type="button" class="btn btn-outline btn-warning" onclick="Order_Type('51');">환불요청</button>
                                            <button type="button" class="btn btn-outline btn-danger" onclick="Order_Type('52');">환불승인</button>

                                            <?php if ($page_type == PAGE2) { ?>
                                                <button type="button" class="btn btn-lg btn-primary float-right w120 ml-1" onclick="javascript:Order_Invoice();">송장번호 등록</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5><strong>상세 정보</strong></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="ibox-content custom_orderhistory">
                                <form id="frm" method="post" action="/php/order.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type;?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/> 
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$PURCHASE_SEQ?>"/>
                                    <input type="hidden" id="ATTACH_FILE_ID" name="ATTACH_FILE_ID" value="<?=$_db_ATTACH_FILE_ID?>"/>
                                    <input type="hidden" id="M_start_date" name="M_start_date" value="<?=$M_start_date?>"/>
                                    <input type="hidden" id="M_end_date" name="M_end_date" value="<?=$M_end_date?>"/>
                                    <input type="hidden" id="M_TYPE_CD" name="M_TYPE_CD" value="<?=$M_TYPE_CD?>"/>
                                    <input type="hidden" id="M_STATE_CD" name="M_STATE_CD" value="<?=$M_STATE_CD?>"/>
                                    <input type="hidden" id="M_PUR_NUM" name="M_PUR_NUM" value="<?=$M_PUR_NUM?>"/>
                                    <input type="hidden" id="M_DLVY_NAME" name="M_DLVY_NAME" value="<?=$M_DLVY_NAME?>"/>
                                    <input type="hidden" id="M_CATEGORY1_SEQ" name="M_CATEGORY1_SEQ" value="<?=$M_CATEGORY1_SEQ?>"/>
                                    <input type="hidden" id="M_CATEGORY2_SEQ" name="M_CATEGORY2_SEQ" value="<?=$M_CATEGORY2_SEQ?>"/>
                                    <input type="hidden" id="M_CATEGORY3_SEQ" name="M_CATEGORY3_SEQ" value="<?=$M_CATEGORY3_SEQ?>"/>
                                    <input type="hidden" id="M_CATEGORY1_NAME" name="M_CATEGORY1_NAME" value="<?=$M_CATEGORY1_NAME?>"/>
                                    <input type="hidden" id="M_CATEGORY2_NAME" name="M_CATEGORY2_NAME" value="<?=$M_CATEGORY2_NAME?>"/>
                                    <input type="hidden" id="M_CATEGORY3_NAME" name="M_CATEGORY3_NAME" value="<?=$M_CATEGORY3_NAME?>"/>
                                    <input type="hidden" id="TYPE_CD" name="TYPE_CD" value="<?=$TYPE_CD?>"/> 
                                    <input type="hidden" id="STATE_CD" name="STATE_CD" value="<?=$STATE_CD?>"/> 
                                    <input type="hidden" id="INICIS_SEQ" name="INICIS_SEQ" value="<?=$INICIS_SEQ?>"/>
                                    <input type="hidden" id="TOTAL_PRICE" name="TOTAL_PRICE" value="<?=$_db_TOTAL_PRICE?>"/>
                                    <input type="hidden" id="TOTAL_NOW_PRICE" name="TOTAL_NOW_PRICE" value="<?=$_db_TOTAL_NOW_PRICE?>"/>
                                    <input type="hidden" id="REAL_PRCIE" name="REAL_PRCIE" value="<?=$REAL_PRCIE?>"/>
                                    <input type="hidden" id="REAL_DLVY_PRICE" name="REAL_DLVY_PRICE" value="<?=$REAL_DLVY_PRICE?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                            <div class="form-group row">
                                                <h3 class="col-sm-6 text-right"> 주문정보 </h3>
                                            </div>

                                            <div class="custom_half_wrap">
                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label text-right">주문번호</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="PURCHASE_SEQ" name="PURCHASE_SEQ" value="<?=$PURCHASE_SEQ?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">주문상태</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="STATE_CD" name="STATE_CD" value="<?=$_db_STATE_CD_NM?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">ID</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="ID" name="ID" value="<?=$_db_ID?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">총 주문 개수</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="TOTAL_COUNT" name="TOTAL_COUNT" value="<?=$_db_TOTAL_COUNT?>개" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">현 주문 개수</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="TOTAL_COUNT" name="TOTAL_COUNT" value="<?=$_db_TOTAL_NOW_COUNT?>개" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label text-right">결제수단</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="TYPE_CD" name="TYPE_CD" value="<?=$_db_TYPE_CD_NM?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">주문일자</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="reg_date_nm" name="reg_date_nm" value="<?=$_db_reg_date_nm?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">회원/비회원</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="ID" name="ID" value="<?=$_db_STATE_TYPE_NM?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">총 주문 금액</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="TOTAL_PRICE" name="TOTAL_PRICE_TEXT" value="<?=$_db_TOTAL_PRICE_TEXT?>원" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">현 주문 금액</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="TOTAL_PRICE" name="TOTAL_NOW_PRICE_TEXT" value="<?=$_db_TOTAL_NOW_PRICE_TEXT?>원" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row"></div>
                                            <div class="hr-line-dashed"></div>

                                            <div class="custom_half_wrap">
                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <h3 class="col-sm-3 text-right"> 주문자정보 </h3>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label text-right">* 주문자</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="NAME" name="NAME" value="<?=$_db_NAME?>">
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">* 주문자 - 연락처</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="MOBILE" name="MOBILE" value="<?=$_db_MOBILE?>">
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">* 주문자 - 이메일</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="EMAIL" name="EMAIL" value="<?=$_db_EMAIL?>">
                                                        </div>
                                                    </div>
                                                </div> <!-- custom_half 주문자 정보 -->

                                                <div class="hr-line-dashed"></div>
                                            
                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <h3 class="col-sm-3 text-right"> 배송정보 </h3>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label text-right">* 배송정보 - 이름</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="DLVY_NAME" name="DLVY_NAME" value="<?=$_db_DLVY_NAME?>">
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">* 배송정보 - 연락처</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="DLVY_MOBILE" name="DLVY_MOBILE" value="<?=$_db_DLVY_MOBILE?>">
                                                        </div>
                                                        
                                                        <label class="col-sm-2 col-form-label text-right">* 배송정보 - 우편번호</label>

                                                        <div class="col-sm-2 custom_address">
                                                            <input type="text" class="form-control" id="DLVY_ADDRESS_ZIPCODE" name="DLVY_ADDRESS_ZIPCODE" value="<?=$_db_DLVY_ADDRESS_ZIPCODE?>" readonly>
                                                            <button type="button" id="addressBtn" class="btn btn-default" onclick="execDaumPostcode()">
                                                                <i class="fa fa-map-marker"></i>
                                                                주소검색
                                                            </button>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">* 배송정보 - 주소</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="DLVY_ADDRESS" name="DLVY_ADDRESS" value="<?=$_db_DLVY_ADDRESS?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">배송정보 - 상세주소</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="DLVY_ADDRESSDETAIL" name="DLVY_ADDRESSDETAIL" value="<?=$_db_DLVY_ADDRESSDETAIL?>">
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">배송정보 - 배송메세지</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="DLVY_MESSAGE" name="DLVY_MESSAGE" value="<?=$_db_DLVY_MESSAGE?>">
                                                        </div>
                                                    </div>
                                                </div> <!-- custom_half 배송지 정보 -->
                                            </div>


                                            <div class="form-group row"></div>
                                            <div class="hr-line-dashed"></div>

                                            <div class="custom_half_wrap">
                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <h3 class="col-sm-3 text-right"> 무통장 </h3>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label text-right">무통장 - 입금기한일</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="NO_BANK_DATE" name="NO_BANK_DATE" value="<?=$_db_NO_BANK_DATE_NM?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">무통장 - 은행</label>

                                                        <div class="col-sm-2">
                                                            <select class="form-control" id="NO_BANK_CD" name="NO_BANK_CD" disabled>
                                                                <?php gfn_getComboList("은행", "AD007", $_db_NO_BANK_CD, "은행", "", "", "Y")?>
                                                            </select>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">무통장 - 예금주</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="NO_BANK_NAME" name="NO_BANK_NAME" value="<?=$_db_NO_BANK_NAME?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">무통장 - 입금계좌</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="NO_BANK_ACCOUNT" name="NO_BANK_ACCOUNT" value="<?=$_db_NO_BANK_ACCOUNT?>" readonly>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">무통장 - 입금자</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="NO_BANK_DEPOSITOR" name="NO_BANK_DEPOSITOR" value="<?=$_db_NO_BANK_DEPOSITOR?>">
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">현금영수증 요청</label>

                                                        <div class="col-sm-2">
                                                            <div class="i-checks">
                                                                <label class="mt-2"> 
                                                                    <div class="icheckbox_square-green"  style="position: relative;">
                                                                        <input type="checkbox" name="NO_BANK_CASH_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div> <!-- custom_half 무통장 -->

                                                <div class="hr-line-dashed"></div>

                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <h3 class="col-sm-3 text-right"> 현금영수증 </h3>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label text-right">현금영수증여부</label>

                                                        <div class="col-sm-2">
                                                            <div class="i-checks">
                                                                <label class="mt-2"> 
                                                                    <div class="icheckbox_square-green"  style="position: relative;">
                                                                        <input type="checkbox" name="CASH_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked2?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        
                                                        <label class="col-sm-2 col-form-label text-right">현금영수증 - 연락처</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="CASH_MOBILE" name="CASH_MOBILE" value="<?=$_db_CASH_MOBILE?>">
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">현금영수증 - 이메일</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="CASH_EMAIL" name="CASH_EMAIL" value="<?=$_db_CASH_EMAIL?>">
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">현금영수증 - 사업자번호</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="CASH_BUSINESS" name="CASH_BUSINESS" value="<?=$_db_CASH_BUSINESS?>">
                                                        </div>

                                                    </div>

                                                </div> <!-- custom_half 현금영수증 -->
                                            </div>

                                            <div class="form-group row"></div>
                                            <div class="hr-line-dashed"></div>


                                            <div class="custom_half_wrap">
                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <h3 class="col-sm-3 text-right"> 세금계산서 </h3>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label text-right">세금계산서 여부</label>

                                                        <div class="col-sm-2">
                                                            <div class="i-checks">
                                                                <label class="mt-2"> 
                                                                    <div class="icheckbox_square-green"  style="position: relative;">
                                                                        <input type="checkbox" name="TAX_BILL_YN" value="Y" style="position: absolute; opacity: 0;" <?=$checked3?>>
                                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">사업자등록증</label>

                                                        <div class="col-sm-2">
                                                            <div class="input-group">
                                                                <div class="custom-file">
                                                                    <input id="ATTACH" name="ATTACH" type="file" data-idx="0" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#file_list', 'jpg|jpeg|png', 10);">
                                                                    <label class="custom-file-label" for="ATTACH">파일 선택</label>
                                                                </div>
                                                            </div>

                                                            <div id="file_list">
                                                                <?php if ($mode == 'MOD') { ?>
                                                                    <?= $file_html ?>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <label class="col-sm-2 col-form-label text-right">세금계산서 - 이메일</label>

                                                        <div class="col-sm-2">
                                                            <input type="text" class="form-control" id="TAX_BILL_EMAIL" name="TAX_BILL_EMAIL" value="<?=$_db_TAX_BILL_EMAIL?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="custom_half">
                                                    <div class="form-group row">
                                                        <h3 class="col-sm-3 text-right"> 메모 </h3>
                                                    </div>

                                                    <div class="col-sm-10" style="height:200px;">
                                                        <textarea class="form-control h-100" id="NOTE" name="NOTE"><?=$_db_NOTE?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row"></div>
                                            <div class="hr-line-solid"></div>

                                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                                <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='orderHistory_main.php?<?=$query_string?>';">취소</button>
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
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
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

        $('.dataTables-example').DataTable({
            pageLength: <?=$limit?>,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            createdRow: function (row, data, dataIndex) {
                const api = this.api();
                const rowNumber = api.page.info().start + dataIndex + 1;
                $(row).find('.simple_numbers').text(rowNumber);
            },
            buttons: [
                {extend: 'copy',
                    title: '<?=$title_name?>',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    }
                },
                {extend: 'csv',
                    title: '<?=$title_name?>',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    },
                    customize: function (csv) {
                    return '\uFEFF' + csv; // CSV 데이터 앞에 UTF-8 BOM 문자를 추가하여 UTF-8-SIG로 인코딩
                    }
                },
                {extend: 'excel',
                    title: '<?=$title_name?>',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    }
                },
                {extend: 'print', title: '<?=$title_name?>',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                    },
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    }
                }
            ]
        });

        $("[name=prdChkAll]").click(function() {
            allCheckFunc(this);
        });

        $("[name=prdChk]").each(function() {
            $(this).click(function() {
                oneCheckFunc($(this));
            });
        });
    });

    function allCheckFunc(obj) {
        $("[name=prdChk]").prop("checked", $(obj).prop("checked"));
    }

    /* 체크박스 체크시 전체선택 체크 여부 */
    function oneCheckFunc(obj) {
        var allObj = $("[name=prdChkAll]");
        var objName = $(obj).attr("name");

        if ($(obj).prop("checked")) {
            checkBoxLength = $("[name=" + objName + "]").length;
            checkedLength = $("[name=" + objName + "]:checked").length;

            if (checkBoxLength == checkedLength) {
                allObj.prop("checked", true);
            } else {
                allObj.prop("checked", false);
            }
        } else {
            allObj.prop("checked", false);
        }
    }

    /**
     * name : Order_Type
     * comment : 주문상태 변경
     */
    function Order_Type(op_state) {
        let totalcountArray = [];
        let pkArray = [];
        let seqArray = [];
        let codeArray = [];
        let stateArray = [];
        let priceArray = [];

        let M_obj =  $("[name=prdChk]");
        let checkedCount = 0;

        M_obj.each(function() {
            let O_obj = $("#"+this.id);

            if (O_obj.is(":checked")) {
                checkedCount++;

                let totalcount = O_obj.parent("td").data('totalcount');
                let pk = O_obj.parent("td").data('pk');
                let seq = O_obj.parent("td").data('seq');
                let code = O_obj.parent("td").data('code');
                let state = O_obj.parent("td").data('state');
                let price = O_obj.parent("td").data('val');

                if (totalcountArray.indexOf(totalcount) === -1) {
                    totalcountArray.push(totalcount);
                }

                if (pkArray.indexOf(pk) === -1) {
                    pkArray.push(pk);
                }

                seqArray.push(seq);
                codeArray.push(code);
                stateArray.push(state);
                priceArray.push(price);
            }
        });

        if (checkedCount === 0) {
            alert('하나 이상의 체크박스를 선택해주세요.');
            return false;
        }

        let SEQ = $("#SEQ").val();
        let TYPE_CD = $("#TYPE_CD").val();
        let STATE_CD = $("#STATE_CD").val();
        let INICIS_SEQ = $("#INICIS_SEQ").val();
        let TOTAL_PRICE = $("#TOTAL_PRICE").val();
        let TOTAL_NOW_PRICE = $("#TOTAL_NOW_PRICE").val();
        let REAL_DLVY_PRICE = $("#REAL_DLVY_PRICE").val();
        let MEM_MOBILE = $("#MOBILE").val();

        let totalcountString = totalcountArray.join(',');
        let pkString = pkArray.join(',');
        let seqString = seqArray.join(',');
        let codeString = codeArray.join(',');
        let stateString = stateArray.join(',');
        let priceString = priceArray.join(',');

        var list = {
              'mode' : 'ORDERCHANGE'
            , 'SEQ' : SEQ
            , 'TYPE_CD' : TYPE_CD
            , 'STATE_CD' : STATE_CD
            , 'INICIS_SEQ' : INICIS_SEQ
            , 'TOTAL_PRICE' : TOTAL_PRICE
            , 'TOTAL_NOW_PRICE' : TOTAL_NOW_PRICE
            , 'REAL_DLVY_PRICE' : REAL_DLVY_PRICE
            , 'MOBILE' : MEM_MOBILE
            , 'totalcount' : totalcountString
            , 'pk' : pkString
            , 'val': seqString
            , 'Options' : codeString
            , 'state' : stateString
            , 'price' : priceString
            , 'change_stage' : op_state
        };

        if (confirm("주문상태를 변경하시겠습니까?")) {
            $.ajax({
                  type: "POST"
                , url: "/php/ajax_module.php"
                , data: list
                , success: function(data) {
                    // 처리 성공 시 실행할 코드
                    let json = JSON.parse(data);

                    alert(json.msg);

                    if (json.code == 200) {
                        location.reload();
                    }
                }
                , error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }

    /**
     * name : Order_Invoice
     * comment : 송장번호 추가, 변경
     */
    function Order_Invoice() {
        let totalcountArray = [];
        let pkArray = [];
        let seqArray = [];
        let codeArray = [];
        let invoiceArray = [];

        let M_obj =  $("[name=prdChk]");

        M_obj.each(function() {
            let O_obj = $("#"+this.id);

            let totalcount = O_obj.parent("td").data('totalcount');
            let pk = O_obj.parent("td").data('pk');
            let seq = O_obj.parent("td").data('seq');
            let code = O_obj.parent("td").data('code');
            let invoice = O_obj.closest("tr").find("input[name=INVOICE]").val();

            if (totalcountArray.indexOf(totalcount) === -1) {
                totalcountArray.push(totalcount);
            }

            if (pkArray.indexOf(pk) === -1) {
                pkArray.push(pk);
            }

            seqArray.push(seq);
            codeArray.push(code);
            invoiceArray.push(invoice);
        });

        let SEQ = $("#SEQ").val();

        let totalcountString = totalcountArray.join(',');
        let pkString = pkArray.join(',');
        let seqString = seqArray.join(',');
        let codeString = codeArray.join(',');
        let invoiceString = invoiceArray.join(',');

        var list = {
              'mode' : 'ORDERINVOICE'
            , 'SEQ' : SEQ
            , 'totalcount' : totalcountString
            , 'pk' : pkString
            , 'val': seqString
            , 'Options' : codeString
            , 'Invoices' : invoiceString
        };

        if (confirm("송장번호를 등록하시겠습니까?")) {
            $.ajax({
                  type: "POST"
                , url: "/php/ajax_module.php"
                , data: list
                , success: function(data) {
                    // 처리 성공 시 실행할 코드
                    let json = JSON.parse(data);

                    alert(json.msg);

                    if (json.code == 200) {
                        location.reload();
                    }
                }
                , error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }

    /**
     * name : execDaumPostcode
     * comment : 우편번호 등록
     */
    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.
                let addr = ''; // 주소 변수
    
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                $('#DLVY_ADDRESS_ZIPCODE').val(data.zonecode);
                $('#DLVY_ADDRESS').val(addr);
            }
        }).open();
    }

    /**
     * name : fileupload
     * comment : 파일 업로드
     */
    function fileupload(obj, id, strExt, limitSize) {
        gfnfile = {
          mode : 'O' // [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
        , obj  : obj // input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
        , id   : id // 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
        , strExt : strExt// 확장자 ex) jpg|gif|jpeg|png|pdf|zip
        , limitSize : limitSize // 파일의 사이즈를 확인
        , fileMap : '' // mode가 M인경우 다중파일일 경우 값 저장을 위하여
        , formData_del : '' // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
        , del_count : ''// mode가 M인경우 삭제 pk값보관
        , file_list_row : '' // mode가 M인경우 다중파일의 pk값을 보관
        , row_val : ''//mode가 M인경우  다중파일의  max값을 지정해줌
        , ues : 'A'// 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile (gfnfile);
    }

    /**
     * name : mod
     * comment : 수정
     */
    function mod() { // 수정
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("수정하시겠습니까?")) {
            $("#frm").submit();
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("#NAME").val() == "") {
            alert("주문자 정보 입력\n[이름을 입력해주세요.]");
            $("#NAME").focus();
            return false;
        }

        if ($("#MOBILE").val() == "") {
            alert("주문자 정보 입력\n[연락처를 입력해주세요.]");
            $("#MOBILE").focus();
            return false;
        }

        if ($("#EMAIL").val() == "") {
            alert("주문자 정보 입력\n[이메일을 입력해주세요.]");
            $("#EMAIL").focus();
            return false;
        }

        if ($("#DLVY_NAME").val() == "") {
            alert("배송정보 입력\n[이름을 입력해주세요.]");
            $("#DLVY_NAME").focus();
            return false;
        }

        if ($("#DLVY_MOBILE").val() == "") {
            alert("배송정보 입력\n[연락처를 입력해주세요.]");
            $("#DLVY_MOBILE").focus();
            return false;
        }

        if ($("#DLVY_ADDRESS_ZIPCODE").val() == "") {
            alert("배송정보 입력\n[주소를 선택해주세요.]");
            $("#DLVY_ADDRESS_ZIPCODE").focus();
            return false;
        }

        return true;
    }
</script>