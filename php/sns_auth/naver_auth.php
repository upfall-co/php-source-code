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
            , 'redirect_uri'  => NAVER_LOGIN_REDIRECT_URI
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

        $ACCESS_TOKEN_NAVER = isset($data_result['response']['id']) ? $data_result['response']['id'] : '';

        $table = 'MEMBER';
        $rtnUrl = "";

        $arrValue = array();
        $arrValue[':ACCESS_TOKEN_NAVER'] = gfn_encrypted($ACCESS_TOKEN_NAVER);
        $arrValue[':key'] = $_SESSION['projectkey'];

        $sql = "
             SELECT ID
                  , NAME
               FROM {$table}
              WHERE 1
                AND GETDECRYPT(ACCESS_TOKEN_NAVER, :key) = :ACCESS_TOKEN_NAVER";

        $name_sql = "연동 확인";
        $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        $FoldName = $_SESSION['SNSIFNO']['PAGE'];

        if (!empty($data)) {
            $_SESSION['MEMBER']['ID'] = $data['ID'];
            $_SESSION['MEMBER']['NAME'] = $data['NAME'];

            if ($_SESSION['INFOR']['LOGIN_CHK']) {
                $rtnUrl = $_SESSION['INFOR']['URL'];
            } else {
                $rtnUrl = $FoldName. '/mypage/orderhistory.php'; // 임시
            }

            $script = <<<script
                            <script>
                                alert('로그인되었습니다.');
                                window.close();
                                window.opener.location.replace('{$rtnUrl}');
                            </script>
                        script;
        } else {
            $EMAIL = isset($data_result['response']['email']) ? $data_result['response']['email'] : '';
            $MOBILE = isset($data_result['response']['mobile']) ? $data_result['response']['mobile'] : '';

            $where = '';
            $arrValue = array();
            $arrValue[':EMAIL'] = $EMAIL;

            if (!empty($MOBILE)) {
                $MOBILE = str_replace('-', '', $MOBILE);
                $arrValue[':MOBILE'] = $MOBILE;
                $where .= " OR MOBILE = :MOBILE";
            }

            $sql = "
                 SELECT ID
                   FROM {$table}
                  WHERE EMAIL = :EMAIL
                  {$where}";

            $name_sql = "가입 유무 확인";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $NAME = isset($data_result['response']['name']) ? $data_result['response']['name'] : '';
                $msg = "해당 이메일 및 연락처로 등록된 아이디가 존재합니다. 해당 아이디와 연동 하시겠습니까?";

                $_SESSION['SNSIFNO']['ID'] = $data['ID'];
                $_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'] = $ACCESS_TOKEN_NAVER;
                $_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'] = "";
                $_SESSION['SNSIFNO']['NAME'] = $NAME;
                $_SESSION['SNSIFNO']['CREATED'] = time();

                $script = <<<script
                                <script src='{$FoldName}/js/jquery-3.5.1.min.js'></script>
                                <script>
                                    if (confirm('{$msg}')) {
                                        var list = {
                                            'mode' : 'MEMBER_TOKEN'
                                        };
                                        $.ajax({
                                              type: "POST"
                                            , url: "/php/ajax_module.php"
                                            , data: list
                                            , success: function(data) {
                                                // 처리 성공 시 실행할 코드
                                                let json = JSON.parse(data);
                                                alert(json.msg);
                                                window.close();
                                                if (json.code == 200) {
                                                  window.opener.location.replace(json.url);
                                                }
                                            }
                                            , error: function(jqXHR, textStatus, errorThrown) {
                                                console.log(textStatus, errorThrown);
                                            }
                                        });
                                    } else {
                                        window.close();
                                    }
                                </script>
                            script;
            } else {
                $MOBILE = isset($data_result['response']['mobile']) ? $data_result['response']['mobile'] : '';
                $EMAIL = isset($data_result['response']['email']) ? $data_result['response']['email'] : '';
                $NAME = isset($data_result['response']['name']) ? $data_result['response']['name'] : '';

                $_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'] = $ACCESS_TOKEN_NAVER;
                $_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'] = "";
                $_SESSION['SNSIFNO']['NAME'] = $NAME;
                $_SESSION['SNSIFNO']['MOBILE'] = $MOBILE;
                $_SESSION['SNSIFNO']['EMAIL'] = $EMAIL;
                $_SESSION['SNSIFNO']['CREATED'] = time();

                $msg = "등록된 아이디가 존재하지않습니다. 가입을 진행하시겠습니까?";
                $rtnUrl = $FoldName. '/mypage/join.php';

                $script = <<<script
                                <script>
                                    if (confirm('{$msg}')) {
                                        window.close();
                                        window.opener.location.replace('{$rtnUrl}');
                                    } else {
                                        window.close();
                                    }
                                </script>
                            script;
            }
        }

        die($script);
    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();
        dieAndMsgWindowClose($arrRtn['msg']);
    }
?>