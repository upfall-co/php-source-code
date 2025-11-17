<?php
/**
 * 파일명 : member.php
 * 내용 : 사용자 계정관리 (등록, 수정, 삭제)
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/07    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $arrRtn = array(
          'code' => 500
        , 'msg'  => ''
        , 'mode' => ''
        , 'url' => ''
    );

    try {
        //파라미터 정리
        $mode = get_request_param('mode');

        switch ($mode) {
            case 'JOIN' :
                $arrRes = ufn_Member_Join(); // 회원가입
                break;
            case 'LOGIN' :
                $arrRes = ufn_Member_Login(); // 로그인
                break;
            case 'MOD' :
                $arrRes = ufn_Member_MOD(); // 사용자 수정
                break;
            case 'AD_MOD' :
                $arrRes = ufn_Member_AD_MOD(); // 관리자 사용자 수정
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }

                        
        if ($arrRes['code'] != 200) {
            throw new Exception($arrRes['msg'], $arrRes['code']);
        }

        if ($mode == "JOIN" || $mode == "LOGIN" ||
            $mode == "MOD" || $mode == "AD_MOD") {
    
            $arrRtn['code'] = $arrRes['code'];
            $arrRtn['msg'] = $arrRes['msg'];
            $arrRtn['url'] = $arrRes['url'];

            dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
        } else {
            echo json_encode($arrRtn);
        }

        //성공
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        dieAndErrorMove($arrRtn['msg']);
    }

    /**
     * name :ufn_Member_Join
     * comment : 사용자 회원가입
     */
    function ufn_Member_Join() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            //트랜잭션
            $mysqldb->link->beginTransaction();

            $TYPE = get_request_param('TYPE'); // 홈페이지 구분

            $ID = get_request_param('member_id'); // 계정 아이디
            $TYPE_CD = get_request_param('TYPE_CD'); // 회원구분
            $PASSWORD = get_request_param('member_pw'); // 계정 비밀번호
            $PASSWORD_CHK = get_request_param('member_pw_chk'); // 비밀번호 확인
            $NAME = get_request_param('member_name'); // 계정 이름
            $MOBILE = get_request_param('member_tel'); // 계정 휴대폰
            $EMAIL = get_request_param('member_email'); // 계정 이메일
            $ADDRESS_ZIPCODE = get_request_param('ADDRESS_ZIPCODE'); // 우편번호
            $ADDRESS = get_request_param('ADDRESS'); // 주소
            $ADDRESSDETAIL = get_request_param('ADDRESSDETAIL'); // 상세주소
            $BUSINESS_NAME = get_request_param('member_business_name'); // 사업자명
            $BUSINESS_NUMBER = get_request_param('member_business_number'); // 사업자등록번호

            //파라미터 체크
            gfn_isValidation(302, $ID, "아이디");
            gfn_isValidation(302, $PASSWORD, "비밀번호");

            if (!empty($PASSWORD)) {
                if (!validatePassword($PASSWORD, 2, 8)) {
                    gfn_isValidation(999, "", "비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을 포함하여 8자 이상으로 등록해야 합니다.");
                }
            }

            if (!empty($PASSWORD) && !empty($PASSWORD_CHK)) {
                if ($PASSWORD != $PASSWORD_CHK) {
                    gfn_isValidation(999, "", "비밀번호가 일치하지 않습니다.");
                }
            }

            gfn_isValidation(302, $NAME, "이름");
            gfn_isValidation(302, $MOBILE, "연락처");
            gfn_isValidation(302, $EMAIL, "이메일");

            if ($TYPE == "20") {
                gfn_isValidation(301, $ADDRESS_ZIPCODE, "주소");
            }

            if (empty($TYPE_CD)) {
                $TYPE_CD = "MBR";
            }

            $table = 'MEMBER_DEL';

            $arrValue = array();
            $arrValue[':ID'] = $ID;

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE ID = :ID";

            $name_sql = "탈퇴 아이디 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 0) {
                gfn_isValidation(999, "", "사용 불가능한 아이디입니다.");
            }

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'MEMBER';

            $arrValue = array();
            $arrValue[':ID'] = $ID;

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE ID = :ID";

            $name_sql = "아이디 중복 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 0) {
                gfn_isValidation(999, "", "중복된 아이디가 존재합니다.");
            }

            $MOBILE = str_replace('-', '', $MOBILE);

            $arrValue = array();
            $arrValue[':MOBILE'] = $MOBILE;
            $arrValue[':EMAIL'] = $EMAIL;

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE 1
                    AND (MOBILE = :MOBILE
                     OR EMAIL = :EMAIL)";

            $name_sql = "아이디 중복 확인(이메일 및 연락처)";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 0) {
                gfn_isValidation(999, "", "해당 연락처 및 이메일로 등록된 아이디가 존재합니다.");
            }

            $ACCESS_TOKEN_NAVER = "";
            $ACCESS_TOKEN_KAKAO = "";

            if (isset($_SESSION['SNSIFNO'])) {
                if (!empty($_SESSION['SNSIFNO'])) {
                    if (isset($_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'])) {
                        $ACCESS_TOKEN_NAVER = $_SESSION['SNSIFNO']['ACCESS_TOKEN_NAVER'];
                    }

                    if (isset($_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'])) {
                        $ACCESS_TOKEN_KAKAO = $_SESSION['SNSIFNO']['ACCESS_TOKEN_KAKAO'];
                    }
                    
                    if (!empty($ACCESS_TOKEN_NAVER)) {
                        $ACCESS_TOKEN_NAVER = gfn_getEncrypt(gfn_encrypted($ACCESS_TOKEN_NAVER), $_SESSION['projectkey']);
                    }

                    if (!empty($ACCESS_TOKEN_KAKAO)) {
                        $ACCESS_TOKEN_KAKAO = gfn_getEncrypt(gfn_encrypted($ACCESS_TOKEN_KAKAO), $_SESSION['projectkey']);
                    }
                }
            }

            $values = array( 
                  'ID' => $ID
                , 'PASSWORD' => gfn_getEncrypt(gfn_encrypted($PASSWORD), $_SESSION['projectkey'])
                , 'NAME' => $NAME
                , 'TYPE_CD' => $TYPE_CD
                , 'MOBILE' => $MOBILE
                , 'EMAIL' => $EMAIL
                , 'ACCESS_TOKEN_NAVER' => $ACCESS_TOKEN_NAVER
                , 'ACCESS_TOKEN_KAKAO' => $ACCESS_TOKEN_KAKAO
                , 'ADDRESS_ZIPCODE' => $ADDRESS_ZIPCODE
                , 'ADDRESS' => $ADDRESS
                , 'ADDRESSDETAIL' => $ADDRESSDETAIL
                , 'BUSINESS_NAME' => $BUSINESS_NAME
                , 'BUSINESS_NUMBER' => $BUSINESS_NUMBER
                , 'reg_user' => $NAME // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "피크닉 계정 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            $Info_value = array(
                  'userid' => bizppurio_userid
                , 'userpw' => bizppurio_password
                , 'apikey' => bizppurio_apikey
                , 'senderkey' => bizppurio_senderkey
                , 'tpl_code' => 'bizp_2023111513460118915336661'
            );
            
            $message_emtitle = array();
            
            $message = array(
                 "고객명" => $NAME
               , "고객이름" => $NAME
               , "고객아이디" => $ID
               , "가입일" => date('Y-m-d H:i:s')
            );
            
            $Body_value = array( 
                  'type' => 'at'
                , 'from' => '023183233'
                , 'to' => $MOBILE
                , 'emtitle' => $message_emtitle
                , 'message' => $message
                , 'button' => array()
                , 'item' => array()
                , 'link' => array()
                , 'resend' => "lms"
                , 'subject' => ""
                , 'file' => array()
            );
            
            gfn_Bizalimtalk_send($Info_value, $Body_value);

            $_SESSION['MEMBER']['ID'] = $ID;
            $_SESSION['MEMBER']['NAME'] = $NAME;

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '등록되었습니다.';

            if ($_SESSION['INFOR']['LOGIN_CHK']) {
                $arrRtn['url'] = $_SESSION['INFOR']['URL'];
            } else {
                if ($TYPE == "10") { // 컬렉션
                    $arrRtn['url'] = artFoldName. '/main.php';; // 임시
                } else if ($TYPE == "20") { // 샵
                    $arrRtn['url'] = shopFoldName. '/index.php';; // 임시
                }
            }

            unset($_SESSION['SNSIFNO']);
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_Member_Login
     * comment : 사용자 로그인
     */
    function ufn_Member_Login() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $ID = get_request_param('member_id'); // 계정 아이디
            $PASSWORD = get_request_param('member_pw'); // 계정 비밀번호
            $TYPE = get_request_param('TYPE'); // 홈페이지 구분

            $ID = trim($ID);
            $PASSWORD = trim($PASSWORD);

            //파라미터 체크
            gfn_isValidation(302, $ID, "아이디");
            gfn_isValidation(302, $PASSWORD, "비밀번호");
            
            $table = 'MEMBER';

            $arrValue = array();
            $arrValue[':ID'] = $ID;
            $arrValue[':PASSWORD'] = gfn_encrypted($PASSWORD);
            $arrValue[':key'] = $_SESSION['projectkey'];

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE ID = :ID
                    AND GETDECRYPT(PASSWORD, :key) = :PASSWORD ";

            $name_sql = "계정 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total == 0) {
                gfn_isValidation(999, "", "아이디 및 비밀번호가 일치하지 않습니다.");
            }

            $_SESSION['MEMBER']['ID'] = $ID;

            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '로그인되었습니다.';
            
            if ($_SESSION['INFOR']['LOGIN_CHK']) {
                $arrRtn['url'] = $_SESSION['INFOR']['URL'];
            } else {
                if ($TYPE == "10") { // 컬렉션
                    $arrRtn['url'] = artFoldName. '/mypage/orderhistory.php'; // 임시
                } else if ($TYPE == "20") { // 샵
                    $arrRtn['url'] = shopFoldName. '/mypage/orderhistory.php'; // 임시
                }
                
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_Member_MOD
     * comment : 사용자 정보 수정
     */
    function ufn_Member_MOD() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $page_type = get_request_param('page_type'); // 페이지 구분
            $type = get_request_param('type'); // 정보수정 , 비밀번호 수정구분
            $NAME = get_request_param('NAME'); // 계정 이름
            $MOBILE = get_request_param('member_tel'); // 계정 휴대폰
            $EMAIL = get_request_param('member_email'); // 계정 이메일
            $ADDRESS_ZIPCODE = get_request_param('ADDRESS_ZIPCODE'); // 우편번호
            $ADDRESS = get_request_param('ADDRESS'); // 주소
            $ADDRESSDETAIL = get_request_param('ADDRESSDETAIL'); // 상세주소
            $BUSINESS_NAME = get_request_param('member_business_name'); // 사업자명
            $BUSINESS_NUMBER = get_request_param('member_business_number'); // 사업자등록번호

            $PASSWORD = get_request_param('member_pw'); // 비밀번호
            $PASSWORD_CHK = get_request_param('member_pw_chk'); // 비밀번호 확인

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $values = array();
            
            if ($type == "PW") {
                if (!empty($PASSWORD) && !empty($PASSWORD_CHK)) {
                    if ($PASSWORD != $PASSWORD_CHK) {
                        gfn_isValidation(999, "", "비밀번호가 일치하지 않습니다.");
                    }

                    if (!validatePassword($PASSWORD, 2, 8)) {
                        gfn_isValidation(999, "", "비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을 포함하여 8자 이상으로 등록해야 합니다.");
                    }

                    $values['PASSWORD'] = gfn_getEncrypt(gfn_encrypted($PASSWORD), $_SESSION['projectkey']);
                }
            } else if ($type == "INFO") {
                gfn_isValidation(302, $MOBILE, "연락처");
                gfn_isValidation(302, $EMAIL, "이메일");

                if ($page_type == PAGE2) {
                    gfn_isValidation(301, $ADDRESS_ZIPCODE, "주소");
                }

                $MOBILE = str_replace('-', '', $MOBILE);

                $values['MOBILE'] = $MOBILE;
                $values['EMAIL'] = $EMAIL;
                $values['ADDRESS_ZIPCODE'] = $ADDRESS_ZIPCODE;
                $values['ADDRESS'] = $ADDRESS;
                $values['ADDRESSDETAIL'] = $ADDRESSDETAIL;
                $values['BUSINESS_NAME'] = $BUSINESS_NAME;
                $values['BUSINESS_NUMBER'] = $BUSINESS_NUMBER;
            } else {
                gfn_isValidation(999, "", "잘못된 접근입니다.");
            }

            $values['mod_user'] = $NAME;
            $values['mod_ip'] = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');
            
            $table = 'MEMBER';

            $name_sql = "계정 수정";
            $clefResult = $mysqldb->update($table, $values, ['ID' => $_SESSION['MEMBER']['ID']], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '수정되었습니다.';

            if ($page_type == PAGE1) {
                $arrRtn['url'] = artFoldName. '/mypage/edit.php';; // 미술품
            } else if ($page_type == PAGE2) {
                $arrRtn['url'] = shopFoldName. '/mypage/edit.php';; // 샵
            }
            
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :ufn_Member_AD_MOD
     * comment : 관리자용 사용자 정보 수정
     */
    function ufn_Member_AD_MOD() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $page_type = get_request_param('page_type');
            $m_seq = get_request_param('m_seq');
            $mp_seq = get_request_param('mp_seq');
            $M_TYPE_CD = get_request_param('M_TYPE_CD');
            $M_ID = get_request_param('M_ID');
            $M_NAME = get_request_param('M_NAME');
            $M_MOBILE = get_request_param('M_MOBILE');
            $M_start_date = get_request_param('M_start_date'); // 날짜
            $M_end_date = get_request_param('M_end_date'); // 날짜 
            $SEQ = get_request_param('SEQ');

            $PASSWORD = get_request_param('PASSWORD'); // 계정 이름
            $NAME = get_request_param('NAME'); // 계정 이름
            $MOBILE = get_request_param('MOBILE'); // 계정 휴대폰
            $EMAIL = get_request_param('EMAIL'); // 계정 이메일
            $ADDRESS_ZIPCODE = get_request_param('ADDRESS_ZIPCODE'); // 우편번호
            $ADDRESS = get_request_param('ADDRESS'); // 주소
            $ADDRESSDETAIL = get_request_param('ADDRESSDETAIL'); // 상세주소
            $BUSINESS_NAME = get_request_param('BUSINESS_NAME'); // 사업자명
            $BUSINESS_NUMBER = get_request_param('BUSINESS_NUMBER'); // 사업자등록번호

            
            $NAME = trim($NAME); // 계정 이름
            $MOBILE = trim($MOBILE); // 계정 휴대폰
            $EMAIL = trim($EMAIL); // 계정 이메일

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            
            $values = array();

            gfn_isValidation(302, $MOBILE, "연락처");
            gfn_isValidation(302, $EMAIL, "이메일");

            if (!empty($PASSWORD)) {
                $PASSWORD = trim($PASSWORD); // 계정 비밀번호

                if (!validatePassword($PASSWORD, 2, 8)) {
                    gfn_isValidation(999, "", "비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을 포함하여 8자 이상으로 등록해야 합니다.");
                }

                $values['PASSWORD'] = gfn_getEncrypt(gfn_encrypted($PASSWORD), $_SESSION['projectkey']);
            }

            $MOBILE = str_replace('-', '', $MOBILE);

            $values['NAME'] = $NAME;
            $values['MOBILE'] = $MOBILE;
            $values['EMAIL'] = $EMAIL;
            $values['ADDRESS_ZIPCODE'] = $ADDRESS_ZIPCODE;
            $values['ADDRESS'] = $ADDRESS;
            $values['ADDRESSDETAIL'] = $ADDRESSDETAIL;
            $values['BUSINESS_NAME'] = $BUSINESS_NAME;
            $values['BUSINESS_NUMBER'] = $BUSINESS_NUMBER;

            $values['mod_user'] = $_SESSION['adm']['name'];
            $values['mod_ip'] = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');

            $table = 'MEMBER';

            $name_sql = "계정 수정";
            $clefResult = $mysqldb->update($table, $values, ['ID' => $SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            $arrParams = array(
                  'm_seq' => $m_seq
                , 'mp_seq' => $mp_seq
                , 'page_type' => $page_type
                , 'TYPE_CD' => $M_TYPE_CD
                , 'ID' => $M_ID
                , 'NAME' => $M_NAME
                , 'MOBILE' => $M_MOBILE
                , 'start_date' => $M_start_date // 시작일
                , 'end_date' => $M_end_date // 종료일
            );
    
            $query_string = http_build_query($arrParams);

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '수정되었습니다.';
            $arrRtn['url'] = "../adm/board/memberManagement_main.php?". $query_string; // 임시
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }
 ?>
