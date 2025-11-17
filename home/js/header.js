$(document).ready(function () {

  const headerConfig = [
    {
      "depth1": {
          "depth1Name": "exhibition",
          "anchor": "/home/sub01/exhibition.php?cate=EXHIBITION"
      },
    },
    {
      "depth1": {
          "depth1Name": "program",
          "anchor": "/home/sub02/program.php?cate=PROGRAM"
      },
      "depth2": {
        "depth2Name": ["exhibition-associated", "annual program"],
        "depth2Anchor": ["/home/sub02/program_list.php?PROGRAM_CD=EXAS", "/home/sub02/program_list.php?PROGRAM_CD=ANPG"]
      }
    },
    {
      "depth1": {
          "depth1Name": "collaboration",
          "anchor": "/home/sub03/collabo.php?cate=COLLABO"
      },
    },
    {
      "depth1": {
          "depth1Name": "SPACE",
          "anchor": "/home/sub04/space.php"
      },
    },
    {
      "depth1": {
          "depth1Name": "about",
          "anchor": "/home/sub05/location.php"
      },
    },
  ];

  const headerEl = document.querySelector("header");
  const pcDepth1Container = document.querySelector("header .header__wrap nav ul");
  const moDepth1Container = document.querySelector(".header__mo nav ul");

  headerSetting(headerConfig, pcDepth1Container, moDepth1Container);

  headerRootInter();

  // if($(window).innerWidth() > 1024){
  //   headerRootInterPc();
  // }else{
  //   headerRootInterMo();
  // }

  // headerDepthInter(10, 40);
  // headerInter();

});



// 헤더 뎁스 요소 세팅
function headerSetting(headerConfig, pcDepth1Container, moDepth1Container) {
  function createDepthNode(config, el, isPc) {
    config.forEach((obj) => {
      const depth1Element = document.createElement("li");
      depth1Element.classList.add("depth1");

      let depth1Anchor;

      if (obj.depth2 != undefined || obj.depth2 != null) {
        if (isPc) {
          depth1Anchor = document.createElement("a");
          depth1Anchor.setAttribute("href", obj.depth1.anchor);
          depth1Anchor.innerHTML = obj.depth1.depth1Name;
        } else {
          depth1Anchor = document.createElement("div");
          // depth1Anchor.innerHTML = `${obj.depth1.depth1Name}<button type="button"><img src="/img/select__arrow.svg" /></button>`;
          depth1Anchor.innerHTML = obj.depth1.depth1Name;
          depth1Anchor.setAttribute("onclick", `moDepth1Inter(this)`);
        }

        depth1Element.appendChild(depth1Anchor);

        const depth2Element = document.createElement("ul");
        depth2Element.classList.add("depth2");

        for (let i = 0; i < obj.depth2.depth2Name.length; i++) {
          const liElement = document.createElement("li");
          const anchorElement = document.createElement("a");

          anchorElement.setAttribute("href", obj.depth2.depth2Anchor[i]);
          anchorElement.innerHTML = obj.depth2.depth2Name[i];

          liElement.appendChild(anchorElement);
          depth2Element.appendChild(liElement);
        }

        depth1Element.appendChild(depth2Element);
      } else {
        depth1Anchor = document.createElement("a");
        depth1Anchor.setAttribute("href", obj.depth1.anchor);

        depth1Anchor.innerHTML = obj.depth1.depth1Name;

        depth1Element.appendChild(depth1Anchor);
      }

      el.appendChild(depth1Element);
    });
  }

  // createDepthNode(headerConfig, pcDepth1Container, true);
  createDepthNode(headerConfig, moDepth1Container, false);
}



// 모바일 헤더 인터렉션
function moDepth1Inter(e) {
  let isRes = $(window).innerWidth() > 768 ? "flex" : "grid";
  let thisDepth2 = $(e).siblings(".depth2");

  if ($(e).hasClass("active")) {
    $(".header__mo nav > ul .depth1 > div").removeClass("active");

    $(".header__mo nav > ul .depth1 .depth2").stop().slideUp(200);
  } else {
    $(".header__mo nav > ul .depth1 > div").removeClass("active");
    $(e).addClass("active");

    $(".header__mo nav > ul .depth1 .depth2").stop().slideUp(200);
    thisDepth2.stop().slideDown(200).css({
      display: isRes,
    });
  }
}



// 스크롤 탑 여부에 따른 인터렉션 산출 함수
function headerInter() {
  const headerEl = $("header");

  isTop($(window).scrollTop(), headerEl);

  $(window).on("scroll", function () {
    let scrollTop = $(this).scrollTop();
    isTop(scrollTop, headerEl);
  });

  headerEl.on("mouseenter", function () {
    if (
      !pathName.includes("member") &&
      !pathName.includes("mypage") &&
      !pathName.includes("policy_view")
    ) {
      headerOnMouseenter(this);
    }
  });

  headerEl.on("mouseleave", function () {
    if (
      !pathName.includes("member") &&
      !pathName.includes("mypage") &&
      !pathName.includes("policy_view")
    ) {
      headerOnMouseLeave(this, $(window).scrollTop());
    }
  });

}

function headerRootInter(){
  const $headerRootEl = $(".header__root_container");

  let prevScrollPos = 0;

  $(window).on("scroll", function () {
    let newScrollPos = window.scrollY;
    let direction = newScrollPos > prevScrollPos ? "down" : "up";

    if (direction == "down") {
      $headerRootEl.slideUp(300);
    } else if (direction == "up") {
      $headerRootEl.slideDown(300);
    }

    prevScrollPos = newScrollPos;
  });

}



// 헤더 뎁스 인터렉션
function headerDepthInter() {
  let depth1 = $("header .header__wrap nav ul .depth1");

  depth1.on("mouseenter", function () {
    let thisDepth2 = $(this).find(".depth2");

    if (thisDepth2.length != 0) {
      thisDepth2.addClass("active");
      thisDepth2.stop().fadeIn(400).css({
        display: "flex",
        "margin-top": 20,
      });
    }
  });

  depth1.on("mouseleave", function () {
    let thisDepth2 = $(this).find(".depth2");

    if (thisDepth2.length != 0) {
      thisDepth2.removeClass("active");
      thisDepth2.stop().fadeOut(200).css({
        "margin-top": 40,
      });
    }
  });
}



// 헤더 햄버거 버튼 인터렉션
function headerBtnInter(e) {

  const isRes = resCalc(false, false, true)

  $(e).toggleClass("active");

  if ($(e).hasClass("active") == true) {
    $("header").off("mouseenter");
    $("header").off("mouseleave");

    $("header").removeClass("top");

    $(".header__mo").addClass("active");

    $(e).find("img").attr("src", "/home/img/header__ham--close.svg")

    if(isRes){
      $("html").css({
        overflow: "hidden",
      });
    }

  } else {
    isTop($(window).scrollTop(), $("header"));

    $("header").on("mouseenter", function () {
      headerOnMouseenter(this);
    });
    $("header").on("mouseleave", function () {
      headerOnMouseLeave(this, $(window).scrollTop());
    });

    $(".header__mo").removeClass("active");

    $(e).find("img").attr("src", "/home/img/header__ham.svg")

    if(isRes){
      $("html").css({
        "overflow-y": "auto",
      });
    }

  }
}



// 스크롤 탑 여부에 따른 인터렉션 컴포넌트 (마우스 엔터)
function headerOnMouseenter(target) {
  $(target).removeClass("top");
}

// 스크롤 탑 여부에 따른 인터렉션 컴포넌트 (마우스 리브)
function headerOnMouseLeave(target, scrollTop) {
  if (scrollTop == 0) {
    $(target).addClass("top");
  } else {
    $(target).removeClass("top");
  }
}

// 스크롤 탑 여부에 따른 인터렉션 컴포넌트 (스크롤)
function isTop(scrollTop, headerEl) {
    if (
      !pathName.includes("member") &&
      !pathName.includes("mypage") &&
      !pathName.includes("policy_view")
    ) {
      if (scrollTop == 0) {
        headerEl.addClass("top");
      } else {
        headerEl.removeClass("top");
      }
    }
}