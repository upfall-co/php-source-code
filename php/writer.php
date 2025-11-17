<?php
/**
 * 파일명 : writer.php
 * 내용 : 작가 (등록, 수정, 삭제)
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
        $sub_type = get_request_param('sub_type');
        $M_TITLE = get_request_param('M_TITLE'); // 작가명
        $M_MAIN_YN = get_request_param('M_MAIN_YN'); // 노출여부

        $arrParams = array(
              'mp_seq' => $mp_seq
            , 'm_seq' => $m_seq
            , 'page_type' => $page_type
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);

        if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE1) {
                $url = "../adm/board/recruit_main.php?{$query_string}";
            }
        } else {
            $url = "../adm/board/writer_main.php?{$query_string}";
        }

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];
        $arrRtn['url'] = $url; 

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

            $TITLE = get_request_param('TITLE'); // 작가명, 카테고리명
            $MOBILE = get_request_param('MOBILE'); // 연락처
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $page_type = get_request_param('page_type'); // 페이지 타입
            $sub_type = get_request_param('sub_type'); // 서브 페이지 타입

            if (!empty($MOBILE))  {
                $MOBILE = trim($MOBILE); //연락처
            }

            $title_val = "";
            $seq_name = '';
            $msg = "";
            
            if ($page_type == PAGE1) {
                $seq_name = "ART";
                $title_val = "작가명";
                $msg = "이미 등록된 작가입니다.";
            } else if ($page_type == PAGE2) {
                $seq_name = "CATE1";
                $title_val = "카테고리명";
                $msg = "이미 등록된 카테고리입니다.";
            } else if ($page_type == PAGE3) {
                $seq_name = "RECRU";
                $title_val = "업종명";
                $msg = "이미 등록된 업종입니다.";
            }
 
            gfn_isValidation(302, $TITLE, $title_val);

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "카테고리 시퀀스";
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

            $table = 'CATEGORY1';

            $CATEGORY1_SEQ = $data['seq'];

            $arrValue = array();
            $where = '';

            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
            $arrValue[':TITLE'] = $TITLE;

            if ($page_type == PAGE3) {
                $where .= " AND SUB_TYPE = :SUB_TYPE";
                $arrValue[':SUB_TYPE'] = $sub_type;
            }

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE CATEGORY1_SEQ NOT IN (:CATEGORY1_SEQ)
                    AND TITLE = :TITLE
                    {$where}";

            $name_sql = $title_val. " 중복 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 0) {
                gfn_isValidation(999, "", $msg);
            }

            $values = array(
                  'CATEGORY1_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'SUB_TYPE' => $sub_type // 서브 페이지 타입
                , 'TITLE' => $TITLE // 작가명, 카테고리
                , 'MOBILE' => $MOBILE // 담당자 연락처
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 노출여부
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = $title_val. "추가";
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

            $CATEGORY1_SEQ = get_request_param('SEQ'); // 시퀀스
            $TITLE = get_request_param('TITLE'); // 작가명
            $MOBILE = get_request_param('MOBILE'); // 연락처
            $ORDER_NUMBER = get_request_param('ORDER_NUMBER'); // 정렬값
            $MAIN_YN = get_request_param('MAIN_YN'); // 노출여부
            $page_type = get_request_param('page_type'); // 페이지 타입
            $sub_type = get_request_param('sub_type'); // 서브 페이지 타입

            $title_val = "";
            $seq_name = '';
            $msg = "";
            
            if ($page_type == PAGE1) {
                $seq_name = "ART";
                $title_val = "작가명";
                $msg = "이미 등록된 작가입니다.";
            } else if ($page_type == PAGE2) {
                $seq_name = "CATE1";
                $title_val = "카테고리명";
                $msg = "이미 등록된 카테고리입니다.";
            } else if ($page_type == PAGE3) {
                $seq_name = "RECRU";
                $title_val = "업종명";
                $msg = "이미 등록된 업종입니다.";
            }
 
            gfn_isValidation(302, $TITLE, $title_val);

            if (empty($MAIN_YN)){
                $MAIN_YN = 'N';
            }

            if (empty($ORDER_NUMBER)){
                $ORDER_NUMBER = 0;
            }

            if (!empty($MOBILE)){
                $MOBILE = trim($MOBILE); //연락처
            }


            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'CATEGORY1';

            $arrValue = array();
            $where = '';

            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
            $arrValue[':TITLE'] = $TITLE;

            if ($page_type == PAGE3) {
                $where .= " AND SUB_TYPE = :SUB_TYPE";
                $arrValue[':SUB_TYPE'] = $sub_type;
            }

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE CATEGORY1_SEQ NOT IN (:CATEGORY1_SEQ)
                    AND TITLE = :TITLE
                    {$where}";

            $name_sql = $title_val." 중복 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total > 0) {
                gfn_isValidation(999, "", $msg);
            }

            $values = array(
                  'PAGE_TYPE' => $page_type // 페이지 타입
                , 'SUB_TYPE' => $sub_type // 서브 페이지 타입
                , 'TITLE' => $TITLE // 작가명
                , 'MOBILE' => $MOBILE // 담당자 연락처
                , 'MAIN_YN' => $MAIN_YN // 노출여부
                , 'ORDER_NUMBER' => $ORDER_NUMBER // 노출여부
                , 'mod_user' => $_SESSION['adm']['name'] // 수정자
                , 'mod_ip' => $ip // 등록자 아이피
                , 'mod_date' => date('Y-m-d H:i:s') // 수정날자
            );

            $name_sql = $title_val." 수정";
            $clefResult = $mysqldb->update($table, $values, ['CATEGORY1_SEQ' => $CATEGORY1_SEQ], $name_sql);

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

            $CATEGORY1_SEQ = get_request_param('SEQ'); // 시퀀스
            
            //작품 시퀀스 뽑기
            $sql = "
                 SELECT CATEGORY3_SEQ
                   FROM CATEGORY3
                  WHERE CATEGORY1_SEQ = :pk";

            $name_sql ="카테고리3 리스트 ";

            $clefResult = $mysqldb->select($sql, [':pk' => $CATEGORY1_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 시퀀스

                    //작품옵션 삭제
                    $sql = "
                    DELETE FROM CATEGORY_OPTION
                    WHERE CATEGORY3_SEQ = :pk";

                    $name_sql = $_db_CATEGORY3_SEQ." 카테고리3 옵션 삭제 ";

                    $clefResult = $mysqldb->delete($sql, [':pk' => $_db_CATEGORY3_SEQ], $name_sql);

                    if (!$clefResult->getResult()) {
                        gfn_isValidation(503);
                    }
                }
            }

            //작품 삭제
            $sql = "
                 DELETE FROM CATEGORY3
                  WHERE CATEGORY1_SEQ = :pk";

            $name_sql = $CATEGORY1_SEQ." 카테고리3 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CATEGORY1_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            //시리즈 삭제
            $sql = "
                 DELETE FROM CATEGORY2
                  WHERE CATEGORY1_SEQ = :pk";

            $name_sql = $CATEGORY1_SEQ." 카테고리2 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CATEGORY1_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            //작품 삭제
            $sql = "
                DELETE FROM CATEGORY1
                 WHERE CATEGORY1_SEQ = :pk";

            $name_sql = $CATEGORY1_SEQ." 카테고리1 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $CATEGORY1_SEQ], $name_sql);

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