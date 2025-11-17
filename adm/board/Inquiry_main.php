<?php
/**
 * 파일명 : Inquiry_main.php
 * 내용 : 1:1문의 페이지
 * 최초작성날짜 : 2023/08/09
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/09    V1.0
 * 김민성    2023/11/21    샵기능추가
 */
    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';
    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/Inquiry_main_code.php';
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
                        <form id="frm" method="get" action="<?=$_SERVER['PHP_SELF'];?>">
                            <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>">
                            <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>">
                            <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>">

                            <div class="row">
                                <div class="col-sm-2">
                                    <label class="col-form-label ml-2" for="order_id">문의</label>

                                    <select class="form-control" id="TYPE_CD" name="TYPE_CD" >
                                        <?php gfn_getComboList("문의타입","COL002",$type_gb2,"문의", "", "", "", $page_type)?>
                                    </select>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label ml-2" for="order_id">이름</label>
                                        <input type="text" id="NAME" name="NAME" value="<?=$NAME;?>" placeholder="이름을 입력해주세요." class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label ml-2" for="order_id">문의명</label>
                                        <input type="text" id="TITLE" name="TITLE" value="<?=$TITLE;?>" placeholder="문의명을 입력해주세요." class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label ml-2" for="order_id">답변상태</label>

                                        <select class="form-control" id="QUESTION_CD" name="QUESTION_CD" >
                                            <?php gfn_getComboList("답변상태","AD008",$type_gb,"답변상태","","","Y")?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label class="col-form-label ml-2" for="order_id">등록일</label>
                                    
                                    <div class="form-group row ml-1">
                                        <div class="input-group date w140" id="data_1">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="start_date" value="<?= $start_date?>">
                                        </div>

                                        <span class="ml-2 mr-2 mt-2">~</span>

                                        <div class="input-group date w140" id="data_2">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="end_date" value="<?= $end_date?>">
                                        </div>
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
                    </div>

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dt-bootstrap4">
                                <table class="table table-striped table-bordered table-hover dataTables-example dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" role="grid">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_asc" tabindex="0" aria-cotrols="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10px;">번호</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 40px;">문의</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 40px;">이름</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 150px;">문의명</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 20px;">답변상태</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 80px;">등록일</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                         <?php getMain_List(); ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th rowspan="1" colspan="1">번호</th>
                                            <th rowspan="1" colspan="1">문의</th>
                                            <th rowspan="1" colspan="1">이름</th>
                                            <th rowspan="1" colspan="1">문의명</th>
                                            <th rowspan="1" colspan="1">답변상태</th>
                                            <th rowspan="1" colspan="1">등록일</th>
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

    $(document).ready(function(){
        //달력
        $('#data_1').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        $('#data_2').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        $('.dataTables-example').DataTable({
            pageLength: <?=$limit?>,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            language: {
                emptyTable: "등록된 1:1문의가 없습니다." // "No data available in table" 대신 사용할 메시지 설정
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