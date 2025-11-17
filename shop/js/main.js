var mainBannerSwiper;
var newPrdSwiper;

$(document).ready(function () {
    mainBannerSwiper = new Swiper(".mainBannerSwiper", {
        slidesPerView: 1,
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
        },
        loop: true,
        speed: 600,
        pagination: {
            el: ".mainSec1 .swiper-pagination",
        },
    });

    newPrdSwiper = new Swiper(".newPrdSwiper", {
        slidesPerView: 2,
        spaceBetween: 15,
        autoplay: false,
        loop: true,
        speed: 600,
        navigation: {
            prevEl: ".mainSec2 .swiper-button-prev",
            nextEl: ".mainSec2 .swiper-button-next",
        },
        breakpoints: {
            1400: {
                slidesPerView: 4,
                spaceBetween: 100,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
        }
    });

    // 상품 탭
    $(".prd_cate_tab li").click(function () {
        $(".prd_cate_tab li").removeClass("selected");
        $(this).addClass("selected");

        chage_prd_box($(this).data('category'));
    });

    $(".prd_cate_tab li").eq(0).click();

    $(".prd_cate_tab li").hover(function () {
        $(".prd_cate_tab li").css("opacity", "0.6");
        $(this).css("opacity", "1");

        }, function () {
            $(".prd_cate_tab li").css("opacity", "1");
    });

    // 푸터 토글
    $("#footer_toggle").click(function(){
        $(this).toggleClass("open close");
        $("#footer.footer_main").toggleClass("open close");
    });
});



function chage_prd_box(SEQ) {
    var list = {
          'mode' : 'MPRDBOX'
        , 'page_type' : 'shop'
        , 'SEQ': SEQ
    };

    $.ajax({
          type: "POST"
        , url: "/php/ajax_module.php"
        , data: list
        , success: function(data) {
            // 처리 성공 시 실행할 코드
            let json = JSON.parse(data);

            let element = document.querySelector('.chg_prd_box ul');
            element.innerHTML = '';
            
            if (json.length !== 0) {
                // 서버에서 받아온 데이터로 option 요소들 생성
                $.each(json, function (key, value) {
                    element.innerHTML += get_info(value);
                });
            }
        }
        , error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });

    var list = {
          'mode' : 'GETCATEG2'
        , 'page_type' : 'shop'
        , 'SEQ': SEQ
    };

    $.ajax({
        type: "POST"
      , url: "/php/ajax_module.php"
      , data: list
      , success: function(data) {
          // 처리 성공 시 실행할 코드
          let json = JSON.parse(data);
          
          if (json.length !== 0) {
              $('#chg_prd_box_a').attr("href", "/shop/product/list.php?cate1="+ json.CATEGORY1_SEQ);
          }
      }
      , error: function(jqXHR, textStatus, errorThrown) {
          console.log(textStatus, errorThrown);
      }
  });


}

/**
 * name : get_info
 * comment : 메인노출 html
 */
function get_info(value) {
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
        str += '	        <div class="prdBadge"><span>' + BADGE_CO + '</span></div> \n';
    }

    str += '	        <img src="' + value.MAIN_ATTACH_FILE_ID + '" alt="썸네일"> \n';
    str += '	        <img src="' + value.HOVER_ATTACH_FILE_ID + '" alt="썸네일"> \n';
    str += '	    </a> \n';
    str += '	    <a href="' + URL + '" class="prdName">' + value.CATEGORY3_NAME + '</a>\n';

    if (value.SALE_YN == "Y") {
        str += '	    <div class="prdSalePercent"><span>' + value.SALE_PERCENT + '</span>%</div> \n';
    }

    str += '	    <div class="prdPrice"><span>' + value.PRICE + '</span></div>\n';

    if (value.SALE_YN == "Y") {
        str += '	    <div class="prdSalePrice"><span>' + value.OID_PRICE + '</span></div> \n';
    }

    str += '	</li> \n';

    return str;
}


