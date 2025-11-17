<?php
/**
 * 파일명 : Mypage_edit_code.php
 * 내용 : 마이페이지 - 개인정보수정
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/07    V1.0
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
        if (empty($_SESSION['MEMBER'])) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $table = 'MEMBER'; // 사용자

        $sql = "
             SELECT ID
                  , TYPE_CD
                  , NAME
                  , MOBILE
                  , EMAIL
                  , ADDRESS_ZIPCODE
                  , ADDRESS
                  , ADDRESSDETAIL
                  , BUSINESS_NAME
                  , BUSINESS_NUMBER
               FROM {$table}
              WHERE ID = :ID";

        $name_sql = "계정 상세정보 리스트";
        $clefResult = $mysqldb->get($sql, [':ID' => $_SESSION['MEMBER']['ID']], $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $_db_ID = _check_var($data['ID']); // 아이디
        $_db_TYPE_CD = _check_var($data['TYPE_CD']); // 구분
        $_db_NAME = _check_var($data['NAME']); // 이름
        $_db_MOBILE = _check_var($data['MOBILE']); // 전화번호
        $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
        $_db_ADDRESS_ZIPCODE = _check_var($data['ADDRESS_ZIPCODE']); // 우편번호
        $_db_ADDRESS = _check_var($data['ADDRESS']); // 주소
        $_db_ADDRESSDETAIL = _check_var($data['ADDRESSDETAIL']); // 상세주소
        $_db_BUSINESS_NAME = _check_var($data['BUSINESS_NAME']); // 사업자명
        $_db_BUSINESS_NUMBER = _check_var($data['BUSINESS_NUMBER']); // 사업자등록번호

        if (!empty($_db_MOBILE)) {
            $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>