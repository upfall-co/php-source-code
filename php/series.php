<?php
/**
 * 파일명 : series.php
 * 내용 : 시리즈, 카테고리2 (등록, 수정, 삭제)
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
 * 김민성    2023/11/10    shop 기능추가
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
        $M_PROGRAM_CD = get_request_param('M_PROGRAM_CD'); // 중분류
        $M_CATEGORY1_SEQ = get_request_param('M_CATEGORY1_SEQ'); // 작가명
        $M_TITLE = get_request_param('M_TITLE'); // 시리즈
        $M_MAIN_YN = get_request_param('M_MAIN_YN'); // 노출여부
        $sub_type = get_request_param('sub_type'); // 서브 페이지 타입

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'sub_type' => $sub_type
            , 'PROGRAM_CD' => $M_PROGRAM_CD
            , 'CATEGORY1_SEQ' => $M_CATEGORY1_SEQ
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];

        if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE1) {
                $arrRtn['url'] = "../adm/board/recruit2_main.php?{$query_string}"; 
            } else if ($sub_type == SUB_PAGE2) {
                $arrRtn['url'] = "../adm/board/program_main.php?{$query_string}"; 
            }
        } else {
            $arrRtn['url'] = "../adm/board/series_main.php?{$query_string}"; 
        }   

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

            $PROGRAM_CD = get_request_param('PROGRAM_CD'); // 중분류 코드 값
            $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ'); // 작가 시퀀스
            $TITLE = get_request_param('TITLE'); // 시리즈명
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 상세제목
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $page_type = get_request_param('page_type'); // 페이지 타입
            $sub_type = get_request_param('sub_type'); // 서브 페이지 타입
            $CONTENT_TEXT = isset($_POST['editor']) ? $_POST['editor'] : ''; // 내용

            $category1_name = "";
            $title_val = "";
            $seq_name = '';
            $msg = "";

            if ($page_type == PAGE1) {
                $seq_name = "SER";
                $category1_name = "작가";
                $category1_val = "작가명";
                $title_name = "시리즈"; // Copy, CSV, Excel, Print 제목
                $title_val = "시리즈명";
            } else if ($page_type == PAGE2) {
                $seq_name = "CATE2";
                $category1_name = "카테고리";
                $category1_val = "카테고리명";
                $title_name = "분류"; // Copy, CSV, Excel, Print 제목
                $title_val = "분류명";
            } else if ($page_type == PAGE3) {
                if ($sub_type == SUB_PAGE1) {
                    $seq_name = "RECRU2";
                    $category1_name = "업종";
                    $category1_val = "업종명";
                    $title_name = "세부업종"; // Copy, CSV, Excel, Print 제목
                    $title_val = "세부업종명";
                } else if ($sub_type == SUB_PAGE2) {
                    $seq_name = "PROGRAM2";
                    $category1_name = "카테고리";
                    $category1_val = "카테고리명";
                    $title_name = "분류"; // Copy, CSV, Excel, Print 제목
                    $title_val = "분류명";
                }
            }
 
            gfn_isValidation(301, $CATEGORY1_SEQ, $category1_name);
            gfn_isValidation(302, $TITLE, $title_val);

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $TITLE = trim($TITLE); //시리즈명

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

            $table = 'CATEGORY2';
            $type = 'SERIES';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = '';
            $ATTACH_FILE_ID = "";

            $CATEGORY2_SEQ = $data['seq'];

            if ($page_type == PAGE1 || $sub_type == SUB_PAGE2) {
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
                                    $ATTACH_GROUP = 4;
                                } else if ($key_value == "ATTACH2") {
                                    $idx = $count;
                                    $ATTACH_GROUP = 2;
                                } else if ($key_value == "ATTACH3") {
                                    $idx = $count;
                                    $ATTACH_GROUP = 1;
                                }

                                gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                            }
                        }
                    }
                }
            }

            $values = array(
                  'CATEGORY2_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'SUB_TYPE' => $sub_type // 서브 페이지 타입
                , 'PROGRAM_CD' => $PROGRAM_CD // 중분류 구분 값
                , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ // 작가 시퀀스
                , 'TITLE' => $TITLE // 제목
                , 'SUB_TITLE' => $SUB_TITLE // 추가제목
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 호버 이미지
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "카테고리2 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            $arrValue = array();
            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
            $arrValue[':ORDER_NUMBER'] = $ORDER_NUMBER;
            $arrValue[':PAGE_TYPE'] = $page_type;
            $arrValue[':SUB_TYPE'] = $sub_type;

            $sql = "
                 SELECT *
                  FROM {$table}
                  WHERE CATEGORY1_SEQ = :CATEGORY1_SEQ
                    AND ORDER_NUMBER = :ORDER_NUMBER
                    AND PAGE_TYPE = :PAGE_TYPE
                    AND SUB_TYPE = :SUB_TYPE";

            $name_sql = "정렬 값 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 1) {
                $arrValue2 = array();
                $arrValue2[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
                $arrValue2[':PAGE_TYPE'] = $page_type;
                $arrValue2[':SUB_TYPE'] = $sub_type;

                $sql = "
                     SELECT MAX(ORDER_NUMBER) + 1 AS MAX_COUNT
                      FROM {$table}
                      WHERE CATEGORY1_SEQ = :CATEGORY1_SEQ
                        AND PAGE_TYPE = :PAGE_TYPE
                        AND SUB_TYPE = :SUB_TYPE";

                $clefResult = $mysqldb->get($sql, $arrValue2, $name_sql);
                $COUNT = $clefResult->getResultSet();

                $values = array(
                      'ORDER_NUMBER' => $COUNT['MAX_COUNT'] // 페이지 타입
                );

                $name_sql = "정렬 중복 수정";
                $clefResult = $mysqldb->update($table, $values, ['CATEGORY2_SEQ' => $CATEGORY2_SEQ], $name_sql);

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

            $PROGRAM_CD = get_request_param('PROGRAM_CD'); // 중분류 코드 값
            $CATEGORY1_SEQ = get_request_param('R_CATEGORY1_SEQ'); //작가, 카테고리1 시퀀스
            $CATEGORY2_SEQ = get_request_param('SEQ'); // 시퀀스
            $TITLE = get_request_param('TITLE'); // 시리즈명, 카테고리2명
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 추가제목
            $R_ORDER_NUMBER = get_request_param('R_ORDER_NUMBER'); // 정렬값 변경확인값
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); // 호버 이미지
            $page_type = get_request_param('page_type'); // 페이지 타입
            $sub_type = get_request_param('sub_type'); // 서브 페이지 타입
            $CONTENT_TEXT = isset($_POST['editor']) ? $_POST['editor'] : ''; // 내용

            $category1_name = "";
            $title_val = "";
            $seq_name = '';
            $msg = "";

            if ($page_type == PAGE1) {
                $category1_name = "작가";
                $category1_val = "작가명";
                $title_name = "시리즈"; // Copy, CSV, Excel, Print 제목
                $title_val = "시리즈명";
            } else if ($page_type == PAGE2) {
                $category1_name = "카테고리";
                $category1_val = "카테고리명";
                $title_name = "분류"; // Copy, CSV, Excel, Print 제목
                $title_val = "분류명";
            } else if ($page_type == PAGE3) {
                if ($sub_type == SUB_PAGE1) {
                    $seq_name = "RECRU2";
                    $category1_name = "업종";
                    $category1_val = "업종명";
                    $title_name = "세부업종"; // Copy, CSV, Excel, Print 제목
                    $title_val = "세부업종명";
                } else if ($sub_type == SUB_PAGE2) {
                    $seq_name = "PROGRAM2";
                    $category1_name = "카테고리";
                    $category1_val = "카테고리명";
                    $title_name = "분류"; // Copy, CSV, Excel, Print 제목
                    $title_val = "분류명";
                }
            }

            gfn_isValidation(302, $TITLE, $title_val);

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $TITLE = trim($TITLE); //시리즈명

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'CATEGORY2';
            $type = 'SERIES';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = "";

            if ($page_type == PAGE1 || $sub_type == SUB_PAGE2) {
                if (is_array($_FILES)) {
                    if (!empty($key_val)) {
                        $key_val = json_decode($key_val, true);
                    }
                    
                    foreach ($_FILES as $key => $val) {
                        if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                            if (empty($ATTACH_FILE_ID)) {
                                $ATTACH_FILE_ID = 'ATTACH_'. $CATEGORY2_SEQ;
                            }

                            $key_value = $key;
                            $arrRes = json_decode(one_file_upload($dir, $key), true);

                            if ($arrRes['code'] != 200) {
                                throw new Exception($arrRes['msg'], $arrRes['code']);
                            }

                            if (is_array($arrRes['file'])) {
                                if ($key_value == "ATTACH") {
                                    $idx = $count;
                                    $ATTACH_GROUP = 4;
                                } else if ($key_value == "ATTACH2") {
                                    $idx = $count;
                                    $ATTACH_GROUP = 2;
                                } else if ($key_value == "ATTACH3") {
                                    $idx = $count;
                                    $ATTACH_GROUP = 1;
                                }

                                foreach ($arrRes['file'] as $key => $val) {
                                    $FIND_FILE = gfn_file_upload("T", '', $ATTACH_FILE_ID, $ATTACH_GROUP);

                                    if (!empty($FIND_FILE) || $FIND_FILE > 0) {
                                        gfn_file_upload("U", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
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
                , 'SUB_TYPE' => $sub_type // 서브 페이지 타입
                , 'TITLE' => $TITLE // 시리즈
                , 'SUB_TITLE' => $SUB_TITLE // 추가제목
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 호버 이미지
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = "카테고리2 수정";
            $clefResult = $mysqldb->update($table, $values, ['CATEGORY2_SEQ' => $CATEGORY2_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            if ($R_ORDER_NUMBER != $ORDER_NUMBER) {
                $arrValue = array();
                $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
                $arrValue[':ORDER_NUMBER'] = $ORDER_NUMBER;
                
                $sql = "
                     SELECT *
                       FROM {$table}
                      WHERE CATEGORY1_SEQ = :CATEGORY1_SEQ
                        AND ORDER_NUMBER = :ORDER_NUMBER";

                $name_sql = "정렬 값 확인";
                $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $total = $clefResult->getCount();

                if ($total > 0) {
                    $arrValue2 = array();
                    $arrValue2[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;

                    $sql = "
                         SELECT MAX(ORDER_NUMBER) + 1 AS MAX_COUNT
                           FROM {$table}
                          WHERE CATEGORY1_SEQ = :CATEGORY1_SEQ";
    
                    $clefResult = $mysqldb->get($sql, $arrValue2, $name_sql);
                    $COUNT = $clefResult->getResultSet();

                    $values = array(
                        'ORDER_NUMBER' => $COUNT['MAX_COUNT'] // 정렬값
                    );
    
                    $name_sql = "정렬 중복 수정";
                    $clefResult = $mysqldb->update($table, $values, ['CATEGORY2_SEQ' => $CATEGORY2_SEQ], $name_sql);
    
                    if (!$clefResult->getResult()) {
                        gfn_isValidation(502);
                    }
                } else {
                    $values = array(
                        'ORDER_NUMBER' => $ORDER_NUMBER // 정렬값
                    );
    
                    $name_sql = "정렬 중복 수정";
                    $clefResult = $mysqldb->update($table, $values, ['CATEGORY2_SEQ' => $CATEGORY2_SEQ], $name_sql);
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

            $CATEGORY2_SEQ = get_request_param('SEQ'); // 시퀀스

            //작품 시퀀스 뽑기
            $sql = "
                 SELECT CATEGORY3_SEQ
                   FROM CATEGORY3
                  WHERE CATEGORY2_SEQ = :pk";

            $name_sql ="카테고리3 리스트 ";

            $clefResult = $mysqldb->select($sql, [':pk' => $CATEGORY2_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 시퀀스

                    //작품옵션 삭제
                    $sql = "
                         DELETE FROM CATEGORY_OPTION
                          WHERE CATEGORY3_SEQ = :pk";

                    $name_sql = $_db_CATEGORY3_SEQ." 카테고리3 옵션 삭제 ";

                    $clefResult = $mysqldb->delete($sql, [':pk' => $_db_CATEGORY3_SEQ], $name_sql);

                    if (!$clefResult->getResult()) {
                        gfn_isValidation(503);
                    }
                }
            }

            //작품 삭제
            $sql = "
                 DELETE FROM CATEGORY3
                  WHERE CATEGORY2_SEQ = :pk";

            $name_sql = $CATEGORY2_SEQ." 카테고리3 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CATEGORY2_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $sql = "
                DELETE FROM CATEGORY2
                 WHERE CATEGORY2_SEQ = :pk";

            $name_sql = $CATEGORY2_SEQ." 카테고리2 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CATEGORY2_SEQ], $name_sql);

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