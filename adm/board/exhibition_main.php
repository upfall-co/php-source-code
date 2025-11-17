<?php
/**
 * 파일명 : exhibition_main.php
 * 내용 : exhibition  - 메인 
 * 최초작성날짜 : 2023/11/28
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/11/28     V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    $CATEGORY1_SEQ = 'EXHIBITION';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/category_main_code.php';
?>

<?php include_once '../board/category_main.php';?>