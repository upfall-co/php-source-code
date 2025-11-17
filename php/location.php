<?php
/**
 * 파일명 : faq.php
 * 내용 : FAQ (등록, 수정, 삭제)
 * 최초작성날짜 : 2024/06/07
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준    2024/06/07     V1.0
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
        global $page_type;

        $mode = get_request_param('mode');
        $page_type = get_request_param('page_type');

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

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
        );

        $query_string = http_build_query($arrParams);

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];
        $arrRtn['url'] = "../adm/board/home_index_location_main.php?{$query_string}";

        dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
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
            $mysqldb->link->beginTransaction();

            global $page_type;

            $M_ADDRESS = get_request_param('M_ADDRESS'); // 제1 주소
            $M_NAVER_LINK = get_request_param('M_NAVER_LINK'); //제1 네이버 맵 링크
            $M_KAKAO_LINK = get_request_param('M_KAKAO_LINK'); //제1 카카오 맵 링크
            $D_ADDRESS = get_request_param('D_ADDRESS'); // 제2 주소
            $D_NAVER_LINK = get_request_param('D_NAVER_LINK'); //제2 네이버 맵 링크
            $D_KAKAO_LINK = get_request_param('D_KAKAO_LINK'); //제2 카카오 맵 링크
            $OPERATE = get_request_param('OPERATE'); //운영
            $PARKKING = get_request_param('PARKKING'); //주차
            $FACILITIES = get_request_param('FACILITIES'); //시설
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); //파일아이디
            
            gfn_isValidation(302, $M_ADDRESS, '제1 주소');
            gfn_isValidation(302, $D_ADDRESS, '제2 주소');

            $ip = $_SERVER['REMOTE_ADDR'];
            $table = 'LOCATION';
            $type = 'FILE';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $key_value = '';
            $ATTACH_FILE_ID = "";

            $seq_name = 'LOCA';

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "LOCATION 시퀀스";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            $data = $clefResult->getResultSet();

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
                                $idx = 1;
                                $ATTACH_GROUP = 1;
                            }

                            gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                        }
                    }
                }
            }


            $values = array(
                  'LOCATION_SEQ' => $data['seq']
                , 'PAGE_TYPE' => $page_type
                , 'M_ADDRESS' => $M_ADDRESS
                , 'M_NAVER_LINK' => $M_NAVER_LINK
                , 'M_KAKAO_LINK' => $M_KAKAO_LINK
                , 'D_ADDRESS' => $D_ADDRESS
                , 'D_NAVER_LINK' => $D_NAVER_LINK
                , 'D_KAKAO_LINK' => $D_KAKAO_LINK
                , 'OPERATE' => $OPERATE
                , 'PARKKING' => $PARKKING
                , 'FACILITIES' => $FACILITIES
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID
                , 'reg_user' => $_SESSION['adm']['name'] //등록자
                , 'reg_ip' => $ip // 등록 ip
                , 'reg_date' => date('Y-m-d H:i:s') //등록날짜
            );

            $name_sql='LOCATION 추가';
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult) {
                gfn_isValidation(501);
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '추가되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e -> getCode();
            $arrRtn['msg'] = $e -> getMessage();
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

            global $page_type;

            $LOCATION_SEQ = get_request_param('seq'); //LOCATION 시퀀스
            $M_ADDRESS = get_request_param('M_ADDRESS'); // 제1 주소
            $M_NAVER_LINK = get_request_param('M_NAVER_LINK'); //제1 네이버 맵 링크
            $M_KAKAO_LINK = get_request_param('M_KAKAO_LINK'); //제1 카카오 맵 링크
            $D_ADDRESS = get_request_param('D_ADDRESS'); // 제2 주소
            $D_NAVER_LINK = get_request_param('D_NAVER_LINK'); //제2 네이버 맵 링크
            $D_KAKAO_LINK = get_request_param('D_KAKAO_LINK'); //제2 카카오 맵 링크
            $OPERATE = get_request_param('OPERATE'); //운영
            $PARKKING = get_request_param('PARKKING'); //주차
            $FACILITIES = get_request_param('FACILITIES'); //시설
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); //파일아이디

            gfn_isValidation(302, $M_ADDRESS, '제1 주소');
            gfn_isValidation(302, $D_ADDRESS, '제2 주소');

            $arrValue = array();
            $table = 'LOCATION';
            $ip = $_SERVER['REMOTE_ADDR'];
            $type = 'FILE';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }

                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                        if (empty($ATTACH_FILE_ID)) {
                            $ATTACH_FILE_ID = 'ATTACH_'. $LOCATION_SEQ;
                        }

                        $key_value = $key;
                        $arrRes = json_decode(one_file_upload($dir, $key), true);

                        if (is_array($arrRes['file'])) {
                            foreach ($arrRes['file'] as $key => $val) {
                                if ($key_value == "ATTACH") {
                                    $ATTACH_GROUP = 1;
                                    $idx = 1;
                                }

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
                  'PAGE_TYPE' => $page_type
                , 'M_ADDRESS' => $M_ADDRESS
                , 'M_NAVER_LINK' => $M_NAVER_LINK
                , 'M_KAKAO_LINK' => $M_KAKAO_LINK
                , 'D_ADDRESS' => $D_ADDRESS
                , 'D_NAVER_LINK' => $D_NAVER_LINK
                , 'D_KAKAO_LINK' => $D_KAKAO_LINK
                , 'OPERATE' => $OPERATE
                , 'PARKKING' => $PARKKING
                , 'FACILITIES' => $FACILITIES
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 수정자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = 'faq 수정';
            $clefResult = $mysqldb->update($table, $values, ['LOCATION_SEQ' => $LOCATION_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '수정되었습니다.';
        } catch (Exception $e){
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e -> getCode();
            $arrRtn['msg'] = $e -> getMessage();
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

            $SEQ = get_request_param('SEQ');
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID');

            $sql = "
                 DELETE FROM LOCATION
                  WHERE 1
                    AND LOCATION_SEQ = :pk";

            $name_sql = $SEQ."LOCATION 삭제";
            $clefResult = $mysqldb->delete($sql, [':pk' => $SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '삭제되었습니다.';
        } catch (Exception $e){
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e -> getCode();
            $arrRtn['msg'] = $e -> getMessage();
        } finally {
            return $arrRtn;
        }
    }
?>