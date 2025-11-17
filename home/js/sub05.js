$(document).ready(function () {

    if(pathName.includes("notice")){
        const noticeConfigController = new noticeConfig();
        noticeConfigController.noticeContentsBorderSetting();
    }

    if(pathName.includes("about")){
        const aboutConfigController = new aboutConfig();
        aboutConfigController.slideInit();
    }
    
});

class noticeConfig{

    constructor() {
        this.noticeS1ContentsContainer = document.querySelector(".noti__s1_contents_container");
        this.noticeS1ContentsAll = this.noticeS1ContentsContainer.querySelectorAll(".noti__s1_contents");
    }

    noticeContentsBorderSetting(){
        
        const {
            noticeS1ContentsAll
        } = this;

        const isRes = resCalc(true, true, false);

        noticeS1ContentsAll.forEach(
            (el, idx) => {

                if(isRes){

                    // console.log(noticeS1ContentsAll.length)

                    if(noticeS1ContentsAll.length %2 !== 0 ){

                        if(
                            idx === (noticeS1ContentsAll.length - 2) ||
                            idx === (noticeS1ContentsAll.length - 3)
                        ){
                            el.classList.add("nb");
                            el.classList.add("last");
                        }

                    }else{

                        if(
                            idx === (noticeS1ContentsAll.length - 2)
                        ){
                            el.classList.add("nb");
                        }

                    }
    
                    if(idx%2 !== 0){
                        el.classList.add("na");
                    }

                }
                


            }
        )

    }

}

class aboutConfig{

    constructor() {
        this.aboutS1Slide = document.querySelector(".about__s1_slide");
    }

    slideInit(){

        const {
            aboutS1Slide
        } = this;

        const slide = new Swiper(aboutS1Slide, {
            slidesPerView: 1,
            loop: true,
    
            navigation: {
              nextEl: ".about__s1_nav--next",
              prevEl: ".about__s1_nav--prev",
            },
          });

    }

}