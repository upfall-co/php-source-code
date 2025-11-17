<body>
    <div id="wrapper">
        <?php
            include_once __DIR__ .'/../common/nav.php';
        ?>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
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
                                    <?php if ($CATEGORY1_SEQ != "COLLABO") { ?>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="col-form-label ml-2" for="order_id"><?=$category2_name?></label>

                                                <select class="form-control" id="CATEGORY2_SEQ" name="CATEGORY2_SEQ" >
                                                    <option value=""><?=$category2_name?></option>
                                                    <?php getSERIESComboList();?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id"><?=$title_val?></label>

                                            <input type="text" id="TITLE" name="TITLE" value="<?=$TITLE;?>" placeholder="<?=$title_val?>을/를 입력해주세요." class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <?php if ($CATEGORY1_SEQ != "COLLABO") { ?>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="col-form-label ml-2" for="order_id">메인노출여부</label>

                                                <select class="form-control" id="TITLE_YN" name="TITLE_YN" >
                                                    <?php gfn_getComboList("메인노출구분","AD002",$TITLE_YN,"메인노출여부")?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id">노출여부</label>

                                            <select class="form-control" id="MAIN_YN" name="MAIN_YN" >
                                                <?php gfn_getComboList("노출구분","AD002",$type_gb,"노출여부")?>
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

                            <div class="ibox-tools">
                                <button type="button" class="btn btn-primary btn-s m-t-xs" onclick="javascript:location.href='category_details.php?<?=$INS_query_string?>&mode=INS';">등록</button>
                            </div>
                        </div>

                        <div class="ibox-content">
                            <div class="table-responsive">
                                <div id="DataTables_Table_0_wrapper" class="dt-bootstrap4">
                                    <table class="table table-striped table-bordered table-hover dataTables-example dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" role="grid">
                                        <thead>
                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10px;">번호</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 140px;"><?=$category2_val?></th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 140px;"><?=$title_name?></th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 100px;">썸네일 이미지</th>
                                                <?php if ($CATEGORY1_SEQ != "COLLABO") { ?>
                                                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 20px;">메인노출여부</th>
                                                <?php } ?>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 10px;">노출여부</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 10px;">정렬</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 30px;">등록자</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 80px;">기간</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php getMain_List(); ?>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th rowspan="1" colspan="1">번호</th>
                                                <th rowspan="1" colspan="1"><?=$category2_val?></th>
                                                <th rowspan="1" colspan="1"><?=$title_name?></th>
                                                <th rowspan="1" colspan="1">썸네일 이미지</th>
                                                <?php if ($CATEGORY1_SEQ != "COLLABO") { ?>
                                                    <th rowspan="1" colspan="1">메인노출여부</th>
                                                <?php } ?>
                                                <th rowspan="1" colspan="1">노출여부</th>
                                                <th rowspan="1" colspan="1">정렬</th>
                                                <th rowspan="1" colspan="1">등록자</th>
                                                <th rowspan="1" colspan="1">기간</th>
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
                emptyTable: "등록된 카테고리가 없습니다." // "No data available in table" 대신 사용할 메시지 설정
            },
            rowCallback: function(row, data, index) {  // 자동 순번 매김
                const api = this.api();
                $(row).find('.simple_numbers').text(api.page.info().start + index + 1);
            },
            buttons: [
                {extend: 'copy', title: '<?=$title_name?>'},
                {extend: 'csv', title: '<?=$title_name?>'},
                {extend: 'excel', title: '<?=$title_name?>'},
                {extend: 'print', title: '<?=$title_name?>',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                    }
                }
            ]
        });
    });
</script>