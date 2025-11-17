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

        //파라미터 정리
        $naver_code = get_request_param('code', 'GET');
        $state = get_request_param('state', 'GET');

        //파라미터 체크
        if (empty($naver_code) || empty($state)) {
            gfn_isValidation(700);
        }

        //변수 정리
        $r_url = '';

        //네이버 로그인 토큰 받기
        $method = 'GET';
        $type = '';
        $grant_type = 'authorization_code';

        $arrHeaders = array();
        $arrParams = array(
              'grant_type' => $grant_type
            , 'client_id' => NAVER_LOGIN_CLIENT_ID
            , 'client_secret' => NAVER_LOGIN_CLIENT_SECRET
            , 'redirect_uri'  => NAVER_DELETE_REDIRECT_URI
            , 'code' => $naver_code
            , 'state' => $state
        );

        $type = 'naver_login';
        $query_string = http_build_query($arrParams);
        $url = NAVER_LOGIN_TOKEN_URL . $query_string;

        $arrRes = curl_f($url, $method, $arrHeaders, $arrParams, $type);

        $http_code = json_decode($arrRes[0], true);
        $data_result = json_decode($arrRes[1], true);

        //사용자 액세스 토큰
        $access_token = isset($data_result['access_token']) ? $data_result['access_token'] : '';

        if ($http_code != 200) {
            gfn_isValidation(999, "" , "네이버 로그인 오류");
        }
        
        //액세스 토큰 체크
        if (empty($access_token)) {
            gfn_isValidation(999, "" , "네이버 로그인 오류");
        }

        //정보 조회
        $url = NAVER_LOGIN_USER_INFO_URL;
        $method = 'GET';
        $type = '';
        $auth = "Authorization: Bearer {$access_token}";
        $arrHeaders = array($auth);
        $arrFields = array();

        $arrRes = curl_f($url, $method, $arrHeaders, $arrFields, $type);

        $http_code = json_decode($arrRes[0], true);
        $data_result = json_decode($arrRes[1], true);

        //응답코드 체크
        if ($http_code != 200) {
            gfn_isValidation(999, "" , "네이버 로그인 오류");
        }

        //사용자 토큰 정보
        $ACCESS_TOKEN_NAVER = isset($data_result['response']['id']) ? $data_result['response']['id'] : '';

        $table = 'MEMBER';
        $rtnUrl = "";
        $USER_NAVER_TOKEN = "";

        $arrValue = array();
        $USER_NAVER_TOKEN = gfn_encrypted($ACCESS_TOKEN_NAVER);

        //사용자 토큰 비교
        $arrValue = array();
        $arrValue[':ID'] = $_SESSION['MEMBER']['ID'];
        $arrValue[':key'] = $_SESSION['projectkey'];

        $sql = "
             SELECT GETDECRYPT(ACCESS_TOKEN_NAVER, :key) AS ACCESS_TOKEN_NAVER
               FROM MEMBER
              WHERE ID = :ID";

        $name_sql = "네이버 토큰 확인";
        $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        $ACCESS_TOKEN_NAVER_NM = _check_var($data['ACCESS_TOKEN_NAVER']);

        if ($ACCESS_TOKEN_NAVER_NM != $USER_NAVER_TOKEN) {
            $back_url = "/index.php";
            $msg = "등록된 NAVER계정과 현재 접속된 NAVER계정의 토큰이 다릅니다";

            $script = <<<script
                            <script>
                                if (confirm('{$msg}')) {
                                    window.opener.location.reload();
                                    window.close();
                                } else {
                                    window.close();
                                }
                            </script>
                        script;

            die($script);
        } else {
            $TokentReturn = gfn_TOKEN_DELETE("NAVER", $access_token);

            if ($TokentReturn['access_token'] == $access_token) {
                $code = 200;
                $msg = '네이버 토큰 연동이 해제되었습니다.';

                $values['ACCESS_TOKEN_NAVER'] = "";

                $name_sql = "토큰 해제 및 토큰 값 수정";
                $clefResult = $mysqldb->update($table, $values, ['ID' => $_SESSION['MEMBER']['ID']], $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }

                $mysqldb->link->commit();

                $script = <<<script
                                <script>
                                    if (confirm('{$msg}')) {
                                        window.opener.location.reload();
                                        window.close();
                                    } else {
                                        window.close();
                                    }
                                </script>
                            script;

                die($script); 
            } else {
                $msg = '네이버 토큰 연동해제에 실패했습니다. \n(재로그인 또는 관리자에게 문의해 주시기 바랍니다)';

                $script = <<<script
                                <script>
                                    if (confirm('{$msg}')) {
                                        window.opener.location.reload();
                                        window.close();
                                    } else {
                                        window.close();
                                    }
                                </script>
                            script;

                die($script);
            }
        }

        $arrRtn['code'] = $code;
        $arrRtn['msg'] = $msg;
    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();
        dieAndMsgWindowClose($arrRtn['msg']);
    }
?>