<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

//관리자 체크
_check_admin();

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

$arrRtn = array(
    'code'  => 500,
    'msg'   => '',
    'mode'  => '',
    'url'   => '',
);

try {
    //파라미터 정리
    $mode = get_request_param('mode');

    switch ($mode) {
        case 'INS' :
            $arrRes = ufn_AD_INS(); // 관리자 계정 등록
            break;
        case 'MOD' :
            $arrRes = ufn_AD_MOD(); // 관리자 계정 수정
            break;
        case 'DEL' :
            $arrRes = ufn_AD_DEL(); // 관리자 계정 삭제
            break;
        default :
            throw new Exception('잘못된 접근입니다.');
    }

    if ($arrRes['code'] != 200) {
        throw new Exception($arrRes['msg'], $arrRes['code']);
    }

    $R_stat = get_request_param('R_stat');
    $m_seq = get_request_param('m_seq');
    $mp_seq = get_request_param('mp_seq');
    $page_type = get_request_param('page_type');
    $M_ID = get_request_param('M_ID');
    $M_NAME = get_request_param('M_NAME');
    $M_MOBILE = get_request_param('M_MOBILE');

    $arrParams = array(
          'm_seq' => $m_seq
        , 'mp_seq' => $mp_seq
        , 'page_type' => $page_type
        , 'ID' => $M_ID
        , 'NAME' => $M_NAME
        , 'MOBILE' => $M_MOBILE
    );

    $query_string = http_build_query($arrParams);

    //성공
    $arrRtn['code'] = $arrRes['code'];
    $arrRtn['msg']  = $arrRes['msg'];

    if ($R_stat) {
        $arrRtn['url'] = "../";
    } else {
        $arrRtn['url'] = "/adm/board/adm_main.php?". $query_string;
    }

    dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    dieAndErrorMove($arrRtn['msg']);

}

/**
 * name :ufn_AD_INS
 * comment : 관리자 계정 등록
 */
function ufn_AD_INS() {
    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
          'code' => 500
        , 'msg' => ''
        , 'url' => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $id         = get_request_param('id');
        $pw         = get_request_param('pw');
        $pw2        = get_request_param('pw2');
        $name       = get_request_param('name');
        $mobile1    = get_request_param('mobile1');
        $mobile2    = get_request_param('mobile2');
        $mobile3    = get_request_param('mobile3');
        $email      = get_request_param('email');
        $arrMenuSeq = isset($_POST['menu_seq']) ? $_POST['menu_seq'] : array();

        //파라미터 체크
        gfn_isValidation(302, $id, "아이디");
        gfn_isValidation(302, $name, "이름");
        gfn_isValidation(302, $pw, "비밀번호");

        if (!empty($pw) && !empty($pw2)) {
            if ($pw != $pw2) {
                gfn_isValidation(999, "", "비밀번호가 일치하지 않습니다.");
            }
        }

        //trim
        $id = trim($id);
        $name = trim($name);
        $mobile2 = trim($mobile2);
        $mobile3 = trim($mobile3);
        $email = trim($email);

        //변수 정리
        $ip             = $_SERVER['REMOTE_ADDR'];
        $table          = 'adm';
        $mobile         = "{$mobile1}{$mobile2}{$mobile3}";
        $str_menu_seq   = implode(',', $arrMenuSeq);

        $arrValue = array();
        $arrValue[':id'] = $id;

        $sql = "
             SELECT *
               FROM {$table}
              WHERE id = :id";

        $name_sql = "아이디 중복 확인";
        $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $total = $clefResult->getCount();

        if ($total > 0) {
            gfn_isValidation(999, "", "중복된 아이디가 존재합니다.");
        }

        $values         = array(
             'id'          => $id
           , 'name'          => $name
           , 'mobile'        => $mobile
           , 'menu_access' => $str_menu_seq
           , 'email'         => $email
           , 'member_type' => 'SUBADM' // 병원 권한 dlatl
           , 'reg_user' => $_SESSION['adm']['name'] //등록자
           , 'reg_ip' => $ip // 등록자 아이피
           , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
        );

        if (!empty($pw) && !empty($pw2)) {
            $pw             = trim($pw);
            $values['pw']   = gfn_getEncrypt(gfn_encrypted($pw), $_SESSION['projectkey']);
        }

        //DB
        $name_sql = "피크닉 계정 추가";
        $clefResult = $mysqldb->insert($table, $values, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(501);
        }

        //성공
        $mysqldb->link->commit();
        $arrRtn['code'] = 200;
        $arrRtn['msg']  = '등록되었습니다.';
    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();
    } finally {
        return $arrRtn;
    }
}

/**
 * name :ufn_AD_MOD
 * comment : 관리자 계정 수정
 */
function ufn_AD_MOD() {
    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
          'code' => 500
        , 'msg' => ''
        , 'url' => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $R_stat         = get_request_param('R_stat');
        $id         = get_request_param('R_ID');
        $pw         = get_request_param('pw');
        $pw2        = get_request_param('pw2');
        $name       = get_request_param('name');
        $mobile1    = get_request_param('mobile1');
        $mobile2    = get_request_param('mobile2');
        $mobile3    = get_request_param('mobile3');
        $email      = get_request_param('email');
        $arrMenuSeq = isset($_POST['menu_seq']) ? $_POST['menu_seq'] : array();

        //파라미터 체크
        gfn_isValidation(302, $name, "이름");

        if (!empty($pw) && !empty($pw2)) {
            if ($pw != $pw2) {
                gfn_isValidation(999, "", "비밀번호가 일치하지 않습니다.");
            }
        }

        //trim
        $name           = trim($name);
        $mobile2        = trim($mobile2);
        $mobile3        = trim($mobile3);
        $email          = trim($email);

        //변수 정리
        $ip             = $_SERVER['REMOTE_ADDR'];
        $table          = 'adm';
        $mobile         = "{$mobile1}{$mobile2}{$mobile3}";
        $str_menu_seq   = implode(',', $arrMenuSeq);

        $values         = array(
             'name'          => $name
           , 'mobile'        => $mobile
           , 'email'         => $email
           , 'mod_user'      => $_SESSION['adm']['name']
           , 'mod_ip'        => $ip
           , 'mod_date'      => date('Y-m-d H:i:s')
        );

        if ($R_stat != "direct") {
            $values['menu_access'] = $str_menu_seq;
        }

        if (!empty($pw) && !empty($pw2)) {
            $pw             = trim($pw);
            $values['pw']   = gfn_getEncrypt(gfn_encrypted($pw), $_SESSION['projectkey']);
        }

        //DB
        $clefResult = $mysqldb->update($table, $values, ['id' => $id]);

        if (!$clefResult->getResult()) {
            gfn_isValidation(502);
        }

        //세션 초기화
        $_SESSION['adm']['name'] = $name;

        //성공
        $mysqldb->link->commit();
        $arrRtn['code'] = 200;
        $arrRtn['msg']  = '수정되었습니다.';
    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();
    } finally {
        return $arrRtn;
    }
}

/**
 * name :ufn_AD_DEL
 * comment : 관리자 계정 삭제
 */
function ufn_AD_DEL() {
    
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
       , 'msg' => ''
    );

    try {
        $mysqldb->link->beginTransaction();

        $id = get_request_param('R_ID'); // ID

        $sql = "
            DELETE FROM adm
             WHERE id = :pk";

        $name_sql = $id." 관리자 계정 삭제 삭제 ";

        $clefResult = $mysqldb->delete($sql, [':pk' => $id], $name_sql);

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