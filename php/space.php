<?php
/**
 * 파일명 : space.php
 * 내용 : 공간 (등록, 수정, 삭제)
 * 최초작성날짜 : 2023/11/29
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/11/29     V1.0
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
            case 'INS' :
                $arrRes = row_insert();
                break;
            case 'MOD' :
                $arrRes = row_update();
                break;
            case 'DEL' :
                $arrRes = row_delete();
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }

        if ($arrRes['code'] != 200) {
            throw new Exception($arrRes['msg'], $arrRes['code']);
        }

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];

        echo json_encode($arrRtn);

    //성공
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        dieAndErrorMove($arrRtn['msg']);
    }


    /**
     * name :row_insert
     * comment : 등록
     */
    function row_insert() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            //트랜잭션
            $mysqldb->link->beginTransaction();

            $TYPE_CD = get_request_param('TYPE_CD'); // 층수구분
            $TITLE = get_request_param('TITLE'); // 제목
            $DATE_TEXT = get_request_param('DATE_TEXT'); // 기간 텍스트
            $MOBILE = get_request_param('MOBILE'); // 연락처
            $EMAIL = get_request_param('EMAIL'); // 이메일
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $page_type = get_request_param('page_type'); // 페이지 타입
            $key_val = get_request_param('key_val'); // 업로드 key값

            $seq_name = '';
 
            gfn_isValidation(301, $TYPE_CD, "층수");
            gfn_isValidation(302, $TITLE, "제목");

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $seq_name = 'SPC';

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "공간 시퀀스";
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

            $table = 'SPACE';
            $type = 'SPACE';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $key_value = '';
            $ATTACH_FILE_ID = "";

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }

                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                        $ATTACH_FILE_ID = 'ATTACH_'. $data['seq'];

                        if ($key == "files") { // 멀티 이미지
                            $key_value = $key;
                            $arrRes = json_decode(multiple_file_upload($dir, "files"), true);
                        } else { // 썸네일, 메인 이미지
                            $key_value = $key;
                            $arrRes = json_decode(one_file_upload($dir, $key), true);
                        }

                        if ($arrRes['code'] != 200) {
                            throw new Exception($arrRes['msg'], $arrRes['code']);
                        }

                        foreach ($arrRes['file'] as $key => $val) {
                            if ($key_value == "files") {
                                $idx = $key_val[$key];
                                $ATTACH_GROUP = 1;
                            }

                            gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                        }
                    }
                }
            }

            $values = array(
                  'SPACE_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TYPE_CD' => $TYPE_CD // 층수 구분
                , 'TITLE' => $TITLE // 제목
                , 'DATE_TEXT' => $DATE_TEXT // 기간텍스트
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID ?? ""// 썸네일 이미지
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "공간 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '등록되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :row_update
     * comment : 수정
     */
    function row_update() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $SPACE_SEQ = get_request_param('SEQ'); // 시퀀스
            $TYPE_CD = get_request_param('TYPE_CD'); // 층수구분
            $TITLE = get_request_param('TITLE'); // 제목
            $DATE_TEXT = get_request_param('DATE_TEXT'); // 기간 텍스트
            $MOBILE = get_request_param('MOBILE'); // 연락처
            $EMAIL = get_request_param('EMAIL'); // 이메일
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); // 호버 이미지
            $page_type = get_request_param('page_type'); // 페이지 타입
            $formData_del = get_request_param('formData_del'); // 업로드 삭제 id 값
            $key_val = get_request_param('key_val'); // 업로드 key값

            gfn_isValidation(301, $TYPE_CD, "층수");
            gfn_isValidation(302, $TITLE, "제목");

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'SPACE';
            $type = 'SPACE';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = "";

            if (!empty($formData_del) && !empty($ATTACH_FILE_ID)) {
                $formData_del = json_decode($formData_del, true);
                
                foreach ($formData_del as $val) {
                    gfn_file_upload("D", '', $ATTACH_FILE_ID, 1, $val);
                }
            }

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }
                
                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                        if (empty($ATTACH_FILE_ID)) {
                            $ATTACH_FILE_ID = 'ATTACH_'. $SPACE_SEQ;
                        }

                        if ($key == "files") { // 멀티 썸네일 이미지
                            $key_value = $key;
                            $arrRes = json_decode(multiple_file_upload($dir, "files"), true);
                        }

                        if (is_array($arrRes['file'])) {
                            foreach ($arrRes['file'] as $key => $val) {
                                if ($key_value == "files") {
                                    $idx = $key_val[$key];
                                    $ATTACH_GROUP = 1;
                                }

                                gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                            }
                        }
                    }
                }
            }

            $values = array(
                  'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TYPE_CD' => $TYPE_CD // 층수구분
                , 'TITLE' => $TITLE // 제목
                , 'DATE_TEXT' => $DATE_TEXT // 기간텍스트
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID ?? "" // 썸네일 이미지
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = "공간 수정";
            $clefResult = $mysqldb->update($table, $values, ['SPACE_SEQ' => $SPACE_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '수정되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :row_delete
     * comment : 삭제
     */
    function row_delete() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
            'code' => 500
          , 'msg' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $SPACE_SEQ = get_request_param('SEQ'); // 시퀀스

            $sql = "
                DELETE FROM SPACE
                 WHERE SPACE_SEQ = :pk";

            $name_sql = $SPACE_SEQ." 공간 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $SPACE_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '삭제되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }
?>