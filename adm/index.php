<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

//변수 정리
$_url   = '';

//로그인 체크
if (empty($_SESSION['adm'])) {
    $_url = '/adm/login/login.php';
} else {
    if ($_SESSION['adm']['member_type_cd'] == "SUPADM" || $_SESSION['adm']['member_type_cd'] == "ADM") {
       $_url = '/adm/main/?m_seq=2&mp_seq=1&'.PAGEPAR1; // 임시 2023.08.01 김민성

    } else if (!empty($_SESSION['adm']['first_menu'])) {
        $_url = '/adm'. $_SESSION['adm']['first_menu'].'?m_seq='.$_SESSION['adm']['seq'].'&mp_seq='.$_SESSION['adm']['parent_seq'].'&page_type='.$_SESSION['adm']['page_type'];
    }
}

//header
header("Location: {$_url}");