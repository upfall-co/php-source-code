<?php
    define("SUB", "sub");
    include_once $_SERVER['DOCUMENT_ROOT'] . '/php/shop_index_code.php';

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";
?>

<body>

  <!-- 상단 헤더 -->
  <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

  <div id="content" class="paddingTop60 footer_100">
    <main class="page_contact wrapper">
      <ul class="contact_img">
        <li class="scroll_motion curtain"><img src="<?= shopFoldName ?>/img/contact1.jpg"></li>
        <li class="scroll_motion curtain delay1"><img src="<?= shopFoldName ?>/img/contact2.jpg"></li>
        <li class="scroll_motion curtain delay2"><img src="<?= shopFoldName ?>/img/contact3.jpg"></li>
      </ul>
    
      <div class="contact_cont">
        <div class="cont_lt">
          <div class="top">
            <div class="desc">
              <p>
                피크닉 전시와 연계된 테마로 꾸려지는 셀렉트 숍이자 피크닉에서 직접 디자인하고 <br>선별한 제품을 만나 볼 수 있는 브랜드 숍입니다.
              </p>
              <p>특별한 큐레이션을 통해 다양한 관점을 제안합니다</p>
            </div>
          </div>
          <div class="bt">
            <ul class="contact_info">
              <li>
                <div class="lt">전화번호</div>
                <div class="rt"><a href="tel:02-6245-6372">02-6245-6372</a></div>
              </li>
              <li>
                <div class="lt">이메일</div>
                <div class="rt"><a href="mailto:shop@piknic.kr">shop@piknic.kr</a></div>
              </li>
              <li>
                <div class="lt">운영시간</div>
                <div class="rt">
                  <p><span>화 - 일</span>10:00 ~ 18:30</p>
                  <p><span>월</span>휴점</p>
                </div>
              </li>
            </ul>

            <ul class="contact_info border_top">
              <li>
                <div class="lt">주소</div>
                <div class="rt">
                  <ul class="address_list">
                    <li>
                      <p>서울특별시 중구 퇴계로 6가길 30</p>
                      <div>
                        <a href="https://map.naver.com/p/entry/place/1017034682?placePath=%252Fhome%253Fentry%253Dpll&searchType=place&lng=126.9780778&lat=37.5570119&c=15.00,0,0,0,dh" target="_blank">naver</a>
                        <a href="https://kko.to/u49hDboTGL" target="_blank">kakao</a>
                      </div>
                    </li>
                  </ul>
                </div>
              </li>
              <li>
                <div class="lt">운영</div>
                <div class="rt">10:00⎯18:30 TUE⎯SUN | MON OFF</div>
              </li>
            </ul>
          </div>
        </div>
        <div class="cont_rt">
          <div id="contactMap">
           <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d790.7301061176045!2d126.97746546964748!3d37.55693856141073!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357ca3c24784bd27%3A0xd58c4f0cfd7644a7!2z7Lm07Y6YIO2UvO2BrOuLiQ!5e0!3m2!1sko!2skr!4v1705640875521!5m2!1sko!2skr" width="650" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </main>
    
    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
  </div>

</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php" ?>