<?php
    define("SUB", "MYPAGE");
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');
    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";

    if (isset($_SESSION['MEMBER'])) {
        if (!empty($_SESSION['MEMBER'])) {
            $_url = artFoldName. '/main.php';

            header("Location: {$_url}");
        }
    }

    $_SESSION['SNSIFNO']['PAGE'] = artFoldName;

    $TYPE = "LOGIN";

    include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/sns_header.php"
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="login">
        <div class="sub_title eng">Login</div>

        <div class="wrapper_500">
            <form id="frm" method="post" action="/php/member.php" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="LOGIN"/>
                <input type="hidden" id="TYPE" name="TYPE" value="10"/>
                
                <ul class="form_wrap">
                    <li>
                        <div class="form_label_ani">
                            <input id="member_id" name="member_id" type="text" placeholder="아이디">
                            <label title="Id">아이디</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                    <li>
                        <div class="form_label_ani">
                            <input id="member_pw" name="member_pw" type="password" placeholder="비밀번호">
                            <label title="Pw">비밀번호</label>
                            <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                        </div>
                    </li>
                </ul>
            </form>

            <!-- 임시 마이페이지 링크 넣어둠 -->
            <div id="loginBtn" class="black_btn shadow_btn" onclick="onclickLogin();">로그인</div>

            <ul class="find_wrap">
                <li onclick="onFindId();">아이디 찾기</li>
                <li onclick="onFindPw();">비밀번호 찾기</li>
            </ul>

            <div class="social_wrap">

                <a href="javascript:void(0)" class="naver" onclick="javascript:naverLogin();">
                    <img src="/img/naver_logo.svg" alt="naver" title="naver" />
                    <span>네이버 계정으로 로그인</span>
                </a>

                <a href="javascript:void(0)" class="kakao" onclick="javascript:kakaoLogin();">
                    <img src="/img/kakao_logo.svg" alt="kakao" title="kakao" />
                    <span>카카오 계정으로 로그인</span>
                </a>

            </div>

            <div class="loing_bt_wrap">
                <div class="join_row">
                    <p>아직 회원이 아니신가요?</p>
                    <a href="<?= artFoldName ?>/mypage/join.php" class="border_btn">간편 회원가입</a>
                </div>
                <a href="<?= artFoldName ?>/order/orderHistory_nomember.php" id="orderHistoryBtn" class="border_btn">비회원 주문조회</a>
            </div>

        </div>

    </main>

    <!-- 아이디 찾기 팝업 -->
    <?php include_once "./find_id_popup.php" ?>
    <!-- 비밀번호 찾기 팝업 -->
    <?php include_once "./find_pw_popup.php" ?>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>

</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script>
    function onclickLogin() {
        if ($("#member_id" ).val() === "") {
            alert("아이디를 입력해주세요.");
            $("#member_id").focus();
            return false
        }

        if ($("#member_pw" ).val() === "") {
            alert("비밀번호를 입력해주세요.");
            $("#member_pw").focus();
            return false
        }

        $("#frm").submit();
    }
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/sns_bottom.php" ?>