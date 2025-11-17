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
    $pop_title    = get_request_param('pop_title', 'GET');
    $page_type      = get_request_param('page_type', 'GET');
    $m_seq = get_request_param('m_seq', 'GET');
    $mp_seq = get_request_param('mp_seq', 'GET');
    
    $title_name = "팝업"; // Copy, CSV, Excel, Print 제목

    //변수 정리
    $arrValue       = array();
    $limit          = 10;
    $total          = 0;
    $table          = 'popup';
    $where          = '';

    //검색
    if (!empty($pop_title)) {
        $where .= " AND pop_title LIKE :pop_title";
        $arrValue[':pop_title'] = "%{$pop_title}%";
    }

    if (!empty($page_type)) {
        $where .= " AND page_type LIKE :page_type";
        $arrValue[':page_type'] = "%{$page_type}%";
    }
    //DB 총 개수
    $sql = "
        SELECT 
           pop_seq 
        FROM {$table}
        WHERE 1
            {$where}
    ";
    //_p($sql);
    $clefResult = $mysqldb->count($sql, $arrValue);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $total      = $clefResult->getCount();

    //DB
    $sql = "
        SELECT
            *
        FROM {$table}
        WHERE 1
            {$where}
        ORDER BY pop_seq DESC";

    //_p($sql);
    $clefResult = $mysqldb->select($sql, $arrValue);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }
    $list       = $clefResult->getResultSet();

    $INS_arrParams = array( // 초기화 및 등록
        'page_type' => $page_type
      , 'mp_seq' => $mp_seq
      , 'm_seq' => $m_seq
    );

    $INS_query_string = http_build_query($INS_arrParams);

    $arrParams = array(
        'm_seq' => $m_seq
      , 'mp_seq' => $mp_seq
      , 'page_type' => $page_type
      , 'pop_title' => $pop_title
    );

    $query_string = http_build_query($arrParams);
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
                    <div class="ibox-title">
                        <h5><strong>상세검색</strong></h5>

                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>

                    <div class="ibox-content custom_search_top">
                        <form id="frm"  method="get" action="<?=$_SERVER['PHP_SELF'];?>">
                            <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>">
                            <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>">
                            <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>">

                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label ml-2" for="order_id">제목</label>
                                        <input type="text" id="pop_title" name="pop_title" value="<?=$pop_title;?>" placeholder="제목을 입력해주세요." class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 pr-2">
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary btn-lg">검색</button>
                                        <button type="button" class="btn btn-primary btn-w-m btn-lg" onclick="javascript:location.href='<?=$_SERVER['PHP_SELF'];?>?<?=$INS_query_string?>';">검색 초기화</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title p-md">
                        <h5><?=$title_name?></h5>

                        <div class="ibox-tools">
                            <button type="button" class="btn btn-primary btn-s m-t-xs" onclick="javascript:location.href='<?=$table;?>_write.php?<?=$INS_query_string?>&mode=INS';">등록</button>
                        </div>
                    </div>

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dt-bootstrap4">
                                <table class="table table-striped table-bordered table-hover dataTables-example dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" role="grid">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10px;">번호</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 380px;">제목</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 120px;">노출기간</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 10px;">노출여부</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 120px;">최종수정일시</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php                                        
                                            if (!empty($list)) {
                                                foreach ($list as $data) {
                                                    //DB 변수 정리
                                                    $_db_seq            = _check_var($data['pop_seq']);
                                                    $_db_title          = _check_var($data['pop_title']);
                                                    $_db_open_yn        = _check_var($data['pop_open_yn']);
                                                    $_db_start_date     = _check_var($data['pop_start_date']);
                                                    $_db_end_date       = _check_var($data['pop_end_date']);
                                                    $_db_reg_date       = _check_var($data['pop_reg_date']);
                                                    $_db_mod_date       = _check_var($data['pop_mod_date']);

                                                    //변수 정리
                                                    $str_lang           = (!empty($_db_lang))       ? $config['lang'][$_db_lang] : '';
                                                    $date               = (empty($_db_mod_date))    ? $_db_reg_date : $_db_mod_date;

                                                    echo <<<TR
                                                                <tr class="gradeA odd" role="row">
                                                                    <td class="simple_numbers"></td>
                                                                    <td><a href="{$table}_view.php?seq={$_db_seq}&{$query_string}">{$_db_title}</a></td>
                                                                    <td>{$_db_start_date} ~ {$_db_end_date}</td>
                                                                    <td class="center">{$_db_open_yn}</td>
                                                                    <td class="center">{$date}</td>
                                                                </tr>
                                                            TR;
                                                } 
                                            }
                                        ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th rowspan="1" colspan="1">번호</th>
                                            <th rowspan="1" colspan="1">제목</th>
                                            <th rowspan="1" colspan="1">노출기간</th>
                                            <th rowspan="1" colspan="1">노출여부</th>
                                            <th rowspan="1" colspan="1">최종수정일시</th>
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

<script>
    // Upgrade button class name
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';

    //테이블 엑셀 기능
    $(document).ready(function(){
        $('.dataTables-example').DataTable({
            pageLength: <?=$limit?>,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            language: {
                emptyTable: "등록된 팝업이 없습니다." // "No data available in table" 대신 사용할 메시지 설정
            },
            createdRow: function (row, data, dataIndex) {
                const api = this.api();
                const rowNumber = api.page.info().start + dataIndex + 1;
                $(row).find('.simple_numbers').text(rowNumber);
            },
            buttons: [
                {extend: 'copy',
                    title: '<?=$title_name?>',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    }
                },
                {extend: 'csv',
                    title: '<?=$title_name?>',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    },
                    customize: function (csv) {
                    return '\uFEFF' + csv; // CSV 데이터 앞에 UTF-8 BOM 문자를 추가하여 UTF-8-SIG로 인코딩
                    }
                },
                {extend: 'excel',
                    title: '<?=$title_name?>',
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    }
                },
                {extend: 'print', title: '<?=$title_name?>',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                    },
                    exportOptions: {
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 0) {
                                    return $(node).text(); // 렌더링된 값을 얻기 위해 데이터 대신 노드를 사용
                                } else {
                                    return node.textContent;
                                }
                            }
                        }
                    }
                }
            ]
        });
    });
</script>