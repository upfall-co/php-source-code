<?php
    define("SUB", "MYPAGE");
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');
    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/header.php";

    if (isset($_SESSION['MEMBER'])) {
        if (!empty($_SESSION['MEMBER'])) {
            $_url = shopFoldName. '/index.php';

            header("Location: {$_url}");
        }
    }

    $_SESSION['SNSIFNO']['PAGE'] = shopFoldName;

    $TYPE = "LOGIN";

    include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/sns_header.php"
?>

<body>

    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/gmenu.php" ?>

    <div id="content">
        <main id="login" class="padding170">
            
            <div class="wrapper_500">
                <div class="center_sub_title">login</div>
                <form id="frm" method="post" action="/php/member.php" enctype="multipart/form-data">
                    <input type="hidden" id="mode" name="mode" value="LOGIN"/>
                    <input type="hidden" id="TYPE" name="TYPE" value="20"/>

                    <div class="login_form_wrap">
                        <ul class="form_wrap with_title">
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">아이디</div>
                                    <fieldset>
                                        <input id="member_id" name="member_id" type="text" placeholder="아이디">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                            <li>
                                <div class="form_label_ani">
                                    <div class="form_title">비밀번호</div>
                                    <fieldset>
                                        <input id="member_pw" name="member_pw" type="password" placeholder="비밀번호">
                                        <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= shopFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                    </fieldset>
                                </div>
                            </li>
                        </ul>
                        <div id="loginBtn" class="black_btn shadow_btn" onclick="onclickLogin();">로그인</div>
                    </div>
                </form>

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
                        <a href="<?= shopFoldName ?>/mypage/join.php" class="border_btn">Join</a>
                    </div>
                    <a href="<?= shopFoldName ?>/order/orderHistory_nomember.php" id="orderHistoryBtn" class="black_btn">비회원 주문조회</a>
                </div>

            </div>

        </main>

        <!-- 아이디 찾기 팝업 -->
        <?php include_once "./find_id_popup.php" ?>
        <!-- 비밀번호 찾기 팝업 -->
        <?php include_once "./find_pw_popup.php" ?>

        <!-- 하단 푸터 -->
        <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/footer.php" ?>
    </div>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/bottom.php"; ?>

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

<?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/include/sns_bottom.php" ?>