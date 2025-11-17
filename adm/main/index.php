<?php
/**
 * 파일명 : index.php
 * 내용 : 관리자 화면 메인 화면
 * 최초작성날짜 : 2023/03/09
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment      
 * 김민성    2023/03/09     V1.0
 */

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
    $dates = get_request_param('daterange','GET');
    $page = get_request_param('page', 'GET');
    $page_type = get_request_param('page_type', 'GET');

    $title_name = "접속통계"; // Copy, CSV, Excel, Print 제목

    //파라미터 체크
    if (!is_numeric($page)) {
        $page = 1;
    }

    //변수 정리
    $arrValue       = array();
    $arrDate        = array();
    $arrDateGap     = array();
    $yoil_chart     = array();
    $month_chart    = array();
    $limit          = 10;
    $start_date     = date('Y-m-d', strtotime('-7 days'));
    $end_date       = date('Y-m-d');
    $table          = 'web_log';
    $where          = '';

    //검색
    //날짜
    if (!empty($dates)) {
        $arrDate    = explode(' - ', $dates);
        $start_date = trim($arrDate[0]);
        $end_date   = trim($arrDate[1]);
    }

    $where .= " AND log_date BETWEEN :sdate AND :edate";
    $where .= " AND PAGE_TYPE = :PAGE_TYPE";
    $arrValue[':sdate'] = $start_date;
    $arrValue[':edate'] = $end_date;
    $arrValue[':PAGE_TYPE'] = $page_type;

    //date gap
    $arrDateGap     = _date_gap($start_date, $end_date);

    //초기화
    foreach ($arrDateGap as $key => $val) {
        $arrDateGap[$key] = 0;
    }

    //DB 요일별
    $sql = "
         SELECT a.day
              , COUNT(log_ip) AS data 
         FROM (SELECT day
                 FROM (SELECT 0 AS 'day'
                        UNION ALL
                       SELECT 1
                        UNION ALL
                       SELECT 2
                        UNION ALL
                       SELECT 3
                        UNION ALL
                       SELECT 4
                        UNION ALL
                       SELECT 5
                        UNION ALL
                       SELECT 6
                 ) yoils
         ) a LEFT OUTER JOIN
         (SELECT weekday(log_date) AS log_date
               , log_ip
            FROM {$table} x
           WHERE 1
             AND log_date BETWEEN :sdate AND :edate
             AND PAGE_TYPE = :PAGE_TYPE
           GROUP BY log_date, log_ip 
         ) AS b ON (
             a.day = b.log_date
         )
         GROUP BY a.day
    ";
    
    $name_sql = "요일별방문자";
    $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $yoil_data  = $clefResult->getResultSet();

    if (is_array($yoil_chart)) {
        for ($i = 0; $i < 7; $i++) {
            $item = $yoil_data[$i];

            if (!empty($item)) {
                $yoil_chart['data'][] = $item['data'];
            }
        }
    }

    //DB 월별
    $sql = "
         SELECT a.month
              , COUNT(log_ip) AS data
         FROM (SELECT month
                 FROM (SELECT '01' AS 'month'
                        UNION ALL
                       SELECT '02'
                        UNION ALL
                       SELECT '03'
                        UNION ALL
                       SELECT '04'
                        UNION ALL
                       SELECT '05'
                        UNION ALL
                       SELECT '06'
                        UNION ALL
                       SELECT '07'
                        UNION ALL
                       SELECT '08'
                        UNION ALL
                       SELECT '09'
                        UNION ALL
                       SELECT '10'
                        UNION ALL
                       SELECT '11'
                        UNION ALL
                       SELECT '12'
                 ) month
         ) a LEFT OUTER JOIN
         (SELECT log_month
               , log_ip
            FROM {$table} x
           WHERE 1
             AND log_date BETWEEN :sdate AND :edate
             AND PAGE_TYPE = :PAGE_TYPE
           GROUP BY log_month, log_ip
         ) AS b ON (
             a.month = b.log_month
         )
         GROUP BY a.month
    ";
    
    $name_sql = "월별방문자";
    $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $month_data = $clefResult->getResultSet();
    if (is_array($month_chart)) {
        foreach ($month_data as $item) {
            if (!empty($item)) {
                $month_chart['data'][] = $item['data'];
            }
        }
    }

    //DB 일별
    $sql = "
         SELECT
             log_date, COUNT(1) AS LOG_IP_CNT 
         FROM (
             SELECT
                 log_date, log_ip
             FROM {$table} 
             WHERE 1
               AND PAGE_TYPE = :PAGE_TYPE
             GROUP BY log_date, log_ip
         ) AS TBL
         WHERE 1
             AND log_date BETWEEN :sdate AND :edate
         GROUP BY log_date
    ";
    
    $name_sql = "일별방문자";
    $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $daily_data = $clefResult->getResultSet();

    if (is_array($arrDateGap)) {
        foreach ($daily_data as $item) {
            if (!empty($item)) {
                $arrDateGap[$item['log_date']] = $item['LOG_IP_CNT'];
            }
        }
    }

    //DB 로그
    $sql = "
         SELECT log_seq
           FROM {$table}
          WHERE 1
             {$where}
    ";
    
    $name_sql = "디비로그";
    $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }

    $total = $clefResult->getCount();

    //DB
    $sql = "
         SELECT *
           FROM {$table}
          WHERE 1
             {$where}
          ORDER BY log_unixtime DESC
          limit 0, 1000";
    
    $name_sql = "디비";
    $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }
    $list = $clefResult->getResultSet();

    //페이징
    $arrParams = array(
        'dates' => $dates
    );

    $query_string   = http_build_query($arrParams);
} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg'] = $e->getMessage();
    echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);

}
?>

<body class="pace-done">

<div id="wrapper">

    <?php
        include_once __DIR__ .'/../common/nav.php';
    ?>

        <div class="wrapper wrapper-content animated fadeInRight">

            <div class="row">
                <div class="col-lg-12">
                    <div class="float-right">
                        <div class="form-group " id="data_5">
                        <form id="frm" method="get" action="<?=$_SERVER['PHP_SELF'];?>">
                            <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>">
                            <div class="input-daterange input-group" id="datepicker">
                                <input class="form-control" type="text" name="daterange" value="<?=$start_date;?> - <?=$end_date;?>">
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>

           <div class="row">
                <div class="col-lg-6">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>요일별방문자</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#" class="dropdown-item">Config option 1</a>
                                    </li>
                                    <li><a href="#" class="dropdown-item">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content">
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="yoil_chart" style="padding: 0px; position: relative;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>월별방문자</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>

                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>

                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#" class="dropdown-item">Config option 1</a>
                                    </li>
                                    <li><a href="#" class="dropdown-item">Config option 2</a>
                                    </li>
                                </ul>

                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content">
                            <div class="flot-chart">
                                <div class="flot-chart-content" id="month_chart" style="padding: 0px; position: relative;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>일별방문자 </h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>

                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>

                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#" class="dropdown-item">Config option 1</a>
                                    </li>

                                    <li><a href="#" class="dropdown-item">Config option 2</a>
                                    </li>
                                </ul>

                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content">
                            <div id="morris-one-line-chart" style="position: relative; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>접속통계</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>

                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content">
                            <div class="table-responsive">
                                <div id="DataTables_Table_0_wrapper" class="dt-bootstrap4">
                                    <table class="table table-striped table-bordered table-hover dataTables-example dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" role="grid">
                                        <thead>
                                            <tr role="row">
                                                <th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 10px;">번호</th>
                                                <th class="sorting text-center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 60px;">유입일자</th>
                                                <th class="sorting text-center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 324.766px;">페이지명</th>
                                                <th class="sorting text-center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 238.672px;">유입주소(Referer)</th>
                                                <th class="sorting text-center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending" style="width: 183.188px;">아이피</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                            //리스트
                                            if (!empty($list)) {
                                                foreach ($list as $data) {
                                                    //DB 변수 정리
                                                    $_db_log_date       = _check_var($data['log_date']);
                                                    $_db_log_url        = _check_var($data['log_url']);
                                                    $_db_log_referer    = _check_var($data['log_referrer']);
                                                    $_db_log_ip         = _check_var($data['log_ip']);

                                                    echo <<<TR
                                                                <tr class="gradeA odd" role="row" style:"overflow:hidden">
                                                                    <td class="simple_numbers"></td>
                                                                    <td>{$_db_log_date}</td>
                                                                    <td style="max-width: 500px; overflow:hidden; white-space: nowrap; text-overflow: ellipsis">{$_db_log_url}</td>
                                                                    <td style="max-width: 500px; overflow:hidden; white-space: nowrap; text-overflow: ellipsis">{$_db_log_referer}</td>
                                                                    <td>{$_db_log_ip}</td>
                                                                </tr>
                                                            TR;
                                                }
                                            }
                                        ?>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th class="text-center" rowspan="1" colspan="1">번호</th>
                                                <th class="text-center" rowspan="1" colspan="1">유입일자</th>
                                                <th class="text-center" rowspan="1" colspan="1">페이지명</th>
                                                <th class="text-center" rowspan="1" colspan="1">유입주소(Referer)</th>
                                                <th class="text-center" rowspan="1" colspan="1">아이피</th>
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

<script type="text/javascript" src="https://gcore.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://gcore.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
// 엑셀 및 정렬
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';

$(document).ready(function(){
    $("#datepicker").on("apply.daterangepicker", function(ev,picker) {
        $("#frm").submit();
    });

    $('input[name="daterange"]').daterangepicker({
        startDate: "<?=$start_date;?>",
        endDate: "<?=$end_date;?>",
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.dataTables-example').DataTable({
        pageLength: <?=$limit?>,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        rowCallback: function(row, data, index) {  // 자동 순번 매김
            const api = this.api();
            $(row).find('.simple_numbers').text(api.page.info().start + index + 1);
        },
        buttons: [
            {extend: 'copy', title: '<?=$title_name?>'},
            {extend: 'csv', title: '<?=$title_name?>',
                customize: function (csv) {
                return '\uFEFF' + csv; // CSV 데이터 앞에 UTF-8 BOM 문자를 추가하여 UTF-8-SIG로 인코딩
                }},
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

//요일별 차트
$(function() {
    var barData = [
        [1, <?= $yoil_chart['data'][0] ?>], // 월
        [2, <?= $yoil_chart['data'][1] ?>], // 화
        [3, <?= $yoil_chart['data'][2] ?>], // 수
        [4, <?= $yoil_chart['data'][3] ?>], // 목
        [5, <?= $yoil_chart['data'][4] ?>], // 금
        [6, <?= $yoil_chart['data'][5] ?>], // 토
        [7, <?= $yoil_chart['data'][6] ?>], // 일
    ];

    var barOptions = {
        series: {
            bars: {
                show: true,
                barWidth: 0.8,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 0.8
                    }, {
                        opacity: 0.8
                    }]
                }
            }
        },
        xaxis: {
            tickDecimals: 0,
            ticks: [
                [1, '월'],
                [2, '화'],
                [3, '수'],
                [4, '목'],
                [5, '금'],
                [6, '토'],
                [7, '일']
            ]
        },
        yaxis: {
            tickDecimals: 0
        },
        colors: ["#1ab394"],
        grid: {
            color: "#999999",
            hoverable: true,
            clickable: true,
            tickColor: "#D4D4D4",
            borderWidth: 0
        },
        legend: {
            show: false
        },
        tooltip: true,
        tooltipOpts: {
                content: function(label, x, y) {
                var monthLabels = ['월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일'];
                var monthIndex = x - 1;
                var month = monthLabels[monthIndex];
                return  month + "</br> 요일별방문자수: " + y;
            }
        }
    };
    $.plot($("#yoil_chart"), [barData], barOptions);
});

//월별 차트
$(function() {
    var barData = {
        label: "bar",
        data: [
            [1, <?= $month_chart['data'][0] ?>], // 1월
            [2, <?= $month_chart['data'][1] ?>], // 2월
            [3, <?= $month_chart['data'][2] ?>], // 3월
            [4, <?= $month_chart['data'][3] ?>], // 4월
            [5, <?= $month_chart['data'][4] ?>], // 5월
            [6, <?= $month_chart['data'][5] ?>], // 6월
            [7, <?= $month_chart['data'][6] ?>], // 7월
            [8, <?= $month_chart['data'][7] ?>], // 8월
            [9, <?= $month_chart['data'][8] ?>], // 9월
            [10, <?= $month_chart['data'][9] ?>], // 10월
            [11, <?= $month_chart['data'][10] ?>], // 11월
            [12, <?= $month_chart['data'][11] ?>], // 12월
        ]
    };

    var barOptions = {
        series: {
            bars: {
                show: true,
                barWidth: 0.8,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 0.8
                    }, {
                        opacity: 0.8
                    }]
                }
            }
        },
        xaxis: {
            tickDecimals: 0,
            ticks: [
                [1, '1월'],
                [2, '2월'],
                [3, '3월'],
                [4, '4월'],
                [5, '5월'],
                [6, '6월'],
                [7, '7월'],
                [8, '8월'],
                [9, '9월'],
                [10, '10월'],
                [11, '11월'],
                [12, '12월']
            ]
        },
        yaxis: {
            tickDecimals: 0
        },
        colors: ["#1ab394"],
        grid: {
            color: "#999999",
            hoverable: true,
            clickable: true,
            tickColor: "#D4D4D4",
            borderWidth: 0
        },
        legend: {
            show: false
        },
        tooltip: true,
        tooltipOpts: {
            content: function(label, x, y) {
                var monthLabels = ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'];
                var monthIndex = x - 1;
                var month = monthLabels[monthIndex];
                return  month + "</br> 월별방문자수: " + y;
            }
        }
    };

    $.plot($("#month_chart"), [barData], barOptions);
});


// 일별 차트
// 주어진 x축 데이터 배열
const a = <?=json_encode(array_keys($arrDateGap));?>;
const b = <?=json_encode(array_values($arrDateGap));?>;

// 데이터 배열에 값을 저장하기 위한 변수
let data = a.map((year, index) => ({ year: year, value: b[index] }));

// 연도를 기준으로 데이터 배열을 정렬하는 함수
function sortByYear(a, b) {
    return Date.parse(a.year) - Date.parse(b.year);
}

// 중복된 값 제거하는 함수
function removeDuplicates(arr) {
    return arr.filter((item, index) => arr.indexOf(item) === index);
}

function updateChart() {
    // 중복된 연도의 데이터 중에서 최신 데이터만 선택하여 uniqueData 배열에 저장
    const uniqueData = {};
    data.forEach(item => {
        if (!(item.year in uniqueData) || Date.parse(uniqueData[item.year].year) < Date.parse(item.year)) {
            uniqueData[item.year] = item;
        }
    });

    // uniqueData를 배열로 변환하고 연도를 기준으로 정렬
    const sortedUniqueData = Object.values(uniqueData).sort(sortByYear);

    // 기존 차트 제거 (있을 경우)
    if (window.morrisChart) {
        window.morrisChart.destroy();
    }

    // Morris Line Chart 생성
    window.morrisChart = Morris.Line({
        element: 'morris-one-line-chart',
        data: sortedUniqueData,
        xkey: 'year',
        ykeys: ['value'],
        resize: true,
        lineWidth: 4,
        labels: ['일별방문자'],
        lineColors: ['#1ab394'],
        pointSize: 5,
        xLabelFormat: function (x) {
            const date = new Date(x);
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    });
}

// 초기 차트 생성
updateChart();

// 예시 사용법: 새로운 데이터 포인트 추가
// 인수를 원하는 값으로 바꾸세요
// 새 데이터를 추가할 때마다 이 함수를 호출할 수 있습니다
function addDataPoint(year, value) {
    data.push({ year: year, value: value });
    updateChart(); // 새 데이터로 차트를 업데이트합니다
}
</Script>

<div class="daterangepicker dropdown-menu show-calendar opensright" style="top: 2740.77px; left: 266px; right: auto; display: none;">
    <div class="calendar first right">
        <div class="calendar-date">
            <table class="table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th colspan="5" class="month">Jan 2015</th>
                        <th class="next available">
                            <i class="fa fa-arrow-right icon icon-arrow-right glyphicon glyphicon-arrow-right"></i>
                        </th>
                    </tr>
                    <tr>
                        <th>Su</th>
                        <th>Mo</th>
                        <th>Tu</th>
                        <th>We</th>
                        <th>Th</th>
                        <th>Fr</th>
                        <th>Sa</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="off disabled" data-title="r0c0">28</td>
                        <td class="off disabled" data-title="r0c1">29</td>
                        <td class="off disabled" data-title="r0c2">30</td>
                        <td class="off disabled" data-title="r0c3">31</td>
                        <td class="available in-range" data-title="r0c4">1</td>
                        <td class="available in-range" data-title="r0c5">2</td>
                        <td class="available in-range" data-title="r0c6">3</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r1c0">4</td>
                        <td class="available in-range" data-title="r1c1">5</td>
                        <td class="available in-range" data-title="r1c2">6</td>
                        <td class="available in-range" data-title="r1c3">7</td>
                        <td class="available in-range" data-title="r1c4">8</td>
                        <td class="available in-range" data-title="r1c5">9</td>
                        <td class="available in-range" data-title="r1c6">10</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r2c0">11</td>
                        <td class="available in-range" data-title="r2c1">12</td>
                        <td class="available in-range" data-title="r2c2">13</td>
                        <td class="available in-range" data-title="r2c3">14</td>
                        <td class="available in-range" data-title="r2c4">15</td>
                        <td class="available in-range" data-title="r2c5">16</td>
                        <td class="available in-range" data-title="r2c6">17</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r3c0">18</td>
                        <td class="available in-range" data-title="r3c1">19</td>
                        <td class="available in-range" data-title="r3c2">20</td>
                        <td class="available in-range" data-title="r3c3">21</td>
                        <td class="available in-range" data-title="r3c4">22</td>
                        <td class="available in-range" data-title="r3c5">23</td>
                        <td class="available in-range" data-title="r3c6">24</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r4c0">25</td>
                        <td class="available in-range" data-title="r4c1">26</td>
                        <td class="available in-range" data-title="r4c2">27</td>
                        <td class="available in-range" data-title="r4c3">28</td>
                        <td class="available in-range" data-title="r4c4">29</td>
                        <td class="available in-range" data-title="r4c5">30</td>
                        <td class="available active end-date" data-title="r4c6">31</td>
                    </tr>
                    <tr>
                        <td class="available off" data-title="r5c0">1</td>
                        <td class="available off" data-title="r5c1">2</td>
                        <td class="available off" data-title="r5c2">3</td>
                        <td class="available off" data-title="r5c3">4</td>
                        <td class="available off" data-title="r5c4">5</td>
                        <td class="available off" data-title="r5c5">6</td>
                        <td class="available off" data-title="r5c6">7</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="calendar second left">
        <div class="calendar-date">
            <table class="table-condensed">
                <thead>
                    <tr>
                        <th class="prev available">
                            <i class="fa fa-arrow-left icon icon-arrow-left glyphicon glyphicon-arrow-left"></i>
                        </th>
                        <th colspan="5" class="month">Jan 2015</th>
                        <th class="next available">
                            <i class="fa fa-arrow-right icon icon-arrow-right glyphicon glyphicon-arrow-right"></i>
                        </th>
                    </tr>
                    <tr>
                        <th>Su</th>
                        <th>Mo</th>
                        <th>Tu</th>
                        <th>We</th>
                        <th>Th</th>
                        <th>Fr</th>
                        <th>Sa</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="available off" data-title="r0c0">28</td>
                        <td class="available off" data-title="r0c1">29</td>
                        <td class="available off" data-title="r0c2">30</td>
                        <td class="available off" data-title="r0c3">31</td>
                        <td class="available active start-date" data-title="r0c4">1</td>
                        <td class="available in-range" data-title="r0c5">2</td>
                        <td class="available in-range" data-title="r0c6">3</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r1c0">4</td>
                        <td class="available in-range" data-title="r1c1">5</td>
                        <td class="available in-range" data-title="r1c2">6</td>
                        <td class="available in-range" data-title="r1c3">7</td>
                        <td class="available in-range" data-title="r1c4">8</td>
                        <td class="available in-range" data-title="r1c5">9</td>
                        <td class="available in-range" data-title="r1c6">10</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r2c0">11</td>
                        <td class="available in-range" data-title="r2c1">12</td>
                        <td class="available in-range" data-title="r2c2">13</td>
                        <td class="available in-range" data-title="r2c3">14</td>
                        <td class="available in-range" data-title="r2c4">15</td>
                        <td class="available in-range" data-title="r2c5">16</td>
                        <td class="available in-range" data-title="r2c6">17</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r3c0">18</td>
                        <td class="available in-range" data-title="r3c1">19</td>
                        <td class="available in-range" data-title="r3c2">20</td>
                        <td class="available in-range" data-title="r3c3">21</td>
                        <td class="available in-range" data-title="r3c4">22</td>
                        <td class="available in-range" data-title="r3c5">23</td>
                        <td class="available in-range" data-title="r3c6">24</td>
                    </tr>
                    <tr>
                        <td class="available in-range" data-title="r4c0">25</td>
                        <td class="available in-range" data-title="r4c1">26</td>
                        <td class="available in-range" data-title="r4c2">27</td>
                        <td class="available in-range" data-title="r4c3">28</td>
                        <td class="available in-range" data-title="r4c4">29</td>
                        <td class="available in-range" data-title="r4c5">30</td>
                        <td class="available in-range" data-title="r4c6">31</td>
                    </tr>
                    <tr>
                        <td class="available off" data-title="r5c0">1</td>
                        <td class="available off" data-title="r5c1">2</td>
                        <td class="available off" data-title="r5c2">3</td>
                        <td class="available off" data-title="r5c3">4</td>
                        <td class="available off" data-title="r5c4">5</td>
                        <td class="available off" data-title="r5c5">6</td>
                        <td class="available off" data-title="r5c6">7</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ranges">
        <div class="range_inputs">
            <div class="daterangepicker_start_input">
                <label for="daterangepicker_start">From</label>
                <input class="input-mini" type="text" name="daterangepicker_start" value="">
            </div>
            <div class="daterangepicker_end_input">
                <label for="daterangepicker_end">To</label>
                <input class="input-mini" type="text" name="daterangepicker_end" value="">
            </div>
            <button class="applyBtn btn btn-small btn-sm btn-success">Apply</button>
            &nbsp;
            <button class="cancelBtn btn btn-small btn-sm btn-default">Cancel</button>
        </div>
    </div>
</div>

</body>

</html>
