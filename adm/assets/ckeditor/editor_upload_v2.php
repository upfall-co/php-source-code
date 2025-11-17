<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

$arrRtn = array(
    'uploaded'  => 0,
    'filename'  => '',
    'url'       => '',
    'error'     => array(
        'message' => ''
    )
);

try {
    //변수 정리
    $dir    = '/upload/editor';

    if ($_FILES['upload']['size'] > 0) {
        //파일 확장자 변수 정리
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        $fileinfo   = pathinfo($_FILES['upload']['name']);
        $ext        = strtolower($fileinfo['extension']);

        //확장자 체크
        if (!in_array($ext, $allowed_ext)) {
            throw new Exception('이미지 파일은 jpg, jpeg, png, gif 확장자만 가능합니다. code(502)');
        }

        //파일 업로드
        $arrRes     = file_upload_proc($dir, $_FILES['upload'], true);
        if ($arrRes[0] == false) {
            throw new Exception($arrRes[1]);
        }

        //성공
        $arrRtn['uploaded'] = 1;
        $arrRtn['filename'] = $arrRes[1];
        $arrRtn['url']      = "{$dir}/{$arrRes[0]}";
    }

} catch (Exception $e) {
    $arrRtn['filename'] = $_FILES['upload']['name'];
    $arrRtn['error']['message'] = $e->getMessage();

} finally {
    echo json_encode($arrRtn);
}