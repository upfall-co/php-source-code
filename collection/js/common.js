$(document).ready(function(){

  // 스크롤에 따른 헤더 클래스 변경
  $(window).scroll(function() {

    if ($(window).scrollTop() > 50) {
      //$('html,body').addClass("padding_top");
      $("#header").addClass("sm_header");
    }
    else if ($(window).scrollTop() == 0) {
      //$('html,body').removeClass("padding_top");
      $("#header").removeClass("sm_header");
    }

  });

  // 햄버거 버튼
  $(".hamburger").click(function(){
    $(".hamburger").toggleClass("is-active")
    $("#hamBg").stop().fadeToggle();
    $("#hamWrap").toggleClass("active");

    if ( $(this).hasClass("is-active") ) {
      $('html, body').addClass("noScroll");
    }
    else {
      $('html, body').removeClass("noScroll");
    }
  });

  // 햄버거 배경
  $("#hamBg").click(function(){
    $("#hamBg").stop().fadeOut();
    $("#hamWrap").removeClass("active");
    $('html, body').removeClass("noScroll");
  });

  // 폼 입력할 때 label 위로 뜨는 효과
  $(".form_label_ani:not(.no_float) input").each(function(index, item){
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
  $(".form_label_ani:not(.no_float) textarea").each(function(index, item){
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
  $(".form_label_ani.just_input input").each(function(index, item){
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
function popClose (){
  $(".popup_bg").stop().fadeOut();
  $(".popup").stop().fadeOut();
  $('html, body').removeClass("noScroll");
}

// 폼 입력창 X 버튼 (내용 지우기)
function eraseThisForm (e){
  $(e).siblings("input").val('');
  $(e).siblings("input").removeClass("focus");
  $(e).siblings("textarea").val('');
  $(e).siblings("textarea").removeClass("focus");
  $(e).siblings("label").removeClass("label_float");
  $(e).removeClass("erase_float");
}