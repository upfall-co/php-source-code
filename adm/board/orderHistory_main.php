<?php
/**
 * 파일명 : orderHistory_main.php
 * 내용 : 주문내역 페이지
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 이보경
 * ------------------------------------
 * name       date        comment
 * 이보경    2023/08/08    V1.0
 */
    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';
    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/orderHistory_main_code.php';
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

                        <div class="ibox-content m-b-sm border-bottom custom_search_top">
                            <form id="frm" method="get" action="<?=$_SERVER['PHP_SELF'];?>">
                                <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>">
                                <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>">
                                <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>">

                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id"><?=$category1_name?></label>

                                            <select class="form-control" id="CATEGORY1_SEQ" name="CATEGORY1_SEQ">
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

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id"><?=$category3_name?></label>

                                            <select class="form-control" id="CATEGORY3_SEQ" name="CATEGORY3_SEQ" >
                                                <option value=""><?=$category3_name?></option>
                                                <?php getPRODUCTComboList();?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="CATEGORY1_NAME"><?=$category1_val?></label>

                                            <input type="text" class="form-control" id="CATEGORY1_NAME" name="CATEGORY1_NAME" value="<?=$CATEGORY1_NAME?>" max-length="100">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="CATEGORY2_NAME"><?=$category2_val?></label>

                                            <input type="text" class="form-control" id="CATEGORY2_NAME" name="CATEGORY2_NAME" value="<?=$CATEGORY2_NAME?>" max-length="100">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="CATEGORY3_NAME"><?=$category3_val?></label>

                                            <input type="text" class="form-control" id="CATEGORY3_NAME" name="CATEGORY3_NAME" value="<?=$CATEGORY3_NAME?>" max-length="100">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id">결제수단</label>

                                            <select class="form-control" id="TYPE_CD" name="TYPE_CD" >
                                                <?php gfn_getComboList("결제수단", "COL003", $TYPE_CD, "결제수단")?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id">주문상태</label>

                                            <select class="form-control" id="STATE_CD" name="STATE_CD" >
                                                <?php gfn_getComboList("주문상태", "COL005", $STATE_CD, "상태")?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id">주문번호</label>

                                            <input type="text" class="form-control" id="PUR_NUM" name="PUR_NUM" value="<?=$PUR_NUM?>" max-length="50">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="col-form-label ml-2" for="order_id">주문자</label>

                                            <input type="text" class="form-control" id="DLVY_NAME" name="DLVY_NAME" value="<?=$DLVY_NAME?>" max-length="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="col-form-label ml-2" for="order_id">주문일자</label>
                                        
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
                                                <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 40px;">번호</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">결제수단</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">주문일자</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">주문번호</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;"><?=$category3_val?></th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">총 주문 금액</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">현금영수증</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">주문자</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">주문구분<br>(회원/ 비회원)</th>
                                                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: auto;">주문상태</th>
                                                
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php getMain_List(); ?>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th rowspan="1" colspan="1">번호</th>
                                                <th rowspan="1" colspan="1">결제수단</th>
                                                <th rowspan="1" colspan="1">주문일자</th>
                                                <th rowspan="1" colspan="1">주문번호</th>
                                                <th rowspan="1" colspan="1"><?=$category3_val?></th>
                                                <th rowspan="1" colspan="1">총 주문 금액</th>
                                                <th rowspan="1" colspan="1">현금영수증</th>
                                                <th rowspan="1" colspan="1">주문자</th>
                                                <th rowspan="1" colspan="1">주문구분<br>(회원/ 비회원)</th>
                                                <th rowspan="1" colspan="1">주문상태</th>
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

    $(document).ready(function() {
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
                emptyTable: "등록된 주문내역이 없습니다." // "No data available in table" 대신 사용할 메시지 설정
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

    let SERIES_value = '<?=$CATEGORY2_SEQ?>'; // 시리즈 코드 값
    let PRODUCT_value = '<?=$CATEGORY3_SEQ?>'; // 작품코드 값

    const selectBox = document.getElementById('CATEGORY1_SEQ'); // 작가명
    const selectBox3 = document.getElementById('CATEGORY2_SEQ'); // 작가명


    selectBox.addEventListener('change', () => {
        const selectedValue = selectBox.value;
        const selectedValue2 = selectBox3.value;
        const selectBox2 = document.getElementById('CATEGORY2_SEQ'); // 시리즈
        const selectBox4 = document.getElementById('CATEGORY3_SEQ'); // 작품
        const options = selectBox2.options;
        const options2 = selectBox4.options;

        Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

        if (selectedValue === '') { // 전체 옵션 선택한 경우
            //시리즈 초기화
            selectBox2.selectedIndex = -1; // 선택된 option 요소 초기화
            Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

            selectBox2.options[0].hidden = false;
            selectBox2.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '시리즈'

            //작품 초기화
            selectBox4.selectedIndex = -1; // 선택된 option 요소 초기화
            Array.from(options2).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

            selectBox4.options[0].hidden = false;
            selectBox4.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '작품'
        } else { // 일반적인 경우
            const filteredOptions = Array.from(options).filter(option => {
                const dataCode = option.getAttribute('data-code1');
                return dataCode === selectedValue; // 선택한 값과 일치하는 option 요소만 필터링
            });

            //작품 초기화
            selectBox4.selectedIndex = -1; // 선택된 option 요소 초기화
            Array.from(options2).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

            selectBox4.options[0].hidden = false;
            selectBox4.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '작품'

            //시리즈 해당 필터링된 option
            filteredOptions.forEach(option => option.hidden = false); // 필터링된 option 요소만 보이기

            selectBox2.options[0].hidden = false;

            if (SERIES_value != '') {
                for (let i = 0; i < selectBox2.options.length; i++) {
                    if (selectBox2.options[i].value === SERIES_value) {
                        selectBox2.selectedIndex = i;
                        SERIES_value = '';
                        PRODUCT_value = '';
                        break;
                    }
                }
            } else {
                selectBox2.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '시리즈'
                selectBox4.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '작품'
            }
        }
    });

    selectBox.dispatchEvent(new Event('change')); // change 이벤트 강제 발생

    // 작품
    PRODUCT_value = '<?=$CATEGORY3_SEQ?>';

    selectBox3.addEventListener('change', () => {
        const selectedValue2 = selectBox3.value;
        const selectBox4 = document.getElementById('CATEGORY3_SEQ'); // 작품
        const options2 = selectBox4.options;

        Array.from(options2).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

        if (selectedValue2 === '') { // 전체 옵션 선택한 경우
            selectBox4.selectedIndex = -1; // 선택된 option 요소 초기화
            Array.from(options2).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

            selectBox4.options[0].hidden = false;
            selectBox4.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '작품'
        } else { // 일반적인 경우
            const filteredOptions = Array.from(options2).filter(option => {
                const dataCode = option.getAttribute('data-code1');
                return dataCode === selectedValue2; // 선택한 값과 일치하는 option 요소만 필터링
            });

            filteredOptions.forEach(option => option.hidden = false); // 필터링된 option 요소만 보이기

            selectBox4.options[0].hidden = false;

            if (PRODUCT_value != '') {
                for (let i = 0; i < selectBox4.options.length; i++) {
                    if (selectBox4.options[i].value === PRODUCT_value) {
                        selectBox4.selectedIndex = i;
                        PRODUCT_value = '';
                        break;
                    }
                }
            } else {
                selectBox4.selectedIndex = 0; // 첫 번째 필터링된 option 요소 선택 '작품'
            }
        }
    });

    selectBox3.dispatchEvent(new Event('change')); // change 이벤트 강제 발생
</script>