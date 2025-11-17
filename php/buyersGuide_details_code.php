<?php
/**
 * 파일명 : buyersGuide_details_code.php
 * 내용 : 구매안내 상세페이지
 * 최초작성날짜 : 2023/08/30
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/30    V1.0
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
        $QUESTIONS_SEQ = get_request_param('seq', 'GET');
        $M_MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부
        $M_TYPE_CD = get_request_param('TYPE_CD','GET'); // 구분
        $M_ASKED = get_request_param('ASKED','GET'); // 질문

        $_db_TYPE_CD = "";
        $_db_ASKED = "";
        $_db_ANSWER = "";
        $_db_ORDER_NUMBER = 0;

        $checked = 'checked'; // 노출여부
        $table = 'QUESTIONS'; // 관리자 테이블

        if ($page_type == PAGE1) {
            $title_name = "구매안내"; // Copy, CSV, Excel, Print 제목
        } else if ($page_type == PAGE2) {
            $title_name = "FAQ"; // Copy, CSV, Excel, Print 제목
        }

        if ($mode == 'MOD') {
            $sql = "
                 SELECT QUESTIONS_SEQ
                      , TYPE_CD
                      , MAIN_YN
                      , ASKED
                      , ANSWER
                      , ORDER_NUMBER
                   FROM {$table}
                  WHERE QUESTIONS_SEQ = :QUESTIONS_SEQ
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "구매안내 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':QUESTIONS_SEQ' => $QUESTIONS_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_QUESTIONS_SEQ = _check_var($data['QUESTIONS_SEQ']); // 시퀀스
                $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
                $_db_TYPE_CD = _check_var($data['TYPE_CD']); // FAQ관련 COL002
                $_db_ASKED = _check_var($data['ASKED']); // 질문
                $_db_ANSWER = _check_var($data['ANSWER']); // 답변
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값

                if ($_db_MAIN_YN == "N") {
                    $checked = '';
                }
            }
        }
        
        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'MAIN_YN' => $M_MAIN_YN
            , 'TYPE_CD' => $M_TYPE_CD
            , 'ASKED' => $M_ASKED
        );

        $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>
