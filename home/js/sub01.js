$(document).ready(function () {

    if(!pathName.includes("board_view")){
        const subpageCateConfigController = new subpageCateConfig();
        subpageCateConfigController.spCateSetting();
    }

    visualSlideConfig(".visual_slide__container", ".b_view__s1_slide");
  
});

class subpageCateConfig {

    constructor(){
    
        this.spTitleContainer = document.querySelector(".subpage__title_container");
        this.spCateWrap = this.spTitleContainer.querySelector(".category_wrap");
        
    }

    spCateSetting(){

        const {
            spCateWrap
        } = this
        
        const cateAll = spCateWrap.querySelectorAll("li");

        if(!getCATE2){
            cateAll[0].classList.add("active");
        }

        cateAll.forEach(
            (li) => {
                if(li.dataset.cate === getCATE2){
                    li.classList.add("active");
                }
            }
        )

    }

}