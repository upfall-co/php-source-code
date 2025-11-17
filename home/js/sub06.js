$(document).ready(function () {

    // const contConfigController = new contConfig();

    // if(pathName.includes("contact")){
    //     contConfigController.contContentsSetting();
    // }
  
    recruitToInter();
});

class contConfig{

    constructor() {
        
        this.contS1ContentsContainer = $(".cont__s1_contents_container");
        this.contS1Data = [
            {
                anchor: `/home/sub01/exhibition.php?cate=EXHIBITION`,
                title: `전시`,
                subtitle: `exhibition`,
                mailAddr: `cs@piknic.kr`,
                telNum: `02 6245 6372`,
                time: `10:00 ⎯ 18:30`,
                date: `TUE ⎯ SUN`,
            },
            {
                anchor: `/home/sub04/space.php?cate=PROGRAM`,
                title: `공간`,
                subtitle: `space`,
                mailAddr: `info@piknic.kr`,
                telNum: `02 6245 6372`,
                time: `10:00 ⎯ 18:00`,
                date: `MON ⎯ FRI`,
            },
            {
                anchor: `/home/sub03/collabo.php?cate=COLLABO`,
                title: `제휴 협력`,
                subtitle: `cooperate`,
                mailAddr: `pr@piknic.kr`,
                telNum: `02 6245 6372`,
                time: `10:00 ⎯ 18:00`,
                date: `MON ⎯ FRI`,
            },
            {
                anchor: `/home/sub06/recruit.php`,
                title: `채용`,
                subtitle: `recruit`,
                mailAddr: `recruit@piknic.kr`,
                telNum: `02 6245 6372`,
                time: `10:00 ⎯ 18:00`,
                date: `MON ⎯ FRI`,
            }
        ]

    }

    contContentsSetting(){

        const {
            contS1ContentsContainer,
            contS1Data,
        } = this;

        const HTMLStrArr = [];

        contS1Data.forEach(
          (data) => {

            const HTMLStr = `<div class="cont__s1_contents_wrap">

                              <a href="${data.anchor}" class="title_wrap">
                                <p class="title">${data.title}</p>
                                <p class="subtitle">${data.subtitle}</p>
                              </a>

                              <div class="info_container">

                                <ul class="info_wrap">
                                  <li>
                                    <a href="mailto:${data.mailAddr}">${data.mailAddr}</a>
                                  </li>
                                  <li class="separator">|</li>
                                  <li>
                                    <a href="tel:${data.telNum}">${data.telNum}</a>
                                  </li>
                                </ul>

                                <ul class="date_wrap">
                                  <li>${data.time}</li>
                                  <li>${data.date}</li>
                                </ul>

                              </div>

                            </div>`;
    
            HTMLStrArr.push(HTMLStr);
    
          }
        );
    
        contS1ContentsContainer.append(HTMLStrArr.join(""));

    }

}

// 업종
function recruitToInter(){
    const recruitArr = $(".recr__s1_contents_container li");
    const recruitPcContainer = $(".recr__s1_depth2_container .recr__s1_depth2");
    const recruitPcContainer2 = $(".recr__s1_depth3_container .recr__s1_depth3");

    const $recruitDepth1MoSelect = $(".recr__s1_depth1--mo select");

    const $callAjax = (listData) => {

        $.ajax({
            url: '/php/ajax_module.php',
            type: 'POST',
            data: listData,
            dataType: 'json',
            success: function (data) {
                if (data.length !== 0) {
                    $.each(data, function (key, value) {
                        const seq = value.CATEGORY2_SEQ; 
                        const title = value.TITLE; 
                        const subTitle = value.SUB_TITLE;
                        const contentText = value.CONTENT_TEXT;

                        $(".recr__s1_depth2_container").addClass("recr__s1_depth_container_style");
                
                        // li 태그 추가
                        recruitPcContainer.append(`
                            <li data-seq=${seq}>
                                <p class="title">${title}</p>
                                <p class="contents">${subTitle}</p>
                            </li>
                        `);
                    });
                    recruitDeatils();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });

    }
    
    recruitArr.on("click", function(){
        recruitArr.each(
            (idx, el) => {

                $(el).removeClass("active");

                if(idx === $(this).index()){
                    $(this).addClass("active");
                }

            }
        );

        seq = $(this).data('seq');
        type_Cd = $(this).data('type');

        var list = {
              'mode': 'RECRUIT'
            , 'SEQ': seq
        };

        $callAjax(list);

        recruitPcContainer.empty();
        recruitPcContainer2.empty();

    });
    
    $recruitDepth1MoSelect.on("change", function(){

        seq = $(this).val();
        type_Cd = $(this).data('type');

        var list = {
              'mode': 'RECRUIT'
            , 'SEQ': seq
        };

        $callAjax(list);

        recruitPcContainer.empty();
        recruitPcContainer2.empty();

    });
    
}



// 세부업종
function recruitDeatils(){
    const recruitArr2 = $(".recr__s1_depth2_container .recr__s1_depth2 li");
    const recruitPcContainer = $(".recr__s1_depth3_container .recr__s1_depth3");
    
    recruitArr2.on("click", function(){
        seq = $(this).data('seq');
        type_Cd = $(this).data('type');

        var list = {
              'mode': 'RECRUIT_DETAIL'
            , 'SEQ': seq
        };

        recruitPcContainer.empty();

        $.ajax({
            url: '/php/ajax_module.php',
            type: 'POST',
            data: list,
            dataType: 'json',
            success: function (data) {
                if (data.length !== 0) {
                        const contentText = data.CONTENT_TEXT;

                        $(".recr__s1_depth3_container").addClass("recr__s1_depth_container_style");

                        // li 태그 추가
                        recruitPcContainer.append(`
                        <li>
                            ${contentText}
                        </li>
                        `);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });

    });  
}