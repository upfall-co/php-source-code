<?php
/**
 * 파일명 : home_index_location_main_code.php
 * 내용 : index_location 내역 code
 * 최초작성날짜 : 2024/04/25
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준     2024/04/25    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
          'code' => 500
        , 'msg' => ''
    );

    try {
        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');

        $mode = "";
        $_db_LOCATION_SEQ = "";
        $_db_M_ADDRESS = "";
        $_db_M_NAVER_LINK = "";
        $_db_M_KAKAO_LINK = "";
        $_db_D_ADDRESS = "";
        $_db_D_NAVER_LINK = "";
        $_db_D_KAKAO_LINK = "";
        $_db_OPERATE = "";
        $_db_PARKKING = "";
        $_db_FACILITIES = "";
        $_db_ATTACH_FILE_ID = "";
        $_db_MAIN_ATTACH_FILE_ID = "";

        $arrValue = array();
        $arrValue[':PAGE_TYPE'] = $page_type;

        $table = 'LOCATION'; // 공통테이블

        $sql = "
             SELECT *
               FROM {$table}
              WHERE PAGE_TYPE = :PAGE_TYPE";

        $name_sql = "LOCATION 개수 확인";
        $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $total = $clefResult->getCount();

        if ($total) {
            $mode = 'MOD';
        } else {
            $mode = 'INS';
        }

        $sql = "
             SELECT LOCATION_SEQ
                  , M_ADDRESS
                  , M_NAVER_LINK
                  , M_KAKAO_LINK
                  , D_ADDRESS
                  , D_NAVER_LINK
                  , D_KAKAO_LINK
                  , OPERATE
                  , PARKKING
                  , FACILITIES
                  , ATTACH_FILE_ID
               FROM {$table}
              WHERE PAGE_TYPE = :PAGE_TYPE";

        $name_sql = "HOME_INDEX_LOCATION 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();

        if (!empty($list)) {
            foreach ($list as $data) {
                $_db_LOCATION_SEQ = _check_var($data['LOCATION_SEQ']); // LOCATION 시퀀스
                $_db_M_ADDRESS = _check_var($data['M_ADDRESS']); // 제1 주소
                $_db_M_NAVER_LINK = _check_var($data['M_NAVER_LINK']); // 제1 네이버 링크
                $_db_M_KAKAO_LINK = _check_var($data['M_KAKAO_LINK']); // 제1 카카오 링크
                $_db_D_ADDRESS = _check_var($data['D_ADDRESS']); // 제2 주소
                $_db_D_NAVER_LINK = _check_var($data['D_NAVER_LINK']); // 제2 네이버 링크
                $_db_D_KAKAO_LINK = _check_var($data['D_KAKAO_LINK']); // 제2 네이버 링크
                $_db_OPERATE = _check_var($data['OPERATE']); // 운영
                $_db_PARKKING = _check_var($data['PARKKING']); // 주차
                $_db_FACILITIES = _check_var($data['FACILITIES']); // 시설
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                if (!empty($_db_ATTACH_FILE_ID)) {
                    $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                    if (!empty($file_list)) { // 메인 이미지
                        foreach ($file_list as $list) {
                            $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                            $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                            $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 

                            $_db_MAIN_ATTACH_FILE_ID = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                        }
                    }
                }
            }
        }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
