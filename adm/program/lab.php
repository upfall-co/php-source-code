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
    $mode   = get_request_param('mode');

    //변수 정리
    $arrRes = array();
    $table  = 'bbs';
    $type   = 'lab';

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
    $arrRtn['url']  = "../board/{$type}.php";
    dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    dieAndErrorMove($arrRtn['msg']);

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
        $title          = get_request_param('p_title');
        $name           = get_request_param('p_name');
        $content        = isset($_POST['editor']) ? $_POST['editor'] : '';

        //파라미터 체크
        gfn_isValidation(302, $title, "제목");

        //trim
        $title          = trim($title);
        $name           = trim($name);

        //변수 정리
        $ip             = $_SERVER['REMOTE_ADDR'];
        $table          = 'bbs';
        $type           = 'lab';
        $dir            = UPLOAD_DIR ."/{$table}/{$type}";
        $values         = array(
            'title'         => $title,
            'name'          => $name,
            'content'       => $content,
            'e_type'        => $type,
            'reg_user'      => SE_ADM_NAME,
            'reg_ip'        => $ip,
        );

        //파일 업로드
        if (isset($_FILES['p_img']['name']) === false || empty($_FILES['p_img']['name'])) {
            gfn_isValidation(307, $_FILES['p_img']['name'], "썸네일 이미지");
        } else {
            $return_msg = file_upload_proc($dir, $_FILES['p_img'], true);

            if ($return_msg[0] == false) {
                gfn_isValidation(999, "", "{$return_msg[1]}");
            }

            $values['thumbnail_img'] = $return_msg[0];
            $values['thumbnail_img_name'] = $return_msg[1];
        }

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
        $seq = get_request_param('seq');
        $title = get_request_param('p_title');
        $name = get_request_param('p_name');
        $content = isset($_POST['editor']) ? $_POST['editor'] : '';

        //파라미터 체크
        if (empty($seq) || !is_numeric($seq)) {
            gfn_isValidation(700);
        }

        gfn_isValidation(302, $title, "제목");

        //trim
        $title = trim($title);
        $name = trim($name);

        //변수 정리
        $ip = "";

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $table = 'bbs';
        $type = 'lab';
        $dir = UPLOAD_DIR ."/{$table}/{$type}";
        $values = array(
            'title' => $title,
            'name' => $name,
            'content' => $content,
            'e_type' => $type,
            'mod_user' => SE_ADM_NAME,
            'mod_ip' => $ip,
            'mod_date' => date('Y-m-d H:i:s')
        );

        //파일 업로드
        if (isset($_FILES['p_img']['name']) === false || empty($_FILES['p_img']['name'])) {
            //throw new Exception('썸네일 이미지를 선택해주세요.');
        } else {
            $return_msg = file_upload_proc($dir, $_FILES['p_img'], true);

            if ($return_msg[0] == false) {
                gfn_isValidation(999, "", "{$return_msg[1]}");
            }

            $values['thumbnail_img'] = $return_msg[0];
            $values['thumbnail_img_name'] = $return_msg[1];
        }

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
        $seq = get_request_param('seq');

        //파라미터 체크
        if (empty($seq) || !is_numeric($seq)) {
            gfn_isValidation(700);
        }

        //변수 정리
        $table = 'bbs';
        $type = 'lab';

        //DB
        $sql = "
            DELETE FROM {$table} 
            WHERE 1 
                AND seq = :pk
                AND e_type = '{$type}'
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