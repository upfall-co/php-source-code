<!---------------------------------------- SNS 간편로그인 -->
<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/include/sns_header.php" ?>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/include/sns_bottom.php" ?>

<div class="sns_login_wrap">
  <button type="button" class="login_naver" onclick="javascript:naverLogin();">
      <div class="logo"><img src="/img/member/login_naver.png" alt="naver" title="naver"></div>
      <p>네이버 계정으로 로그인</p>
  </button>
  <button type="button" class="login_kakao" onclick="javascript:kakaoLogin();">
      <div class="logo">
          <img src="/img/member/login_kakao.png" alt="kakao" title="kakao">
      </div>
      <p>카카오 계정으로 로그인</p>
  </button>
</div>