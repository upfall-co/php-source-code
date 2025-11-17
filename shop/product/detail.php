<?php
    define("SUB", "SHOP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/shop_Shopdetail_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main class="page_prd_detail">
        
        <!-- 상단 현재 위치 / 서브 타이틀 영역 -->
        <div class="sub_title_wrap wrapper">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/page_route.php" ?>
            <div class="page_title"><?=$_db_TITLE?></div>
        </div>

        <section class="sec_detail_info">
            <div class="wrapper">
                <div class="detail_thumb_slide">
                    <?=$BADGE_CO_html?>
                    <div class="swiper prdThumbSwiper">
                        <div class="swiper-wrapper">
                                <?=$file_html?>
                        </div>
                    </div>

                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"><img src="<?= shopFoldName ?>/img/icon_slide_prev.svg" alt="이전"></div>
                    <div class="swiper-button-next"><img src="<?= shopFoldName ?>/img/icon_slide_next.svg" alt="다음"></div>
                </div>

                <div class="info_sec">
                    <div class="prdDesc">
                        <?=$_db_SUB_TITLE?>
                    </div>

                    <input type="hidden" id="SALE_YN" name="SALE_YN" value="<?=$_db_M_SALE_YN?>"/>
                    <input type="hidden" id="OID_PRICE" name="OID_PRICE" value="<?=$_db_M_OID_PRICE?>"/>
                    <input type="hidden" id="SALE_PERCENT" name="SALE_PERCENT" value="<?=$_db_M_SALE_PERCENT?>"/>
                    <input type="hidden" id="PRICE" name="PRICE" value="<?=$_db_PRICE?>"/>

                    <div class="detail_price_row">
                        <?php if ($_db_M_SALE_YN == "Y") { ?>
                            <div class="prdSalePrice"><span><?=$_db_M_OID_PRICE_TEXT?></span></div>
                            <div class="prdUnitPrice"><span><?=$_db_PRICE_TEXT?></span></div>
                            <div class="prdSalePercent"><span><?=$_db_M_SALE_PERCENT?></span>%</div>
                        <?php } else { ?>
                            <div class="prdUnitPrice"><span><?=$_db_PRICE_TEXT?></span></div>
                        <?php } ?>
                    </div>

                    <ul class="toggle_guide">
                        <li>
                            <div class="toggle_title">
                                <div class="title">배송 안내</div>
                                <div class="arrow"><span>자세히 보기</span><img src="<?= shopFoldName ?>/img/product/toggle_guide_arrow.png" alt="자세히 보기"></div>
                            </div>
                            <div class="toggle_cont">
                                <?=$terms['privacy_statement4'];?>
                            </div>
                        </li>
                        <li>
                            <div class="toggle_title">
                                <div class="title">교환 · 환불 안내</div>
                                <div class="arrow"><span>자세히 보기</span><img src="<?= shopFoldName ?>/img/product/toggle_guide_arrow.png" alt="자세히 보기"></div>
                            </div>
                            <div class="toggle_cont">
                                <?=$terms['privacy_statement5'];?>
                            </div>
                        </li>
                        </ul>

                    <div class="option_shipping_wrap">
                        <select id="shopSelect" onchange="changeOptionValue(this)">
                            <option value="">옵션을 선택하세요</option>
                            <?=$OP_html?>
                        </select>

                        <div class="selected_option_list">
                            <ul>
                            </ul>
                        </div>

                        <div class="shipping_info">
                            <span>배송비</span>
                            <input type="hidden" id="DELIVERY_PRICE" name="DELIVERY_PRICE" value="<?=$DELIVERY_PRICE?>"/>
                            <input type="hidden" id="DELIVERY_IF_PRICE" name="DELIVERY_IF_PRICE" value="<?=$DELIVERY_IF_PRICE?>"/>
                            <span class="prdShippingFee" id = "DELIVERY_PRICE_TEXT"><?=$DELIVERY?></span>
                        </div>

                        <div class="final_price_wrap">
                            <span>총 상품 금액</span>
                            <input type="hidden" id="Shopseq" name="Shopseq" value="<?=$CATEGORY3_SEQ?>"/>
                            <input type="hidden" id="totalPrice" name="totalPrice" value="0"/>
                            <div class="prdFinalPrice"><span id="totalPrice_text">0</span></div>
                        </div>
                    </div>


                    <div class="shop_btn_wrap">
                        <a href="javascript:void(0);" class="border_btn" onclick="onCartAdd();">Cart</a>

                        <?php if (!$login_chk) {?>
                            <a href="javascript:void(0);" class="black_btn shadow_btn" onclick="onOrderChk();">Order</a>
                        <?php } else {?>
                            <a href="javascript:void(0);" class="black_btn shadow_btn" onclick="onOrderAdd('Order');">Order</a>
                        <?php }?>
                    </div>
                </div>
            </div>
        </section>

        <?= getsecRelation_List();?>

        <section class="sec_editor">
            <div class="wrapper">
            <div class="sub_sec_title">Detail</div>

            <div class="editor_wrap">
                <?=$_db_CONTENT_TEXT?>
            </div>
            </div>
        </section>

        </main>

        <!-- 장바구니 담기 팝업 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/cart_popup.php" ?>

        <!-- 비회원 상태에서 order 클릭 시 팝업 -->
        <div id="noMemberOrderPop" class="popup sm_popup">
            <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>
            <div class="center_txt">주문 유형을 선택하세요.</div>
            <div class="pop_btn_wrap">
                <a href="javascript:void(0);" class="border_btn" onclick="onOrderLogin();">로그인 주문</a>
                <div class="black_btn" onclick="onOrderAdd('Order');">비회원 주문</div>
            </div>
        </div>
        
        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>

<script>
    // 상품 옵션 카운트
    function onCartAdd() {
        var Shopseq = $("#Shopseq").val();
        var mPrice = $("#PRICE").val();
        var totalPrice = $("#totalPrice").val();
        var selectedOptions = $(".selected_option_list ul li");
        var codeArray = [];
        var quantityArray = [];

        selectedOptions.each(function() {
            var code = $(this).data('code');
            var quantity = $(this).find(".option_count").val();
            
            codeArray.push(code);
            quantityArray.push(quantity);
        });

        var codeString = codeArray.join(',');
        var quantityString = quantityArray.join(',');

        if (gfn_isNull(codeString) ||
            totalPrice == 0 || totalPrice == "0") {
            alert("옵션을 선택해주세요.");
            return;
        } 

        var list = {
              'mode' : 'CARTADD'
            , 'page_type' : '<?=PAGE2?>'
            , 'TYPE_CD' : 'CART'
            , 'MPRICE' : mPrice
            , 'val': Shopseq
            , 'Options' : codeString
            , 'Quantitys' : quantityString
        };

        $.ajax({
              type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function(data) {
                // 처리 성공 시 실행할 코드
                let json = JSON.parse(data);

                if (json.code == 200) {
                    $(".popup_bg").stop().fadeIn();
                    $("#cartAddPop").stop().fadeIn();  
                    $('html, body').addClass("noScroll");
                } else {
                    alert(json.msg);
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    function onOrderChk() {
        var Shopseq = $("#Shopseq").val();
        var mPrice = $("#PRICE").val();
        var totalPrice = $("#totalPrice").val();
        var selectedOptions = $(".selected_option_list ul li");

        var seqArray = [];
        var priceArray = [];
        var codeArray = [];
        var quantityArray = [];

        seqArray.push(Shopseq);

        selectedOptions.each(function() {
            var code = $(this).data('code');
            var quantity = $(this).find(".option_count").val();
            var mval = $(this).data('mval');
            
            codeArray.push(code);
            priceArray.push(mval);
            quantityArray.push(quantity);
        });

        var seqString = seqArray.join(',');
        var mPriceString = priceArray.join(',');
        var codeString = codeArray.join(',');
        var quantityString = quantityArray.join(',');

         if (gfn_isNull(codeString) ||
            totalPrice == 0 || totalPrice == "0") {
            alert("옵션을 선택해주세요.");
            return;
        } 
        
        $(".popup_bg").stop().fadeIn();
        $("#noMemberOrderPop").stop().fadeIn();
        $('html, body').addClass("noScroll");
    }

    function onOrderLogin() {
        onOrderAdd('Login');
    }

    function onOrderAdd(Btn_type) {
        var Shopseq = $("#Shopseq").val();
        var mPrice = $("#PRICE").val();
        var totalPrice = $("#totalPrice").val();
        var selectedOptions = $(".selected_option_list ul li");

        var seqArray = [];
        var priceArray = [];
        var codeArray = [];
        var quantityArray = [];

        seqArray.push(Shopseq);

        selectedOptions.each(function() {
            var code = $(this).data('code');
            var quantity = $(this).find(".option_count").val();
            var mval = $(this).data('mval');
            
            codeArray.push(code);
            priceArray.push(mval);
            quantityArray.push(quantity);
        });

        var seqString = seqArray.join(',');
        var mPriceString = priceArray.join(',');
        var codeString = codeArray.join(',');
        var quantityString = quantityArray.join(',');

         if (gfn_isNull(codeString) ||
            totalPrice == 0 || totalPrice == "0") {
            alert("옵션을 선택해주세요.");
            return;
        } 

        var list = {
              'mode' : 'ORDERADD'
            , 'page_type' : '<?=PAGE2?>'
            , 'TYPE_CD' : 'ORDER'
            , 'Prices' : mPriceString
            , 'val': seqString
            , 'Options' : codeString
            , 'Quantitys' : quantityString
            , 'BTN_TYPE' : Btn_type
        };

        $.ajax({
              type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function(data) {
                // 처리 성공 시 실행할 코드
                let json = JSON.parse(data);

                if (json.code == 200) {
                    if (Btn_type == 'Order') {
                        location.href = "<?= shopFoldName ?>/order/ordersheet.php?SEQ=" + json.seq;
                    } else if (Btn_type == "Login") { // 비회원에서 -> 로그인 주문 클릭시 
                        location.href = "<?= shopFoldName ?>/mypage/login.php";
                    }
                } else {
                    alert(json.msg);
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>

</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php" ?>