<?php
/**
 * 파일명 : space_details_code.php
 * 내용 : 공간 관리페이지 코드
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

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    try {
        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $mode = get_request_param('mode', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $SPACE_SEQ = get_request_param('seq', 'GET');
        $M_TYPE_CD = get_request_param('TYPE_CD', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET');
        $M_MAIN_YN = get_request_param('MAIN_YN', 'GET');

        $_db_TYPE_CD = '';
        $_db_TITLE = '';
        $_db_DATE_TEXT = '';
        $_db_MOBILE = '';
        $_db_EMAIL = '';
        $_db_ORDER_NUMBER = 0;
        $_db_ATTACH_FILE_ID = '';

        $checked = 'checked'; // 노출여부
        $table = 'SPACE'; // 관리자 테이블

        $file_json = "";

        if ($mode == 'MOD') {
            $arrValue = array();
            $arrValue[':SPACE_SEQ'] = $SPACE_SEQ;
            $arrValue[':PAGE_TYPE'] = $page_type;

            $sql = "
                 SELECT SPACE_SEQ
                      , TYPE_CD
                      , TITLE
                      , DATE_TEXT
                      , MOBILE
                      , EMAIL
                      , MAIN_YN
                      , ORDER_NUMBER
                      , ATTACH_FILE_ID
                   FROM {$table}
                  WHERE SPACE_SEQ = :SPACE_SEQ
                    AND PAGE_TYPE = :PAGE_TYPE";

            $name_sql = "공간 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_SPACE_SEQ = _check_var($data['SPACE_SEQ']); // 공간 시퀀스
                $_db_TYPE_CD = _check_var($data['TYPE_CD']); // 층수
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_DATE_TEXT = _check_var($data['DATE_TEXT']); // 기간 텍스트
                $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 썸네일이미지

                $file_html = '';

                if ($_db_MAIN_YN == "N") {
                    $checked = '';
                }

                if (!empty($_db_ATTACH_FILE_ID)) {
                    $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);
                
                    if (!empty($file_list)) { // 작품 상세 멀티 이미지
                        foreach ($file_list as $list) {
                            $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                            $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                            $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                            $_db_attach_file_group = _check_var($list['ATTACH_GROUP']); // pk 1
                            $_db_attach_file_group_count = _check_var($list['ATTACH_GROUP_COUNT']); // pk2
                            $_db_attach_file_size = _check_var($list['ATTACH_FILE_SIZE']); // 파일사이즈
                            $_db_attach_file_type = _check_var($list['ATTACH_FILE_TYPE']); // 파일타입
                            $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;

                            $fileData[] = array(
                                'ATTACH_FILE_TEMP_NAME' => $_db_attach_file_temp_name
                                , 'ATTACH_FILE_REAL_NAME' => $_db_attach_file_real_name
                                , 'ATTACH_GROUP' => $_db_attach_file_group
                                , 'ATTACH_GROUP_COUNT' => $_db_attach_file_group_count
                                , 'ATTACH_FILE_SIZE' => $_db_attach_file_size
                                , 'ATTACH_FILE_TYPE' => $_db_attach_file_type
                                , 'PATH' => $path_File
                                , 'data_type' => 'N'
                            );
                        }
                        $file_json = json_encode($fileData);
                    }
                }
            }
        }

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'TYPE_CD' => $M_TYPE_CD
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>


