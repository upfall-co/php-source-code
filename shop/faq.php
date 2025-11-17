<?php
    define("SUB", "sub");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/faq_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>

  <!-- 상단 헤더 -->
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

  <div id="content" class="paddingTop60">
    <main class="page_faq wrapper">
      <div class="center_sub_title">faq</div>

      <ul class="faq_tab">
        <li data-faq-category="all" class="active"><span>전체</span></li>
        <li data-faq-category="product"><span>상품</span></li>
        <li data-faq-category="shipping"><span>배송</span></li>
        <li data-faq-category="payment"><span>결제</span></li>
        <li data-faq-category="enter"><span>입점</span></li>
        <li data-faq-category="operation"><span>운영</span></li>
      </ul>

      <ul class="faq_cont">
        <?php getList_FAQ(); ?>
      </ul>

      <div class="inquiry_bt">
        <span>더 궁금하신 점이 있다면?</span>
        <a href="<?= shopFoldName ?>/inquiry.php" class="black_btn"><span>문의하기</span></a>
      </div>

    </main>
    
    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
  </div>

  <script>
    $(document).ready(function(){
      $(".faq_tab li").click(function(){
        $(this).siblings().removeClass("active");
        $(this).addClass("active");

        var tabCate = $(this).data('faq-category');

          if (tabCate == "all") { // 전체
              $(".faq_cont li").show();
          } else if (tabCate == "product") { // 상품
              $(".faq_cont li").hide();
              $(".faq_cont li[data-faq-category='product']").show();
          } else if (tabCate == "shipping") { // 배송
              $(".faq_cont li").hide();
              $(".faq_cont li[data-faq-category='shipping']").show();
          } else if (tabCate == "payment") { // 결제
              $(".faq_cont li").hide();
              $(".faq_cont li[data-faq-category='payment']").show();
          } else if (tabCate == "enter") { // 입점
              $(".faq_cont li").hide();
              $(".faq_cont li[data-faq-category='enter']").show();
          } else if (tabCate == "operation") { // 운영
              $(".faq_cont li").hide();
              $(".faq_cont li[data-faq-category='operation']").show();
          }
      });

      $(".faq_cont > li .q_row").click(function(){
        if ( $(this).hasClass("open") == false ){
          $(this).parent(".faq_cont > li").siblings().find(".q_row").removeClass("open");
          $(this).parent(".faq_cont > li").siblings().find(".a_row").stop().slideUp(200);
          $(this).addClass("open");
          $(this).next(".a_row").stop().slideDown(250).css("display", "flex");
        } else {
          $(this).removeClass("open");
          $(this).next(".a_row").stop().slideUp(200);
        }

      });
    });
  </script>

</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php" ?>