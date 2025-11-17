<?php
/**
 * 파일명 : contact_details_code.php
 * 내용 : CONTACT 상세 페이지 코드
 * 최초작성날짜 : 2024/03/18
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성     2024/03/18    V1.0
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
        $CONTACT_SEQ = get_request_param('seq', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET'); // 제목
        $M_MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부
        $M_TITLE_YN = get_request_param('TITLE_YN','GET'); // 메인노출여부

        $_db_TITLE = '';
        $_db_ORDER_NUMBER = 0;
        $_db_TITLE = '';
        $_db_TITLE_EN = '';
        $_db_LINK_URL = '';
        $_db_MOBILE = '';
        $_db_EMAIL = '';
        $_db_DATE_VALUE = '';

        $checked = 'checked'; // 노출여부
        $table = 'CONTACT'; // 관리자 테이블

        if ($mode == 'MOD') {
            $disabled = 'disabled';

            $sql = "
                 SELECT CONTACT_SEQ
                      , TITLE
                      , TITLE_EN
                      , LINK_URL
                      , MOBILE
                      , EMAIL
                      , DATE_VALUE
                      , MAIN_YN
                      , ORDER_NUMBER
                   FROM {$table}
                  WHERE CONTACT_SEQ = :CONTACT_SEQ
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "CONTACT 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':CONTACT_SEQ' => $CONTACT_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_CONTACT_SEQ = _check_var($data['CONTACT_SEQ']); // 시리즈 시퀀스
                $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_TITLE_EN = _check_var($data['TITLE_EN']); // 제목 - 영문 
                $_db_LINK_URL = _check_var($data['LINK_URL']); // 링크
                $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                $_db_DATE_VALUE = _check_var($data['DATE_VALUE']); // 날짜정보

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

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>


