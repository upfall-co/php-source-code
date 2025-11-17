<?php
    define("SUB", "SHOP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/collection_Shopdetail.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="detail">
        <section class="secInfo">
            <div class="wrapper">

                <div class="shop_thumb_slide">
                    <div class="img_wrap detailSwiper">
                        <div class="swiper-wrapper">
                            <?=$file_html?>
                        </div>
    
                        <div class="prev_next_btn">
                            <a href="javascript:void(0);" class="shop_prev"><img src="<?= artFoldName ?>/img/shop/relation_prev.png" alt="이전 작품"></a>
                            <a href="javascript:void(0);" class="shop_next"><img src="<?= artFoldName ?>/img/shop/relation_next.png" alt="다음 작품"></a>
                        </div>
                    </div>
                    <div class="mo_zoom">
                        <img src="<?= artFoldName ?>/img/shop/mo_zoom_icon.svg" alt="확대">
                        <span>이미지를 누르면 크게 확인할 수 있습니다.</span>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

                <div class="info_wrap">
                    <div class="shop_title"><?=$_db_TITLE?></div>

                    <ul class="shop_info_list">
                        <li>
                            <div class="lt">Artist</div>
                            <div class="rt"><?=$_db_CATEGORY1_NAME?></div>
                        </li>
                        <li>
                            <div class="lt">Quantity</div>
                            <div class="rt"><?=$_db_QUANTITY?>ea</div>
                        </li>
                        <li>
                            <div class="lt">Frame</div>
                            <div class="rt"><?=$_db_FRAME?></div>
                        </li>
                        <li>
                            <div class="lt">Edition / Size</div>
                            <div class="rt">
                                <select id="shopSelect" onchange="changeOptionValue(this)">
                                    <option value="">선택</option>
                                    <?=$OP_html?>
                                </select>
                            </div>
                        </li>
                        <li class="selected_option">
                            <ul>
                            </ul>
                        </li>
                        <li class="price_wrap">
                            <div class="lt">Price</div>
                            <input type="hidden" id="Shopseq" name="Shopseq" value="<?=$CATEGORY3_SEQ?>"/>
                            <input type="hidden" id="totalPrice" name="totalPrice" value="0"/>
                            <div class="rt"><span id="totalPrice_text">0</span>&nbsp;원</div>
                        </li>
                    </ul>

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

        <section class="secEditor">
            <div class="wrapper">
                <div class="editor_wrap">
                    <?=$_db_CONTENT_TEXT?>
                </div>
            </div>
        </section>

        <?php getsecRelation_List(); ?>
        
        <a href="<?=$back_url?>" class="list_btn border_btn">목록으로</a>

    </main>

    <!-- 장바구니 담기 팝업 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/cart_popup.php" ?>

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
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script src='<?= artFoldName ?>/js/jquery.zoom.min.js'></script>
<script>
    $(document).ready(function() {
        $('#detail .secInfo .img_box').zoom({
            on: 'click'
        });
    });

    function onCartAdd() {
        var Shopseq = $("#Shopseq").val();
        var totalPrice = $("#totalPrice").val();
        var selectedOptions = $(".selected_option ul li");
        var codeArray = [];

        selectedOptions.each(function() {
            var code = $(this).data('code');
            
            codeArray.push(code);
        });

        var codeString = codeArray.join(',');

        if (gfn_isNull(codeString) ||
        selectedOptions.length == 0 || selectedOptions.length == "0") {
            alert("Edition / Size를 선택해주세요.");
            return;
        }

        var list = {
              'mode' : 'CARTADD'
            , 'page_type' : '<?=PAGE1?>'
            , 'TYPE_CD' : 'CART'
            , 'val': Shopseq
            , 'Options' : codeString
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
        var totalPrice = $("#totalPrice").val();
        var selectedOptions = $(".selected_option ul li");

        var seqArray = [];
        var codeArray = [];

        seqArray.push(Shopseq);

        selectedOptions.each(function() {
            var code = $(this).data('code');
            
            codeArray.push(code);
        });

        var seqString = seqArray.join(',');
        var codeString = codeArray.join(',');

        if (gfn_isNull(codeString) ||
            totalPrice == 0 || totalPrice == "0") {
            alert("Edition / Size를 선택해주세요.");
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
        var totalPrice = $("#totalPrice").val();
        var selectedOptions = $(".selected_option ul li");

        var seqArray = [];
        var codeArray = [];

        seqArray.push(Shopseq);

        selectedOptions.each(function() {
            var code = $(this).data('code');
            
            codeArray.push(code);
        });

        var seqString = seqArray.join(',');
        var codeString = codeArray.join(',');

        if (gfn_isNull(codeString) ||
            totalPrice == 0 || totalPrice == "0") {
            alert("Edition / Size를 선택해주세요.");
            return;
        }

        var list = {
              'mode' : 'ORDERADD'
            , 'page_type' : '<?=PAGE1?>'
            , 'TYPE_CD' : 'ORDER'
            , 'val': seqString
            , 'Options' : codeString
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
                        location.href = "<?= artFoldName ?>/order/ordersheet.php?SEQ=" + json.seq;
                    } else if (Btn_type == "Login") { // 비회원에서 -> 로그인 주문 클릭시 
                        location.href = "<?= artFoldName ?>/mypage/login.php";
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