var prdThumbSwiper;
var alsoLikeSwiper;

$(document).ready(function(){

  prdThumbSwiper = new Swiper(".prdThumbSwiper", {
    slidesPerView: 1,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    loop: true,
    speed: 600,
    pagination: {
      el: ".detail_thumb_slide .swiper-pagination",
    },
    navigation: {
      prevEl: ".detail_thumb_slide .swiper-button-prev",
      nextEl: ".detail_thumb_slide .swiper-button-next",
    },
  });

  alsoLikeSwiper = new Swiper(".alsoLikeSwiper", {
    slidesPerView: 2,
    spaceBetween: 15,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    loop: true,
    speed: 600,
    navigation: {
      prevEl: ".sec_relation_prd .swiper-button-prev",
      nextEl: ".sec_relation_prd .swiper-button-next",
    },
    breakpoints: {
      1400: {
        slidesPerView: 5,
        spaceBetween: 30,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 25,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
    }
  });

  // 상품 상세 안내 토글
  $(".toggle_guide .toggle_title .arrow").click(function(){
    $(this).toggleClass("open");
    $(this).parents(".toggle_title").siblings(".toggle_cont").stop().slideToggle(200);
  });

/**
 * name :changeOptionValue
 * comment :  상품 옵션 추가
 */
changeOptionValue = (target) => {
    const value = target.value;

    if (value == "") {
      return;
    }

    const selectedOptionVal = target.options[target.selectedIndex].value;
    const selectedOptionName = target.options[target.selectedIndex].text;
    const selectedOptionPrice = target.options[target.selectedIndex].dataset['val'];
    const selectedOptionMPrice = target.options[target.selectedIndex].dataset['mval'];

    let selectPrice = parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice);

    const newOptionHtml = `<li class="selected_option" id="${selectedOptionVal}" data-code="${selectedOptionVal}" data-val="${selectedOptionPrice}" data-mval="${selectedOptionMPrice}">
                              <div class="selected_info">
                                  <div class="option_name">${selectedOptionName}</div>
                                  <div class="count_wrap">
                                      <input type="button" title="-" id="minusBtn${selectedOptionVal}" onclick="countMinus('${selectedOptionVal}', 'detail')">
                                      <input type="text" value="1" id="optionCount${selectedOptionVal}" class="option_count" onchange="countInput('${selectedOptionVal}', 'detail')">
                                      <input type="button" title="+" id="plusBtn${selectedOptionVal}" onclick="countPlus('${selectedOptionVal}', 'detail')">
                                  </div>
                                  <div class="option_price"><span id="option_price${selectedOptionVal}">${selectPrice.toLocaleString()}</span></div>
                              </div>
                              <div class="del_btn" onclick="deleteOption(this, '${selectedOptionVal}');">
                                  <img src="../img/product/icon_del.svg" alt="삭제">
                              </div>
                          </li>`;

    $(".selected_option_list ul").append(newOptionHtml);

    let currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
    let newValue = currentValue + parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice);

    $("#totalPrice").val(newValue);
    $("#totalPrice_text").text(newValue.toLocaleString());

    target.options[target.selectedIndex].style.display = 'none';
    target.selectedIndex = 0;

    changeDELIVERY('detail');
  }
});

