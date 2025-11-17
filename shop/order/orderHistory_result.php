<?php
    define("SUB", "MYPAGE");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/orderhistory_result_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60 footer_100">
        <main id="mypage" class="page_orderhistory detail wrapper">
            <input type="hidden" id="SEQ" name="SEQ" value="<?=$PURCHASE_SEQ?>"/> 
            <input type="hidden" id="TYPE_CD" name="TYPE_CD" value="<?=$TYPE_CD?>"/> 
            <input type="hidden" id="STATE_CD" name="STATE_CD" value="<?=$STATE_CD?>"/> 
            <input type="hidden" id="INICIS_SEQ" name="INICIS_SEQ" value="<?=$INICIS_SEQ?>"/>
            <input type="hidden" id="TOTAL_NOW_PRICE" name="TOTAL_NOW_PRICE" value="<?=$_db_TOTAL_NOW_PRICE?>"/> 
            <input type="hidden" id="REAL_DLVY_PRICE" name="REAL_DLVY_PRICE" value="<?=$REAL_DLVY_PRICE?>"/>
            <input type="hidden" id="MOBILE" name="MOBILE" value="<?=$_db_MOBILE?>"/>
            
            <?php if ($login_chk) { ?>	
                <div class="center_sub_title">마이페이지</div>

                <ul class="mypage_tab">
                    <li class="active"><a href="<?= shopFoldName ?>/mypage/orderhistory.php">주문내역조회</a></li>
                    <li><a href="<?= shopFoldName ?>/mypage/inquiry.php">1:1문의 내역</a></li>
                    <li><a href="<?= shopFoldName ?>/mypage/edit.php">개인정보 수정</a></li>
                </ul>
            <?php } ?>	

            <div class="mypage_title">주문내역 상세조회</div>
            <ul class="order_table detail_table">
                <li>
                    <div class="thead">
                        <div>
                            <span>주문일자</span>
                            <div class="td_order_date"><?=$_db_reg_date_nm?></div>
                        </div>
                        <div>
                            <span>주문번호</span>
                            <a href="javascript:void(0);" class="td_order_number">
                                <span><?=$_db_PURCHASE_SEQ?></span>
                            </a>
                        </div>
                    </div>

                    <div class="tbody">
                        <div class="td_order_info_wrap">
                            <div class="td_2">
                                <div class="td_order_name"><?=$_db_M_CATEGORY3_NAME?></div>
                                <div class="price_and_type">
                                    <div class="td_order_price">총<span><?=$_db_TOTAL_PRICE_TEXT?></span></div>
                                </div>
                            </div>
                            <div class="td_3">
                                <div class="td_order_state"><p><?=$_db_STATE_CD_NM?></p></div>
                                <div class="td_order_tracking">
                                    <button type="button" onclick="onTrackingPop('<?=$_db_PURCHASE_SEQ?>');" class="border_btn">배송조회</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <!-- 상품 체크 리스트 -->
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/order/prd_history_list.php" ?>

            <div class="btn_wrap">
                <?php if ( $STATE_MODE == "CANCEL") {?>
                    <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="Order_Cancel('41');">주문 취소</a>
                <?php } else {?> 
                    <a href="javascript:void(0);" id="" class="border_btn w_300" onclick="Order_Cancel('51');">환불 요청</a>
                <?php } ?>
            </div>

            <section class="order_detail_info_wrap">
                <div>
                    <div class="mypage_title">배송 정보</div>
                    <ul class="order_detail_table border_top">
                        <li>
                            <div class="lt">이름</div>
                            <div class="rt"><?=$_db_DLVY_NAME?></div>
                        </li>
                        <li>
                            <div class="lt">휴대폰</div>
                            <div class="rt"><?=$_db_DLVY_MOBILE?></div>
                        </li>
                        <li>
                            <div class="lt">주소</div>
                            <div class="rt">(<?=$_db_DLVY_ADDRESS_ZIPCODE?>) <?=$_db_DLVY_ADDRESS?></div>
                        </li>
                        <li>
                            <div class="lt">상세주소</div>
                            <div class="rt"><?=$_db_DLVY_ADDRESSDETAIL?></div>
                        </li>
                        <li>
                            <div class="lt">배송메세지</div>
                            <div class="rt"><?=$_db_DLVY_MESSAGE?></div>
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="mypage_title">결제 정보</div>

                    <ul class="order_detail_table border_top">
                        <li>
                            <div class="lt">결제비용</div>
                            <div class="rt"><span><?=$_db_TOTAL_PRICE_TEXT?></span></div>
                        </li>
                        <li>
                            <div class="lt">결제유형</div>
                            <div class="rt"><?=$_db_TYPE_CD_NM?></div>
                        </li>
                        
                        <?php if ($TYPE_CD == 'NBKB') { ?>
                            <li>
                                <div class="lt">입금은행</div>
                                <div class="rt"><?=$_db_NO_BANK_CD_NM?></div>
                            </li>
                            <li>
                                <div class="lt">입금계좌</div>
                                <div class="rt"><?=$_db_NO_BANK_ACCOUNT?></div>
                            </li>
                            <li>
                                <div class="lt">입금기한</div>
                                <div class="rt"><?=$_db_NO_BANK_DATE_NM?></div>
                            </li>
                        <?php } else  if ($TYPE_CD == 'CCARD') { ?>
                            <li>
                                <div class="lt">결제카드사</div>
                                <div class="rt"><?=$INIS_CARD_P_FN_NM?></div>
                            </li>
                            <li>
                                <div class="lt">카드번호</div>
                                <div class="rt"><?=$INIS_CARD_NUM?></div>
                            </li>
                        <?php } else  if ($TYPE_CD == 'RTBT') { ?>
                        <?php }?>
                    </ul>
                </div>
            </section>
        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>

        <!-- 택배 정보 조회 팝업 -->
        <div id ="tracking_popup">
        
        <div>
    <div>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>

<script>
    /* 전체 동의 */
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

    $(function() {
        $("[name=prdChkAll]").click(function() {
            allCheckFunc(this);
        });

        $("[name=prdChk]").each(function() {
            $(this).click(function() {
                oneCheckFunc($(this));
            });
        });
    });

    // 택배 정보 조회 팝업
    function onTrackingPop(SEQ) {
        $.ajax({
            type: "GET",
            url: "<?=shopFoldName?>/mypage/delivery_tracking_popup.php?SEQ="+SEQ + "&page_type=<?=PAGE?>", // 실제 경로로 수정해주세요
            success: function(data) {
                document.getElementById("tracking_popup").innerHTML = data;

                $(".popup_bg").stop().fadeIn();
                $("#trackingPop").stop().fadeIn();  
                $('html, body').addClass("noScroll");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>