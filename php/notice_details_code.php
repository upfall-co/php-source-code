<?php
/**
 * 파일명 : notice_details_code.php
 * 내용 : 공지사항 관리
 * 최초작성날짜 : 2023/08/09
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/09     V1.0
 * 전상범     2023/08/09    개발
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
        $INFORM_SEQ = get_request_param('seq', 'GET');
        $TYPE_CD = "NOL"; // home 기타 구분을 위해 사용
        $M_TITLE = get_request_param('TITLE', 'GET'); // 제목
        $M_MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부
        $M_TITLE_YN = get_request_param('TITLE_YN','GET'); // 메인노출여부

        $_db_TITLE = '';
        $_db_ORDER_NUMBER = 0;
        $_db_CONTENT_TEXT = '';
        $_db_OPTION_TEXT = '';

        $checked = 'checked'; // 노출여부
        $checked2 = ''; // 메인노출여부
        $table = 'INFORM'; // 관리자 테이블

        if ($mode == 'MOD') {
            $disabled = 'disabled';

            $sql = "
                 SELECT INFORM_SEQ
                      , TITLE
                      , TITLE_YN
                      , MAIN_YN
                      , ORDER_NUMBER
                      , CONTENT_TEXT
                      , OPTION_TEXT
                   FROM {$table}
                  WHERE INFORM_SEQ = :INFORM_SEQ
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "공지사항 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':INFORM_SEQ' => $INFORM_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_INFORM_SEQ = _check_var($data['INFORM_SEQ']); // 시리즈 시퀀스
                $_db_TITLE_YN = _check_var($data['TITLE_YN']); // 메인노출여부
                $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
                $_db_OPTION_TEXT = _check_var($data['OPTION_TEXT']); // 기타내용

                if ($_db_MAIN_YN == "N") {
                    $checked = '';
                }
            }
        }

        $arrParams = array(
            'm_seq' => $m_seq
          , 'mp_seq' => $mp_seq
          , 'page_type' => $page_type
          , 'TITLE' => $M_TITLE
          , 'MAIN_YN' => $M_MAIN_YN
        );

        if ($page_type == PAGE3) {
            $arrParams['TITLE_YN'] = $M_TITLE_YN;
        }

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>


