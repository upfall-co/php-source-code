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
    $mode   = get_request_param('code_mode');

    //변수 정리
    $arrRes = array();
    $table  = 'common_code';

    switch ($mode) {
        case 'reg' :
            $arrRes = row_insert();
            break;
        case 'mod' :
            $arrRes = row_update();
            break;
        case 'del' :
            $arrRes = row_delete();
            break;
        default :
            throw new Exception('잘못된 접근입니다.');
    }

    //성공
    $arrRtn['code'] = $arrRes['code'];
    $arrRtn['msg']  = $arrRes['msg'];
    echo json_encode($arrRtn);

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    echo json_encode($arrRtn);

}

//등록
function row_insert() {

    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn     = array(
        'code'  => 500,
        'msg'   => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $code_name      = get_request_param('code_name');
        $code_type      = get_request_param('code_type');
        $sorting        = get_request_param('code_sorting');

        //파라미터 체크
        gfn_isValidation(302, $code_name, "코드 이름");
        gfn_isValidation(301, $code_type, "코드 구분");

        //trim
        $code_name      = trim($code_name);

        //변수 정리
        $ip             = $_SERVER['REMOTE_ADDR'];
        $table          = 'common_code';
        $values         = array(
            'sorting'       => $sorting,
            'code_name'     => $code_name,
            'code_type'     => $code_type,
            'reg_user'      => SE_ADM_NAME,
            'reg_ip'        => $ip,
        );

        //DB
        $clefResult = $mysqldb->insert($table, $values);
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

//수정
function row_update() {

    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn     = array(
        'code'  => 500,
        'msg'   => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $seq            = get_request_param('code_seq');
        $code_name      = get_request_param('code_name');
        $code_type      = get_request_param('code_type');
        $sorting        = get_request_param('code_sorting');

        //파라미터 체크
        if (empty($seq) || !is_numeric($seq)) {
            gfn_isValidation(700);
        }

        gfn_isValidation(302, $code_name, "코드 이름");
        gfn_isValidation(301, $code_type, "코드 구분");

        //trim
        $code_name      = trim($code_name);

        //변수 정리
        $ip             = $_SERVER['REMOTE_ADDR'];
        $table          = 'common_code';
        $values         = array(
            'sorting'       => $sorting,
            'code_name'     => $code_name,
            'code_type'     => $code_type,
            'mod_user'      => SE_ADM_NAME,
            'mod_ip'        => $ip,
            'mod_date'      => date('Y-m-d H:i:s')
        );

        //DB
        $clefResult = $mysqldb->update($table, $values, ['seq' => $seq]);
        if (!$clefResult->getResult()) {
            gfn_isValidation(502);
        }

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

//삭제
function row_delete() {

    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn     = array(
        'code'  => 500,
        'msg'   => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $seq        = get_request_param('code_seq');

        //파라미터 체크
        if (empty($seq) || !is_numeric($seq)) {
            gfn_isValidation(700);
        }

        //변수 정리
        $table      = 'common_code';

        //DB
        $sql = "
            DELETE FROM {$table} 
            WHERE 1 
                AND seq = :pk
            LIMIT 1
        ";
        $clefResult = $mysqldb->delete($sql, [':pk' => $seq]);
        if (!$clefResult->getResult()) {
            gfn_isValidation(503);
        }

        //성공
        $mysqldb->link->commit();
        $arrRtn['code'] = 200;
        $arrRtn['msg']  = '삭제되었습니다.';

    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();

    } finally {
        return $arrRtn;
    }
}