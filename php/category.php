<?php
/**
 * 파일명 : category.php
 * 내용 : 카테고리 (등록, 수정, 삭제)
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

        $CATEGORY1_SEQ = get_request_param('R_CATEGORY1_SEQ'); // 카테고리
        $m_seq = get_request_param('m_seq');
        $mp_seq = get_request_param('mp_seq');
        $page_type = get_request_param('page_type');

        if (!empty($CATEGORY1_SEQ)) {
            $url_name = strtolower($CATEGORY1_SEQ);
        }

        if ($CATEGORY1_SEQ == "SHOP" ||  $mode == "DEL") {
            $arrParams = array(
                  'm_seq' => $m_seq
                , 'mp_seq' => $mp_seq
                , 'page_type' => $page_type
            );

            $query_string = http_build_query($arrParams);
        }

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];

        if ($CATEGORY1_SEQ == "SHOP") {
            $arrRtn['url'] = "../adm/board/movement_main.php?{$query_string}"; 

            dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
        } else if ($mode == "DEL") {
            $lowercaseStr = strtolower($CATEGORY1_SEQ);

            $arrRtn['url'] = "../adm/board/{$lowercaseStr}_main.php?{$query_string}"; 
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

            $CATEGORY1_SEQ = get_request_param('R_CATEGORY1_SEQ'); // 카테고리
            $TITLE = get_request_param('TITLE'); // 제목
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 제목 설명
            $SDATE = get_request_param('SDATE'); // 시작일
            $EDATE = get_request_param('EDATE'); // 종료일
            $LINK_URL = get_request_param('LINK_URL'); // 외부링크
            $CONTENT_TITLE = get_request_param('CONTENT_TITLE'); // 상세 제목
            $CONTENT_TEXT = isset($_POST['CONTENT_TEXT']) ? $_POST['CONTENT_TEXT'] : ''; // 내용
            $RELATED_VALUE = isset($_POST['RELATED_VALUE']) ? $_POST['RELATED_VALUE'] : array(); // 관련값
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $TITLE_YN = get_request_param('TITLE_YN'); // 메인노출여부
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $page_type = get_request_param('page_type'); // 페이지 타입
            $key_val = get_request_param('key_val'); // 업로드 key값

            if ($CATEGORY1_SEQ == "SHOP") {
                $CATEGORY2_SEQ = "SHOP"; // 시리즈, 분류
            } else if ($CATEGORY1_SEQ == "COLLABO") {
                $CATEGORY2_SEQ = "COLLABO"; // 시리즈, 분류
            } else {
                $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ'); // 시리즈, 분류
            }

            gfn_isValidation(305, $CATEGORY1_SEQ, "카테고리");
            gfn_isValidation(302, $TITLE, "제목");

            if (empty($TITLE_YN)){
                $TITLE_YN = 'N';
            }

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $RELATED_VALUE_INFO = "";

            if (!empty($RELATED_VALUE)) {
                foreach ($RELATED_VALUE as $value) {
                    if (empty($RELATED_VALUE_INFO)) {
                        $RELATED_VALUE_INFO .= $value;
                    } else {
                        $RELATED_VALUE_INFO .= ",". $value;
                    }
                }
            }


            if (empty($SDATE)){
                $SDATE = null;
            }

            if (empty($EDATE)){
                $EDATE = null;
            }

            $seq_name = "CATE" . $CATEGORY1_SEQ;

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "카테고리2 시퀀스";
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

            $table = 'CATEGORY3';
            $type = $CATEGORY1_SEQ;
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = '';
            $ATTACH_FILE_ID = "";

            $CATEGORY3_SEQ = $data['seq'];

            if ($page_type == PAGE3) {
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
                                if ($key_value == "ATTACH") {
                                    $idx = $count;
                                    $ATTACH_GROUP = 1;
                                } else if ($key_value == "files") {
                                    $idx = $key_val[$key];
                                    $ATTACH_GROUP = 2;
                                }

                                gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                            }
                        }
                    }
                }
            }

            $values = array(
                  'CATEGORY3_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ // 작가 시퀀스
                , 'CATEGORY2_SEQ' => $CATEGORY2_SEQ // 작가 시퀀스
                , 'TITLE' => $TITLE // 제목
                , 'SUB_TITLE' => $SUB_TITLE // 제목 설명
                , 'SDATE' => $SDATE // 시작일
                , 'EDATE' => $EDATE // 종료일
                , 'LINK_URL' => $LINK_URL // 외부링크
                , 'CONTENT_TITLE' => $CONTENT_TITLE // 상세제목
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                , 'RELATED_VALUE' => $RELATED_VALUE_INFO
                , 'TITLE_YN' => $TITLE_YN // 메인노출여부
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 썸네일 이미지
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "카테고리3 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            $sql = "
                 SELECT *
                  FROM {$table}
                  WHERE CATEGORY1_SEQ = '{$CATEGORY1_SEQ}'
                    AND CATEGORY2_SEQ = '{$CATEGORY2_SEQ}'
                    AND ORDER_NUMBER = '{$ORDER_NUMBER}'
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "정렬 값 확인";
            $clefResult = $mysqldb->count($sql, '', $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 1) {
                $sql = "
                     SELECT MAX(ORDER_NUMBER) + 1 AS MAX_COUNT
                       FROM {$table}
                      WHERE CATEGORY1_SEQ = '{$CATEGORY1_SEQ}'
                        AND CATEGORY2_SEQ = '{$CATEGORY2_SEQ}'
                        AND PAGE_TYPE = '{$page_type}'";

                $clefResult = $mysqldb->get($sql, null, $name_sql);
                $COUNT = $clefResult->getResultSet();

                $values = array(
                      'ORDER_NUMBER' => $COUNT['MAX_COUNT'] // 페이지 타입
                );

                $name_sql = "정렬 중복 수정";
                $clefResult = $mysqldb->update($table, $values, ['CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(502);
                }
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

            $CATEGORY3_SEQ = get_request_param('SEQ'); // 시퀀스
            $CATEGORY1_SEQ = get_request_param('R_CATEGORY1_SEQ'); // 카테고리
            $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ'); // 분류
            $TITLE = get_request_param('TITLE'); // 제목
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 제목 설명
            $SDATE = get_request_param('SDATE'); // 시작일
            $EDATE = get_request_param('EDATE'); // 종료일
            $LINK_URL = get_request_param('LINK_URL'); // 외부 링크
            $CONTENT_TITLE = get_request_param('CONTENT_TITLE'); // 상세 제목
            $CONTENT_TEXT = isset($_POST['CONTENT_TEXT']) ? $_POST['CONTENT_TEXT'] : ''; // 내용
            $RELATED_VALUE = isset($_POST['RELATED_VALUE']) ? $_POST['RELATED_VALUE'] : array(); // 관련값
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $TITLE_YN = get_request_param('TITLE_YN'); // 메인노출여부
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $R_ORDER_NUMBER = get_request_param('R_ORDER_NUMBER'); // 정렬값 변경확인값
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); // 호버 이미지
            $page_type = get_request_param('page_type'); // 페이지 타입
            $formData_del = get_request_param('formData_del'); // 업로드 삭제 id 값
            $key_val = get_request_param('key_val'); // 업로드 key값

            if ($CATEGORY1_SEQ == "SHOP") {
                $CATEGORY2_SEQ = "SHOP"; // 시리즈, 분류
            } else if ($CATEGORY1_SEQ == "COLLABO") {
                $CATEGORY2_SEQ = "COLLABO"; // 시리즈, 분류
            } else {
                $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ'); // 시리즈, 분류
            }

            gfn_isValidation(305, $CATEGORY1_SEQ, "카테고리");
            gfn_isValidation(302, $TITLE, "제목");

            if (empty($TITLE_YN)){
                $TITLE_YN = 'N';
            }

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $RELATED_VALUE_INFO = "";

            if (!empty($RELATED_VALUE)) {
                foreach ($RELATED_VALUE as $value) {
                    if (empty($RELATED_VALUE_INFO)) {
                        $RELATED_VALUE_INFO .= $value;
                    } else {
                        $RELATED_VALUE_INFO .= ",". $value;
                    }
                }
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
            
            $table = 'CATEGORY3';
            $type = $CATEGORY1_SEQ;
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = "";

            if (!empty($formData_del) && !empty($ATTACH_FILE_ID)) {
                $formData_del = json_decode($formData_del, true);
                
                foreach ($formData_del as $val) {
                    gfn_file_upload("D", '', $ATTACH_FILE_ID, 2, $val);
                }
            }

            if ($page_type == PAGE3) {
                if (is_array($_FILES)) {
                    if (!empty($key_val)) {
                        $key_val = json_decode($key_val, true);
                    }
                    
                    foreach ($_FILES as $key => $val) {
                        if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                            if (empty($ATTACH_FILE_ID)) {
                                $ATTACH_FILE_ID = 'ATTACH_'. $CATEGORY3_SEQ;
                            }

                            if ($key == "files") { // 멀티 썸네일 이미지
                                $key_value = $key;
                                $arrRes = json_decode(multiple_file_upload($dir, "files"), true);
                            } else {
                                $key_value = $key;
                                $arrRes = json_decode(one_file_upload($dir, $key), true);
                            }

                            if ($arrRes['code'] != 200) {
                                throw new Exception($arrRes['msg'], $arrRes['code']);
                            }

                            if (is_array($arrRes['file'])) {
                                foreach ($arrRes['file'] as $key => $val) {
                                    if ($key_value == "ATTACH") {
                                        $idx = $count;
                                        $ATTACH_GROUP = 1;
                                    } else  if ($key_value == "files") {
                                        $idx = $key_val[$key];
                                        $ATTACH_GROUP = 2;
                                    }

                                    if ($key_value != "files") {
                                        $FIND_FILE = gfn_file_upload("T", '', $ATTACH_FILE_ID, $ATTACH_GROUP);

                                        if (!empty($FIND_FILE) || $FIND_FILE > 0) {
                                            gfn_file_upload("U", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                        } else {
                                            gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                        }
                                    } else {
                                        gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $values = array(
                  'PAGE_TYPE' => $page_type // 페이지 타입
                , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ // 카테고리
                , 'CATEGORY2_SEQ' => $CATEGORY2_SEQ // 분류
                , 'TITLE' => $TITLE // 제목
                , 'SUB_TITLE' => $SUB_TITLE // 제목 설명
                , 'SDATE' => $SDATE // 시작일
                , 'EDATE' => $EDATE // 종료일
                , 'LINK_URL' => $LINK_URL // 외부링크
                , 'CONTENT_TITLE' => $CONTENT_TITLE // 상세제목
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                , 'RELATED_VALUE' => $RELATED_VALUE_INFO
                , 'TITLE_YN' => $TITLE_YN // 메인노출여부
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 썸네일 이미지
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = "카테고리3 수정";
            $clefResult = $mysqldb->update($table, $values, ['CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            if ($R_ORDER_NUMBER != $ORDER_NUMBER) {
                $sql = "
                     SELECT *
                       FROM {$table}
                      WHERE CATEGORY1_SEQ = '{$CATEGORY1_SEQ}'
                        AND CATEGORY2_SEQ = '{$CATEGORY2_SEQ}'
                        AND ORDER_NUMBER = '{$ORDER_NUMBER}'";

                $name_sql = "정렬 값 확인";
                $clefResult = $mysqldb->count($sql, '', $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $total = $clefResult->getCount();

                if ($total > 0) {
                    $sql = "
                         SELECT MAX(ORDER_NUMBER) + 1 AS MAX_COUNT
                           FROM {$table}
                          WHERE CATEGORY1_SEQ = '{$CATEGORY1_SEQ}'
                            AND CATEGORY2_SEQ = '{$CATEGORY2_SEQ}'
                            AND PAGE_TYPE = '{$page_type}'";
    
                    $clefResult = $mysqldb->get($sql, null, $name_sql);
                    $COUNT = $clefResult->getResultSet();

                    $values = array(
                        'ORDER_NUMBER' => $COUNT['MAX_COUNT'] // 정렬값
                    );
    
                    $name_sql = "정렬 중복 수정";
                    $clefResult = $mysqldb->update($table, $values, ['CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);
    
                    if (!$clefResult->getResult()) {
                        gfn_isValidation(502);
                    }
                } else {
                    $values = array(
                        'ORDER_NUMBER' => $ORDER_NUMBER // 정렬값
                    );
    
                    $name_sql = "정렬 중복 수정";
                    $clefResult = $mysqldb->update($table, $values, ['CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);
                }
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

            $CATEGORY3_SEQ = get_request_param('SEQ'); // 시퀀스

            //작품 삭제
            $sql = "
                 DELETE FROM CATEGORY3
                  WHERE CATEGORY3_SEQ = :pk";

            $name_sql = $CATEGORY3_SEQ." 카테고리3 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CATEGORY3_SEQ], $name_sql);

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