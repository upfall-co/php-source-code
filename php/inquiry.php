<?php
/**
 * 파일명 : inquiry.php
 * 내용 : 1:1문의
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/08    V1.0
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
        $PAGE_TYPE = get_request_param('PAGE_TYPE'); // 페이지 타입

        switch ($mode) {
            case 'INS' :
                $arrRes = ufn_Inquiry_INS();
                break;
            case 'MOD' :
                $arrRes = ufn_Inquiry_MOD();
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];
        $arrRtn['url'] = $arrRes['url'];

        if ($mode == "INS" || $mode == "MOD") {
            if ($PAGE_TYPE == PAGE1) {
                dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
            } else if ($PAGE_TYPE == PAGE2) {
                if ($mode == "INS") {
                    echo json_encode($arrRtn);
                } else if ($mode == "MOD") {
                    dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
                }
            } else {
                dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
            }
        } else {
            echo json_encode($arrRtn);
        }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        dieAndErrorMove($arrRtn['msg']);
    }

    /**
     * name :ufn_Inquiry_INS
     * comment : 1:1 문의 등록
     */
    function ufn_Inquiry_INS() {
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

            $PAGE_TYPE = get_request_param('PAGE_TYPE'); // 페이지 타입
            $QUESTION_CD = get_request_param('QUESTION_CD'); // 진행 구분 AD008
            $NAME = get_request_param('inquiry_name'); // 문의자 이름
            $MOBILE = get_request_param('inquiry_tel'); // 문의자 휴대폰
            $EMAIL = get_request_param('inquiry_email'); // 문의자 이메일
            $PURCHASE_SEQ = get_request_param('PURCHASE'); // 문의자 주문번호
            $PRODUCT_TITLE = get_request_param('TITLE'); // 문의자 문의작품
            $TYPE_CD = get_request_param('TYPE_CD'); // 문의자 문의분류 COL002
            $TITLE = get_request_param('TITLE'); // 문의제목
            $CONTENT_TEXT = get_request_param('CONTENT_TEXT'); // 문의자 문의내용
            $PASSWORD = get_request_param('PASSWORD'); // 문의자 문의내용
            $key_val = get_request_param('key_val'); // 업로드 key값

            //파라미터 체크
            if ($PAGE_TYPE == PAGE1) {
                gfn_isValidation(302, $NAME, "이름");
            } else if ($PAGE_TYPE == PAGE2) {
                gfn_isValidation(301, $TYPE_CD, "유형");
            }

            gfn_isValidation(302, $PRODUCT_TITLE, "문의 제목");
            gfn_isValidation(302, $CONTENT_TEXT, "문의 내용");

            $ID = '';

            if (!empty($MOBILE)) {
                $MOBILE = str_replace('-', '', $MOBILE);
            }
            
            if (isset($_SESSION['MEMBER'])) {
                if (!empty($_SESSION['MEMBER'])) {
                    $ID = $_SESSION['MEMBER']['ID'];
                }
            }

            if (!empty($PASSWORD)) {
                $PASSWORD = gfn_getEncrypt(gfn_encrypted($PASSWORD), $_SESSION['projectkey']);
            }

            $seq_name = 'INQ';

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "문의 시퀀스";
            $clefResult = $mysqldb->get($sql, null, $name_sql);
            $data = $clefResult->getResultSet();

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'INQUIRY';
            $type = 'INQUIRY';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $key_value = '';
            $ATTACH_FILE_ID = "";

            if (is_array($_FILES)) {
                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드
                        if ($key == "ATTACH") { // file_1로 실행함 멀티 파일업로드
                            continue;
                        }

                        $ATTACH_FILE_ID = 'ATTACH_'. $data['seq'];

                        $key_value = $key;
                        $arrRes = json_decode(multiple_file_upload($dir, "file_1"), true);

                        if ($arrRes['code'] != 200) {
                            throw new Exception($arrRes['msg'], $arrRes['code']);
                        }

                        if (is_array($arrRes['file'])) {
                            if (!empty($key_val) && $key != "ATTACH" && $key == "file_1") {
                                $key_val = json_decode($key_val, true);
                            }

                            foreach ($arrRes['file'] as $key => $val) {
                                $idx = $key_val[$key];
                                $ATTACH_GROUP = 1;

                                gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $EMAIL, $ip);
                            }
                        }
                    }
                }
            }

            $values = array( 
                  'INQUIRY_SEQ' => $data['seq'] //시퀀스 값
                , 'TYPE_CD' => $TYPE_CD // 문의분류 COL002 
                , 'PAGE_TYPE' => $PAGE_TYPE // 페이지 타입
                , 'QUESTION_CD' => $QUESTION_CD // 진행 구분 AD008
                , 'ID' => $ID // 아이디 [로그인경우]
                , 'NAME' => $NAME // 문의자명
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'PURCHASE_SEQ' => $PURCHASE_SEQ // 주문번호
                , 'PRODUCT_TITLE' => $PRODUCT_TITLE // 문의작품
                , 'TITLE' => $TITLE // 문의제목
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 문의내용
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID ?? "" // 파일
                , 'PASSWORD' => $PASSWORD // 비밀번호
                , 'reg_user' => $NAME // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "문의 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '등록되었습니다.';

            if ($PAGE_TYPE == PAGE1) {
                if (isset($_SESSION['MEMBER'])) {
                    if (!empty($_SESSION['MEMBER'])) {
                        $arrRtn['url'] = artFoldName. '/mypage/inquiry.php';; // 로그인시 사용자 문의하기
                    } else {
                        $arrRtn['url'] = artFoldName. '/help/inquiry.php';; // HELP메뉴 문의하기
                    }
                } else {
                    $arrRtn['url'] = artFoldName. '/help/inquiry.php';; // 
                }
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
     * name :ufn_Inquiry_MOD
     * comment : 1:1 문의 수정
     */
    function ufn_Inquiry_MOD() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
            , 'url' => ''
        );

        try {
            global $query_string;

            //트랜잭션
            $mysqldb->link->beginTransaction();

            $m_seq = get_request_param('m_seq');
            $mp_seq = get_request_param('mp_seq');
            $M_TYPE_CD = get_request_param('M_TYPE_CD'); // 문의
            $M_NAME = get_request_param('M_NAME'); // 이름
            $M_TITLE = get_request_param('M_TITLE'); // 문의명
            $M_QUESTION_CD = get_request_param('M_QUESTION_CD'); // 답변상태
            $M_start_date = get_request_param('M_start_date'); // 시작일
            $M_end_date = get_request_param('M_end_date'); // 종료일

            $PAGE_TYPE = get_request_param('PAGE_TYPE'); // 페이지 타입
            $QUESTION_CD = get_request_param('QUESTION_CD'); // 진행 구분 AD008
            $INQUIRY_SEQ = get_request_param('SEQ'); // 1:1문의 시퀀스
            $ANSWERS_SEQ = get_request_param('ANSWERS_SEQ'); // 답변 시퀀스
            $CONTENT_TEXT = isset($_POST['editor']) ? $_POST['editor'] : ''; // 답변 내용

            $CONTENT_TEXT = trim($CONTENT_TEXT); // 답변 문의내용

            //파라미터 체크
            gfn_isValidation(301, $QUESTION_CD, "진행구분");

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            
            $table = 'INQUIRY';
            $table_AN = 'ANSWERS';

            if (empty($ANSWERS_SEQ)) {
                $seq_name = 'ANS';

                $sql = "
                    SELECT nextval('{$seq_name}') as seq";

                $name_sql = "답변 시퀀스";
                $clefResult = $mysqldb->get($sql, null, $name_sql);
                $data = $clefResult->getResultSet();

                $values = array( 
                      'INQUIRY_SEQ' => $INQUIRY_SEQ //시퀀스 값
                    , 'ANSWERS_SEQ' => $data['seq'] // 답변 시퀀스 값
                    , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                    , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                    , 'reg_ip' => $ip // 등록자 아이피
                    , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
                );
  
                $name_sql = "답변 추가";
                $clefResult = $mysqldb->insert($table_AN, $values, $name_sql);
  
                if (!$clefResult->getResult()) {
                    gfn_isValidation(501);
                }

                $values = array(
                      'QUESTION_CD' => $QUESTION_CD // 진행 구분 AD008
                    , 'ANSWERS_SEQ' => $data['seq'] // 답변 시퀀스 값
                    , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                    , 'mod_ip' => $ip // 등록자 아이피
                    , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
                );

                $name_sql = "문의 수정";
                $clefResult = $mysqldb->update($table, $values, ['INQUIRY_SEQ' => $INQUIRY_SEQ], $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }
            } else {
                $values = array( 
                      'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                    , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                    , 'mod_ip' => $ip // 등록자 아이피
                    , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
                );

                $name_sql = "답변 수정";
                $clefResult = $mysqldb->update($table_AN, $values, ['ANSWERS_SEQ' => $ANSWERS_SEQ], $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }

                $values = array(
                      'PAGE_TYPE' => $PAGE_TYPE // 페이지 타입
                    , 'QUESTION_CD' => $QUESTION_CD // 진행 구분 AD008
                    , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                    , 'mod_ip' => $ip // 등록자 아이피
                    , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
                );

                $name_sql = "문의 수정";
                $clefResult = $mysqldb->update($table, $values, ['INQUIRY_SEQ' => $INQUIRY_SEQ], $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }
            }
    
            $arrParams = array(
                  'm_seq' => $m_seq
                , 'mp_seq' => $mp_seq
                , 'page_type' => $PAGE_TYPE
                , 'TYPE_CD' => $M_TYPE_CD
                , 'NAME' => $M_NAME
                , 'TITLE' => $M_TITLE
                , 'QUESTION_CD' => $M_QUESTION_CD
                , 'start_date' => $M_start_date
                , 'end_date' => $M_end_date
            );
    
            $query_string = http_build_query($arrParams);

            //성공
            $mysqldb->link->commit();
            $arrRtn['msg'] = '수정되었습니다.';
            $arrRtn['code'] = 200;
            $arrRtn['url'] = "../adm/board/Inquiry_main.php?{$query_string}"; 

        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }

    }

?>