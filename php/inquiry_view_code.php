<?php
/**
 * 파일명 : inquiry_view_code.php
 * 내용 : 1:1 문의 상세보기
 * 최초작성날짜 : 2023/08/09
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/09    V1.0
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
                  , PURCHASE_SEQ
                  , PRODUCT_TITLE
                  , TYPE_CD
                  , ZCM_COM_NM('COL002',TYPE_CD) AS TYPE_CD_NM
                  , QUESTION_CD
                  , TITLE
                  , CONTENT_TEXT
                  , (SELECT D.CONTENT_TEXT FROM ANSWERS D WHERE M.ANSWERS_SEQ = D.ANSWERS_SEQ) AS ANSWERS_CONTENT
                  , ID
                  , IF(PASSWORD IS NOT NULL AND PASSWORD != '', 'Y', 'N') AS PASSWORD_YN
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
        $PURCHASE_SEQ = _check_var($data['PURCHASE_SEQ']); // 주문번호
        $PRODUCT_TITLE = _check_var($data['PRODUCT_TITLE']); // 문의작품
        $TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 문의분류
        $QUESTION_CD = _check_var($data['QUESTION_CD']); // 답변상태
        $TITLE = _check_var($data['TITLE']); // 문의제목
        $CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 문의제목
        $ANSWERS_CONTENT = _check_var($data['ANSWERS_CONTENT']); // 답변내용
        $ID = _check_var($data['ID']); // 아이디 체크
        $PASSWORD_YN = _check_var($data['PASSWORD_YN']); // 비밀번호 여부

        if ($PASSWORD_YN == "Y") {
            if ($_SESSION['INQ']['CHK'] == 'N') { // 비밀번호를 입력하고 들어왔는지 아닌지 확인 
                if (isset($_SESSION['MEMBER'])) {
                    if (!empty($_SESSION['MEMBER'])) { // 본인의 문의내역은 비밀번호 없이 접근이 가능하나 혹시모를 접근 권한 확인
                        if ($ID != $_SESSION['MEMBER']['ID']) {
                            dieAndErrorMove('잘못된 접근입니다.');
                        }
                    } else {
                        dieAndErrorMove('잘못된 접근입니다.');
                    }
                } else {
                    dieAndErrorMove('잘못된 접근입니다.');
                }
            }
        }

        if (!empty($MOBILE)) {
            $MOBILE = formatPhoneNumber($MOBILE);
        }

        if (!empty($CONTENT_TEXT)) {
            $CONTENT_TEXT = nl2br($CONTENT_TEXT);
        }

        $_SESSION['INQ']['CHK'] = 'N'; // 접근제한 방식 비밀번호 입력시 Y로 하여 사용자와달라도 문의내역에 접속가능 그이후 N처리하여 재접근 불가
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>