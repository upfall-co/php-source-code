<div id="agreePop2" class="popup find_popup">
  <div class="x_btn" onclick="popClose();"><img src="<?= artFoldName ?>/img/pop_x_btn.png" alt="닫기"></div>
  <div class="find_txt">
    <div class="big">개인정보 취급방침</div>
    <div class="sm">
      <?=$terms['privacy_statement'];?>
    </div>
  </div>

</div>
<div class="popup_bg"></div>

<script>
    function onAgreePop2() {
        $(".popup_bg").stop().fadeIn();
        $("#agreePop2").stop().fadeIn();
        $('html, body').addClass("noScroll");
    }
    function popClose() {
        $(".popup_bg").stop().fadeOut();
        $(".popup").stop().fadeOut();
        $('html, body').removeClass("noScroll");
    }
</script>