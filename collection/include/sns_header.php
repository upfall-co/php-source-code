<?php
    //카카오 로그인
    $arrParams = array(
          'client_id' => KAKAO_DEV_REST_API_KEY
        , 'response_type' => 'code'
        , 'state' => md5(microtime() . mt_rand())
    );

    if ($TYPE == "DELETE") {
      $arrParams['redirect_uri'] = KAKAO_DELETE_REDIRECT_URI;
    } else if ($TYPE == "LOGIN") {
      $arrParams['redirect_uri'] = KAKAO_LOGIN_REDIRECT_URI;
    }

    $query_string = http_build_query($arrParams);
    $kakao_oauth_url = KAKAO_LOGIN_OAUTH_URL . $query_string;

    //네이버 로그인
    $arrParams = array(
          'client_id' => NAVER_LOGIN_CLIENT_ID
        , 'response_type' => 'code'
        , 'state' => md5(microtime() . mt_rand())
    );

    if ($TYPE == "DELETE") {
      $arrParams['redirect_uri'] = NAVER_DELETE_REDIRECT_URI;
    } else if ($TYPE == "LOGIN") {
      $arrParams['redirect_uri'] = NAVER_LOGIN_REDIRECT_URI;
    }

    $query_string = http_build_query($arrParams);
    $naver_oauth_url  = NAVER_LOGIN_OAUTH_URL . $query_string;

    //구글 로그인
    $arrParams = array(
          'client_id' => GOOGLE_LOGIN_CLIENT_ID
        , 'redirect_uri' => GOOGLE_LOGIN_REDIRECT_URI
        , 'response_type' => 'code'
        , 'scope' => GOOGLE_SCOPE_EMAIL. ' '. GOOGLE_SCOPE_PROFILE. ' '. GOOGLE_SCOPE_READONLY
        , 'state' => md5(microtime() . mt_rand())
    );

    $query_string = http_build_query($arrParams);
    $google_oauth_url  = GOOGLE_LOGIN_OAUTH_URL . $query_string;
?>