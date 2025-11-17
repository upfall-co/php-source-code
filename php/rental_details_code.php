<?php
/**
 * 파일명 : rental_details_code.php
 * 내용 : CONTACT 상세 페이지 코드
 * 최초작성날짜 : 2025/04/09
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준     2025/04/09    V1.0
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
        $RENTAL_SEQ = get_request_param('seq', 'GET');
        $M_COMPANY = get_request_param('COMPANY', 'GET'); // 회사(단체)명
        $M_AGENCY = get_request_param('AGENCY','GET'); // 대행사명
        $M_TYPE_CD = get_request_param('TYPE_CD','GET'); // 문의 구분

        $_db_TYPE_CD_NM = '';
        $_db_COMPANY = '';
        $_db_AGENCY = '';
        $_db_TITLE = '';
        $_db_NAME = '';
        $_db_MOBILE = '';
        $_db_EMAIL = '';
        $HOPE_DATE = '';
        $_db_CONTENT_TEXT = '';
        $_db_ATTACH_FILE_ID = '';
        $file_html = '';

        $table = 'RENTAL_INQUIRY'; // 관리자 테이블

        if ($mode == 'MOD') {
            $sql = "
                 SELECT RENTAL_SEQ
                      , ZCM_COM_NM('COL013', TYPE_CD) AS TYPE_CD_NM
                      , COMPANY
                      , AGENCY
                      , TITLE
                      , HSDATE
                      , HEDATE
                      , NAME
                      , MOBILE
                      , EMAIL
                      , CONTENT_TEXT
                      , ATTACH_FILE_ID
                   FROM {$table}
                  WHERE RENTAL_SEQ = :RENTAL_SEQ
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "대관문의 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':RENTAL_SEQ' => $RENTAL_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_RENTAL_SEQ = _check_var($data['RENTAL_SEQ']); // 시리즈 시퀀스
                $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 문의구분
                $_db_COMPANY = _check_var($data['COMPANY']); // 회사(단체)명
                $_db_AGENCY = _check_var($data['AGENCY']); // 대행사명
                $_db_TITLE = _check_var($data['TITLE']); // 행사명
                $_db_HSDATE = _check_var($data['HSDATE']); // 희망기간 - 시작일
                $_db_HEDATE = _check_var($data['HEDATE']); // 희망기간 - 종료일
                $_db_NAME = _check_var($data['NAME']); // 담당자명 (직함)
                $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 문의내용
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 문의내용

                if (!empty($_db_HSDATE) && !empty($_db_HEDATE)) {
                    $HOPE_DATE = $_db_HSDATE.' ~ '.$_db_HEDATE;
                }

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                if (!empty($_db_ATTACH_FILE_ID)) {
                    $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);
    
                    if (!empty($file_list)) { // 메인 이미지
                        foreach ($file_list as $list) {
                            $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                            $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                            $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                            $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
    
                            //디자인 나오면 적용
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
        }

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'COMPANY' => $M_COMPANY
            , 'AGENCY' => $M_AGENCY
            , 'TYPE_CD' => $M_TYPE_CD
        );

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>


