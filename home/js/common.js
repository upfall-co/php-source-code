const thisUrl = new URL(window.location.href);
const pathName = thisUrl.pathname;
const searchParam = thisUrl.searchParams;
const getCATE2 = searchParam.get("cate2");

$(document).ready(function () {

  footerBorderSetting();

});





// 모달 열기
// modalOpenBtn : 모달 열기 버튼
// modal : 모달 요소의 클래스 또는 아이디
// modalContainer : 모달 콘텐츠 요소의 클래스 또는 아이디
function modalShowing(modalOpenBtn, modal, modalContainer){

  const thisDataModal = $(modalOpenBtn).data("modal");

  $(modal).stop().fadeIn(200).css("display", "flex");
  $(modalContainer).css("display", "none");
  $(`${modalContainer}.${thisDataModal}`).css("display", "flex");

}

// 모달 열기
// modal : 모달 요소의 클래스 또는 아이디
function modalClose(modal){

  $(modal).stop().fadeOut(200);
  $("html").css({
    "overflow-y": "auto",
  });

}





// 탑버튼
function scrollTopBtn() {

  $(window).on("scroll", function(){

    if($(window).scrollTop() != 0){
      $(".top_btn").stop().fadeIn(200).css("display", "flex");
    }else{
      $(".top_btn").stop().fadeOut(200);
    }

  });

  $(".top_btn").on("click", function () {
    $("html, body").animate({ scrollTop: 0 }, 400);
  });

  // footer에 걸치기가 필요할 때

  // let startPos;

  // let isPosRes = () => {

  //   if($(window).innerWidth() <= 540){
  //     return startPos = "-55px";
  //   }else{
  //     return startPos = "-95px";
  //   }

  // }
  // isPosRes();

  // $(window).resize(function(){
  //   isPosRes();
  // })

  // gsap.to(".top_btn", {
  //   scrollTrigger: {
  //     trigger: "footer",
  //     // markers: true,
  //     start: "top bottom",
  //     oonEnter: function () { 
  //       $(".top_btn").css({
  //         position: "absolute",
  //         top: startPos,
  //         bottom: "auto"
  //       });
  //     },
  //     onLeaveBack: function () {
  //       $(".top_btn").css({
  //         position: "fixed",
  //         top: "auto",
  //         bottom: "30px"
  //       });
  //     },
  //   },
  // });

}

function visualSlideConfig(originSlideEl, targetSlideEl) {
  const orgSlide = document.querySelector(originSlideEl);
  const targetSlide = document.querySelector(targetSlideEl);

  if (orgSlide) {

    const visualSlide = new Swiper(targetSlide, {
      slidesPerView: 1,
      loop: true,

      pagination: {
        el: ".visual_slide__pager",
        bulletActiveClass: "active",
        clickable: true,
      },

      navigation: {
        nextEl: ".visual_slide__nav--next",
        prevEl: ".visual_slide__nav--prev",
      },
    });

  }

}

function viewInfoEnter(el, targetEl){

  const getTarget = $(el).find(targetEl);

  const eventTrigger = () => {
    return resCalc(true, false, false);
  }

  if(eventTrigger()){
    getTarget.stop().fadeIn(400).css({
      display: "flex"
    });
  }

}

function viewInfoLeave(el, targetEl){

  const getTarget = $(el).find(targetEl);

  const eventTrigger = () => {
    return resCalc(true, false, false);
  }
  
  if(eventTrigger()){
    getTarget.stop().fadeOut(400);
  }

}

function resCalc (pc, tab, mo){
    
  if($(window).innerWidth() > 1024){
      return pc;
    }
    else if(
      $(window).innerWidth() < 1024 &&
      $(window).innerWidth() > 540
    ){
      return tab;
    }else if(
      $(window).innerWidth() < 540 &&
      $(window).innerWidth() > 0
    ){
      return mo;
    };

}

function footerBorderSetting(){
  
  const footerEl = document.querySelector("footer");

  const targetPage = [
    "program",
    "collabo",
    "space",
    "about",
    "location",
    "notice",
    "contact",
    "recruit",
    "board_view"
  ];

  targetPage.forEach(
    (page) => {

      if(pathName.includes(page)){
        footerEl.classList.add("footer--line");
      }

    }
  )

}