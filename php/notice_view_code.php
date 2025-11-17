<?php
/**
 * 파일명 : notice_view_code.php
 * 내용 : 공지사항 상세정보
 * 최초작성날짜 : 2023/08/10
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/10    V1.0
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
        $INFORM_SEQ = get_request_param('SEQ', 'GET');

        $table = 'INFORM';

        $sql = "
             SELECT TITLE
                  , CONTENT_TEXT
                  , DATE_FORMAT(reg_date, '%Y. %m. %d') AS reg_date_nm
               FROM {$table} 
              WHERE INFORM_SEQ = :INFORM_SEQ";

        $name_sql = "공지사항 상세정보";
        $clefResult = $mysqldb->get($sql, [':INFORM_SEQ' => $INFORM_SEQ], $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $_db_TITLE = _check_var($data['TITLE']); // 문의제목
        $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 문의제목
        $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 작성일
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>