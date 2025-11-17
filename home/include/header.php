<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;
use Clef\Logger;
use Clef\SiteConfig;

$mysqldb = new Pdo7();
$clefResult = new ClefResult();
Logger::write('web_log', PAGE3);

//site config
$_title_data = SiteConfig::title_data(PAGE3);
$_favicon = SiteConfig::favicon_data(PAGE3);
$_meta = SiteConfig::meta_tag(PAGE3);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no, address=no, email=no">

  <!-- META -->
  <meta name="title" content="<?= $_meta['meta']['title']; ?>">
  <meta name="keyword" content="<?= $_meta['meta']['keywords']; ?>">
  <meta name="description" content="<?= $_meta['meta']['description']; ?>">
  <!-- //META -->

  <!-- OG -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= HOST_HOME; ?>">
  <meta property="og:title" content="<?= $_meta['og']['title']; ?>">
  <meta property="og:description" content="<?= $_meta['og']['description']; ?>">
  <meta property="og:image" content="<?= $_meta['og']['img']; ?>">
  <!-- //OG -->

  <!-- favicon -->
  <link rel="icon" type="image/svg" sizes="192x192" href="<?=$_favicon['favicon']['img'];?>"/>
  <!-- //favicon -->

  <!-- DatePicker -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!-- google -->
  <meta name="google-site-verification" content="a2F5vxOSb1wWVaTBNevnSavYb_Bs3x2OzzvE9DZJhOY" />
  <!-- google -->

  <meta name="naver-site-verification" content="8e255041542d5dee665fc7ed119533ca0e6475fd" />

  <!--   <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes"> -->
  <title><?=$_title_data;?></title>
  <?php
      gfn_page_chk();
  //error_reporting(E_ALL);
  //ini_set("display_errors", 1);

  echo "<link rel='stylesheet' href='" . homeFoldName . "/css/base.css'>";
  echo "<link rel='stylesheet' href='" . homeFoldName . "/css/common.css'>";
  echo "<script src='" . homeFoldName . "/js/library/jquery-3.6.1.min.js'></script>"; // jquery ver3.6.1
  // echo "<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js'></script>";
  // echo "<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js'></script>";
  echo "<script type='text/javascript' src='/js/clef_common.js'></script>";
  echo "<script type='text/javascript' src='" . homeFoldName . "/js/header.js'></script>";
  echo "<script type='text/javascript' src='" . homeFoldName . "/js/common.js'></script>";

  if (SUB === "00") {

    echo "<link type='text/css' href='" . homeFoldName . "/css/swiper-bundle.min.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/form.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/main.css' rel='stylesheet'>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/library/swiper-bundle.min.js'></script>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/main.js'></script>";

  } else if (SUB === "01") {

    echo "<link type='text/css' href='" . homeFoldName . "/css/swiper-bundle.min.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/board.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/sub01.css' rel='stylesheet'>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/library/swiper-bundle.min.js'></script>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/sub01.js'></script>";

  } else if (SUB === "02") {

    echo "<link type='text/css' href='" . homeFoldName . "/css/swiper-bundle.min.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/board.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/sub02.css' rel='stylesheet'>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/library/swiper-bundle.min.js'></script>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/sub02.js'></script>";

  } else if (SUB === "03") {

    echo "<link type='text/css' href='" . homeFoldName . "/css/swiper-bundle.min.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/board.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/sub03.css' rel='stylesheet'>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/library/swiper-bundle.min.js'></script>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/sub03.js'></script>";

  } else if (SUB === "04") {
    echo "<link type='text/css' href='" . homeFoldName . "/css/swiper-bundle.min.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/sub04.css' rel='stylesheet'>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/library/swiper-bundle.min.js'></script>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/sub04.js'></script>";

  } else if (SUB === "05") {

    echo "<link type='text/css' href='" . homeFoldName . "/css/swiper-bundle.min.css' rel='stylesheet'>";
    echo "<link type='text/css' href='" . homeFoldName . "/css/sub05.css' rel='stylesheet'>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/library/swiper-bundle.min.js'></script>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/sub05.js'></script>";

  } else if (SUB === "06") {

    echo "<link type='text/css' href='" . homeFoldName . "/css/sub06.css' rel='stylesheet'>";
    echo "<script type='text/javascript' src='" . homeFoldName . "/js/sub06.js'></script>";

  }

  echo "<link type='text/css' href='". homeFoldName."/css/popup.css?v=". CSSYYYYMMDD ."' rel='stylesheet'>";

  ?>
</head>