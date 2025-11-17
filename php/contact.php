<?php
/**
 * 파일명 : contact.php
 * 내용 : Contact (등록, 수정, 삭제)
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
        $M_TITLE = get_request_param('M_TITLE'); // 제목
        $M_MAIN_YN = get_request_param('M_MAIN_YN'); // 노출여부

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];
        $arrRtn['url'] = "../adm/board/contact_main.php?{$query_string}"; 

        dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
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
            $TITLE = get_request_param('TITLE'); // 제목
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $TITLE_EN = get_request_param('TITLE_EN'); // 영문
            $LINK_URL = get_request_param('LINK_URL'); // 링크
            $MOBILE = get_request_param('MOBILE'); // 연락처
            $EMAIL = get_request_param('EMAIL'); // 이메일
            $DATE_VALUE = get_request_param('DATE_VALUE'); // 시간정보

            gfn_isValidation(302, $TITLE, "제목");

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $seq_name = 'CNT';

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "CONTACT 시퀀스";
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

            $table = 'CONTACT';

            $values = array(
                  'CONTACT_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TITLE' => $TITLE // 제목
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'TITLE_EN' => $TITLE_EN // 영문
                , 'LINK_URL' => $LINK_URL // 링크
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'DATE_VALUE' => $DATE_VALUE // 날짜정보
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "CONTACT 추가";
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

            $CONTACT_SEQ = get_request_param('SEQ'); // 시퀀스
            $TITLE = get_request_param('TITLE'); // 제목
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $page_type = get_request_param('page_type'); // 페이지 타입
            $TITLE_EN = get_request_param('TITLE_EN'); // 영문
            $LINK_URL = get_request_param('LINK_URL'); // 링크
            $MOBILE = get_request_param('MOBILE'); // 연락처
            $EMAIL = get_request_param('EMAIL'); // 이메일
            $DATE_VALUE = get_request_param('DATE_VALUE'); // 시간정보

            gfn_isValidation(302, $TITLE, "제목");

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

            $table = 'CONTACT';

            $values = array(
                  'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TITLE' => $TITLE // 제목
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 정렬
                , 'TITLE_EN' => $TITLE_EN // 영문
                , 'LINK_URL' => $LINK_URL // 링크
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'DATE_VALUE' => $DATE_VALUE // 날짜정보
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = "CONTACT 수정";
            $clefResult = $mysqldb->update($table, $values, ['CONTACT_SEQ' => $CONTACT_SEQ], $name_sql);

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

            $CONTACT_SEQ = get_request_param('SEQ'); // 시퀀스

            $sql = "
                DELETE FROM CONTACT
                 WHERE CONTACT_SEQ = :pk";

            $name_sql = $CONTACT_SEQ." CONTACT 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CONTACT_SEQ], $name_sql);

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