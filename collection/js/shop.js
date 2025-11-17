var detailSwiper;
var relationSwiper;

$(document).ready(function () {

  detailSwiper = new Swiper(".detailSwiper", {
    slidesPerView: 1,
    //autoHeight: true,
    allowTouchMove: false,
    /* autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    }, */
    loop: true,
    speed: 600,
    observer: true,
    observeParents: true,
    navigation: {
      prevEl: ".shop_thumb_slide .shop_prev",
      nextEl: ".shop_thumb_slide .shop_next",
    },
    pagination: {
      el: ".shop_thumb_slide .swiper-pagination",
      clickable: true,
    },
  });

  relationSwiper = new Swiper(".relationSwiper", {
    slidesPerView: 2,
    spaceBetween: 10,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    loop: true,
    speed: 600,
    navigation: {
      prevEl: ".relation_prd .swiper-button-prev",
      nextEl: ".relation_prd .swiper-button-next",
    },
    breakpoints: {
      1024: {
        slidesPerView: 5,
        spaceBetween: 24,
      },
      768: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
      540: {
        slidesPerView: 3,
        spaceBetween: 15,
      },
    }
  });

// 상품 옵션 추가
changeOptionValue = (target) => {
    const value = target.value;

    if (value == "") {
      return;
    }

    const selectedOptionVal = target.options[target.selectedIndex].value;
    const selectedOptionName = target.options[target.selectedIndex].text;
    const selectedOptionPrice_text = target.options[target.selectedIndex].dataset['price'];
    const selectedOptionPrice = target.options[target.selectedIndex].dataset['val'];

    $(".selected_option ul").append('<li data-code='+selectedOptionVal+' data-val='+selectedOptionPrice+'><div class="selected_info"><div class="option_name">' + selectedOptionName + '</div><div class="option_price">'+ selectedOptionPrice_text +'원</div></div><div class="del_btn" onclick="deleteOption(this);"><img src="../img/shop/del_btn.png" alt="삭제"></div></li>');

    var currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
    var newValue = currentValue + parseInt(selectedOptionPrice);

    $("#totalPrice").val(newValue);
    $("#totalPrice_text").text(newValue.toLocaleString());

    target.options[target.selectedIndex].style.display = 'none';
    target.selectedIndex = 0;
  }

});

// 상품 옵션 삭제
function deleteOption (e){
  var optionValue = $(e).parent("li").data('code'); // 선택한 옵션의 value 값 가져오기
  var optionElement = document.querySelector('#shopSelect [value="' + optionValue + '"]'); // 선택한 옵션의 DOM 요소 가져오기

  // 삭제한 옵션을 다시 보이게 설정
  if (optionElement) {
    optionElement.style.display = 'block';
  }

  var currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
  var newValue = currentValue - parseInt($(e).parent("li").data('val'));
  
  $("#totalPrice").val(newValue);
  $("#totalPrice_text").text(newValue.toLocaleString());

  $(e).parent("li").remove();
}


