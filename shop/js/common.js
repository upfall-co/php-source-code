$(document).ready(function () {
    sideNavEffect();
    settingNavHei();

    scrollMotionTrigger();

    $(window).scroll(function () {

        if ($(window).scrollTop() > 0) {
            $("#moHeader").addClass("sm_header");
            $("#fixed_nav").addClass("sm_header");
        }
        else if ($(window).scrollTop() == 0) {
            $("#moHeader").removeClass("sm_header");
            $("#fixed_nav").removeClass("sm_header");
        }
    });

    $(window).resize(function () {
        sideNavEffect();
        settingNavHei();
    });

    // 폼 입력할 때 label 위로 뜨는 효과
    $(".form_label_ani:not(.no_float) input").each(function (index, item) {
        if ($(this).val().trim() == '') {
            $(this).removeClass("focus");
            $(this).siblings("label").removeClass("label_float");
            $(this).siblings(".erase_btn").removeClass("erase_float");
            return false;
        } else {
            $(this).addClass("focus");
            $(this).siblings("label").addClass("label_float");
            $(this).siblings(".erase_btn").addClass("erase_float");
        }
    });

    $(".form_label_ani:not(.no_float) textarea").each(function (index, item) {
        if ($(this).val().trim() == '') {
            $(this).removeClass("focus");
            $(this).siblings("label").removeClass("label_float");
            $(this).siblings(".erase_btn").removeClass("erase_float");
            return false;
        } else {
            $(this).addClass("focus");
            $(this).siblings("label").addClass("label_float");
            $(this).siblings(".erase_btn").addClass("erase_float");
        }
    });

    $(".form_label_ani.just_input input").each(function (index, item) {
        if ($(this).val().trim() == '') {
            $(this).removeClass("focus");
            $(this).siblings("label").show();
            return false;
        } else {
            $(this).addClass("focus");
            $(this).siblings("label").hide();
        }
    });

    if (!$(".form_label_ani:not(.no_float) input").val()) {
        $(this).addClass("focus");
        $(this).siblings("label").addClass("label_float");
        $(this).siblings(".erase_btn").addClass("erase_float");
    } else {
        $(this).removeClass("focus");
        $(this).siblings("label").removeClass("label_float");
        $(this).siblings(".erase_btn").removeClass("erase_float");
    }

    $(".form_label_ani:not(.no_float) input").focus(function (e) {
        $(this).addClass("focus");
        $(this).siblings("label").addClass("label_float");
        $(this).siblings(".erase_btn").addClass("erase_float");
    });

    $(".form_label_ani:not(.no_float) input").blur(function (e) {
        if ($(this).val() == '') {
            $(this).removeClass("focus");
            $(this).siblings("label").removeClass("label_float");
            $(this).siblings(".erase_btn").removeClass("erase_float");
        } else {
            $(this).addClass("focus");
            $(this).siblings("label").addClass("label_float");
            $(this).siblings(".erase_btn").addClass("erase_float");
        }
    });

    if (!$(".form_label_ani:not(.no_float) textarea").val()) {
        $(this).addClass("focus");
        $(this).siblings("label").addClass("label_float");
        $(this).siblings(".erase_btn").addClass("erase_float");
    } else {
        $(this).removeClass("focus");
        $(this).siblings("label").removeClass("label_float");
        $(this).siblings(".erase_btn").removeClass("erase_float");
    }

    $(".form_label_ani:not(.no_float) textarea").focus(function (e) {
        $(this).addClass("focus");
        $(this).siblings("label").addClass("label_float");
        $(this).siblings(".erase_btn").addClass("erase_float");
    });

    $(".form_label_ani:not(.no_float) textarea").blur(function (e) {
        if ($(this).val() == '') {
            $(this).removeClass("focus");
            $(this).siblings("label").removeClass("label_float");
            $(this).siblings(".erase_btn").removeClass("erase_float");
        } else {
            $(this).addClass("focus");
            $(this).siblings("label").addClass("label_float");
            $(this).siblings(".erase_btn").addClass("erase_float");
        }
    });

    if (!$(".form_label_ani.just_input input").val()) {
        $(this).addClass("focus");
        $(this).siblings("label").hide();
    } else {
        $(this).removeClass("focus");
        $(this).siblings("label").show();
    }

    $(".form_label_ani.just_input input").focus(function (e) {
        $(this).addClass("focus");
        $(this).siblings("label").hide();
    });

    $(".form_label_ani.just_input input").blur(function (e) {
        if ($(this).val() == '') {
            $(this).removeClass("focus");
            $(this).siblings("label").show();
        } else {
            $(this).addClass("focus");
            $(this).siblings("label").hide();
        }
    });

});

// 폼 핸드폰
autoHyphen = (target) => {
    target.value = target.value
        .replace(/[^0-9]/g, '')
        .replace(/^(\d{2,3})(\d{3,4})(\d{4})$/, `$1-$2-$3`);
}

// 팝업 닫기
function popClose() {
    $(".popup_bg").stop().fadeOut();
    $(".popup").stop().fadeOut();
    $('html, body').removeClass("noScroll");
}

// 폼 입력창 X 버튼 (내용 지우기)
function eraseThisForm(e) {
    $(e).siblings("input").val('');
    $(e).siblings("input").removeClass("focus");
    $(e).siblings("textarea").val('');
    $(e).siblings("textarea").removeClass("focus");
    $(e).siblings("label").removeClass("label_float");
    $(e).removeClass("erase_float");
}

// 떠오르는 효과
function scrollMotionTrigger() {
    $(".scroll_motion:visible").each(function (q) {
        gsap.to($(this), {
            scrollTrigger: {
                trigger: $(this),
                start: "top 90%",
                end: "bottom top",
                toggleClass: { targets: $(".scroll_motion:visible").eq(q), className: "active" },
                once: true,
            },
        });
    });
}

// hover 시 아래 2뎁스 & 오른쪽 내비게이션 노출
let CATEGORY2_VALUE = "";

function sideNavEffect() {
    if ($(window).width() > 1024) {
        $(".depth1").hover(function () {
            if ($(this).find("div").hasClass("depth_open_btn") == true) {
                if (CATEGORY2_VALUE != $(this).data('seq')) {
                    CATEGORY2_VALUE = $(this).data('seq');

                    $(this).siblings().find(".depth2").stop().slideUp(800);
                    $(this).find(".depth2").stop().slideDown(500);
                    // $("#navAddPrdBox").addClass("sideNavOpen");
                    // $("#navAddBg").stop().fadeIn(100);
                } else {
                    return;
                }

                var getInfoPromise = ufn_getBestInfo(CATEGORY2_VALUE);
                var getRecommendedInfoPromise = ufn_getRecommendedInfo(CATEGORY2_VALUE);

                Promise.all([getInfoPromise, getRecommendedInfoPromise])
                    .then(function() {
                        // 두 개의 AJAX 요청이 모두 완료될 때 실행할 코드
                        $(this).siblings().find(".depth2").stop().slideUp(800);
                        $(this).find(".depth2").stop().slideDown(500);
                        // $("#navAddPrdBox").addClass("sideNavOpen");
                        // $("#navAddBg").stop().fadeIn(100);

                        $("#navAddPrdBox").mouseleave(function () {
                            CATEGORY2_VALUE = "";
                            $(".nav_wrap .depth2").stop().slideUp(800);
                            // $("#navAddPrdBox").removeClass("sideNavOpen");
                            // $("#navAddBg").stop().fadeOut(100);
                        });
                    })
                    .catch(function() {
                        // 오류 처리
                    });
            } else {
                CATEGORY2_VALUE = "";

                $(".nav_wrap .depth2").stop().slideUp(350);
                // $("#navAddPrdBox").removeClass("sideNavOpen");
                // $("#navAddBg").stop().fadeOut(100);
            }
        });
    } else {
        $(".depth_open_btn .mo_arrow").click(function () {
            $(this).toggleClass("open");

            if ($(this).parent("div").hasClass("depth_open_btn") == true) {
                $(this).parents(".depth1").siblings().find(".depth2").stop().slideUp(350);
                $(this).parents(".depth1").siblings().find(".mo_arrow").removeClass("open");
                $(this).parent("div").next(".depth2").stop().slideToggle(450);
                // $("#navAddPrdBox").toggleClass("sideNavOpen");
                // $("#navAddBg").stop().fadeToggle(100);
            }
        });
    }
}

function ufn_getBestInfo(SEQ) {
    var list = {
          'mode' : 'NAVINFO'
        , 'page_type' : 'shop'
        , 'SEQ': CATEGORY2_VALUE
        , 'TYPE' : 'BEST'
    };

    return $.ajax({
          type: "POST"
        , url: "/php/ajax_module.php"
        , data: list
        , success: function(data) {
            // 처리 성공 시 실행할 코드
            let json = JSON.parse(data);

            let element = document.querySelector('.prdBox.prdBox_best');
            element.innerHTML = '';

            let html = "";

            if (json.length !== 0) {
                html += '<div class="title">best sellers</div>';
                html += '<ul>';

                $.each(json, function (key, value) {
                    html += get_BestInfo(value);
                });

                html += '</ul>';

                element.innerHTML = html;
            }
            //console.log(json);
        }
        , error: function(jqXHR, textStatus, errorThrown) {
            //console.log(textStatus, errorThrown);
        }
    });
}

function ufn_getRecommendedInfo(SEQ) {
    var list = {
          'mode' : 'NAVINFO'
        , 'page_type' : 'shop'
        , 'SEQ': CATEGORY2_VALUE
        , 'TYPE' : 'RECOMMENDED'
    };

    return $.ajax({
          type: "POST"
        , url: "/php/ajax_module.php"
        , data: list
        , success: function(data) {
            // 처리 성공 시 실행할 코드
            let json = JSON.parse(data);

            let element = document.querySelector('.prdBox.prdBox_recommend');
            element.innerHTML = '';

            let html = "";

            if (json.length !== 0) {
                html += '<div class="title">recommended</div>';
                html += '<ul>';

                $.each(json, function (key, value) {
                    html += get_RecommendedInfo(value);
                });

                html += '</ul>';

                element.innerHTML = html;
            }

            //console.log(json);
        }
        , error: function(jqXHR, textStatus, errorThrown) {
            //console.log(textStatus, errorThrown);
        }
    });
}

function get_BestInfo(value) {
    let URL = '/shop/product/detail.php?seq=' + value.CATEGORY3_SEQ;

    let str = '';

    str += '	<li> \n';
    str += '	    <a href="' + URL + '"> \n';
    str += '	        <div class="prdThumbnail"><img src="' + value.MAIN_ATTACH_FILE_ID + '" alt="썸네일"></div> \n';
    str += '	        <div class="name_price"> \n';
    str += '	            <div class="prdName">' + value.CATEGORY3_NAME + '</div> \n';
    str += '	            <div class="addPrd_price"> \n';

    if (value.SALE_YN == "Y") {
        str += '	                <div class="prdSalePrice"><span>' + value.OID_PRICE + '</span></div> \n';
        str += '	                <div class="prdSalePercent"><span>' + value.SALE_PERCENT + '</span>%</div> \n';
    }

    str += '	                <div class="prdPrice"><span>' + value.PRICE + '</span></div> \n';
    str += '	            </div> \n';
    str += '	        </div> \n';
    str += '	    </a> \n';
    str += '	</li> \n';

    return str;
}

function get_RecommendedInfo(value) {
    let URL = '/shop/product/detail.php?seq=' + value.CATEGORY3_SEQ;

    let str = '';

    str += '	<li> \n';
    str += '	    <a href="' + URL + '"> \n';
    str += '	        <div class="prdThumbnail"><img src="' + value.MAIN_ATTACH_FILE_ID + '" alt="썸네일"></div> \n';
    str += '	        <div class="name_price"> \n';
    str += '	            <div class="prdName">' + value.CATEGORY3_NAME + '</div> \n';
    str += '	            <div class="addPrd_price"> \n';

    if (value.SALE_YN == "Y") {
        str += '	                <div class="prdSalePrice"><span>' + value.OID_PRICE + '</span></div> \n';
        str += '	                <div class="prdSalePercent"><span>' + value.SALE_PERCENT + '</span>%</div> \n';
    }

    str += '	                <div class="prdPrice"><span>' + value.PRICE + '</span></div> \n';
    str += '	            </div> \n';
    str += '	        </div> \n';
    str += '	    </a> \n';
    str += '	</li> \n';

    return str;
}

function sideNavClose() {
    $("#navAddBg").stop().fadeOut(100);
    $(".depth1 .depth2").stop().slideUp(0);
    $(".mo_arrow").removeClass("open");
    $("#navAddPrdBox").removeClass("sideNavOpen");
}

// 좌측 헤더 내비게이션 높이값 측정
function settingNavHei() {
    var sideBarHei = $("#header .height_100").height();
    var sidelogoHei = $("#header .header_logo").outerHeight(true);
    var sideSubHei = $("#header .sub_wrap").outerHeight(true);
    var sideCartLoginHei = $("#header .cart_login_wrap").outerHeight(true);
    var sideSearchHei = $("#header .search_wrap").outerHeight(true);
    var sideNavCalcHei = sideBarHei - sidelogoHei - sideSubHei - sideCartLoginHei - sideSearchHei;
    var moSideNavCalcHei = sideBarHei - sideSubHei - sideCartLoginHei - sideSearchHei;

    if ($(window).width() < 540) {
        $("#header .nav_wrap").css("height", moSideNavCalcHei);
    } else {
        $("#header .nav_wrap").css("height", sideNavCalcHei);
    }
}

// 모바일 햄버거
function onMoNavToggle(e) {
    $(e).toggleClass("open");
    $("#moHeader").toggleClass("open");
    $("#header").toggleClass("mo_open");
    $('html, body').toggleClass("noScroll");
}