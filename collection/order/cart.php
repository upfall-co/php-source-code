<?php
    define("SUB", "ORDER");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/cart_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="cart">
        <div class="wrapper_1400">

            <div class="sub_title eng">Cart</div>

            <!-- 상품 체크 리스트 -->
            <?php include_once "./prd_list_chk.php" ?>

            <button type="button" id="cartDelBtn" class="border_btn" onclick="onCartDel();">선택삭제</button>

            <!-- 최종 결제 금액 -->
            <?php include_once "./final_pay_price.php" ?>

            <div class="btn_wrap">
                <a href="javascript:void(0);" class="black_btn shadow_btn w_500" id="cartOrderBtn" onclick="onOrderAdd();">주문하기</a>
            </div>

        </div>
    </main>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>
<script>
    function onOrderAdd() {
        var seqArray = [];
        var codeArray = [];

        let M_obj =  $("[name=prdChk]");

        M_obj.each(function() {
            let O_obj = $("#"+this.id);

            if (O_obj.is(":checked")) {
                var seq = O_obj.parent("li").data('seq');
                var code = O_obj.parent("li").data('code');

                if (seqArray.indexOf(seq) === -1) {
                    seqArray.push(seq);
                }
                codeArray.push(code);
            }
        });

        var seqString = seqArray.join(',');
        var codeString = codeArray.join(',');

        var list = {
              'mode' : 'ORDERADD'
            , 'TYPE_CD' : 'CART'
            , 'page_type' : '<?=PAGE1?>'
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

                if (json.code == 200) {
                    location.href = "<?= artFoldName ?>/order/ordersheet.php?SEQ=" + json.seq;
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
                    newCount += currentCount + parseInt($(this).parent("li").data('count'));
                    newValue += currentValue + parseInt($(this).parent("li").data('val'));

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
                    newCount = 0;
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

            currentCount = parseInt($("#totalPrice_count").text().replace(/,/g, ''));
            currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
            
            if ($("#"+obj.id).is(":checked")) {
                newCount = currentCount + parseInt(O_obj.parent("li").data('count'));
                newValue = currentValue + parseInt(O_obj.parent("li").data('val'));

                chk = "Y";
            } else {
                newCount = currentCount - parseInt(O_obj.parent("li").data('count'));
                newValue = currentValue - parseInt(O_obj.parent("li").data('val'));

                chk = "N";
            }

            pk = O_obj.parent("li").data('pk');
            seq = O_obj.parent("li").data('seq');
            code = O_obj.parent("li").data('code');

            pkArray.push(pk);
            seqArray.push(seq);
            codeArray.push(code);
        }

        $("#totalPrice_count").text(newCount.toLocaleString());
        $("#totalPrice_text").text(newValue.toLocaleString());

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