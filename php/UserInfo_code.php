<?php
/**
* 파일명 : UserInfo_code.php
* 내용 : 계정 관리
* 최초작성날짜 : 2023/06/20
* 최초작성자 : 김민성
* ------------------------------------
* name       date        comment
* 김민성    2023/06/20     V1.0
*/

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;
    
    if (empty($_SESSION['Master'])) {
        dieAndErrorMove('잘못된 접근입니다.');
    }

?>