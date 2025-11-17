<script>
    //카카오 로그인
    function kakaoLogin() {
        let url = "<?= $kakao_oauth_url; ?>";
        let popupWidth = 600;
        let popupHeight = 800;
        let popupX = Math.ceil((window.screen.width - popupWidth) / 2);
        let popupY = Math.ceil((window.screen.height - popupHeight) / 2);

        window.open(url, "_pop", "width=" + popupWidth + ", height=" + popupHeight + ", left=" + popupX + ", top=" + popupY);
    }

    //네이버 로그인
    function naverLogin() {
        let url = "<?= $naver_oauth_url; ?>";
        let popupWidth = 600;
        let popupHeight = 800;
        let popupX = Math.ceil((window.screen.width - popupWidth) / 2);
        let popupY = Math.ceil((window.screen.height - popupHeight) / 2);

        window.open(url, "_pop", "width=" + popupWidth + ", height=" + popupHeight + ", left=" + popupX + ", top=" + popupY);
    }

    //구글 로그인
    function googleLogin() {
        let url = "<?= $google_oauth_url; ?>";
        let popupWidth = 600;
        let popupHeight = 800;
        let popupX = Math.ceil((window.screen.width - popupWidth) / 2);
        let popupY = Math.ceil((window.screen.height - popupHeight) / 2);

        window.open(url, "_pop", "width=" + popupWidth + ", height=" + popupHeight + ", left=" + popupX + ", top=" + popupY);
    }
</script>