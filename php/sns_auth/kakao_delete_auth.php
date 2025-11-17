<?php
    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500,
        'msg' => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $kakao_code = get_request_param('code', 'GET');
        $state = get_request_param('state', 'GET');

        //파라미터 체크
        if (empty($kakao_code) || empty($state)) {
            gfn_isValidation(700);
        }

        //변수 정리
        $r_url = '';

        //카카오 로그인 토큰 받기
        $method = 'POST';
        $type = '';
        $content_type = 'application/x-www-form-urlencoded;charset=utf-8';
        $grant_type = 'authorization_code';

        $arrHeaders = array($content_type);
        $arrParams = array(
              'grant_type' => $grant_type
            , 'client_id' => KAKAO_DEV_REST_API_KEY
            , 'redirect_url'  => KAKAO_DELETE_REDIRECT_URI
            , 'code' => $kakao_code
            , 'state' => $state
        );

        //curl
        $arrRes = curl_f(KAKAO_LOGIN_TOKEN_URL, $method, $arrHeaders, $arrParams, $type);

        $http_code = json_decode($arrRes[0], true);
        $data_result = json_decode($arrRes[1], true);

        //사용자 액세스 토큰
        $access_token = isset($data_result['access_token']) ? $data_result['access_token'] : '';

        if (!empty($access_token)) {
            $_SESSION['access_token'] = $access_token;
        }

        if ($http_code != 200) {
            gfn_isValidation(999, "" , "카카오 로그인 오류");
        }

        if (empty($access_token)) {
            gfn_isValidation(999, "" , "카카오 로그인 오류");
        }

        //카카오 사용자 정보 가져오기
        $method = 'POST';
        $type = '';
        $auth = 'Authorization: Bearer '. $access_token;
        $arrHeaders = array($auth);
        $arrParams = array();

        //curl
        $arrRes = curl_f(KAKAO_LOGIN_USER_INFO_URL, $method, $arrHeaders, $arrParams, $type);

        $http_code = json_decode($arrRes[0], true);
        $data_result = json_decode($arrRes[1], true);
        $kakao_account = $data_result['kakao_account'];

        //응답코드 체크
        if ($http_code != 200) {
            gfn_isValidation(999, "" , "카카오 로그인 오류");
        }

        //변수 정리
        $ACCESS_TOKEN_KAKAO = !empty($data_result['id']) ? $data_result['id'] : 0;

        $table = 'MEMBER';
        $rtnUrl = "";
        $USER_KAKAO_TOKEN = "";

        $USER_KAKAO_TOKEN = gfn_encrypted($ACCESS_TOKEN_KAKAO);

        //사용자 토큰 비교
        $arrValue = array();
        $arrValue[':ID'] = $_SESSION['MEMBER']['ID'];
        $arrValue[':key'] = $_SESSION['projectkey'];

        $sql = "
             SELECT GETDECRYPT(ACCESS_TOKEN_KAKAO, :key) AS ACCESS_TOKEN_KAKAO
               FROM MEMBER
              WHERE ID = :ID";

        $name_sql = "카카오 토큰 확인";
        $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        $ACCESS_TOKEN_KAKAO_NM = _check_var($data['ACCESS_TOKEN_KAKAO']);

        if ($ACCESS_TOKEN_KAKAO_NM != $USER_KAKAO_TOKEN) {
            $back_url = "/index.php";
            $msg = "등록된 KAKAO계정과 현재 접속된 KAKAO계정의 토큰이 다릅니다";

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
            $TokentReturn = gfn_TOKEN_DELETE("KAKAO", $access_token);

            if (!isset($TokentReturn['id']) && isset($TokentReturn['code'])) {
                $msg = '에러코드('.$TokentReturn['code'].') 카카오 토큰 연동해제에 실패했습니다. \n(재로그인 또는 관리자에게 문의해 주시기 바랍니다)';

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
                $code = 200;
                $msg = '카카오 토큰 연동이 해제되었습니다.';

                $values['ACCESS_TOKEN_KAKAO'] = "";

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
            }
        }
    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();
        dieAndMsgWindowClose($arrRtn['msg']);
    }

?>