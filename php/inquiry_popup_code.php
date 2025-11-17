<?php
/**
 * 파일명 : inquiry_popup_code.php
 * 내용 : 1:1 문의 상세보기 팝업
 * 최초작성날짜 : 2023/11/21
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/11/21    V1.0
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
        $INQUIRY_SEQ = get_request_param('SEQ', 'GET');

        $table = 'INQUIRY';

        $sql = "
             SELECT NAME
                  , MOBILE
                  , EMAIL
                  , TYPE_CD
                  , ZCM_COM_NM('COL002',TYPE_CD) AS TYPE_CD_NM
                  , QUESTION_CD
                  , TITLE
                  , CONTENT_TEXT
                  , (SELECT D.CONTENT_TEXT FROM ANSWERS D WHERE M.ANSWERS_SEQ = D.ANSWERS_SEQ) AS ANSWERS_CONTENT
                  , DATE_FORMAT(reg_date, '%Y.%m.%d') AS reg_date_nm
               FROM {$table} M
              WHERE INQUIRY_SEQ = :INQUIRY_SEQ";

        $name_sql = "1:1문의 상세정보";
        $clefResult = $mysqldb->get($sql, [':INQUIRY_SEQ' => $INQUIRY_SEQ], $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $NAME = _check_var($data['NAME']); // 이름
        $MOBILE = _check_var($data['MOBILE']); // 연락처
        $EMAIL = _check_var($data['EMAIL']); // 이메일
        $TYPE_CD = _check_var($data['TYPE_CD']); // 문의분류
        $TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 문의분류
        $QUESTION_CD = _check_var($data['QUESTION_CD']); // 답변상태
        $TITLE = _check_var($data['TITLE']); // 문의제목
        $CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 문의제목
        $ANSWERS_CONTENT = _check_var($data['ANSWERS_CONTENT']); // 답변내용
        $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 문의날짜

        if (!empty($MOBILE)) {
            $MOBILE = formatPhoneNumber($MOBILE);
        }

        if (!empty($CONTENT_TEXT)) {
            $CONTENT_TEXT = nl2br($CONTENT_TEXT);
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>