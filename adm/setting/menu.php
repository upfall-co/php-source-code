<?php
//head
define("SUB", "");
include_once __DIR__ .'/../common/head.php';

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;
use Clef\Paging as Paging;

$mysqldb    = new Pdo7();
$clefResult = new ClefResult();

$arrRtn     = array(
    'code'  => 500,
    'msg'   => ''
);

try {
    //파라미터 정리
    $search_type    = get_request_param('search_type', 'GET');
    $search_text    = get_request_param('search_text', 'GET');
    $page_type      = get_request_param('page_type', 'GET');

    //변수 정리
    $arrValue       = array();
    $arrMenu        = array();
    $table          = 'project_menu';
    $where          = '';

    //검색
    if (!empty($search_type) && !empty($search_text)) {
        $where  .= " AND {$search_type} LIKE :search_text";
        $arrValue[':search_text'] = "%{$search_text}%";
    }

    //검색
    if (!empty($page_type)) {
        $where  .= " AND PAGE_TYPE = :page_type";
        $arrValue[':page_type'] = "{$page_type}";
    }

    //DB
    $sql = "
        SELECT
            *
        FROM {$table}
        WHERE 1
            {$where}
        ORDER BY depth ASC, sorting DESC
    ";
    //_p($sql);
    $clefResult = $mysqldb->select($sql, $arrValue);
    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }
    $total      = $clefResult->getCount();
    $list       = $clefResult->getResultSet();

    if (!empty($list)) {
        foreach ($list as $data) {
            //DB 변수 정리
            $_db_m_seq          = _check_var($data['seq']);
            $_db_m_parent_seq   = _check_var($data['parent_seq']);
            $_db_m_depth        = _check_var($data['depth']);
            $_db_m_sorting      = _check_var($data['sorting']);
            $_db_m_name         = _check_var($data['name']);
            $_db_m_link         = _check_var($data['link']);
            $_db_m_use_yn       = _check_var($data['use_yn']);
            $_db_page_type       = _check_var($data['PAGE_TYPE']);
            //변수 정리
            $m_seq_idx          = ($_db_m_depth) ? $_db_m_parent_seq : $_db_m_seq;
            $use_yn             = strtoupper($_db_m_use_yn);

            $arrMenu[$m_seq_idx][$_db_m_seq] = array(
                'seq'           => $_db_m_seq,
                'parent_seq'    => $_db_m_parent_seq,
                'page_type'     => $_db_page_type,
                'depth'         => $_db_m_depth,
                'sorting'       => $_db_m_sorting,
                'name'          => $_db_m_name,
                'link'          => $_db_m_link,
                'use_yn'        => $use_yn
            );
        }
    }

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);

}
?>
<body>
<div id="wrapper">
    <?php
        include_once __DIR__ .'/../common/nav.php';
    ?>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title p-md">
                        <h5>메뉴 관리</h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-primary btn-s m-t-xs" onclick="javascript:openMenuModal('parent', '<?=$page_type?>', 0);">등록</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dt-bootstrap4">
                                <table class="table table-bordered table-hover dataTables-example dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" role="grid">
                                    <thead>
                                        <tr role="row">
                                            <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 275.422px;">메뉴 ID</th>
                                            <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 354.953px;">부모 ID</th>
                                            <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 324.766px;">정렬</th>
                                            <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 238.672px;">메뉴 이름</th>
                                            <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 183.188px;">링크</th>
                                            <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 183.188px;">사용여부</th>
                                            <th tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 183.188px;"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                        //리스트
                                        if (!empty($arrMenu)) {
                                            foreach ($arrMenu as $key => $val) {
                                                    foreach ($arrMenu[$key] as $key2 => $val2) {
                                                        //변수 정리
                                                        $tr_class   = '';
                                                        $font_w     = '';
                                                        $nbsp       = '';
                                                        $html       = '';

                                                        if ($val2['depth']) {
                                                            //자식 메뉴
                                                            $tr_class   = 'child_row';
                                                            $nbsp       = '&nbsp;&nbsp';
                                                        } else {
                                                            //부모 메뉴
                                                            $font_w     = 'font-weight-bold';
                                                            $html       = <<<HTML
                                                                                <button type="button" class="btn btn-sm btn-dark" onclick="javascript:openMenuModal('child', '{$page_type}', 0, {$val2['seq']});" style="font-size: 12px;">하위메뉴 등록</button>
                                                                            HTML;
                                                        }

                                                        echo <<<TR
                                                                <tr class="gradeA odd {$tr_class}" role="row">
                                                                    <td class="sorting_1">{$val2['seq']}</td>
                                                                    <td>{$val2['parent_seq']}</td>
                                                                    <td>{$val2['sorting']}</td>
                                                                    <td class="{$font_w}">{$nbsp}{$val2['name']}</td>
                                                                    <td >{$val2['link']}</td>
                                                                    <td >{$val2['use_yn']}</td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-primary" onclick="javascript:openMenuModal('parent', '{$page_type}', {$val2['seq']});" style="font-size: 12px;">수정</button>
                                                                        {$html}
                                                                    </td>
                                                                </tr>
                                                            TR;
                                                }
                                            }
                                        }

                                        if (!$total) {
                                            echo <<<TR
                                        <tr>
                                            <td colspan="30" class="text-center"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">등록된 메뉴가 없습니다.</font></font></td>
                                        </tr>
                                TR;
                                        }
                                        ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th rowspan="1" colspan="1">메뉴 ID</th>
                                            <th rowspan="1" colspan="1">부모 ID</th>
                                            <th rowspan="1" colspan="1">정렬</th>
                                            <th rowspan="1" colspan="1">메뉴 이름</th>
                                            <th rowspan="1" colspan="1">링크</th>
                                            <th rowspan="1" colspan="1">사용여부</th>
                                            <th rowspan="1" colspan="1"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <?php
            //footer
            include_once __DIR__ .'/../common/footer.php';
        ?>
    </div>
</div>
<?php
//top_btn
include_once __DIR__ .'/../common/bottom.php';
?>
<style>
    .child_row {
        background-color: rgba(0,0,0,.05);
    }
</style>