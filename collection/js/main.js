var mainMoSwiper;

$(document).ready(function(){

  $("#footer_toggle").click(function(){
    $(this).toggleClass("open");
    $("#footer.footer_main").toggleClass("open");
    $("#footer.footer_main").stop().slideToggle();
  });

  $(".main_list.pc li.main_real").hover(function(){
    $(this).siblings("li").addClass("opacity");
    $(this).addClass("hover");
  }, function(){
    $(this).siblings("li").removeClass("opacity");
    $(this).removeClass("hover");
  });

  mainMoSwiper = new Swiper(".mainMoSwiper", {
    slidesPerView: 1,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    loop: true,
    speed: 600,
    observer: true,
    observeParents: true,
    pagination: {
      el: ".mainMoSwiper .swiper-pagination",
      type: "fraction",
    },
    navigation: {
      prevEl: ".mainMoSwiper .swiper-button-prev",
      nextEl: ".mainMoSwiper .swiper-button-next",
    },
  });

});



