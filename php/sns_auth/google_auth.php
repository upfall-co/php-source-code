<?php
    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
          'code'  => 500
        , 'msg' => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //로그인 체크
        if (isset($_SESSION['MEMBER'])) {
            if (!empty($_SESSION['MEMBER'])) {
                gfn_isValidation(999, "", "현재 로그인 상태입니다.");
            }
        }

        $google_code = get_request_param('code', 'GET');
        $state = get_request_param('state', 'GET');

        //파라미터 체크
        if (empty($google_code) || empty($state)) {
            gfn_isValidation(700);
        }

        $method = 'POST';
        $type = '';
        $grant_type = 'authorization_code';

        $arrHeaders = array();

        $arrParams = array(
              'grant_type' => $grant_type
            , 'client_id' => GOOGLE_LOGIN_CLIENT_ID
            , 'client_secret' => GOOGLE_LOGIN_CLIENT_SECRET
            , 'redirect_uri'  => GOOGLE_LOGIN_REDIRECT_URI
            , 'code' => $google_code
            , 'state' => $state
        );

        $type = 'google_login';
        $query_string = http_build_query($arrParams);

        $url = GOOGLE_LOGIN_TOKEN_URL . $query_string;

        $arrRes = curl_f($url, $method, $arrHeaders, $arrParams, $type);

        $http_code = json_decode($arrRes[0], true);
        $data_result = json_decode($arrRes[1], true);
        
        //사용자 액세스 토큰
        $access_token = isset($data_result['access_token']) ? $data_result['access_token'] : '';

        if ($http_code != 200) {
            gfn_isValidation(999, "" , "구글 로그인 오류");
        }

        if (empty($access_token)) {
            gfn_isValidation(999, "" , "구글 로그인 오류");
        }

        $method = 'GET';

        $auth = "Authorization: Bearer {$access_token}";
        $arrHeaders = array($auth);
        $arrFields = array();

        $arrRes = curl_f(GOOGLE_LOGIN_USER_INFO_URL, $method, $arrHeaders, $arrFields, $type);

        $http_code = json_decode($arrRes[0], true);
        $data_result = json_decode($arrRes[1], true);

        //응답코드 체크
        if ($http_code != 200) {
            gfn_isValidation(999, "" , "구글 로그인 오류");
        }

        //_p($arrRes);
        //_p($http_code);
        //_p($data_result);

    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();
    }
?>