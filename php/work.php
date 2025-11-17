<?php
/**
 * 파일명 : work.php
 * 내용 : 작품 (등록, 수정, 삭제)
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/07     V1.0
 * 김민성    2023/11/13    shop 기능추가
 */

    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $arrRtn = array(
          'code' => 500
        , 'msg'  => ''
        , 'mode' => ''
        , 'url' => ''
    );

    try {
        $mode = get_request_param('mode');
        $MULTI = get_request_param('MULTI');

        switch ($mode) {
            case 'INS' :
                if ($MULTI == "Y") {
                    $arrRes = row_insert_multi();
                } else {
                    $arrRes = row_insert();
                }
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

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn);
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

            //작품
            $page_type = get_request_param('page_type'); // 페이지 타입
            $sub_type = get_request_param('sub_type'); // 서브 페이지 타입
            $PROGRAM_CD = get_request_param('PROGRAM_CD'); // 중분류
            $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ'); // 작가, 카테고리
            $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ'); // 시리즈, 분류
            $TYPE = isset($_POST['TYPE_CD']) ? $_POST['TYPE_CD'] : array();
            $TITLE = get_request_param('TITLE'); // 작품, 상품명
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 작품, 상품 서브내용
            $SDATE = isset($_POST['SDATE']) ? $_POST['SDATE'] : ''; //시작일
            $EDATE = isset($_POST['EDATE']) ? $_POST['EDATE'] : ''; //종료일
            $LINK_URL = get_request_param('LINK_URL'); // 외부링크
            $CONTENT_TITLE = get_request_param('CONTENT_TITLE'); // 상세 제목
            $QUANTITY = get_request_param('QUANTITY'); // 수량
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $TITLE_YN = get_request_param('TITLE_YN'); // 메인 노출여부 (PROGRAM)
            $INDEX_YN = get_request_param('INDEX_YN'); // 메인 노출여부
            $BRAND = get_request_param('BRAND'); // 브랜드명
            $FRAME = get_request_param('FRAME'); // 프레임
            $SALE_YN = get_request_param('SALE_YN'); // 할인여부
            $OID_PRICE = get_request_param('OID_PRICE'); // 할인전 금액
            $SALE_PERCENT = get_request_param('SALE_PERCENT'); // 할인율
            $M_PRICE = get_request_param('M_PRICE'); // 금액
            $BADGE_CO = get_request_param('BADGE_CO'); // 뱃지
            $SEARCH_TEXT = get_request_param('SEARCH_TEXT'); // 검색내용
            $CONTENT_TEXT = isset($_POST['editor']) ? $_POST['editor'] : '';
            $key_val = get_request_param('key_val'); // 업로드 key값

            //작품 옵션
            $OPTION_NAME = isset($_POST['OPTION_NAME']) ? $_POST['OPTION_NAME'] : array();
            $OP_QUANTITY = isset($_POST['OP_QUANTITY']) ? $_POST['OP_QUANTITY'] : array();
            $PRICE = isset($_POST['PRICE']) ? $_POST['PRICE'] : array();
            $OP_ORDER_NUMBER = isset($_POST['OP_ORDER_NUMBER']) ? $_POST['OP_ORDER_NUMBER'] : array();

            $OPTION_YN = isset($_POST['option_YN']) ? $_POST['option_YN'] : "[]";

            $CATEGORY1_NM = "";
            $CATEGORY2_NM = "";

            if ($page_type == PAGE1) {
                $CATEGORY1_NM = "작가";
                $CATEGORY2_NM = "시리즈";
            } else if ($page_type == PAGE2) {
                $CATEGORY1_NM = "카테고리";
                $CATEGORY2_NM = "분류";
            }  else if ($page_type == PAGE3) {
                $CATEGORY1_NM = "카테고리";
                $CATEGORY2_NM = "분류";
            }

            gfn_isValidation(301, $CATEGORY1_SEQ, $CATEGORY1_NM);
            gfn_isValidation(301, $CATEGORY2_SEQ, $CATEGORY2_NM);
            gfn_isValidation(302, $TITLE, "제목");

            if (!empty($SDATE) && !empty($EDATE)) {
                if (date($SDATE) > date($EDATE)) {
                    gfn_isValidation(999, '', '기간이 종료일보다 큽니다.');
                }
            }

            if (empty($SDATE)) { //노출 시작날짜
                $SDATE = NULL;
            }

            if (empty($EDATE)) { //노출 종료날짜
                $EDATE = NULL;
            }

            if ($page_type != PAGE3) {
                gfn_isValidation(306, $OPTION_NAME[0], "옵션은 최소 한개 이상 입력해야합니다.");
            }

            if (empty($QUANTITY)) {
                $QUANTITY = 0;
            }

            if (empty($ORDER_NUMBER)) {
                $ORDER_NUMBER = 0;
            }

            if (empty($MAIN_YN)) {
                $MAIN_YN = 'N';
            }

            if (empty($INDEX_YN)) {
                $INDEX_YN = 'N';
            }

            if (empty($TITLE_YN)) {
                $TITLE_YN = 'N';
            }

            if (empty($SALE_YN)) {
                $SALE_YN = 'N';
            }

            $OPTION_YN = json_decode($OPTION_YN, true);

            if (!empty($TYPE)) {
                $TYPE = implode(",", $TYPE);
            } else {
                $TYPE = '';
            }

            $TITLE = trim($TITLE); //제목

            if (!empty($M_PRICE)) {
                $M_PRICE  = str_replace(",", "", $M_PRICE);
            } else {
                $M_PRICE = 0;
            }

            if (!empty($OID_PRICE)) {
                $OID_PRICE  = str_replace(",", "", $OID_PRICE);
            } else {
                $OID_PRICE = 0;
            }

            if (empty($SALE_PERCENT)) {
                $SALE_PERCENT = 0;
            }

            $seq_name = 'PRODU';

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "작품 시퀀스";
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
            $type = 'PRODUCT';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $count = 1;
            $key_value = '';

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
                                $idx = 1;
                                $ATTACH_GROUP = 1;
                            } else if ($key_value == "ATTACH4") {
                                $ATTACH_GROUP = 4;
                                $idx = 1;
                            } else if ($key_value == "files") {
                                $idx = $key_val[$key];
                                $ATTACH_GROUP = 3;
                            }

                            gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                        }
                    }
                }
            }

            $values = array(
                  'CATEGORY3_SEQ' => $data['seq'] // 작품 시퀀스
                , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ // 작가, 카테고리 시퀀스
                , 'CATEGORY2_SEQ' => $CATEGORY2_SEQ // 시리즈, 분류 시퀀스
                , 'OPTION_SEQ' => '' // 옵션 시퀀스
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'SUB_TYPE' => $sub_type // 상세 페이지 타입
                , 'PROGRAM_CD' => $PROGRAM_CD // 중분류 코드
                , 'TYPE_CD' => $TYPE // 구분
                , 'TITLE' => $TITLE // 작품, 상품
                , 'SUB_TITLE' => $SUB_TITLE // 상품 서브내용
                , 'SDATE' => $SDATE // 시작일
                , 'EDATE' => $EDATE // 종료일
                , 'CONTENT_TITLE' => $CONTENT_TITLE // 상세 제목
                , 'LINK_URL' => $LINK_URL // 외부링크
                , 'INDEX_YN' => $INDEX_YN // 메인화면 노출여부
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'TITLE_YN' => $TITLE_YN // 메인노출여부
                , 'QUANTITY' => $QUANTITY // 수량
                , 'FRAME' => $FRAME // 프레임
                , 'BRAND' => $BRAND // 브랜드
                , 'PRICE'=> $M_PRICE // 금액
                , 'SALE_YN' => $SALE_YN // 할인여부
                , 'OID_PRICE' => $OID_PRICE // 할인전금액
                , 'SALE_PERCENT' => $SALE_PERCENT // 할인율
                , 'BADGE_CO' => $BADGE_CO
                , 'SEARCH_TEXT' => $SEARCH_TEXT // 검색내용
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 파일
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            if ($page_type==PAGE3 && $sub_type==SUB_PAGE2) {
                $valuse['SDATE'] = $SDATE;
                $valuse['EDATE'] = $EDATE;
            }

            $name_sql = "카테고리3 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            if ($page_type == PAGE3) {
                unset($values);

                $OPTION_NAME_cnt = count($OPTION_NAME);
                for ($i = 0; $i < $OPTION_NAME_cnt; $i++) {
                    if (!empty($OPTION_NAME)) {

                        $OPTION_NAME_i  = $OPTION_NAME[$i];
                        $OP_QUANTITY_i  = $OP_QUANTITY[$i];
                        $PRICE_i  = str_replace(",", "", $PRICE[$i]);
                        $OP_ORDER_NUMBER_i  = $OP_ORDER_NUMBER[$i];
                        $OP_MAIN_YN_i  = $OPTION_YN[$i]['OP_MAIN_YN'] ?? 'N';
                        $OP_SOLD_YN_i  = $OPTION_YN[$i]['OP_SOLD_YN'] ?? 'N';

                        if (empty($OP_QUANTITY_i)) {
                            $OP_QUANTITY_i = 0;
                        }

                        if (empty($OP_ORDER_NUMBER_i)) {
                            $OP_ORDER_NUMBER_i = 0;
                        }

                        if (empty($OP_MAIN_YN_i)) {
                            $OP_MAIN_YN_i = 'N';
                        }

                        if (empty($OP_SOLD_YN_i)) {
                            $OP_SOLD_YN_i = 'N';
                        }

                        $seq_name = 'OPTION';

                        $sql = "
                            SELECT nextval('{$seq_name}') as seq";

                        $name_sql = "작품옵션 시퀀스";
                        $clefResult = $mysqldb->get($sql, null, $name_sql);
                        $data2 = $clefResult->getResultSet();

                        if (empty($OPTION_NAME_i)) {
                            continue;
                        }

                        $values = array(
                            'OPTION_SEQ' => $data2['seq'] // 작품 옵션 시퀀스
                            , 'CATEGORY3_SEQ' => $data['seq'] // 작품 시퀀스
                            , 'OPTION_NAME' => $OPTION_NAME_i // 옵션명
                            , 'QUANTITY' => $OP_QUANTITY_i // 수량
                            , 'PRICE' => $PRICE_i // 가격
                            , 'ORDER_NUMBER' => $OP_ORDER_NUMBER_i // 정렬
                            , 'MAIN_YN' => $OP_MAIN_YN_i // 노출여부
                            , 'SOLD_YN' => $OP_SOLD_YN_i // 품절여부
                            , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                            , 'reg_ip' => $ip // 등록자 아이피
                            , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
                        );

                        $name_sql = "작품 옵션 추가";
                        $clefResult = $mysqldb->insert('CATEGORY_OPTION', $values, $name_sql);

                        if (!$clefResult->getResult()) {
                            gfn_isValidation(501);
                        }
                    }
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
     * name :row_insert
     * comment : 등록
     */
    function row_insert_multi() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            //트랜잭션
            $mysqldb->link->beginTransaction();

            //작품
            $page_type = get_request_param('page_type'); // 페이지 타입
            $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ1'); // 작가, 카테고리
            $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ1'); // 시리즈, 분류
            $TYPE = isset($_POST['TYPE_CD']) ? $_POST['TYPE_CD'] : array();
            $TITLE = get_request_param('TITLE'); // 작품, 상품명
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 작품, 상품 서브내용
            $QUANTITY = get_request_param('QUANTITY'); // 수량
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $INDEX_YN = get_request_param('INDEX_YN'); // 메인 노출여부
            $BRAND = get_request_param('BRAND'); // 브랜드명
            $FRAME = get_request_param('FRAME'); // 프레임
            $SALE_YN = get_request_param('SALE_YN'); // 할인여부
            $OID_PRICE = get_request_param('OID_PRICE'); // 할인전 금액
            $SALE_PERCENT = get_request_param('SALE_PERCENT'); // 할인율
            $M_PRICE = get_request_param('M_PRICE'); // 금액
            $BADGE_CO = get_request_param('BADGE_CO'); // 뱃지
            $SEARCH_TEXT = get_request_param('SEARCH_TEXT'); // 검색내용
            $CONTENT_TEXT = isset($_POST['editor']) ? $_POST['editor'] : '';
            $key_val = get_request_param('key_val'); // 업로드 key값

            //작품 옵션
            $OPTION_NAME = isset($_POST['OPTION_NAME']) ? $_POST['OPTION_NAME'] : array();
            $OP_QUANTITY = isset($_POST['OP_QUANTITY']) ? $_POST['OP_QUANTITY'] : array();
            $PRICE = isset($_POST['PRICE']) ? $_POST['PRICE'] : array();
            $OP_ORDER_NUMBER = isset($_POST['OP_ORDER_NUMBER']) ? $_POST['OP_ORDER_NUMBER'] : array();

            $OPTION_YN = isset($_POST['option_YN']) ? $_POST['option_YN'] : "[]";

            if ($page_type == PAGE1) {
                $CATEGORY1_NM = "작가";
                $CATEGORY2_NM = "시리즈";
            } else if ($page_type == PAGE2) {
                $CATEGORY1_NM = "카테고리";
                $CATEGORY2_NM = "분류";
            }

            gfn_isValidation(301, $CATEGORY1_SEQ, $CATEGORY1_NM);
            gfn_isValidation(301, $CATEGORY2_SEQ, $CATEGORY2_NM);
            gfn_isValidation(302, $TITLE, "제목");
            gfn_isValidation(306, $OPTION_NAME[0], "옵션은 최소 한개 이상 입력해야합니다.");

            if (empty($QUANTITY)) {
                $QUANTITY = 0;
            }

            if (empty($ORDER_NUMBER)) {
                $ORDER_NUMBER = 0;
            }

            if (empty($MAIN_YN)) {
                $MAIN_YN = 'N';
            }

            if (empty($INDEX_YN)) {
                $INDEX_YN = 'N';
            }

            if (empty($SALE_YN)) {
                $SALE_YN = 'N';
            }

            $OPTION_YN = json_decode($OPTION_YN, true);

            if (!empty($TYPE)) {
                $TYPE = implode(",", $TYPE);
            } else {
                $TYPE = '';
            }

            if (!empty($M_PRICE)) {
                $M_PRICE  = str_replace(",", "", $M_PRICE);
            } else {
                $M_PRICE = 0;
            }

            if (!empty($OID_PRICE)) {
                $OID_PRICE  = str_replace(",", "", $OID_PRICE);
            } else {
                $OID_PRICE = 0;
            }

            if (empty($SALE_PERCENT)) {
                $SALE_PERCENT = 0;
            }

            for ($k = 1; $k <= 5; $k++) {
                $CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ' . $k); // 작가, 카테고리
                $CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ' . $k); // 시리즈, 분류

                if (!empty($CATEGORY1_SEQ) && !empty($CATEGORY2_SEQ)) {
                    $seq_name = 'PRODU';
                    $sql = "
                         SELECT nextval('{$seq_name}') as seq";
    
                    $name_sql = "멀티 작품 시퀀스";
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
                    $type = 'PRODUCT';
                    $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd'). "/". $k;
                    $count = 1;
                    $key_value = '';
    
                    if (is_array($_FILES)) {
                        if (!empty($key_val) && $k == 1) {
                            $key_val = json_decode($key_val, true);
                        }
    
                        foreach ($_FILES as $key => $val) {
                            if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드
    
                                $ATTACH_FILE_ID = 'ATTACH_'. $data['seq'];
    
                                if ($key == "files") { // 멀티 이미지
                                    $key_value = $key;
                                    $arrRes = json_decode(pik_multiple_file_upload($dir, "files"), true);
                                } else { // 썸네일, 메인 이미지
                                    $key_value = $key;
                                    $arrRes = json_decode(pik_one_file_upload($dir, $key), true);
                                }
    
                                if ($arrRes['code'] != 200) {
                                    throw new Exception($arrRes['msg'], $arrRes['code']);
                                }
    
                                foreach ($arrRes['file'] as $key => $val) {
                                    if ($key_value == "ATTACH") {
                                        $idx = 1;
                                        $ATTACH_GROUP = 1;
                                    } else if ($key_value == "ATTACH4") {
                                        $ATTACH_GROUP = 4;
                                        $idx = 1;
                                    } else if ($key_value == "files") {
                                        $idx = $key_val[$key];
                                        $ATTACH_GROUP = 3;
                                    }
    
                                    gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                }
                            }
                        }
                    }
    
                    $values = array(
                          'CATEGORY3_SEQ' => $data['seq'] // 작품 시퀀스
                        , 'CATEGORY1_SEQ' => $CATEGORY1_SEQ // 작가, 카테고리 시퀀스
                        , 'CATEGORY2_SEQ' => $CATEGORY2_SEQ // 시리즈, 분류 시퀀스
                        , 'OPTION_SEQ' => '' // 옵션 시퀀스
                        , 'PAGE_TYPE' => $page_type // 페이지 타입
                        , 'TYPE_CD' => $TYPE // 구분
                        , 'TITLE' => $TITLE // 작품, 상품
                        , 'SUB_TITLE' => $SUB_TITLE // 상품 서브내용
                        , 'INDEX_YN' => $INDEX_YN // 메인화면 노출여부
                        , 'MAIN_YN' => $MAIN_YN // 노출여부
                        , 'QUANTITY' => $QUANTITY // 수량
                        , 'FRAME' => $FRAME // 프레임
                        , 'BRAND' => $BRAND // 브랜드
                        , 'PRICE'=> $M_PRICE // 금액
                        , 'SALE_YN' => $SALE_YN // 할인여부
                        , 'OID_PRICE' => $OID_PRICE // 할인전금액
                        , 'SALE_PERCENT' => $SALE_PERCENT // 할인율
                        , 'BADGE_CO' => $BADGE_CO
                        , 'SEARCH_TEXT' => $SEARCH_TEXT // 검색내용
                        , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                        , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                        , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 파일
                        , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                        , 'reg_ip' => $ip // 등록자 아이피
                        , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
                    );
    
                    $name_sql = "카테고리3 추가";
                    $clefResult = $mysqldb->insert($table, $values, $name_sql);
    
                    if (!$clefResult->getResult()) {
                        gfn_isValidation(501);
                    }
    
                    unset($values);
    
                    $OPTION_NAME_cnt = count($OPTION_NAME);
                    for ($i = 0; $i < $OPTION_NAME_cnt; $i++) {
                        if (!empty($OPTION_NAME)) {
    
                            $OPTION_NAME_i  = $OPTION_NAME[$i];
                            $OP_QUANTITY_i  = $OP_QUANTITY[$i];
                            $PRICE_i  = str_replace(",", "", $PRICE[$i]);
                            $OP_ORDER_NUMBER_i  = $OP_ORDER_NUMBER[$i];
                            $OP_MAIN_YN_i  = $OPTION_YN[$i]['OP_MAIN_YN'] ?? 'N';
                            $OP_SOLD_YN_i  = $OPTION_YN[$i]['OP_SOLD_YN'] ?? 'N';
    
                            if (empty($OP_QUANTITY_i)) {
                                $OP_QUANTITY_i = 0;
                            }
    
                            if (empty($OP_ORDER_NUMBER_i)) {
                                $OP_ORDER_NUMBER_i = 0;
                            }
    
                            if (empty($OP_MAIN_YN_i)) {
                                $OP_MAIN_YN_i = 'N';
                            }
    
                            if (empty($OP_SOLD_YN_i)) {
                                $OP_SOLD_YN_i = 'N';
                            }
    
                            $seq_name = 'OPTION';
    
                            $sql = "
                                 SELECT nextval('{$seq_name}') as seq";
    
                            $name_sql = "작품옵션 시퀀스";
                            $clefResult = $mysqldb->get($sql, null, $name_sql);
                            $data2 = $clefResult->getResultSet();
    
                            if (empty($OPTION_NAME_i)) {
                                continue;
                            }
    
                            $values = array(
                                  'OPTION_SEQ' => $data2['seq'] // 작품 옵션 시퀀스
                                , 'CATEGORY3_SEQ' => $data['seq'] // 작품 시퀀스
                                , 'OPTION_NAME' => $OPTION_NAME_i // 옵션명
                                , 'QUANTITY' => $OP_QUANTITY_i // 수량
                                , 'PRICE' => $PRICE_i // 가격
                                , 'ORDER_NUMBER' => $OP_ORDER_NUMBER_i // 정렬
                                , 'MAIN_YN' => $OP_MAIN_YN_i // 노출여부
                                , 'SOLD_YN' => $OP_SOLD_YN_i // 품절여부
                                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                                , 'reg_ip' => $ip // 등록자 아이피
                                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
                            );
    
                            $name_sql = "작품 옵션 추가";
                            $clefResult = $mysqldb->insert('CATEGORY_OPTION', $values, $name_sql);
    
                            if (!$clefResult->getResult()) {
                                gfn_isValidation(501);
                            }
                        }
                    }
                }
            }

            if (is_array($_FILES)) {
                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                        if ($key == "files") { // 멀티 이미지
                            foreach ($val['tmp_name'] as $tmp_name) {
                                // 임시 파일이 존재하면 삭제
                                if (file_exists($tmp_name)) {
                                    unlink($tmp_name);
                                }
                            }
                        } else { // 썸네일, 메인 이미지
                            if (file_exists($_FILES[$key]['tmp_name'])) {
                                unlink($_FILES[$key]['tmp_name']);
                            }
    
                        }
                    }
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

            //작품
            $page_type = get_request_param('page_type'); // 페이지 타입
            $CATEGORY3_SEQ = get_request_param('SEQ'); // 작품 시퀀스
            $TYPE = isset($_POST['TYPE_CD']) ? $_POST['TYPE_CD'] : array(); // 구분
            $TITLE = get_request_param('TITLE'); // 작품, 상품명
            $SUB_TITLE = get_request_param('SUB_TITLE'); // 상품 서브내용
            $SDATE = isset($_POST['SDATE']) ? $_POST['SDATE'] : ''; //시작일
            $EDATE = isset($_POST['EDATE']) ? $_POST['EDATE'] : ''; //종료일
            $LINK_URL = get_request_param('LINK_URL'); // 외부링크
            $CONTENT_TITLE = get_request_param('CONTENT_TITLE'); // 상세제목
            $QUANTITY = get_request_param('QUANTITY'); // 수량
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $INDEX_YN = get_request_param('INDEX_YN'); // 메인화면 노출여부
            $TITLE_YN = get_request_param('TITLE_YN'); // 메인 노출여부 (PROGRAM)
            $FRAME = get_request_param('FRAME'); // 프레임
            $BRAND = get_request_param('BRAND'); // 브랜드명
            $SALE_YN = get_request_param('SALE_YN'); // 할인여부
            $OID_PRICE = get_request_param('OID_PRICE'); // 할인전 금액
            $SALE_PERCENT = get_request_param('SALE_PERCENT'); // 할인율
            $M_PRICE = get_request_param('M_PRICE'); // 금액
            $BADGE_CO = get_request_param('BADGE_CO'); // 뱃지
            $SEARCH_TEXT = get_request_param('SEARCH_TEXT'); // 검색내용
            $CONTENT_TEXT = isset($_POST['editor']) ? $_POST['editor'] : '';
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); // 파일 아이디
            $formData_del = get_request_param('formData_del'); // 업로드 삭제 id 값
            $key_val = get_request_param('key_val'); // 업로드 key값

            //작품옵션
            $OPTION = isset($_POST['option']) ? $_POST['option'] : "[]";
            $option_del = isset($_POST['option_del'])? $_POST['option_del']:"[]";

            gfn_isValidation(302, $TITLE, "제목");

            if (!empty($SDATE) && !empty($EDATE)) {
                if (date($SDATE) > date($EDATE)) {
                    gfn_isValidation(999, '', '기간이 종료일보다 큽니다.');
                }
            }

            if (empty($SDATE)) { //노출 시작날짜
                $SDATE = NULL;
            }

            if (empty($EDATE)) { //노출 종료날짜
                $EDATE = NULL;
            }

            if (empty($QUANTITY)) {
                $QUANTITY = 0;
            }

            if (empty($ORDER_NUMBER)) {
                $ORDER_NUMBER = 0;
            }

            if (empty($MAIN_YN)) {
                $MAIN_YN = 'N';
            }

            if (empty($INDEX_YN)) {
                $INDEX_YN = 'N';
            }

            if (empty($TITLE_YN)) {
                $TITLE_YN = 'N';
            }

            if (empty($SALE_YN)) {
                $SALE_YN = 'N';
            }

            $OPTION = json_decode($OPTION, true);
            $option_del = json_decode($option_del, true);

            $TYPE = implode(",", $TYPE);

            $TITLE = trim($TITLE); //제목

            if (!empty($M_PRICE)) {
                $M_PRICE = str_replace(",", "", $M_PRICE);
            } else {
                $M_PRICE = 0;
            }

            if (!empty($OID_PRICE)) {
                $OID_PRICE  = str_replace(",", "", $OID_PRICE);
            } else {
                $OID_PRICE = 0;
            }

            if (empty($SALE_PERCENT)) {
                $SALE_PERCENT = 0;
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
            $type = 'PRODUCT';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $key_value = '';
            
            if (!empty($formData_del) && !empty($ATTACH_FILE_ID)) {
                $formData_del = json_decode($formData_del, true);
                
                foreach ($formData_del as $val) {
                    gfn_file_upload("D", '', $ATTACH_FILE_ID, 3, $val);
                }
            }

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }
                
                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                        if (empty($ATTACH_FILE_ID)) {
                            $ATTACH_FILE_ID = 'ATTACH_'. $CATEGORY3_SEQ;
                        }

                        if ($key == "files") { // 멀티 이미지
                            $key_value = $key;
                            $arrRes = json_decode(multiple_file_upload($dir, "files"), true);
                        } else { // 썸네일, 메인 이미지
                            $key_value = $key;
                            $arrRes = json_decode(one_file_upload($dir, $key), true);
                        }

                        if (is_array($arrRes['file'])) {
                            foreach ($arrRes['file'] as $key => $val) {
                                if ($key_value == "ATTACH") {
                                    $ATTACH_GROUP = 1;
                                    $idx = 1;
                                } else if ($key_value == "ATTACH4") {
                                    $ATTACH_GROUP = 4;
                                    $idx = 1;
                                } else  if ($key_value == "files") {
                                    $idx = $key_val[$key];
                                    $ATTACH_GROUP = 3;
                                }

                                if ($key_value != "files") {
                                    $FIND_FILE = gfn_file_upload("T", '', $ATTACH_FILE_ID, $ATTACH_GROUP);

                                    if ($FIND_FILE > 0) {
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

            $values = array(
                  'OPTION_SEQ' => '' // 옵션 시퀀스
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TYPE_CD' => $TYPE // 구분
                , 'TITLE' => $TITLE // 제목
                , 'SUB_TITLE' => $SUB_TITLE // 상품 서브내용
                , 'SDATE' => $SDATE // 시작일
                , 'EDATE' => $EDATE // 종료일
                , 'LINK_URL' => $LINK_URL // 외부링크
                , 'CONTENT_TITLE' => $CONTENT_TITLE // 상세제목
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'INDEX_YN' => $INDEX_YN // 메인화면 노출여부
                , 'TITLE_YN' => $TITLE_YN // 메인노출여부
                , 'QUANTITY' => $QUANTITY // 수량
                , 'FRAME' => $FRAME // 프레임
                , 'BRAND' => $BRAND // 브랜드명
                , 'PRICE'=> $M_PRICE // 금액
                , 'SALE_YN' => $SALE_YN // 할인여부
                , 'OID_PRICE' => $OID_PRICE // 할인전금액
                , 'SALE_PERCENT' => $SALE_PERCENT // 할인율
                , 'BADGE_CO' => $BADGE_CO
                , 'SEARCH_TEXT' => $SEARCH_TEXT // 검색내용
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 파일
                , 'mod_user' => $_SESSION['adm']['name'] // 등록자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
           );

            $name_sql = "카테고리3 수정";
            $clefResult = $mysqldb->update($table, $values, ['CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            if (!empty($OPTION)) {
                foreach($OPTION as $index => $value) {
                    if ($value['dataType'] == "U") {

                        $values = array(
                              'OPTION_NAME' => $value['OPTION_NAME'] // 옵션명
                            , 'QUANTITY' => $value['OP_QUANTITY'] // 수량
                            , 'PRICE' => str_replace(",", "", $value['PRICE']) // 가격
                            , 'ORDER_NUMBER' => $value['OP_ORDER_NUMBER'] // 정렬
                            , 'MAIN_YN' => $value['OP_MAIN_YN'] // 노출여부
                            , 'SOLD_YN' => $value['OP_SOLD_YN'] // 품절여부
                            , 'mod_user' => $_SESSION['adm']['name'] // 등록자
                            , 'mod_ip' => $ip // 등록자 아이피
                            , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                        );

                        $name_sql = "작품옵션 수정";
                        $clefResult = $mysqldb->update('CATEGORY_OPTION', $values, ['OPTION_SEQ' => $value['optionSeq']], $name_sql);

                        if (!$clefResult->getResult()) {
                            gfn_isValidation(502);
                        }
                    } else if ($value['dataType'] == "I") {

                        $seq_name2 = 'OPTION'. $CATEGORY3_SEQ;

                        $sql = "
                             SELECT nextval('{$seq_name2}') as seq";

                        $name_sql = "작품옵션 시퀀스";
                        $clefResult = $mysqldb->get($sql, null, $name_sql);

                        $data2 = $clefResult->getResultSet();

                        $values = array(
                            'OPTION_SEQ' => $data2['seq'] // 작품 옵션 시퀀스
                          , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ // 작품 시퀀스
                          , 'OPTION_NAME' => $value['OPTION_NAME'] // 옵션명
                          , 'QUANTITY' => $value['OP_QUANTITY'] // 수량
                          , 'PRICE' => str_replace(",", "", $value['PRICE']) // 가격
                          , 'ORDER_NUMBER' => $value['OP_ORDER_NUMBER'] // 정렬
                          , 'MAIN_YN' => $value['OP_MAIN_YN'] // 노출여부
                          , 'SOLD_YN' => $value['OP_SOLD_YN'] // 품절여부
                          , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                          , 'reg_ip' => $ip // 등록자 아이피
                          , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
                       );

                        $name_sql = "작품 옵션 추가";
                        $clefResult = $mysqldb->insert('CATEGORY_OPTION', $values, $name_sql);

                        if (!$clefResult->getResult()) {
                            gfn_isValidation(501);
                        }
                    }
                }
            }

            if (!empty($option_del)) {
                foreach($option_del as $index => $value) {
                    if (!empty($value['optionSeq'])) {
                        $optionSeq = $value['optionSeq'];

                        $sql = "
                         DELETE FROM CATEGORY_OPTION
                          WHERE OPTION_SEQ = :pk";

                        $clefResult = $mysqldb->delete($sql, [':pk' => $optionSeq]);

                        if (!$clefResult->getResult()) {
                            gfn_isValidation(503);
                        }
                    }
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

            //작품옵션 삭제
            $sql = "
                 DELETE FROM CATEGORY_OPTION
                  WHERE CATEGORY3_SEQ = :pk";

            $name_sql = $CATEGORY3_SEQ." 작품옵션 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CATEGORY3_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            //작품 삭제
            $sql = "
                 DELETE FROM CATEGORY3
                  WHERE CATEGORY3_SEQ = :pk";

            $name_sql = $CATEGORY3_SEQ. " 카테고리3 삭제 ";

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