$(document).ready(function () {
  //gallTabSet();
  gallViewInter();
});

/**
 * 20221031 로직 변경으로 인한 해당 함수 주석처리
 */
/*function gallTabSet() {
  let locaName = window.location.search;
  let linkArr = ["", "?category=2", "?category=3", "?category=4"];

  for (i = 0; i < linkArr.length; i++) {
    if (locaName.includes("category=" + (i + 1)) == true) {
      $(".sub_tab li").eq(i).addClass("active");
    }
  }
}*/

function gallViewInter() {

  //-------------------- 좋아요 버튼
  $(".fav_btn").on("click", function () {
    $(this).toggleClass("active");
  });


  //-------------------- 클립보드 복사

  let locaUrl = String(location.href),
    getProtocol = locaUrl.split(":");

  $(".copy_btn").on("click", function () {

    if (getProtocol[0] === "http") {

      // 프로토콜이 http일 때
      let copyDemo = document.createElement("input");
      copyDemo.type = "text";
      copyDemo.value = locaUrl;
      document.body.appendChild(copyDemo);
      copyDemo.select();
      document.execCommand("copy");
      document.body.removeChild(copyDemo);

      alert("클립보드에 복사되었습니다");

    } else if (getProtocol[0] === "https") {

      // 프로토콜이 https일 때
      window.navigator.clipboard.writeText(locaUrl).then(function () {
        alert("클립보드에 복사되었습니다");
      });

    }

  });

  
  //-------------------- 댓글 카운팅

  let commentArr = $(".c_v_list").length;
  $(".c_count .count").text(commentArr);


  //-------------------- 댓글 textarea 높이값 조정

  $(".c_textarea textarea").on("input", function(){
    let initHei = $(this).innerHeight();
    let scrollHei = this.scrollHeight;

    if(scrollHei > initHei){
        $(this).css({
            height: scrollHei,
        });
    }

    if($(this).val() == '' || $(this).val() == null){
        $(this).css({
            height: "55px",
        });
    }

  });


  //-------------------- 댓글 사진 첨부 시 미리보기 생성

  $("#pic").on("input", function(){
    let thisFiles = this.files;
    let imgBox = document.querySelector(".added_pic figure img");

    if(thisFiles.length !== 0){

        let renderImg = new FileReader();
        renderImg.onload = function(e){
            imgBox.src = e.target.result;
        }
        renderImg.readAsDataURL(thisFiles[0]);

        $(".added_pic").css({
            display: "flex",
        })

    }

  });

  // 미리보기 삭제

  $(".added_pic_delete").on("click", function(){
    let siblings = document.querySelector("#pic");
    let imgBox = document.querySelector(".added_pic figure img");
    siblings.value = '';
    imgBox.src = '';
    $(".added_pic").css({
        display: "none",
    });
  });


  //-------------------- 파일첨부 시 파일 미리보기 생성

  $("#files").on("input", function(){
    let thisFiles = this.files;
    let fileName = this.files[0].name;

    if(thisFiles.length !== 0){
        $(".added_file .name").text(fileName);
        $(".added_file").css({
            display: "flex",
        });
    }

  });

  $(".added_f_delete").on("click", function(){
    let siblings = document.querySelector("#files");
    siblings.value = '';
    $(".added_file").css({
        display: "none",
    });
  });

}