<?php
    define("SUB", "SHOP");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/shop_list_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main class="page_prd_list">

            <!-- 상단 현재 위치 / 서브 타이틀 영역 -->
            <div class="sub_title_wrap wrapper">
                <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/page_route.php" ?>
                <div class="page_title"><?=$_db_CATEGORY3_TITLE?></div>
            </div>

            <section class="sec_product">
                <div class="wrapper">
                    <div class="sub_sec_title">products</div>

                    <ul class="prdList">
                    </ul>
                </div>
            </section>

        </main>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php" ?>

<script>
    let page = 1;

    $(document).ready(function() {
        let CHK_TYPE = "";
        let SEQ1 = "<?= $cate1 ?>";
        let SEQ2 = "<?= $cate2 ?>";

        morePrdList(SEQ1, SEQ2, page);

        //스크롤이 맨 끝에 내려왔을때
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                morePrdList(SEQ1, SEQ2, page);
            }
        });
    });

    function morePrdList(SEQ1, SEQ2, page_val) {
        let list = {
              'mode': 'PRODUCTINFO'
            , 'page_type' : 'shop'
            , 'SEQ1': SEQ1
            , 'SEQ2': SEQ2
            , 'limit': 8
            , 'page': page_val
        };

        $.ajax({
            type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function (data) {
                let json = JSON.parse(data);

                if (json.length !== 0) {
                    if (json.code == 999) {
                        page--;
                    } else {
                        $.each(json, function (key, value) {
                            $(".prdList").append(get_PRODUCTinfo(value));
                        });

                        showPrdEach();
                    }
                }
            }
            , error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    /**
     * name : get_info
     * comment : 메인노출 html
     */

    function get_PRODUCTinfo(value) {
        let URL = '/shop/product/detail.php?seq=' + value.CATEGORY3_SEQ;

        let BADGE_CO = "";

        if (!gfn_isNull(value.BADGE_CO)) {
            switch (value.BADGE_CO) { 
                case 'NEW':
                    BADGE_CO = 'new';
                    break;
                case 'SALE':
                    BADGE_CO = 'sale';
                    break;
                case 'BEST':
                    BADGE_CO = 'best';
                    break;
                case 'SOLDOUT':
                    BADGE_CO = 'sold out';
                    break;
                default:
                    // 해당하는 id가 없는 경우에 대한 처리
                    break;
            }
        }

        let str = '';

        str += '	<li> \n';
        str += '	    <a href="' + URL + '" class="prdThumbnail"> \n';

        if (!gfn_isNull(value.BADGE_CO)) {
            str += '	        <div class="prdBadge"><span>'+ BADGE_CO +'</span></div> \n';
        }

        str += '	        <img src="' + value.MAIN_ATTACH_FILE_ID + '" > \n';
        str += '	        <img src="' + value.HOVER_ATTACH_FILE_ID + '"> \n';
        str += '	    </a> \n';
        str += '	    <a href="' + URL + '" class="prdName">' + value.CATEGORY3_NAME + '</a>\n';

        if (value.SALE_YN == "Y") {
            str += '	    <div class="prdSalePercent"><span>' + value.SALE_PERCENT + '</span>%</div> \n';
        }
        
        str += '	    <div class="prdPrice"><span>' + value.PRICE + '</span></div> \n';

        if (value.SALE_YN == "Y") {
            str += '	    <div class="prdSalePrice"><span>' + value.OID_PRICE + '</span></div> \n';
        }
        str += '	</li> \n';

        return str;
    }

    function showPrdEach() {
        var prdArray = $(".prdList > li").toArray();

        $.each(prdArray, function(idx) {
            var $prdTarget = $(this).not(".show");

            setTimeout(function() {
                $prdTarget.addClass("show");
            }, 100);
            //}, 300 * idx);
        });
    }
</script>