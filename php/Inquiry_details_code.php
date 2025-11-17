<?php
/**
 * 파일명 : Inquiry_details_code.php
 * 내용 : 1:1문의 관리
 * 최초작성날짜 : 2023/08/09
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/09     V1.0
 * 김민성    2023/11/21    샵기능추가
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
        $INQUIRY_SEQ = get_request_param('seq', 'GET');
        $M_TYPE_CD = get_request_param('TYPE_CD', 'GET');
        $M_NAME = get_request_param('NAME', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET');
        $M_QUESTION_CD = get_request_param('QUESTION_CD', 'GET');
        $M_start_date = get_request_param('start_date', 'GET');
        $M_end_date = get_request_param('end_date', 'GET');

        $_db_ID = '';
        $_db_NAME = '';
        $_db_MOBILE = '';
        $_db_EMAIL = '';
        $_db_PURCHASE_SEQ = '';
        $_db_PRODUCT_TITLE = '';
        $_db_TYPE_CD_NM = '';
        $_db_TITLE = '';
        $_db_CONTENT_TEXT = '';

        $ANSWERS_SEQ = '';
        $_db_AN_CONTENT_TEXT = '';

        $table = 'INQUIRY'; // 관리자 테이블

        if ($mode == 'MOD') {
            $disabled = 'disabled';

            $sql = "
                 SELECT M.INQUIRY_SEQ
                      , M.QUESTION_CD
                      , M.ID
                      , M.NAME
                      , ZCM_COM_NM('COL002', M.TYPE_CD) AS TYPE_CD_NM
                      , M.MOBILE
                      , M.EMAIL
                      , M.PURCHASE_SEQ
                      , M.PRODUCT_TITLE
                      , M.TITLE
                      , M.CONTENT_TEXT
                      , M.ANSWERS_SEQ
                      , D.CONTENT_TEXT AS AN_CONTENT_TEXT
                      , M.ATTACH_FILE_ID
                   FROM {$table} M
                   LEFT OUTER JOIN ANSWERS D ON M.ANSWERS_SEQ = D.ANSWERS_SEQ
                  WHERE M.INQUIRY_SEQ = :INQUIRY_SEQ
                    AND M.PAGE_TYPE = '{$page_type}'";

            $name_sql = "공지사항 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':INQUIRY_SEQ' => $INQUIRY_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_INQUIRY_SEQ = _check_var($data['INQUIRY_SEQ']); // 시리즈 시퀀스
                $_db_ID = _check_var($data['ID']); // 회원 ID
                $_db_NAME = _check_var($data['NAME']); // 이름
                $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                $_db_PURCHASE_SEQ = _check_var($data['PURCHASE_SEQ']); // 주문번호
                $_db_PRODUCT_TITLE = _check_var($data['PRODUCT_TITLE']); // 문의작품
                $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 문의타입
                $_db_TITLE = _check_var($data['TITLE']); // 문의제목
                $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 문의내용
                $_db_ANSWERS_SEQ = _check_var($data['ANSWERS_SEQ']); // 답변 시퀀스
                $_db_AN_CONTENT_TEXT = _check_var($data['AN_CONTENT_TEXT']); // 답변 내용
                $_db_QUESTION_CD = _check_var($data['QUESTION_CD']); // 답변 상태
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                /*if (!empty($_db_CONTENT_TEXT)) {
                    $_db_CONTENT_TEXT = nl2br($_db_CONTENT_TEXT);
                }*/

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                if (!empty($_db_QUESTION_CD)) {
                    $type_gb = $_db_QUESTION_CD;
                }

                if ($page_type == PAGE2) {
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
            , 'NAME' => $M_NAME
            , 'TITLE' => $M_TITLE
            , 'QUESTION_CD' => $M_QUESTION_CD
            , 'start_date' => $M_start_date
            , 'end_date' => $M_end_date
        );

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>


