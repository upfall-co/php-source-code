<?php
/**
 * 파일명 : writer_details_code.php
 * 내용 : 작가 관리
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
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
        $sub_type = get_request_param('sub_type', 'GET');
        $mode = get_request_param('mode', 'GET');
        $CATEGORY1_SEQ = get_request_param('seq', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET');
        $M_MAIN_YN = get_request_param('MAIN_YN', 'GET');

        $_db_CATEGORY1_SEQ = '';
        $_db_TITLE = '';
        $_db_MOBILE = '';
        $_db_MAIN_YN = '';
        $_db_ORDER_NUMBER = 0;

        $checked = 'checked'; // 노출여부
        $table = 'CATEGORY1'; // 관리자 테이블

        if ($page_type == PAGE1) {
            $title_name = "작가"; // Copy, CSV, Excel, Print 제목
            $title_val = "작가명";
        } else if ($page_type == PAGE2) {
            $title_name = "카테고리"; // Copy, CSV, Excel, Print 제목
            $title_val = "카테고리명";
        } else if ($page_type == PAGE3) {
            $title_name = "업종"; // Copy, CSV, Excel, Print 제목
            $title_val = "업종명";
        }

        if ($mode == 'MOD') {
            $arrValue = array();
            $where = '';

            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
            $arrValue[':PAGE_TYPE'] = $page_type;

            if ($page_type == PAGE3) {
                $where .= " AND SUB_TYPE = :SUB_TYPE";
                $arrValue[':SUB_TYPE'] = $sub_type;
            }

            $M_TITLE = get_request_param('TITLE', 'GET'); // 작가명
            $M_MAIN_YN = get_request_param('MAIN_YN', 'GET'); // 노출여부

            $sql = "
                 SELECT CATEGORY1_SEQ
                      , TITLE
                      , MOBILE
                      , MAIN_YN
                      , ORDER_NUMBER
                   FROM {$table}
                  WHERE CATEGORY1_SEQ = :CATEGORY1_SEQ
                    AND PAGE_TYPE = :PAGE_TYPE
                    {$where}";

            $name_sql = "카테고리1 상세정보";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (empty($data)) {
                dieAndErrorMove('잘못된 접근입니다.');
            }

            $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 시퀀스
            $_db_TITLE = _check_var($data['TITLE']); // 작가명, 카테고리1
            $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
            $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
            $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값

            if ($_db_MAIN_YN == "N") {
                $checked = '';
            }
        }

        $arrParams = array(
              'mp_seq' => $mp_seq
            , 'm_seq' => $m_seq
            , 'page_type' => $page_type 
            , 'sub_type' => $sub_type
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);
        if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE1) {
                $back_url = "recruit_main.php?{$query_string}";
            }
        } else {
            $back_url = "writer_main.php?{$query_string}";
        }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>


