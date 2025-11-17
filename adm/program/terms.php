<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

//관리자 체크
_check_admin();

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

$arrRtn = array(
      'code' => 500
    , 'msg' => ''
    , 'mode' => ''
    , 'url' => ''
);

try {
    //파라미터 정리
    $mode = get_request_param('mode');
    $m_seq = get_request_param('m_seq');
    $mp_seq = get_request_param('mp_seq');
    $type = get_request_param('type');

    //변수 정리
    $arrRes = array();
    $table = 'site_config';
    $category = 'terms';

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

    if ($arrRes['code'] != 200) {
        throw new Exception($arrRes['msg'], $arrRes['code']);
    }

    //성공
    $arrRtn['code'] = $arrRes['code'];
    $arrRtn['msg']  = $arrRes['msg'];
    echo json_encode($arrRtn);

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    //dieAndErrorMove($arrRtn['msg']);
    echo json_encode($arrRtn);

}

//등록
function row_insert() {
    global $config;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500,
        'msg' => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $type = get_request_param('type');
        $content = isset($_POST['editor']) ? $_POST['editor'] : '';
        $page_type = get_request_param('page_type');

        //파라미터 체크
        if (empty($page_type)) {
            gfn_isValidation(700);
        }

        if (empty($type) || !in_array($type, array_keys($config['site']['config']['terms_'.$page_type]))) {
            gfn_isValidation(700);
        }

        //변수 정리
        $ip = "";

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $table = 'site_config';
        $category = 'terms';

        $values = array(
            'content' => $content,
            'category' => $category,
            'PAGE_TYPE' => $page_type,
            'type' => $type,
            'reg_user' => SE_ADM_NAME,
            'reg_ip' => $ip
        );

        //DB
        $name_sql = "약관관리 추가" ;
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

//수정
function row_update() {

    global $config;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500,
        'msg' => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $type = get_request_param('type');
        $lang = get_request_param('lang');
        $content = isset($_POST['editor'])   ? $_POST['editor'] : '';
        $page_type = get_request_param('page_type');

        if (empty($page_type)) {
            gfn_isValidation(700);
        }

        //파라미터 체크
        if (empty($type) || !in_array($type, array_keys($config['site']['config']['terms_'.$page_type]))) {
            gfn_isValidation(700);
        }

        //변수 정리
        $ip = "";

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $table = 'site_config';
        $category = 'terms';

        $values = array(
            'content' => $content,
            'mod_user' => SE_ADM_NAME,
            'mod_ip' => $ip,
            'mod_date' => date('Y-m-d H:i:s'),
        );

        //DB
        $name_sql = "약관관리 수정";
        $clefResult = $mysqldb->update($table, $values, ['category' => $category, 'type' => $type, 'page_type' => $page_type], $name_sql);

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
        $arrRtn['msg'] = $e->getMessage();
    } finally {
        return $arrRtn;
    }
}

//삭제
function row_delete() {
    global $config;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500,
        'msg' => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        $type = get_request_param('type');
        $page_type = get_request_param('page_type');

        if (empty($page_type)) {
            gfn_isValidation(700);
        }

        //파라미터 체크
        if (empty($type) || !in_array($type, array_keys($config['site']['config']['terms_'.$page_type]))) {
            gfn_isValidation(700);
        }

        //변수 정리
        $table = 'site_config';
        $category = 'terms';

        //DB
        $sql = "
            DELETE FROM {$table}
            WHERE 1
                AND category = '{$category}'
                AND PAGE_TYPE = '{$page_type}'
                AND type = :pk
            LIMIT 1
        ";

        $name_sql = "약관 관리 삭제";
        $clefResult = $mysqldb->delete($sql, [':pk' => $type], $name_sql);
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