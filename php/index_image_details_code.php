<?php
/**
 * 파일명 : index_image_details_code.php
 * 내용 : 이미지 상세페이지
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
        $IMAGE_SEQ = get_request_param('seq', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET');
        $M_MAIN_YN = get_request_param('MAIN_YN', 'GET');

        $_db_TITLE = "";
        $_db_SUB_TITLE = "";
        $_db_LINK_URL = "";
        $_db_ORDER_NUMBER = 0;
        $_db_ATTACH_FILE_ID = "";

        $_db_MAIN_ATTACH_FILE_ID = "";
        $_db_MAIN_ATTACH_FILE_ID2 = "";
        $_db_MAIN_ATTACH_FILE_ID3 = "";

        $validation = "";

        if ($page_type == PAGE1) {
            $validation = "* ";
        }

        $_db_SDATE = date('Y-m-d');
        $_db_EDATE = date('Y-m-d', strtotime('+1 days'));

        $checked = 'checked'; // 노출여부
        $checked2 = ''; // 노출여부
        $table = 'IMAGE'; // 관리자 테이블

        if ($mode == 'MOD') {
            $sql = "
                 SELECT IMAGE_SEQ
                      , TITLE
                      , SUB_TITLE
                      , MAIN_YN
                      , VIDEO_YN
                      , SDATE
                      , EDATE
                      , ORDER_NUMBER
                      , LINK_URL
                      , ATTACH_FILE_ID
                   FROM {$table}
                  WHERE IMAGE_SEQ = :IMAGE_SEQ
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "이미지 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':IMAGE_SEQ' => $IMAGE_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 서브명
                $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
                $_db_VIDEO_YN = _check_var($data['VIDEO_YN']); // 영상여부
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_LINK_URL = _check_var($data['LINK_URL']); // 외부링크
                $_db_SDATE = _check_var($data['SDATE']); // 시작일
                $_db_EDATE = _check_var($data['EDATE']); // 종료일
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 호버 이미지

                if ($_db_MAIN_YN == "N") {
                    $checked = '';
                }

                if ($_db_VIDEO_YN == "Y") {
                    $checked2 = 'checked';
                }

                $file_html = "";

                if (!empty($_db_ATTACH_FILE_ID)) {
                    $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 2);

                    foreach ($file_list as $data) {
                        $_db_MAIN_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                    }

                    $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 4);

                    foreach ($file_list as $data) {
                        $_db_MAIN_ATTACH_FILE_ID2 = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                    }

                    $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 5);

                    foreach ($file_list as $data) {
                        $_db_MAIN_ATTACH_FILE_ID3 = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
                    }

                    $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 7);

                    foreach ($file_list as $data) {
                        $_db_attach_file_temp_name = _check_var($data['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                        $_db_attach_file_real_name = _check_var($data['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                        $_db_attach_file_path = _check_var($data['ATTACH_FILE_PATH']); // 경로 
                        $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;

                        $file_html .= <<<DIV
                                            <div style = 'margin: 5px 0 0 0;'>
                                                <img style='height:16px;' src='/adm/img/paper-clip.svg' alt='paper clip'>
                                                <a style='display:inline-block' href="{$path_File}" download="{$_db_attach_file_real_name}">{$_db_attach_file_real_name}</a>
                                            </div>
                                        DIV;
                    }
                }
            }
        }

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'MAIN_YN' => $M_MAIN_YN
            , 'TITLE' => $M_TITLE
        );

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>


