<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

unset($_SESSION['MEMBER']);
unset($_SESSION['INFOR']);
unset($_SESSION['ORDER']);
unset($_SESSION['SNSIFNO']);

dieAndMsgReplaceMove($_SESSION['FoldName']. '/', '로그아웃되었습니다.');
unset($_SESSION['FoldName']);
