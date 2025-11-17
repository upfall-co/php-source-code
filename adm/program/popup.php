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
    $mode       = get_request_param('mode');

    //변수 정리
    $arrRes     = array();
    $table      = 'popup';

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
            throw new Exception('모드 오류 code(501)');
    }

    if ($arrRes['code'] != 200) {
        throw new Exception($arrRes['msg'], $arrRes['code']);
    }

    $m_seq = get_request_param('m_seq');
    $mp_seq = get_request_param('mp_seq');
    $page_type = get_request_param('page_type');
    $M_pop_title = get_request_param('M_pop_title'); // 제목

    $arrParams = array(
          'm_seq' => $m_seq
        , 'mp_seq' => $mp_seq
        , 'page_type' => $page_type
        , 'pop_title' => $M_pop_title
    );

    $query_string = http_build_query($arrParams);   

    //성공
    $arrRtn['code'] = $arrRes['code'];
    $arrRtn['msg']  = $arrRes['msg'];
    $arrRtn['url']  = "../board/{$table}.php?{$query_string}";
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
        $title      = get_request_param('p_title');
        $link       = get_request_param('p_link');
        $start_date      = get_request_param('start');
        $end_date      = get_request_param('end');
        $page_type      = get_request_param('page_type');
        $open_yn    = get_request_param('p_open_yn');
        $x          = get_request_param('p_x');
        $y          = get_request_param('p_y');

        //파라미터 체크
        gfn_isValidation(302, $title, "제목");
        gfn_isValidation(302, $x, "X 좌표");
        gfn_isValidation(302, $y, "Y 좌표");

        if (empty($open_yn)) {
            $open_yn = "N";
        }

        //trim
        $title      = trim($title);

        //변수 정리
        $ip         = $_SERVER['REMOTE_ADDR'];
        $table      = 'popup';
        $dir        = UPLOAD_DIR ."/{$table}";

        $values     = array(
            'pop_title'         => $title,
            'page_type'         => $page_type,
            'pop_link'          => $link,
            'pop_open_yn'       => $open_yn,
            'pop_start_date'    => $start_date,
            'pop_end_date'      => $end_date,
            'pop_x'             => $x,
            'pop_y'             => $y,
            'pop_reg_user'      => SE_ADM_ID
        );

        //이미지 업로드
        if (isset($_FILES['p_img']['name']) === false || empty($_FILES['p_img']['name'])) {
            gfn_isValidation(307, $_FILES['p_img']['name'], "파일");
        } else {
            $return_msg = file_upload_proc($dir, $_FILES['p_img'], true);

            if ($return_msg[0] == false) {
                gfn_isValidation(999, "", "{$return_msg[1]}");
            }

            $values['pop_img1']         = $return_msg[0];
            $values['pop_img1_name']    = $return_msg[1];
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
        $seq        = get_request_param('seq');
        $title      = get_request_param('p_title');
        $link       = get_request_param('p_link');
        $start_date      = get_request_param('start');
        $end_date      = get_request_param('end');
        $open_yn    = get_request_param('p_open_yn');
        $x          = get_request_param('p_x');
        $y          = get_request_param('p_y');

        //파라미터 체크
        if (empty($seq) || !is_numeric($seq)) {
            gfn_isValidation(700);
        }

        //파라미터 체크
        gfn_isValidation(302, $title, "제목");
        gfn_isValidation(302, $x, "X 좌표");
        gfn_isValidation(302, $y, "Y 좌표");

        if (empty($open_yn)) {
            $open_yn = "N";
        }

        //trim
        $title      = trim($title);

        //변수 정리
        $ip         = $_SERVER['REMOTE_ADDR'];
        $table      = 'popup';
        $dir        = UPLOAD_DIR ."/{$table}";

        $values     = array(
            'pop_title'         => $title,
            'pop_link'          => $link,
            'pop_open_yn'       => $open_yn,
            'pop_start_date'    => $start_date,
            'pop_end_date'      => $end_date,
            'pop_x'             => $x,
            'pop_y'             => $y,
            'pop_mod_user'      => SE_ADM_ID,
            'pop_mod_date'      => date('Y-m-d H:i:s'),
        );

        //이미지 업로드
        if (isset($_FILES['p_img']['name']) === false || empty($_FILES['p_img']['name'])) {

        } else {
            $return_msg = file_upload_proc($dir, $_FILES['p_img'], true);

            if ($return_msg[0] == false) {
                gfn_isValidation(999, "", "{$return_msg[1]}");
            }

            $values['pop_img1']         = $return_msg[0];
            $values['pop_img1_name']    = $return_msg[1];
        }

        //DB
        $clefResult = $mysqldb->update($table, $values, ['pop_seq' => $seq]);
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
        $seq        = get_request_param('seq');
        
        //파라미터 체크
        if (empty($seq) || !is_numeric($seq)) {
            gfn_isValidation(700);
        }

        //변수 정리
        $table      = 'popup';

        //DB
        $sql = "
            DELETE FROM {$table} 
            WHERE 1 
                AND pop_seq = :pk
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