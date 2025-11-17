<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

$arrRtn     = array(
    'code'  => 500,
    'msg'   => '',
    'mode'  => '',
    'url'   => ''
);

try {
    //파라미터 정리
    $mode           = get_request_param('mode');

    //변수 정리
    $arrRtn['mode'] = $mode;
    $arrRes         = array();

    switch ($mode) {
        case 'reg' :
            $arrRes = row_insert();
            break;
        case 'mod' :
            $arrRes = row_update();
            break;
        default :
            throw new Exception('잘못된 접근 입니다.');
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
        $parent_seq     = get_request_param('parent_seq');
        $name           = get_request_param('menu_name');
        $link           = get_request_param('menu_link');
        $sorting        = get_request_param('menu_sorting');
        $use_yn         = get_request_param('menu_use_yn');
        $page_type       = get_request_param('page_type');

        //파라미터 체크
        if (!is_numeric($parent_seq)) {
            gfn_isValidation(700);
        }

        gfn_isValidation(302, $name, "메뉴이름");
        gfn_isValidation(302, $link, "링크");

        //trim
        $name           = trim($name);
        $sorting        = trim($sorting);

        //변수 정리
        $ip             = $_SERVER['REMOTE_ADDR'];
        $table          = 'project_menu';
        $depth          = ($parent_seq > 0) ? 1 : 0;
        $values         = array(
            'parent_seq'    => $parent_seq,
            'sorting'       => $sorting,
            'PAGE_TYPE'     => $page_type,
            'TYPE_CD'       => "ADM",
            'depth'         => $depth,
            'name'          => $name,
            'link'          => $link,
            'use_yn'        => $use_yn,
            'reg_user'      => $_SESSION['adm']['name'],
            'reg_ip'        => $ip,
        );

        if ($parent_seq > 0) {
            $values['depth'] = 1;
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
        $seq            = get_request_param('seq');
        $parent_seq     = get_request_param('parent_seq');
        $name           = get_request_param('menu_name');
        $link           = get_request_param('menu_link');
        $sorting        = get_request_param('menu_sorting');
        $use_yn         = get_request_param('menu_use_yn');
        $page_type         = get_request_param('page_type');

        //파라미터 체크
        if (empty($seq) || !is_numeric($seq)) {
            gfn_isValidation(700);
        }
        if (!is_numeric($parent_seq)) {
            gfn_isValidation(700);
        }

        gfn_isValidation(302, $name, "메뉴이름");
        gfn_isValidation(302, $link, "링크");

        //trim
        $name           = trim($name);
        $sorting        = trim($sorting);

        //변수 정리
        $ip             = $_SERVER['REMOTE_ADDR'];
        $table          = 'project_menu';
        $depth          = ($parent_seq > 0) ? 1 : 0;
        $values         = array(
            'parent_seq'    => $parent_seq,
            'sorting'       => $sorting,
            'depth'         => $depth,
            'TYPE_CD'     => "ADM",
            'PAGE_TYPE'     => $page_type,
            'name'          => $name,
            'link'          => $link,
            'use_yn'        => $use_yn,
            'mod_user'      => $_SESSION['adm']['name'],
            'mod_ip'        => $ip,
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