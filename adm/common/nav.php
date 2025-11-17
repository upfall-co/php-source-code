<?php 
    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    $LI_HTML = '';

    // 현재 정의된 모든 상수 가져오기
    $constants = get_defined_constants(true)['user'];

    // 'PAGE'으로 시작하는 상수들을 필터링
    $pageConstants = array_filter($constants, function ($constantName) {

        return strpos($constantName, 'PAGE') === 0  && strlen($constantName) <= 5 ;
    }, ARRAY_FILTER_USE_KEY);

    // 'PAGE'으로 시작하는 상수들을 반복
    $Page_cnt = 1;
    $PageHtml = "";
    $_where2 = '';
    $_arrValue = array();

    //PAGE 변수
    foreach ($pageConstants as $constantName => $constantValue) {
        // 원하는 동작 수행
        $PageHtml .= " WHEN M.PAGE_TYPE = '{$constantValue}' THEN ".$Page_cnt;

        // 동적으로 변수 생성
        ${'PAGE' . $Page_cnt} = $constantValue;

        $Page_cnt++;
    }

    // 'PAGENM'으로 시작하는 상수들을 필터링
    $pagenmConstants = array_filter($constants, function ($constantName) {

        return strpos($constantName, 'PAGENM') === 0;
    }, ARRAY_FILTER_USE_KEY);

    // 'PAGENM'으로 시작하는 상수들을 반복
    $Pagenm_cnt = 1;

    //PAGENM 변수
    foreach ($pagenmConstants as $constantName => $constantValue) {

        // 동적으로 변수 생성
        ${'PAGENM' . $Pagenm_cnt} = $constantValue;

        $Pagenm_cnt++;
    }

    //마스터 관리자 제외 추후에 필요
    if ($_SESSION['adm']['member_type_cd'] == 'SUBADM') {
        $_arrMenuAccess2 = explode(',', $_SESSION['adm']['menu_access']);
        $_in = '';
        $_where2 = '';
        $_i = 0;

        foreach ($_arrMenuAccess2 as $val) {
            $_str_in = ':seq'. $_i++;
            $_in .= "{$_str_in},";

            $_arrValue[$_str_in] = $val;
        }

        $_in = rtrim($_in, ',');
        $_where2 .= "seq IN ({$_in})";
    } else {
        $_where2 .= "seq NOT IN ('')";
    }

    $_sql = "
          SELECT *
            FROM (SELECT CASE {$PageHtml}
                         ELSE 0  
                          END AS PAGE_NUMBER
                       , M.PAGE_TYPE
                       , M.seq
                       , M.depth
                       , M.LINK
                       , M.parent_seq
                       , (SELECT nb.seq
                            FROM project_menu AS nb 
                           WHERE M.PAGE_TYPE = nb.PAGE_TYPE
                             AND nb.{$_where2}
                           ORDER BY nb.parent_seq, nb.sorting DESC 
                           LIMIT 1) AS asd
                       , @rank := CASE 
                                  WHEN M.seq = (SELECT nb.seq
                                                  FROM project_menu AS nb 
                                                 WHERE M.PAGE_TYPE = nb.PAGE_TYPE
                                                   AND nb.depth = (CASE
                                                                   WHEN (SELECT ab.seq
                                                                           FROM project_menu AS ab 
                                                                          WHERE M.PAGE_TYPE = ab.PAGE_TYPE
                                                                            AND asd = ab.parent_seq
                                                                            AND ab.{$_where2}
                                                                          ORDER BY ab.parent_seq, ab.sorting DESC 
                                                                          LIMIT 1) IS NOT NULL THEN 1
                                                                  ELSE 0
                                                                   END)
                                                   AND nb.{$_where2}
                                                   AND nb.parent_seq IN(asd, '0')
                                                 ORDER BY nb.seq, nb.sorting DESC
                                                 LIMIT 1) THEN 1
                                  ELSE 2 
                                   END AS Ranks
                       , @prev_page_type := PAGE_TYPE
                    FROM project_menu M, (SELECT @rank := 0, @prev_page_type := NULL) AS vars
                   WHERE PAGE_TYPE IN (SELECT distinct PAGE_TYPE
                                         FROM project_menu
                                        WHERE 1
                                          AND {$_where2})
                     AND {$_where2}
                     AND use_yn = 'Y'
                   ORDER BY PAGE_NUMBER, depth, sorting desc) A
           WHERE A.Ranks = 1
           ORDER BY A.PAGE_NUMBER";
    $name_sql = "메인 메뉴";
    $clefResult = $mysqldb->select($_sql, $_arrValue, $name_sql);
    $_menu_list2 = $clefResult->getResultSet();
    $pagescount= 1;
    foreach ($_menu_list2 as $type_list) {
        $MIN_SEQ = _check_var($type_list['seq']);
        //$MIN_SEQ2 = _check_var($type_list['MIN_SEQ2']);
        $LINK_URL = _check_var($type_list['LINK']);
        $pageType = _check_var($type_list['PAGE_TYPE']);
        $parent_seq = _check_var($type_list['parent_seq']);

        /*if (!empty($MIN_SEQ2)) {
            $MIN_SEQ = $MIN_SEQ2;
        }*/

        $PAGENM = ${'PAGENM'.$pagescount};

        $LI_HTML .= <<<LI
                        <li><a class="dropdown-item" href="/adm{$LINK_URL}?m_seq={$MIN_SEQ}&mp_seq={$parent_seq}&page_type={$pageType}">{$PAGENM}</a></li>
                    LI;
        $pagescount++;
    }
?>

<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold"><?=$_SESSION['adm']['name']?></span>
                        <span class="text-muted text-xs block">menu <b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <?= $LI_HTML ?>
                    </ul>
                </div>
                <div class="logo-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        IN+
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <?= $LI_HTML ?>
                    </ul>
                </div>
            </li>

<?php
    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    //변수 정리

    $_arrMenu = array();
    $_arrActive = array();
    $_arrMenuActive = array();
    $_arrValue = array();
    $_self_url = $_SERVER['PHP_SELF'];
    $_member_type = $_SESSION['adm']['type'];
    $_menu_access = $_SESSION['adm']['menu_access'] ?? null;
    $m_seq = get_request_param('m_seq', 'GET');
    $mp_seq = get_request_param('mp_seq', 'GET');

    $_where = '';
    $_menu_html = '';
    $main_menu_nm = "";
    $sub_menu_nm = "";

    $page_type = get_request_param('page_type', 'GET');
    $page_href = "";
    $page_nm = "";
    $page_param = "";

    $_arrActive_menu = array();
    $page_icon = array();
    $icno_arrval = array();
    $page_icon_at = array();

    $count = 0;
    $icon_count = 0;

    if (!empty($page_type)) {
        if ($page_type == PAGE1) {
            $page_href = "../main/?".PAGEPAR1;
            $page_nm = PAGENM1;
            $page_param = PAGEPAR1;
            $page_icon = ["fa fa-folder-o", "fa fa-desktop", "fa fa-pencil", "fa fa-users", "fa fa-list-ul", "fa fa-bookmark", "fa fa-shopping-cart", "fa fa-money"];
            $page_icon_at = ["fa fa-folder-open-o", "fa fa-desktop", "fa fa-pencil", "fa fa-users", "fa fa-list-ul", "fa fa-bookmark-o", "fa fa-shopping-cart", "fa fa-money"];
        } else if ($page_type == PAGE2) {
            $page_href = "../main/?".PAGEPAR2;
            $page_nm = PAGENM2;
            $page_param = PAGEPAR2;
            $page_icon = ["fa fa-folder-o", "fa fa-desktop", "fa fa-users", "fa fa-list-ul", "fa fa-pencil", "fa fa-bookmark", "fa fa-shopping-cart", "fa fa-money"];
            $page_icon_at = ["fa fa-folder-open-o", "fa fa-desktop", "fa fa-users", "fa fa-list-ul", "fa fa-pencil", "fa fa-bookmark-o", "fa fa-shopping-cart", "fa fa-money"];
        } else if ($page_type == PAGE3) {
            $page_href = "../main/?".PAGEPAR3;
            $page_nm = PAGENM3;
            $page_param = PAGEPAR3;
            $page_icon = ["fa fa-folder-o", "fa fa-desktop", "fa fa-list-ul", "fa fa-map-marker", "fa fa-id-card", "fa fa-building", "fa fa-bookmark", "fa fa-send", "fa fa-pencil", "fa fa-map-marker"];
            $page_icon_at = ["fa fa-folder-open-o", "fa fa-desktop", "fa fa-list-ul", "fa fa-map-marker", "fa fa-id-card-o", "fa fa-building-o", "fa fa-bookmark-o" , "fa fa-send-o", "fa fa-pencil", "fa fa-map-marker"];
        }
    } else {
        dieAndErrorMove("잘못된 접근입니다.");
    }

    //아이콘 추가
    $_sql = "
          SELECT seq
               , name
               , link
               , page_type
            FROM project_menu
           WHERE 1
             AND depth  = 0
             AND use_yn = 'y'
             AND PAGE_TYPE='{$page_type}'
           ORDER BY sorting DESC, seq DESC";

    $name_sql = "메인 메뉴";
    $clefResult = $mysqldb->select($_sql, $_arrValue, $name_sql);
    $_menu_icon_set = $clefResult->getResultSet();

    if (!empty($_menu_icon_set)) {
        if (count($_menu_icon_set) != count($page_icon)) {
            $chk_icon_count = count($_menu_icon_set) - count($page_icon);
    
             for ($i = 0; $i < $chk_icon_count; $i++) {
                array_push($page_icon, "fa fa-folder");
             }
        }
    
        if (count($_menu_icon_set) != count($page_icon_at)) {
            $chk_icon_count = count($_menu_icon_set) - count($page_icon_at);
    
            for ($i = 0; $i < $chk_icon_count; $i++) {
               array_push($page_icon_at, "fa fa-folder-open");
            }
       }

        foreach ($_menu_icon_set as $_icon_set_list) {
            //DB 변수 정리
            $_db_seq = _check_var($_icon_set_list['seq']);
            $_db_page_type  = _check_var($_icon_set_list['page_type']);

            if (!empty($_db_seq)) {
                $icno_arrval[$_db_seq] = $page_icon[$icon_count];
                $icno_arrval[$_db_seq."ac"] = $page_icon_at[$icon_count];
            }

            $icon_count++;
        }
    }

    //DB 메뉴 active 정리
    $_sql = "
          SELECT seq
               , parent_seq
               , link
               , page_type
            FROM project_menu
           WHERE 1
             AND use_yn = 'y'
             AND PAGE_TYPE='{$page_type}'";
    
    $name_sql = "메뉴 총 리스트";
    $clefResult = $mysqldb->select($_sql, null, $name_sql);
    $_active_link = $clefResult->getResultSet();

    if (!empty($_active_link)) {
        foreach ($_active_link as $_data) {
            //DB 변수 정리
            $_db_active_seq = _check_var($_data['seq']);
            $_db_active_parent_seq  = _check_var($_data['parent_seq']);
            $_db_active_link = _check_var($_data['link']);

            //변수 정리
            $_menu_seq = (!empty($_db_active_parent_seq)) ? $_db_active_parent_seq : $_db_active_seq;
            $_active_link = str_replace('.php', '', $_db_active_link);

            $_arrMenuActive[$_menu_seq][] = array(
                  'seq' => $_db_active_seq
                , 'link' => $_active_link
            );
        }
    }

    //마스터 관리자 제외 추후에 필요
    if ($_member_type == 'SUBADM') {
        $_arrMenuAccess = explode(',', $_menu_access);
        $_in = '';
        $_i = 0;

        foreach ($_arrMenuAccess as $val) {
            $_str_in = ':seq'. $_i++;
            $_in .= "{$_str_in},";

            $_arrValue[$_str_in] = $val;
        }

        $_in = rtrim($_in, ',');
        $_where .= " AND seq IN ({$_in})";
    }

    $_sql = "
          SELECT seq
               , name
               , link
            FROM project_menu
           WHERE 1
              {$_where}
             AND depth  = 0
             AND use_yn = 'y'
             AND PAGE_TYPE='{$page_type}'
           ORDER BY sorting DESC, seq DESC";

    $name_sql = "메인 메뉴";
    $clefResult = $mysqldb->select($_sql, $_arrValue, $name_sql);
    $_menu_list = $clefResult->getResultSet();

    if (!empty($_menu_list)) {
        foreach ($_menu_list as $_data) {
            //DB 변수 정리
            $_db_menu_seq = _check_var($_data['seq']);
            $_db_menu_name = _check_var($_data['name']);
            $_db_menu_link = _check_var($_data['link']);

            //변수 정리
            $_menu_param = "m_seq={$_db_menu_seq}&mp_seq=0";
            $_arrMenu[0][$_db_menu_seq] = $_db_menu_name;
            $_arrValue[':parent_seq'] = $_db_menu_seq;
            $_arrActive[$_db_menu_seq] = '';
            $_arrActive_menu[$_db_menu_seq] = $icno_arrval[$_db_menu_seq];
            $_subActive = '';

            //active
            foreach ($_arrMenuActive[$_db_menu_seq] as $key => $val) {
                if (strpos($_self_url, $val['link']) !== false || 
                    (strpos($_self_url, 'details') !== false && $mp_seq == $_db_menu_seq && $m_seq == $val['seq'])||
                    (strpos($_self_url, 'details') !== false && $m_seq == $_db_menu_seq && $m_seq == $val['seq'])) {
                    $_arrActive[$_db_menu_seq] = 'active';
                    $_subActive = $val['seq'];
                    $main_menu_nm = $_db_menu_name;
                    $_arrActive_menu[$_db_menu_seq] =  $icno_arrval[$_db_menu_seq."ac"];;
                }
            }

            //DB 부메뉴 조회
            $_sql = "
                  SELECT seq
                       , name
                       , link
                    FROM project_menu
                   WHERE 1
                      {$_where}
                     AND parent_seq = :parent_seq
                     AND depth = 1
                     AND use_yn = 'y'
                     AND PAGE_TYPE='{$page_type}'
                   ORDER BY sorting DESC, seq DESC";

            $name_sql = "부메뉴";
            $clefResult = $mysqldb->select($_sql, $_arrValue, $name_sql);
            $_sub_menu_list = $clefResult->getResultSet();

            if (!empty($_sub_menu_list)) {
                //서브 메뉴가 있을 시
                $_menu_html .= <<<HTML
                    <li class="{$_arrActive[$_db_menu_seq]}">
                        <a href="#" title="{$_db_menu_name}"><i class="{$_arrActive_menu[$_db_menu_seq]}"></i> <span class="nav-label">{$_db_menu_name}</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level collapse" aria-expanded="false" >
    HTML;

                foreach ($_sub_menu_list as $_sub_date) {
                    //DB 변수 정리
                    $_db_sub_menu_seq = _check_var($_sub_date['seq']);
                    $_db_sub_menu_name = _check_var($_sub_date['name']);
                    $_db_sub_menu_link = _check_var($_sub_date['link']);

                    //변수 정리
                    $_menu_param = "m_seq={$_db_sub_menu_seq}&mp_seq={$_db_menu_seq}";
                    $_arrMenu[$_db_menu_seq][$_db_sub_menu_seq] = $_db_sub_menu_name;

                    $Active = '';

                    if ($_subActive == $_db_sub_menu_seq) {
                        $Active = 'active';
                        $sub_menu_nm = $_db_sub_menu_name;
                    }

                    $_menu_html .= <<<HTML
                        <li class="{$Active}"><a href="..{$_db_sub_menu_link}?{$_menu_param}&{$page_param}">{$_db_sub_menu_name}</a></li>
    HTML;
                }

                $_menu_html .= <<<HTML
                         </ul>
                    </li>
    HTML;
            } else {
                //서브 메뉴가 없을 시
                $_menu_html .= <<<HTML
                 <li class="{$_arrActive[$_db_menu_seq]}">
                    <a href="..{$_db_menu_link}?{$_menu_param}&{$page_param}" title="{$_db_menu_name}"><i class="{$_arrActive_menu[$_db_menu_seq]}"></i> <span class="nav-label">{$_db_menu_name}</span></a>
                </li>
    HTML;
            }

            $count++;
        }
    }
    ?>

<?php 
    if ($_SESSION['adm']['type'] == 'SUPADM') {
        $_active = "";
        $menu_class = "fa fa-folder";

        if (strpos($_SERVER['PHP_SELF'], '/setting/') !== false) {
            $_active = 'active';
            $menu_class = "fa fa-folder-open";
        }

        $_menu_html .= <<<HTML
        <li class="{$_active}">
            <a href="#"><i class="{$menu_class}"></i> <span class="nav-label">관리자 설정</span><span class="fa arrow"></span></a>
            <ul class="nav nav-second-level collapse" aria-expanded="false" >
                <li class="{$_active}"><a href="../setting/menu.php?{$page_param}">메뉴 관리</a></li>
            </ul>
        </li>
        HTML;
    }

    echo $_menu_html;
?>
        </ul>
    </div>
</nav>

    <!-- 우측 상단 -->
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <a href="<?=HOST_HOME?>/<?=$page_type?>/" target="_blank">
                            <i class="fa fa-home"></i>
                        </a>
                    </li>

                    <li>
                        <a href="../board/adm_details.php?page_type=<?=$page_type?>&mode=MOD&m_seq=3&mp_seq=1">
                            <i class="fa fa-user-o"></i>
                        </a>
                    </li>

                    <li>
                        <a href="../program/logout.php">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>관리자 페이지</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?=$page_href?>"><?=$page_nm?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <?php if (empty($sub_menu_nm)) {?>
                            <strong><?=$main_menu_nm?></strong>
                        <?php } else {?>
                            <?=$main_menu_nm?>
                        <?php } ?>
                    </li>
                    <?php if (!empty($sub_menu_nm)) {?>
                        <li class="breadcrumb-item active">
                            <strong><?=$sub_menu_nm?></strong>
                        </li>
                    <?php } ?>
                </ol>
            </div>
        </div>