<?php
/**
 * 파일명 : space_plan_main_code.php
 * 내용 : space 도면 이미지 내역
 * 최초작성날짜 : 2024/03/14
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2024/03/14     V1.0
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

        $_db_ATTACH_FILE_ID = "ATTACH_SPCPALN";

        $_db_MAIN_ATTACH_FILE_ID = "";
        $_db_MAIN_ATTACH_FILE_ID2 = "";
        $_db_MAIN_ATTACH_FILE_ID3 = "";
        $_db_MAIN_ATTACH_FILE_ID4 = "";
        $_db_MAIN_ATTACH_FILE_ID5 = "";
        $_db_MAIN_ATTACH_FILE_ID6 = "";

        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID);

        if (!empty($file_list)) {
            foreach ($file_list as $data) {
                $ATTACH_GROUP = _check_var($data['ATTACH_GROUP']); // 파일가상이름

                if ($ATTACH_GROUP == "1") { // B1
                    $_db_MAIN_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                } else if ($ATTACH_GROUP == "2") { // 1F
                    $_db_MAIN_ATTACH_FILE_ID2 = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                }  else if ($ATTACH_GROUP == "3") { // 2F
                    $_db_MAIN_ATTACH_FILE_ID3 = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                }  else if ($ATTACH_GROUP == "4") { // 3F
                    $_db_MAIN_ATTACH_FILE_ID4 = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                }  else if ($ATTACH_GROUP == "5") { // 4F
                    $_db_MAIN_ATTACH_FILE_ID5 = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                }  else if ($ATTACH_GROUP == "6") { // 별관
                    $_db_MAIN_ATTACH_FILE_ID6 = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                } 
            }
        }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
 ?>