$(document).ready(function () {
  
    spaceToInter();
    spaceSlideInit();

});


function spaceSlideInit(){

    const spaceS2ContentsWrap = document.querySelectorAll(".spac__s2_contents_wrap");

    spaceS2ContentsWrap.forEach(
        (el) => {

            const getTargetSlide = el.querySelector(".spac__s2_slide");
            const slidePrev = getTargetSlide.querySelector(".spac__s2_nav--prev");
            const slideNext = getTargetSlide.querySelector(".spac__s2_nav--next");

            const spacS2Slide = new Swiper(getTargetSlide, {
                slidesPerView: 1,
                loop: true,
          
                navigation: {
                  nextEl: slideNext,
                  prevEl: slidePrev,
                },
            });

        }
    )

}



function spaceToInter(){
    const spaceArr = $(".spac__s2_tab_wrap li");
    const spaceContainer = $(".spac__s2_contents_container");
    
    spaceArr.on("click", function(){
        spaceArr.each(
            (idx, el) => {
  
                $(el).removeClass("active");
  
                if(idx === $(this).index()){
                    $(this).addClass("active");
                }
  
            }
        );
  
        type_Cd = $(this).data('type');
  
        var list = {
            'mode': 'SPACE'
          , 'TYPE_CD': type_Cd
        };
  
        spaceContainer.empty();
  
        $.ajax({
            url: '/php/ajax_module.php',
            type: 'POST',
            data: list,
            dataType: 'json',
            success: function (data) {
                spaceContainer.append(data); // 'insertAdjacentHTML' 대신 'append' 사용
                spaceSlideInit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });

        var list = {
              'mode': 'SPACE_PLAN'
            , 'TYPE_CD': type_Cd
        };
    
        $.ajax({
              type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function (data) {
                let json = JSON.parse(data);
    
                if (json.code == 200) {
                    $("#plan1").attr('srcset', json.file);
                    $("#plan2").attr('srcset', json.file);
                    $("#plan3").attr('src', json.file);
                } else {
                    $("#plan1").attr('srcset', "");
                    $("#plan2").attr('srcset', "");
                    $("#plan3").attr('src', "");
                }
            }
            , error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });   
  }