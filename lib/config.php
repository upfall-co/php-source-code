<?php
//ini_set('session.cookie_secure', true);
//ini_set('session.cookie_samesite', 'None');
//ini_set('session.cookie_samesite', 'Lax'); // 또는 'Strict'
session_start();

//config
$config = array();

//로컬 IP
$config['ip']['local'] = array(
      '127.0.0.1'
    , '::1'
    , ''
);

//테스트 IP
$config['ip']['test'] = array(
    ''
);

//서비스 IP
$config['ip']['service'] = array(
    '10.10.20.193'
);

//사무실 IP
$config['ip']['office'] = array(
      '127.0.0.1'
);

//IP 체크
$config['isLocal'] = in_array($_SERVER['SERVER_ADDR'], $config['ip']['local']);
$config['isTest'] = in_array($_SERVER['SERVER_ADDR'], $config['ip']['test']);
$config['isService'] = in_array($_SERVER['SERVER_ADDR'], $config['ip']['service']);
$config['isOffice'] = in_array($_SERVER['REMOTE_ADDR'], $config['ip']['office']);

//사무실 체크
if ($config['isOffice']) {
    define('ISOFFICE', true);
} else {
    define('ISOFFICE', false);
}

//관리자
define('ADM_EMAIL', '');
define('ADM_PW', '');

//계정관리 이메일
$config['Mcontact']['email']  = array(
      0 => 'mskim@clefad.co.kr'
    , 1 => 'jsb4377@clefad.co.kr'
    , 2 => 'dusrudkr@clefad.co.kr'
);


// 로드 밸런서/프록시 설정 확인 - AWS 'HTTP_X_FORWARDED_PORT'
$port = $_SERVER['HTTP_X_FORWARDED_PORT'] ?? $_SERVER['SERVER_PORT'];

//host
if ($port == 443) {
    //REAL
    $_url = "https://{$_SERVER['SERVER_NAME']}";
    define('HOST_HOME', $_url);
} else {
    if ($config['isTest']) {
        //TEST
        $_url = "http://{$_SERVER['SERVER_NAME']}:{$port}";
        define('HOST_HOME', $_url);
    } else {
        ///DEV, REAL
        $_url = "http://{$_SERVER['SERVER_NAME']}";
        define('HOST_HOME', $_url);
    }
}

// 이니시스 변수 리스트
/* 참조용
    // 부분취소인경우 필요 값
    $_SESSION['INIS']['SEQ'] = $INIS_SEQ; // 이니시스 시퀀스
    $_SESSION['INIS']['TID'] = $INIS_TID; // TID값
    $_SESSION['INIS']['MSG'] = $INIS_MSG; // 메시지 커스텀
    $_SESSION['INIS']['PRICE'] = $INIS_PRICE; // 금액 [취소요청금액, -----]
    $_SESSION['INIS']['CONFIRMPRICE'] = $INIS_CONFIRMPRICE; // 남은금액
    $_SESSION['INIS']['CURRENCY'] = $INIS_CURRENCY; // 통화 (WON, USD)
    $_SESSION['INIS']['TAX'] = $INIS_TAX; // 부과세
    $_SESSION['INIS']['TAXFREE'] = $INIS_TAXFREE; // 비과세
    $_SESSION['INIS']['CANCEL_SEQ'] = $INIS_CANCEL_SEQ; // 취소 시퀀스
    $_SESSION['INIS']['CANCEL_NAME'] = $INIS_CANCEL_NAME; // 취소자
*/

if ($config['isLocal']) {
    define('INIS_MID', 'INIpayTest'); // 이니시스 테스트
    define('INIS_SIGNKEY', ''); // 이니시스 SIGNKEY
    define('INIS_HASHKEY', ''); // 이니시스 hashkey
    define('INIS_KEY', ''); // 이니시스 테스트키
    define('INIS_IV', ''); // 이니시스 테스트 현금영수증키, 가상계좌

    $_url = "http://{$_SERVER['SERVER_NAME']}";
    define('HOST_HOME_EMAIL', $_url);
} else if ($config['isTest']) {
    define('INIS_MID', 'INIpayTest'); // 이니시스 테스트
    define('INIS_SIGNKEY', ''); // 이니시스 SIGNKEY
    define('INIS_HASHKEY', ''); // 이니시스 hashkey
    define('INIS_KEY', ''); // 이니시스 테스트키
    define('INIS_IV', ''); // 이니시스 테스트 현금영수증키, 가상계좌

    $_url = "http://{$_SERVER['SERVER_NAME']}";
    define('HOST_HOME_EMAIL', $_url);
} else if ($config['isService']) {
    define('INIS_MID', ''); // 이니시스 고객용 상점아이디
    define('INIS_SIGNKEY', ''); // 이니시스 고객용 SIGNKEY
    define('INIS_HASHKEY', ''); // 이니시스 hashkey
    define('INIS_KEY', ''); // 이니시스 고객용키
    define('INIS_IV', ''); // 이니시스 고객용 현금영수증키, 가상계좌

    define('HOST_HOME_EMAIL', HOST_HOME);
}

define("SERVERID", gethostbyname($_SERVER['SERVER_NAME']));

//캐시
define('CSSYYYYMMDD', '20221229');
define('JSYYYYMMDD', '20221229');
define('IMGYYYYMMDD', '20221229');

//업로드 경로
define('UPLOAD_DIR', '/upload');

//모드
$config['proc']['mode']     = array(
    'reg'   => '등록',
    'mod'   => '수정',
    'del'   => '삭제'
);


//카카오 디벨로퍼
define('KAKAO_DEV_REST_API_KEY', '50c80180adee96342de253e0f38aba0b');
define('KAKAO_DEV_JS_KEY', '93896bb9f8ab6eca5564c1f9f9238869');

//카카오 URL
define('KAKAO_LOGIN_OAUTH_URL', 'https://kauth.kakao.com/oauth/authorize?');
define('KAKAO_LOGIN_TOKEN_URL', 'https://kauth.kakao.com/oauth/token');
define('KAKAO_LOGIN_USER_INFO_URL', 'https://kapi.kakao.com/v2/user/me');
define('KAKAO_LOGIN_REDIRECT_URI', HOST_HOME .'/php/sns_auth/kakao_auth.php'); //카카오 로그인 Redirect URI
define('KAKAO_DELETE_REDIRECT_URI', HOST_HOME .'/php/sns_auth/kakao_delete_auth.php'); //카카오 연동해제 Redirect URI

//네이버 디벨로퍼
define('NAVER_LOGIN_CLIENT_ID', 'J9Sz3qUYvIJinx_h8GVT');
define('NAVER_LOGIN_CLIENT_SECRET', 'cjJ1ddNRo7');

//네이버 URL
define('NAVER_LOGIN_OAUTH_URL', 'https://nid.naver.com/oauth2.0/authorize?');
define('NAVER_LOGIN_TOKEN_URL', 'https://nid.naver.com/oauth2.0/token?');
define('NAVER_LOGIN_USER_INFO_URL', 'https://openapi.naver.com/v1/nid/me');
define('NAVER_LOGIN_REDIRECT_URI', HOST_HOME .'/php/sns_auth/naver_auth.php'); //네이버 로그인 Redirect URI
define('NAVER_DELETE_REDIRECT_URI', HOST_HOME .'/php/sns_auth/naver_delete_auth.php'); //네이버 로그인 Redirect URI

//구글 디벨로퍼
define('GOOGLE_LOGIN_CLIENT_ID', '');
define('GOOGLE_LOGIN_CLIENT_SECRET', '');

// 구글 URL
define('GOOGLE_LOGIN_OAUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth?');
define('GOOGLE_LOGIN_TOKEN_URL', 'https://oauth2.googleapis.com/token?');
define('GOOGLE_LOGIN_USER_INFO_URL', 'https://www.googleapis.com/oauth2/v3/userinfo');
define('GOOGLE_LOGIN_REDIRECT_URI', HOST_HOME .'/php/sns_auth/google_auth.php'); //구글 로그인 Redirect URI
define('GOOGLE_SCOPE_EMAIL', 'https://www.googleapis.com/auth/userinfo.email');
define('GOOGLE_SCOPE_PROFILE', 'https://www.googleapis.com/auth/userinfo.profile');
define('GOOGLE_SCOPE_READONLY', 'https://www.googleapis.com/auth/contacts.readonly');

// 이메일 정보
define('SMTP_EMAIL', 'piknic_cs@naver.com');
define('SMTP_PW', 'piknic_2024!');

// 비즈뿌리오
define('bizppurio_apikey', 'rznhpYMIClAp');
define('bizppurio_userid', 'piknic_glint');
define('bizppurio_password', 'glint62456372!');
define('bizppurio_senderkey', '720fd9d579f87553c57a4553ac1dee2284a08668');

// 알리고
define('aligo_apikey', '');
define('aligo_userid', '');
define('aligo_senderkey', '');

//연락처 앞자리
$config['mobile'] = array(
    '010', '016', '017', '018', '019',
);

//지역번호 앞자리
$config['area_code'] = array(
    '02', '051', '053', '032', '062', '042', '052',
    '044', '031', '033', '043', '041', '063', '061',
    '054', '055', '064', '070'
);

//세션 정리
$_de_se_seq = !empty($_SESSION['mem']['seq']) ? $_SESSION['mem']['seq'] : 0;
$_de_se_id = isset($_SESSION['mem']['id']) ? $_SESSION['mem']['id'] : '';
$_de_se_name  = isset($_SESSION['mem']['name']) ? $_SESSION['mem']['name'] : '';
$_de_se_email = isset($_SESSION['mem']['email']) ? $_SESSION['mem']['email'] : '';

define('SE_MEM_SEQ', $_de_se_seq);
define('SE_MEM_ID', $_de_se_id);
define('SE_MEM_NAME', $_de_se_name);
define('SE_MEM_EMAIL', $_de_se_email);

//로그인 체크
if (!empty($_de_se_seq)) {
    define('IS_LOGIN', 1);
} else {
    define('IS_LOGIN', 0);
}

//관리자 세션 정리
$_de_se_adm_id = isset($_SESSION['adm']['id']) ? $_SESSION['adm']['id']    : '';
$_de_se_adm_name = isset($_SESSION['adm']['name']) ? $_SESSION['adm']['name']  : '';

define('SE_ADM_ID', $_de_se_adm_id);
define('SE_ADM_NAME', $_de_se_adm_name);

define('EMAILTEMPURL', HOST_HOME_EMAIL.'/php/temp/email_temp.php');

//관리자 로그인 체크
if (!empty($_de_se_adm_id)) {
    define('ADM_IS_LOGIN',  1);
} else {
    define('ADM_IS_LOGIN',  0);
}

// ART 폴더명 변수 선언
define('artFoldName', '/collection');
define('shopFoldName', '/shop');
define('homeFoldName', '/home');

// ART 변수 선언

// 미술품 페이지
define('PAGE1', 'collection');
define('PAGEPAR1', 'page_type=collection');
define('PAGENM1', 'collection');

//숍
define('PAGE2', 'shop');
define('PAGEPAR2', 'page_type=shop');
define('PAGENM2', 'shop');

// 브랜드
define('PAGE3', 'home');
define('PAGEPAR3', 'page_type=home');
define('PAGENM3', 'home');

// 홈 구분 (서브)
define('SUB_PAGE1', 'recruit');
define('SUB_PAGE2', 'program');


//언어
$config['lang'] = array(
      'kor' => '한국어'
    , 'eng' => '영어'
);

//proc
$config['proc']['type'] = array(
      'reg' => '등록'
    , 'mod' => '수정'
    , 'del' => '삭제'
);

//요일 세팅
$config['date'] = array(
     'yyyy' => range(1900, 2100)
    , 'mm' => range(1, 12)
    , 'dd' => array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31)
    , 'week' => array(
          0 => '일'
        , 2 => '화'
        , 3 => '수'
        , 1 => '월'
        , 4 => '목'
        , 5 => '금'
        , 6 => '토'
    )
);

//사이트 설정
$config['site']['config'] = array(
    //약관
    'terms_'.PAGE1 => array(
        'privacy_statement' => '개인정보 취급방침',
        'privacy_statement2' => '서비스 이용약관',
        'privacy_statement3' => '회원탈퇴'
    ),
    'terms_'.PAGE2 => array(
        'privacy_statement' => '개인정보 취급방침',
        'privacy_statement2' => '서비스 이용약관',
        'privacy_statement3' => '회원탈퇴',
        'privacy_statement4' => '배송 안내',
        'privacy_statement5' => '교환·환불 안내'
    ),
    'terms_'.PAGE3 => array(
        'privacy_statement' => '개인정보취급방침'
    ),
);


//게시판 상태
$config['bbs']['status'] = array();
