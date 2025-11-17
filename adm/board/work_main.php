<?php
/**
 * 파일명 : work_main.php
 * 내용 : 작품 페이지
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/07    V1.0
 * 김민성    2023/11/13    shop 기능추가
 */
    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';

    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/work_main_code.php';
?>

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
                            <input type="hidden" id="sub_type" name="sub_type" value="<?=$sub_type?>">
                            <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>">
                            <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>">

                            <div class="row">
                                <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                    <div class="col-sm-2">
                                        <label class="col-form-label ml-2" for="order_id">중분류</label>

                                        <select class="form-control" id="PROGRAM_CD" name="PROGRAM_CD" >
                                            <?php gfn_getComboList("중분류", "COL014", $PROGRAM_CD,"중분류")?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label ml-2" for="order_id"><?=$category1_name?></label>

                                        <select class="form-control" id="CATEGORY1_SEQ" name="CATEGORY1_SEQ" >
                                            <option value=""><?=$category1_name?></option>
                                            <?php getARTISTComboList();?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label ml-2" for="order_id"><?=$category2_name?></label>

                                        <select class="form-control" id="CATEGORY2_SEQ" name="CATEGORY2_SEQ" >
                                            <option value=""><?=$category2_name?></option>
                                            <?php getSERIESComboList();?>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id">메인노출여부</label>

                                            <select class="form-control" id="TITLE_YN" name="TITLE_YN" >
                                                <?php gfn_getComboList("메인노출여부","AD002",$TITLE_YN,"메인노출여부")?>
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
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label ml-2" for="order_id"><?=$title_val?></label>

                                        <input type="text" id="TITLE" name="TITLE" value="<?=$TITLE;?>" placeholder="<?=$title_val?>을/를 입력해주세요." class="form-control">
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
                            <?php if ($page_type == PAGE2) { ?>
                                <button type="button" class="btn btn-primary btn-s m-t-xs" onclick="javascript:location.href='work_details.php?<?=$INS_query_string?>&mode=INS&MULTI=Y';">다중 등록</button>
                            <?php } ?>    
                            <button type="button" class="btn btn-primary btn-s m-t-xs" onclick="javascript:location.href='work_details.php?<?=$INS_query_string?>&mode=INS';">등록</button>
                        </div>
                    </div>

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dt-bootstrap4">
                                <table class="table table-striped table-bordered table-hover dataTables-example dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" role="grid">
                                    <thead>
                                        <tr role="row">
                                            <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10px;">번호</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 80px;"><?=$category1_val?></th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 140px;"><?=$category2_val?></th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 140px;"><?=$title_name?></th>
                                            <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 10px;">메인노출여부</th>
                                            <?php } ?>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 10px;">노출여부</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 10px;">정렬</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 30px;">등록자</th>
                                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 80px;">등록일</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                         <?php getMain_List(); ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th rowspan="1" colspan="1">번호</th>
                                            <th rowspan="1" colspan="1"><?=$category1_val?></th>
                                            <th rowspan="1" colspan="1"><?=$category2_val?></th>
                                            <th rowspan="1" colspan="1"><?=$title_name?></th>
                                            <?php if ($page_type == PAGE3 && $sub_type == SUB_PAGE2) { ?>
                                                <th rowspan="1" colspan="1">메인노출여부</th>
                                            <?php } ?>
                                            <th rowspan="1" colspan="1">노출여부</th>
                                            <th rowspan="1" colspan="1">정렬</th>
                                            <th rowspan="1" colspan="1">등록자</th>
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
    if ('<?=$sub_type?>' == '<?=SUB_PAGE2?>') {
        let PROGRAM_CD_value = '<?=$PROGRAM_CD?>'; // 분류 값

        gfn_setupSelectBox('PROGRAM_CD','CATEGORY1_SEQ',PROGRAM_CD_value);
    }

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
                emptyTable: "등록된 작품이 없습니다." // "No data available in table" 대신 사용할 메시지 설정
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


    let COUNTRY_value = '<?=$CATEGORY2_SEQ?>'; // 시리즈 코드 값

    const selectBox = document.getElementById('CATEGORY1_SEQ'); // 작가명

    selectBox.addEventListener('change', () => {
        const selectedValue = selectBox.value;
        const selectBox2 = document.getElementById('CATEGORY2_SEQ'); // 시리즈
        const options = selectBox2.options;

        Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

        if (selectedValue === '') { // 전체 옵션 선택한 경우
            selectBox2.selectedIndex = -1; // 선택된 option 요소 초기화
            Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

            selectBox2.options[0].hidden = false;
            selectBox2.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '시리즈'
        } else { // 일반적인 경우
            const filteredOptions = Array.from(options).filter(option => {
                const dataCode = option.getAttribute('data-code1');
                return dataCode === selectedValue; // 선택한 값과 일치하는 option 요소만 필터링
            });

            filteredOptions.forEach(option => option.hidden = false); // 필터링된 option 요소만 보이기

            selectBox2.options[0].hidden = false;

            if (COUNTRY_value != '') {
                for (let i = 0; i < selectBox2.options.length; i++) {
                    if (selectBox2.options[i].value === COUNTRY_value) {
                        selectBox2.selectedIndex = i;
                        COUNTRY_value = '';
                        break;
                    }
                }
            } else {
                selectBox2.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '도시'
            }
        }
    });

    selectBox.dispatchEvent(new Event('change')); // change 이벤트 강제 발생
</script>