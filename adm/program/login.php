<?php

/**
 * 파일명 : login.php
 * 내용 : 관리자 로그인 php 셋팅 모음
 * 최초작성날짜 : 2023/02/28
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment      
 * 김민성    2023/02/28     V1.0
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

$mysqldb    = new Pdo7();
$clefResult = new ClefResult();

$arrRtn = array(
    'code' => 500,
    'msg' => ''
);

try {
    //파라미터 체크
    $id = get_request_param('id');
    $pw = get_request_param('pw');
    $save_id = get_request_param('save_id');

    if (empty($id) || empty($pw)) {
        gfn_isValidation(999, "", "아이디 또는 비밀번호");
    }

    //변수 정리
    $table = 'adm';
    $id = trim($id);
    $pw = trim($pw);

    //DB
    $sql = "
         SELECT * 
           FROM {$table} 
          WHERE 1
            AND id = :id";

    $name_sql = "관리자 계정 확인";
    $clefResult = $mysqldb->select($sql, [':id' => $id], $name_sql);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $data = $clefResult->getResultSet();

    if (empty($data)) {
        gfn_isValidation(999, "", "조회되지 않는 아이디 입니다.");
    }

    $sql = "
         SELECT * 
              , ZCM_COM_NM('AD001', member_type) AS member_type_nm
           FROM {$table} 
          WHERE 1 
            AND id = :id 
            AND GETDECRYPT(pw, :key) = :pw 
          LIMIT 1";

    $name_sql = "로그인 값 확인";
    $clefResult = $mysqldb->get($sql, [':id' => $id, ':pw' => gfn_encrypted($pw), ':key' => $_SESSION['projectkey']], $name_sql);


    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $data = $clefResult->getResultSet();

    if (empty($data)) {
        gfn_isValidation(999, "", "비밀번호가 맞지 않습니다.");
    }

    //DB 변수 정리
    $_db_id = _check_var($data['id']);
    $_db_name = _check_var($data['name']);
    $_db_type_cd = _check_var($data['member_type']);
    $_db_type_nm = _check_var($data['member_type_nm']);
    $_db_menu_access = _check_var($data['menu_access']);
    $_db_position = _check_var($data['position']); // 직함
    $_db_tel = _check_var($data['mobile']); // 전화번호
    $_db_email = _check_var($data['email']); // 이메일

    /* 추후 필요
    if ($_db_type_cd != "SUPADM") {
        //DB
        $sql = "
             SELECT *
               FROM project_menu
              WHERE 1
                AND depth = 1
                AND parent_seq = (
                    SELECT seq
                      FROM project_menu
                     WHERE 1
                       AND seq IN ({$_db_menu_access})
                       AND depth = 0
                     ORDER BY sorting DESC
                     LIMIT 1)
              ORDER BY sorting DESC
              LIMIT 1; ";
        
        $name_sql = "사용자 메뉴리스트";
        $clefResult = $mysqldb->get($sql, null, $name_sql);
        $data = $clefResult->getResultSet();

        //DB 변수 정리
        $_db_m_seq  = _check_var($data['seq']);
        $_db_mp_seq = _check_var($data['parent_seq']);
        $_db_m_link = _check_var($data['link']);

        //경로 확인
        if (!empty($_db_m_link) && file_exists(__DIR__ ."/..{$_db_m_link}")) {
            $r_url = "..{$_db_m_link}?m_seq={$_db_m_seq}&mp_seq={$_db_mp_seq}";
        }
    }*/

    $arrParams = array(
        'mem_id' => $_db_id, 'name' => $_db_name
    );

    _log_login($arrParams);

    gfn_Option_Collection(); // 무통장 확인 collection
    gfn_Option_shop(); // 무통장 확인 shop
    gfn_Option_DLVY_END(); // 배송중 제품 확인

    //세션 정리
    $_SESSION['adm']['id'] = $_db_id;
    $_SESSION['adm']['name'] = $_db_name;
    $_SESSION['adm']['menu_access'] = $_db_menu_access;
    $_SESSION['adm']['member_type_cd'] = $_db_type_cd;
    $_SESSION['adm']['member_type_nm'] = $_db_type_nm;
    $_SESSION['adm']['position'] = $_db_position;
    $_SESSION['adm']['mobile'] = $_db_tel;
    $_SESSION['adm']['email'] = $_db_email;
    $_SESSION['adm']['first_menu'] = '';
    $_SESSION['adm']['page_type'] = '';
    $_SESSION['adm']['seq'] = '';
    $_SESSION['adm']['parent_seq'] = '';

    _check_admtype($_SESSION['adm']['member_type_cd']);

    if ($_SESSION['adm']['member_type_cd'] == "SUBADM") {
        // 현재 정의된 모든 상수 가져오기
        $constants = get_defined_constants(true)['user'];

        // 'PAGENM'으로 시작하는 상수들을 필터링
        $pageConstants = array_filter($constants, function ($constantName) {
          return strpos($constantName, 'PAGE') === 0  && strlen($constantName) <= 5 ;
        }, ARRAY_FILTER_USE_KEY);

        // 'PAGE'으로 시작하는 상수들을 반복
        $Page_cnt = 1;
        $PageHtml = "";
        $_arrValue = array();

        foreach ($pageConstants as $constantName => $constantValue) {
          // 원하는 동작 수행
          $PageHtml .= " WHEN M.PAGE_TYPE = '{$constantValue}' THEN ".$Page_cnt;
  
          // 동적으로 변수 생성
          ${'PAGE' . $Page_cnt} = $constantValue;
  
          $Page_cnt++;
        }

        $_arrMenuAccess = explode(',', $_SESSION['adm']['menu_access']);

        $_in = '';
        $_where = '';
        $_i = 0;

        foreach ($_arrMenuAccess as $val) {
            $_str_in = ':seq' . $_i++;
            $_in .= "{$_str_in},";

            $_arrValue[$_str_in] = $val;
        }

        $_in = rtrim($_in, ',');
        $_where .= "seq IN ({$_in})";

        $_sql = "
              SELECT *
                FROM (SELECT CASE {$PageHtml}
                             ELSE 0  
                              END AS PAGE_NUMBER
                           , M.PAGE_TYPE
                           , M.seq
                           , M.depth
                           , M.LINK
                           , M.parent_seq
                           , (SELECT nb.seq
                                FROM project_menu AS nb 
                               WHERE M.PAGE_TYPE = nb.PAGE_TYPE
                                 AND nb.{$_where}
                               ORDER BY nb.parent_seq , nb.sorting DESC 
                               LIMIT 1) AS asd
                           , @rank := CASE 
                                      WHEN M.seq = (SELECT nb.seq
                                                      FROM project_menu AS nb 
                                                     WHERE M.PAGE_TYPE = nb.PAGE_TYPE
                                                       AND nb.depth = (CASE
                                                                       WHEN (SELECT ab.seq
                                                                               FROM project_menu AS ab 
                                                                              WHERE M.PAGE_TYPE = ab.PAGE_TYPE
                                                                                AND asd = ab.parent_seq
                                                                                AND ab.{$_where}
                                                                              ORDER BY ab.parent_seq, ab.sorting DESC 
                                                                              LIMIT 1) IS NOT NULL THEN 1
                                                                      ELSE 0
                                                                       END)
                                                       AND nb.{$_where}
                                                       AND nb.parent_seq IN(asd, '0')
                                                     ORDER BY nb.seq, nb.sorting DESC
                                                     LIMIT 1) THEN 1
                                      ELSE 2 
                                       END AS Ranks
                           , @prev_page_type := PAGE_TYPE
                        FROM project_menu M, (SELECT @rank := 0, @prev_page_type := NULL) AS vars
                       WHERE PAGE_TYPE IN (SELECT distinct PAGE_TYPE
                                             FROM project_menu
                                            WHERE 1
                                              AND {$_where})
                         AND {$_where}
                         AND use_yn = 'Y'
                       ORDER BY PAGE_NUMBER, depth, sorting desc) A
               WHERE A.Ranks = 1
               ORDER BY A.PAGE_NUMBER";

        $name_sql = "사용자 메뉴";
        $clefResult = $mysqldb->get($_sql, $_arrValue, $name_sql);

        $_menu_list = $clefResult->getResultSet();

        $MIN_SEQ = _check_var($_menu_list['seq']);
        $LINK_URL = _check_var($_menu_list['LINK']);
        $page_type = _check_var($_menu_list['PAGE_TYPE']);
        $parent_seq = _check_var($_menu_list['parent_seq']);

        if (!empty($MIN_SEQ2)) {
            $MIN_SEQ = $MIN_SEQ2;
        }

        $_SESSION['adm']['first_menu'] = $LINK_URL;
        $_SESSION['adm']['page_type'] = $page_type;
        $_SESSION['adm']['seq'] = $MIN_SEQ;
        $_SESSION['adm']['parent_seq'] = $parent_seq;
    }

    if ($save_id == 'Y') {
        setcookie('save_id', $_db_id, time() + 2629800, '/'); //아이디 쿠키 저장(1년)
    }

    dieAndMsgReplaceMove('../', '로그인되었습니다.'); //성공

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    dieAndErrorMove($arrRtn['msg']);
}
