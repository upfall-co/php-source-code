<?php
/**
 * 파일명 : buyersGuide.php
 * 내용 : 구매안내 (등록, 수정, 삭제)
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

    $arrRtn = array(
          'code' => 500
        , 'msg'  => ''
        , 'mode' => ''
        , 'url' => ''
    );

    try {
        //파라미터 정리
        $mode = get_request_param('mode');

        switch ($mode) {
            case 'INS' :
                $arrRes = row_insert();
                break;
            case 'MOD' :
                $arrRes = row_update();
                break;
            case 'DEL' :
                $arrRes = row_delete();
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }

        if ($arrRes['code'] != 200) {
            throw new Exception($arrRes['msg'], $arrRes['code']);
        }

        $m_seq = get_request_param('m_seq');
        $mp_seq = get_request_param('mp_seq');
        $page_type = get_request_param('page_type');
        $M_MAIN_YN = get_request_param('M_MAIN_YN'); // 노출여부
        $M_TYPE_CD = get_request_param('M_TYPE_CD'); // 구분
        $M_ASKED = get_request_param('M_ASKED'); // 질문

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'MAIN_YN' => $M_MAIN_YN
            , 'TYPE_CD' => $M_TYPE_CD
            , 'ASKED' => $M_ASKED
        );

        $query_string = http_build_query($arrParams);

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];
        $arrRtn['url'] = "../adm/board/buyersGuide_main.php?{$query_string}"; 

        dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
    //성공
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        dieAndErrorMove($arrRtn['msg']);
    }

    /**
     * name :row_insert
     * comment : 등록
     */
    function row_insert() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            //트랜잭션
            $mysqldb->link->beginTransaction();

            $page_type = get_request_param('page_type'); // 페이지 타입
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $TYPE_CD = get_request_param('TYPE_CD'); // 구분
            $ASKED = get_request_param('ASKED'); // 질문
            $ANSWER = get_request_param('ANSWER'); // 답변

            gfn_isValidation(301, $TYPE_CD, "구분");
            gfn_isValidation(302, $ASKED, "질문");
            gfn_isValidation(302, $ANSWER, "답변");

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $seq_name = 'FAQ'. $TYPE_CD;

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "구매안내 시퀀스";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            $data = $clefResult->getResultSet();

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'QUESTIONS';

            $values = array(
                  'QUESTIONS_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TYPE_CD' => $TYPE_CD // 구분
                , 'ASKED' => $ASKED // 답변
                , 'ANSWER' => $ANSWER // 질문
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "구매안내 추가";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '등록되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :row_update
     * comment : 수정
     */
    function row_update() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $QUESTIONS_SEQ = get_request_param('SEQ'); // 시퀀스
            $page_type = get_request_param('page_type'); // 페이지 타입
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $TYPE_CD = get_request_param('TYPE_CD'); // 구분
            $ASKED = get_request_param('ASKED'); // 질문
            $ANSWER = get_request_param('ANSWER'); // 답변

            gfn_isValidation(301, $TYPE_CD, "구분");
            gfn_isValidation(302, $ASKED, "질문");
            gfn_isValidation(302, $ANSWER, "답변");

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            
            $table = 'QUESTIONS';

            $values = array(
                  'ASKED' => $ASKED // 답변
                , 'ANSWER' => $ANSWER // 질문
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = "구매안내 수정";
            $clefResult = $mysqldb->update($table, $values, ['QUESTIONS_SEQ' => $QUESTIONS_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(502);
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '수정되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :row_delete
     * comment : 삭제
     */
    function row_delete() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
             'code' => 500
            , 'msg' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $QUESTIONS_SEQ = get_request_param('SEQ'); // 시퀀스

            $sql = "
                DELETE FROM QUESTIONS
                 WHERE QUESTIONS_SEQ = :pk";

            $name_sql = $QUESTIONS_SEQ."구매안내 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $QUESTIONS_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '삭제되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }
?>