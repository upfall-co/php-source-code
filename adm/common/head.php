<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

use Clef\SiteConfig;

if (strpos($_SERVER['PHP_SELF'], '/login/login.php') === false &&
    strpos($_SERVER['PHP_SELF'], '/Masterpage/UserInfo.php') === false &&
    strpos($_SERVER['PHP_SELF'], '/Masterpage/index.php') === false) {
    //관리자 갱신
    _check_admin();

    //SUB_ADMIN - 권한 체크
    gfn_ip_isValidation();
}

$page_type = get_request_param('page_type', 'GET');

//site config
$_title_data = SiteConfig::title_data($page_type);
$_favicon = SiteConfig::favicon_data($page_type);
$_meta = SiteConfig::meta_tag($page_type);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- META -->
    <meta name="title" content="<?=$_meta['meta']['title'];?> 관리자">
    <meta name="keyword" content="<?=$_meta['meta']['keywords'];?>">
    <meta name="description" content="<?=$_meta['meta']['description'];?>">
    <!-- //META -->

    <!-- OG -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?=HOST_HOME;?>/adm">
    <meta property="og:title" content="<?=$_meta['og']['title'];?> 관리자">
    <meta property="og:description" content="<?=$_meta['og']['description'];?>">
    <meta property="og:image" content="<?=$_meta['og']['img'];?>">
    <!-- //OG -->

    <title><?=$_title_data;?> 관리자</title>

    <!-- favicon -->
    <link rel="icon" type="image/svg" sizes="192x192" href="<?=$_favicon['favicon']['img'];?>"/>
    <!-- //favicon -->

    <!-- CSS -->
    <link href="/adm/css/bootstrap.min.css" rel="stylesheet">
    <link href="/adm/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="/adm/css/animate.css" rel="stylesheet">
    <link href="/adm/css/style.css" rel="stylesheet">
    <link href="/adm/css/clef_custom.css" rel="stylesheet">

    <link href="/adm/css/plugins/dropzone/dropzone.css" rel="stylesheet">
    <link href="/adm/css/plugins/codemirror/codemirror.css" rel="stylesheet">
    <link href="/adm/css/plugins/dropzone/basic.css" rel="stylesheet">
    <link href="/adm/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
    <link href="/adm/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/adm/css/plugins/admin_style.css" rel="stylesheet">
    <link href="/adm/css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">
    <link href="/adm/css/plugins/dataTables/datatables.min.css" rel="stylesheet">
    <link href="/adm/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">
    <link href="/adm/css/plugins/select2/select2.min.css" rel="stylesheet">
    <link href="/adm/css/plugins/select2/select2-bootstrap4.min.css" rel="stylesheet">

    <!-- TouchSpin(+/-) -->
    <link href="/adm/css/plugins/touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet">

    <!--datepicker3(달력)-->
    <link href="/adm/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <!-- //CSS -->

    <!-- JS -->
    <script src="/adm/js/jquery-3.1.1.min.js"></script>
    <script src="/adm/js/popper.min.js"></script>
    <script src="/adm/js/bootstrap.min.js"></script>
    <script src="/adm/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/adm/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/adm/js/inspinia.js"></script>
    <script src="/adm/js/plugins/pace/pace.min.js"></script>

    <script src="/adm/js/plugins/bs-custom-file/bs-custom-file-input.min.js"></script>
    <script src="/adm/js/plugins/dropzone/dropzone.js"></script>
    <script src="/adm/js/plugins/codemirror/codemirror.js"></script>
    <script src="/adm/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/adm/js/plugins/dataTables/datatables.min.js"></script>
    <script src="/adm/js/plugins/select2/select2.full.min.js"></script>
    <script src="/adm/assets/ckeditor/ckeditor.js"></script>

    <!--morris JS -->
    <script src="/adm/js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="/adm/js/plugins/morris/morris.js"></script>

    <!--flot JS -->
    <script src="/adm/js/plugins/flot/jquery.flot.js"></script>
    <script src="/adm/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="/adm/js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="/adm/js/plugins/flot/jquery.flot.pie.js"></script>
    <script src="/adm/js/plugins/flot/jquery.flot.time.js"></script>

    <!--datepicker(달력)-->
    <script src="/adm/js/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="/adm/js/plugins/dualListbox/jquery.bootstrap-duallistbox.js"></script>
    <script src="/adm/js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="/adm/js/plugins/fullcalendar/moment.min.js"></script>

    <!-- TouchSpin -->
    <script src="/adm/js/plugins/touchspin/jquery.bootstrap-touchspin.min.js"></script>
    <!-- //JS -->

    <script type='text/javascript' src='/js/clef_common.js'></script>
    <script type='text/javascript' src='/js/project_common.js'></script>
</head>