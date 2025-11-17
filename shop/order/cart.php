<?php
    define("SUB", "ORDER");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/cart_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main id="cart">
            <div class="wrapper">

                <!-- 상단 현재 위치 / 서브 타이틀 영역 -->
                <div class="sub_title_wrap wrapper">
                    <ul class="page_route">
                        <li><a href="<?= shopFoldName ?>/index.php">home</a></li>
                        <li><p>cart</p></li>
                    </ul>
                    <div class="page_title">장바구니</div>
                </div>

                <!-- 상품 체크 리스트 -->
                <?php include_once "./prd_list_chk.php" ?>

                <!-- 최종 결제 금액 -->
                <?php include_once "./final_pay_price.php" ?>

                <button type="button" id="cartDelBtn" class="border_btn" onclick="onCartDel();">선택삭제</button>

                <div class="btn_wrap">
                    <a href="javascript:void(0);" class="black_btn shadow_btn w_500" id="cartOrderBtn" onclick="onOrderAdd();">주문하기</a>
                </div>

            </div>
        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>
<script>
    changeDELIVERY('cart');

    function onOrderAdd() {
        var seqArray = [];
        var priceArray = [];
        var codeArray = [];
        var quantityArray = [];

        let M_obj =  $("[name=prdChk]");

        M_obj.each(function() {
            let O_obj = $("#"+this.id);

            if (O_obj.is(":checked")) {
                var seq = O_obj.parent("li").data('seq');
                var code = O_obj.parent("li").data('code');
                var quantity = O_obj.parent("li").parent("ul").find(".option_count").val();
                var mval = O_obj.parent("li").data('mval');

                if (seqArray.indexOf(seq) === -1) {
                    seqArray.push(seq);
                }

                codeArray.push(code);
                priceArray.push(mval);
                quantityArray.push(quantity);
            }
        });

        var seqString = seqArray.join(',');
        var mPriceString = priceArray.join(',');
        var codeString = codeArray.join(',');
        var quantityString = quantityArray.join(',');

        var list = {
              'mode' : 'ORDERADD'
            , 'page_type' : '<?=PAGE2?>'
            , 'TYPE_CD' : 'CART'
            , 'Prices' : mPriceString
            , 'val': seqString
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
                    location.href = "<?= shopFoldName ?>/order/ordersheet.php?SEQ=" + json.seq;
                } else {
                    alert(json.msg);
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    function onCartDel() {
        var pkArray = [];
        var seqArray = [];
        var codeArray = [];

        let M_obj =  $("[name=prdChk]");

        M_obj.each(function() {
            let O_obj = $("#"+this.id);

            if (O_obj.is(":checked")) {
                var pk = O_obj.parent("li").data('pk');
                var seq = O_obj.parent("li").data('seq');
                var code = O_obj.parent("li").data('code');

                pkArray.push(pk);
                seqArray.push(seq);
                codeArray.push(code);
            }
        });

        var pkString = pkArray.join(',');
        var seqString = seqArray.join(',');
        var codeString = codeArray.join(',');

        var list = {
              'mode' : 'CARTDEL'
            , 'pk' : pkString
            , 'val': seqString
            , 'Options' : codeString
        };

        $.ajax({
              type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function(data) {
                // 처리 성공 시 실행할 코드
                let json = JSON.parse(data);

                alert(json.msg);

                if (json.code == 200) {
                    location.reload(); // 리로드 실행
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });

    }

    /* 전체 동의 */
    function allCheckFunc(obj) {
        $("[name=prdChk]").prop("checked", $(obj).prop("checked"));
        cart_chk(obj);
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
                cart_chk(this);
            });
        });
    });

    // 금액 및 체크박스 값 추가
    function cart_chk(obj) {
        let pkArray = [];
        let seqArray = [];
        let codeArray = [];
        let currentCount = 0;
        let newCount = 0;
        let currentValue = 0;
        let newValue = 0;
        let pk = "";
        let seq = "";
        let code = "";
        let chk = "N";

        if (obj.name == "prdChkAll") {
            let M_obj =  $("[name=prdChk]");

            if (obj.checked) {
                M_obj.each(function() {
                    newValue += currentValue + parseInt($(this).closest('.tbody').find(".prdPrice").text().replace(/,/g, ''));

                    pk = $(this).parent("li").data('pk');
                    seq = $(this).parent("li").data('seq');
                    code = $(this).parent("li").data('code');

                    pkArray.push(pk);
                    seqArray.push(seq);
                    codeArray.push(code);
                });

                chk = "Y";
            } else {
                M_obj.each(function() {
                    newValue = 0;

                    pk = $(this).parent("li").data('pk');
                    seq = $(this).parent("li").data('seq');
                    code = $(this).parent("li").data('code');

                    pkArray.push(pk);
                    seqArray.push(seq);
                    codeArray.push(code);
                });

                chk = "N";
            }

        } else {
            let O_obj = $("#"+obj.id);

            currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
            
            if ($("#"+obj.id).is(":checked")) {
                newValue = currentValue + parseInt(O_obj.closest('.tbody').find(".prdPrice").text().replace(/,/g, ''));

                chk = "Y";
            } else {
                newValue = currentValue - parseInt(O_obj.closest('.tbody').find(".prdPrice").text().replace(/,/g, ''));

                chk = "N";
            }

            pk = O_obj.parent("li").data('pk');
            seq = O_obj.parent("li").data('seq');
            code = O_obj.parent("li").data('code');

            pkArray.push(pk);
            seqArray.push(seq);
            codeArray.push(code);
        }

        $("#totalPrice_text").text(newValue.toLocaleString());

        changeDELIVERY('cart');

        var pkString = pkArray.join(',');
        var seqString = seqArray.join(',');
        var codeString = codeArray.join(',');

        var list = {
              'mode' : 'CARTCHK'
            , 'CHEK_YN' : chk
            , 'pk' : pkString
            , 'val': seqString
            , 'Options' : codeString
        };

        $.ajax({
              type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function(data) {
                // 처리 성공 시 실행할 코드
                let json = JSON.parse(data);

                if (json.code != 200) {
                    alert(json.msg);
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>