<?php
/**
 * Created by IntelliJ IDEA.
 * User: ack7
 * Date: 2019-09-02
 * Time: 13:16
 */
include_once "../lib/m_lib.php";


$upload_folder = '/upload/editor';

$result = file_upload_proc($upload_folder, $_FILES['upload'],true);

// 로드 밸런서/프록시 설정 확인 - AWS 'HTTP_X_FORWARDED_PORT'
$port = $_SERVER['HTTP_X_FORWARDED_PORT'] ?? $_SERVER['SERVER_PORT']; 

if($port !=='80') {
    $return = [
        'uploaded' => '1',
        'fileName' => $result[1],
        'url' => 'http://'.$_SERVER['SERVER_NAME'].":{$port}/upload/editor/" . $result[0]
    ];
} else {
    $return = [
        'uploaded' => '1',
        'fileName' => $result[1],
        'url' => 'http://'.$_SERVER['SERVER_NAME']."/upload/editor/" . $result[0]
    ];
}

header("Content-Type: application/json");
echo json_encode($return);
