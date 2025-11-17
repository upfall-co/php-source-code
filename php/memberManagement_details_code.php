<?php
/**
 * 파일명 : memberManagement_details_code.php
 * 내용 : 회원 상세 페이지
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
        $page_type = get_request_param('page_type', 'GET');
        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $mode = get_request_param('mode', 'GET');
        $SEQ = get_request_param('seq', 'GET');
        $M_TYPE_CD = get_request_param('TYPE_CD', 'GET');
        $M_ID = get_request_param('ID', 'GET');
        $M_NAME = get_request_param('NAME', 'GET');
        $M_MOBILE = get_request_param('MOBILE', 'GET');
        $M_start_date = get_request_param('start_date', 'GET');
        $M_end_date = get_request_param('end_date', 'GET');

        if (empty($SEQ)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $table = 'MEMBER'; // 회원 테이블

        $sql = "
             SELECT ID
                  , TYPE_CD
                  , ZCM_COM_NM('AD013', TYPE_CD) AS TYPE_CD_NM
                  , NAME
                  , MOBILE
                  , EMAIL
                  , ADDRESS_ZIPCODE
                  , ADDRESS
                  , ADDRESSDETAIL
                  , IF((ACCESS_TOKEN_KAKAO IS NOT NULL AND ACCESS_TOKEN_KAKAO != '') OR (ACCESS_TOKEN_NAVER IS NOT NULL AND ACCESS_TOKEN_NAVER != ''), 'Y', 'N') AS TOKEN_STATUS
                  , CASE WHEN ACCESS_TOKEN_KAKAO IS NOT NULL AND ACCESS_TOKEN_KAKAO != '' AND ACCESS_TOKEN_NAVER IS NOT NULL AND ACCESS_TOKEN_NAVER != '' THEN CONCAT('카카오, 네이버')
                         WHEN ACCESS_TOKEN_KAKAO IS NOT NULL AND ACCESS_TOKEN_KAKAO != '' THEN '카카오'
                         WHEN ACCESS_TOKEN_NAVER IS NOT NULL AND ACCESS_TOKEN_NAVER != '' THEN '네이버'
                         ELSE ''
                     END AS TOKEN_PROVIDERS
                  , BUSINESS_NAME
                  , BUSINESS_NUMBER
                  , reg_ip
                  , reg_date
               FROM {$table}
              WHERE ID = :ID";

        $name_sql = "계정 상세정보 리스트";
        $clefResult = $mysqldb->get($sql, [':ID' => $SEQ], $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $_db_ID = _check_var($data['ID']); // 아이디
        $_db_TYPE_CD = _check_var($data['TYPE_CD']); // 회원구분
        $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 회원구분명
        $_db_NAME = _check_var($data['NAME']); // 이름
        $_db_MOBILE = _check_var($data['MOBILE']); // 전화번호
        $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
        $_db_ADDRESS_ZIPCODE = _check_var($data['ADDRESS_ZIPCODE']); // 우편번호
        $_db_ADDRESS = _check_var($data['ADDRESS']); // 주소
        $_db_ADDRESSDETAIL = _check_var($data['ADDRESSDETAIL']); // 상세주소
        $_db_TOKEN_STATUS = _check_var($data['TOKEN_STATUS']); // SNS 가입여부
        $_db_TOKEN_PROVIDERS = _check_var($data['TOKEN_PROVIDERS']); // SNS 가입경로
        $_db_BUSINESS_NAME = _check_var($data['BUSINESS_NAME']); // 사업자명
        $_db_BUSINESS_NUMBER = _check_var($data['BUSINESS_NUMBER']); // 사업자등록번호
        $_db_reg_ip = _check_var($data['reg_ip']); // 가입 아이피
        $_db_reg_date = _check_var($data['reg_date']); // 가입일

        if (!empty($_db_MOBILE)) {
            $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
        }

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'TYPE_CD' => $M_TYPE_CD
            , 'ID' => $M_ID
            , 'NAME' => $M_NAME
            , 'MOBILE' => $M_MOBILE
            , 'start_date' => $M_start_date
            , 'end_date' => $M_end_date
      );

      $query_string = http_build_query($arrParams);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>