<?php
//페이징 변수 정리
if (!isset($page)) {
    $page = 1;
}
if (!isset($page_scale)) {
    $page_scale = 0;
}
if (!isset($start)) {
    $start = 0;
}
if (!empty($total)) {
    $page_scale = ceil($total / $limit);
    $start = (int) ((ceil($page / $scale)) * $scale) - $scale;
}

//query_string
$param_cnt = 0;
$str_param = '';

if (!empty($arrParams)) {
    $param_cnt = count($arrParams);

    if ($param_cnt > 0) {
        $str_param .= http_build_query($arrParams);
    }
}
?>

<div class="pager">

<?php
    $shopFoldName = shopFoldName;

    //paging
    if ($total > 0) {
        //이전
        if ($page > 1) {
            $prev = $page - 1;
            $url = "{$_SERVER['PHP_SELF']}?{$str_param}&page={$prev}";
            echo <<<HTML
                    <button type="button" class="pager-btn pager-prev" onclick="javascript:location.href='{$url}';">
                        <img src="{$shopFoldName}/img/pager_prev.png" alt="이전">
                    </button>
                HTML;
        }

        $end = $start + $scale;
        ++$start;

        for ($i = $start; $i <= $end; $i++) {
            $url = "{$_SERVER['PHP_SELF']}?{$str_param}&page={$i}";

            if ($page == $i) {
                echo <<<HTML
                            <button class="page active" onclick="javascript:location.href='{$url}';">{$i}</button>
                        HTML;
            } else {
                echo <<<HTML
                            <button class="page" onclick="javascript:location.href='{$url}';">{$i}</button>
                        HTML;
            }

            if ($i == (int) $page_scale) {
                break;
            }
        }

        if ($page_scale > $page) {
            $next   = $page + 1;
            $url    = "{$_SERVER['PHP_SELF']}?{$str_param}&page={$next}";

            echo <<<HTML
                        <button type="button" class="pager-btn pager-next active" onclick="javascript:location.href='{$url}';">
                            <img src="{$shopFoldName}/img/pager_next.png" alt="다음">
                        </button>
                    HTML;
        }
    }
?>
</div>
