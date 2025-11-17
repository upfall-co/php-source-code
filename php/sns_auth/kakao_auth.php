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

        //로그인 체크
        if (isset($_SESSION['MEMBER'])) {
            if (!empty($_SESSION['MEMBER'])) {
                gfn_isValidation(999, "", "현재 로그인 상태입니다.");
            }
        }

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
            , 'redirect_url'  => KAKAO_LOGIN_REDIRECT_URI
            , 'code' => $kakao_code
            , 'state' => $state
        );

        //curl
        $arrRes = curl_f(KAKAO_LOGIN_TOKEN_URL, $method, $arrHeaders, $arrParams, $type);

        $http_code = json_decode($arrRes[0], true);
        $data_result = json_decode($arrRes[1], true);

        //사용자 액세스 토큰
        $access_token = isset($data_result['access_token']) ? $data_result['access_token'] : '';

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

        $arrValue = array();
        $arrValue[':ACCESS_TOKEN_KAKAO'] = gfn_encrypted($ACCESS_TOKEN_KAKAO);
        $arrValue[':key'] = $_SESSION['projectkey'];

        $sql = "
             SELECT ID
                  , NAME
               FROM {$table}
              WHERE 1
                AND GETDECRYPT(ACCESS_TOKEN_KAKAO, :key) = :ACCESS_TOKEN_KAKAO";

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
            $EMAIL = isset($kakao_account['email']) ? $kakao_account['email'] : '';
            $MOBILE = isset($kakao_account['phone_number']) ? formatKoreanPhoneNumber($kakao_account['phone_number']) : '';

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
                $EMAIL = isset($kakao_account['email']) ? $kakao_account['email'] : '';
                $msg = "해당 이메일 및 연락처로 등록된 아이디가 존재합니다. 해당 아이디와 연동 하시겠습니까?";

                $_SESSION['SNSIFNO']['ID'] = $data['ID'];
                $_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'] = "";
                $_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'] = $ACCESS_TOKEN_KAKAO;
                $_SESSION['SNSIFNO']['NAME'] = $EMAIL;
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
                $NAME = isset($kakao_account['name']) ? $kakao_account['name'] : '';
                $MOBILE = isset($kakao_account['phone_number']) ? formatKoreanPhoneNumber($kakao_account['phone_number']) : '';
                $EMAIL = isset($kakao_account['email']) ? $kakao_account['email'] : '';

                $_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'] = "";
                $_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'] = $ACCESS_TOKEN_KAKAO;
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