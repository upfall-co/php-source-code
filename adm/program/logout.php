<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

//session_unset();
//session_destroy();

unset($_SESSION['adm']);

//header
header('Location:../login/login.php');