<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;
    use Clef\Logger;
    use Clef\SiteConfig;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();
    Logger::write('web_log',PAGE1);

    //site config
    $_title_data = SiteConfig::title_data(PAGE1);
    $_favicon = SiteConfig::favicon_data(PAGE1);
    $_meta = SiteConfig::meta_tag(PAGE1);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=1, maximum-scale=1">
    <!-- 전화, 주소, 이메일 자동링크 없앨때 -->
    <meta name="format-detection" content="telephone=no, address=no, email=no">

    <!-- META -->
    <meta name="title" content="<?=$_meta['meta']['title'];?>">
    <meta name="keyword" content="<?=$_meta['meta']['keywords'];?>">
    <meta name="description" content="<?=$_meta['meta']['description'];?>">
    <!-- //META -->

    <!-- OG -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?=HOST_HOME;?>">
    <meta property="og:title" content="<?=$_meta['og']['title'];?>">
    <meta property="og:description" content="<?=$_meta['og']['description'];?>">
    <meta property="og:image" content="<?=$_meta['og']['img'];?>">
    <!-- //OG -->

    <title><?=$_title_data;?></title>

    <!-- favicon -->
    <link rel="icon" type="image/svg" sizes="192x192" href="<?=$_favicon['favicon']['img'];?>"/>
    <!-- //favicon -->


    <?php
        gfn_page_chk(); // 시크릿페이지 접근확인

        //error_reporting(E_ALL);
        //ini_set("display_errors", 1);

        echo "<link rel='stylesheet' href='" . artFoldName . "/css/all.min.css'>";
        echo "<link rel='stylesheet' href='" . artFoldName . "/css/common.css'>";
        echo "<link rel='stylesheet' href='" . artFoldName . "/css/base.css'>";
        echo "<link rel='apple-touch-icon-precomposed' href='" . artFoldName . "/img/favicon.png' type='image/x-icon'>";
        echo "<link rel='shortcut icon' href='" . artFoldName . "/img/favicon.png' type='image/x-icon'>";
        /* 폰트 */
        echo "<link href='https://webfontworld.github.io/pretendard/Pretendard.css' rel='stylesheet'>";

        echo "<script src='" . artFoldName . "/js/jquery-3.5.1.min.js'></script>";
        echo "<script type='text/javascript' src='/js/clef_common.js'></script>";
        echo "<script type='text/javascript' src='/js/project_common.js'></script>";
        echo "<script type='text/javascript' src='" . artFoldName . "/js/common.js'></script>";

        if (SUB === "00") {
            echo "<link type='text/css' href='" . artFoldName . "/css/main.css' rel='stylesheet'>";
            echo "<link rel='stylesheet' href='https://gcore.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.css'/>";
            echo "<script type='text/javascript' src='" . artFoldName . "/js/main.js'></script>";
            echo "<script src='https://gcore.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.js'></script>";
        } else if (SUB === "SHOP") {
            echo "<link type='text/css' href='" . artFoldName . "/css/shop.css' rel='stylesheet'>";
            echo "<link rel='stylesheet' href='https://gcore.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.css'/>";
            echo "<script type='text/javascript' src='" . artFoldName . "/js/shop.js'></script>";
            echo "<script type='text/javascript' src='https://gcore.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.js'></script>";

            echo "<script src='https://unpkg.co/gsap@3/dist/gsap.min.js'>";
            echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.1/ScrollTrigger.min.js'></script>";
            echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/Observer.min.js'></script>";
        } else if (SUB === "MYPAGE") {
            echo "<link type='text/css' href='" . artFoldName . "/css/mypage.css' rel='stylesheet'>";
            echo "<script type='text/javascript' src='" . artFoldName . "/js/mypage.js'></script>";
        } else if (SUB === "ORDER") {
            echo "<link type='text/css' href='" . artFoldName . "/css/order.css' rel='stylesheet'>";
            echo "<script type='text/javascript' src='" . artFoldName . "/js/order.js'></script>";
        } else if (SUB === "HELP") {
            echo "<link type='text/css' href='" . artFoldName . "/css/help.css' rel='stylesheet'>";
            echo "<script type='text/javascript' src='" . artFoldName . "/js/help.js'></script>";
        }

        echo "<link type='text/css' href='". artFoldName."/css/popup.css?v=". CSSYYYYMMDD ."' rel='stylesheet'>";
    ?>

</head>