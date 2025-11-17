<?php
/**
 * 파일명 : index_image.php
 * 내용 : 이미지 (등록, 수정, 삭제)
 * 최초작성날짜 : 2023/08/30
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/30     V1.0
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

        $m_seq = get_request_param('m_seq');
        $mp_seq = get_request_param('mp_seq');
        $page_type = get_request_param('page_type');
        $M_TITLE = get_request_param('M_TITLE'); // 시리즈
        $M_MAIN_YN = get_request_param('M_MAIN_YN'); // 노출여부

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];
        $arrRtn['url'] = "../adm/board/index_image_main.php?{$query_string}"; 

        dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
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

            $TITLE = get_request_param('TITLE'); // 메인베너
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 서브명
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $VIDEO_YN = get_request_param('VIDEO_YN'); // 영상여부
            $LINK_URL = get_request_param('LINK_URL'); // 외부링크
            $SDATE = get_request_param('SDATE'); // 시작일
            $EDATE = get_request_param('EDATE'); // 종료일
            $page_type = get_request_param('page_type'); // 페이지 타입

            if ($page_type == PAGE1) {
                gfn_isValidation(302, $TITLE, "제목");
                gfn_isValidation(302, $LINK_URL, "링크");
            }
 
            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($VIDEO_YN)){
                $VIDEO_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            if (empty($SDATE)){
                $SDATE = null;
            }

            if (empty($EDATE)){
                $EDATE = null;
            }

            $seq_name = 'IMG';

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "이미지 시퀀스";
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

            $table = 'IMAGE';
            $type = 'MAIN';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = '';

            $CATEGORY2_SEQ = $data['seq'];

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }

                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드
                        $ATTACH_FILE_ID = 'ATTACH_'. $data['seq'];

                        $key_value = $key;
                        $arrRes = json_decode(one_file_upload($dir, $key), true);

                        if ($arrRes['code'] != 200) {
                            throw new Exception($arrRes['msg'], $arrRes['code']);
                        }

                        foreach ($arrRes['file'] as $key => $val) {
                            if ($key_value == "ATTACH") {
                                $idx = $count;
                                $ATTACH_GROUP = 2;
                            } else if ($key_value == "ATTACH2") {
                                $idx = $count;
                                $ATTACH_GROUP = 4;
                            } else if ($key_value == "ATTACH3") {
                                $idx = $count;
                                $ATTACH_GROUP = 5;
                            }  else if ($key_value == "ATTACH7") {
                                $idx = $count;
                                $ATTACH_GROUP = 7;
                            } 

                            gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                        }
                    }
                }
            }

            $values = array(
                  'IMAGE_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TITLE' => $TITLE // 제목
                , 'SUB_TITLE' => $SUB_TITLE // 서브명
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'VIDEO_YN' => $VIDEO_YN // 영상여부
                , 'LINK_URL' => $LINK_URL // 외부링크
                , 'SDATE' => $SDATE // 시작일
                , 'EDATE' => $EDATE // 종료일
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 호버 이미지
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "이미지 추가";
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

            $page_type = get_request_param('page_type'); // 페이지 타입
            $IMAGE_SEQ = get_request_param('SEQ'); // 시퀀스
            $TITLE = get_request_param('TITLE'); // 제목
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 서브제목
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $VIDEO_YN = get_request_param('VIDEO_YN'); // 영상여부
            $LINK_URL = get_request_param('LINK_URL'); // 외부링크
            $SDATE = get_request_param('SDATE'); // 시작일
            $EDATE = get_request_param('EDATE'); // 종료일
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); // 호버 이미지

            if ($page_type == PAGE1) {
                gfn_isValidation(302, $TITLE, "제목");
                gfn_isValidation(302, $LINK_URL, "링크");
            }

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($VIDEO_YN)){
                $VIDEO_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            if (empty($SDATE)){
                $SDATE = null;
            }

            if (empty($EDATE)){
                $EDATE = null;
            }

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            
            $table = 'IMAGE';
            $type = 'MAIN';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = "";

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }
                
                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                        $key_value = $key;
                        $arrRes = json_decode(one_file_upload($dir, $key), true);

                        if ($arrRes['code'] != 200) {
                            throw new Exception($arrRes['msg'], $arrRes['code']);
                        }

                        if (is_array($arrRes['file'])) {
                            if ($key_value == "ATTACH") {
                                $idx = $count;
                                $ATTACH_GROUP = 2;
                            } else if ($key_value == "ATTACH2") {
                                $idx = $count;
                                $ATTACH_GROUP = 4;
                            } else if ($key_value == "ATTACH3") {
                                $idx = $count;
                                $ATTACH_GROUP = 5;
                            } else if ($key_value == "ATTACH7") {
                                $idx = $count;
                                $ATTACH_GROUP = 7;
                            } 

                            foreach ($arrRes['file'] as $key => $val) {
                                $FIND_FILE = gfn_file_upload("T", '', $ATTACH_FILE_ID, $ATTACH_GROUP);

                                if ($FIND_FILE > 0) {
                                    gfn_file_upload("U", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                } else {
                                    gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                }
                            }
                        }
                    }
                }
            }

            $values = array(
                  'TITLE' => $TITLE // 제목
                , 'SUB_TITLE' => $SUB_TITLE // 서브명
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'VIDEO_YN' => $VIDEO_YN // 영상여부
                , 'LINK_URL' => $LINK_URL
                , 'SDATE' => $SDATE // 시작일
                , 'EDATE' => $EDATE // 종료일
                , 'ORDER_NUMBER' => $ORDER_NUMBER
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 호버 이미지
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = "이미지 수정";
            $clefResult = $mysqldb->update($table, $values, ['IMAGE_SEQ' => $IMAGE_SEQ], $name_sql);

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

            $IMAGE_SEQ = get_request_param('SEQ'); // 시퀀스

            $sql = "
                DELETE FROM IMAGE
                 WHERE IMAGE_SEQ = :pk";

            $name_sql = $IMAGE_SEQ." 이미지 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $IMAGE_SEQ], $name_sql);

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