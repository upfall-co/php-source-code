<?php
    define("SUB", "SHOP");

    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

    $search_text = get_request_param('search_text', 'GET');

    if (empty($search_text)) {
        dieAndErrorMove("검색어를 입력해주세요.");
    }

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content" class="paddingTop60">
        <main class="page_prd_list">

            <section class="sec_product">
                <div class="wrapper">
                    <div class="sub_sec_title"><?=$search_text?>의 검색결과입니다.</div>

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
        let SEQ = "<?= $search_text ?>";

        morePrdList(SEQ, page);

        //스크롤이 맨 끝에 내려왔을때
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                page++;
                morePrdList(SEQ, page);
            }
        });
    });

    function morePrdList(SEQ, page_val) {
        let list = {
              'mode': 'PRODUCTINFO_ST'
            , 'page_type' : 'shop'
            , 'search_text': SEQ
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

        let str = '';

        str += '	<li> \n';
        str += '	    <a href="' + URL + '" class="prdThumbnail"> \n';
        str += '	        <img src="' + value.MAIN_ATTACH_FILE_ID + '" > \n';
        str += '	        <img src="' + value.HOVER_ATTACH_FILE_ID + '"> \n';
        str += '	    </a> \n';
        str += '	    <a href="' + URL + '" class="prdName">' + value.CATEGORY3_NAME + '</a>\n';
        str += '	    <div class="prdPrice"><span>' + value.PRICE + '</span></div> \n';
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