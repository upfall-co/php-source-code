<?php
//head
define("SUB", "");
include_once __DIR__ .'/../common/head.php';

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

$mysqldb    = new Pdo7();
$clefResult = new ClefResult();

$arrRtn     = array(
    'code'  => 500,
    'msg'   => ''
);

try {
    //파라미터 정리
    $type = get_request_param('type', 'GET');
    $page_type = get_request_param('page_type', 'GET');
    $m_seq = get_request_param('m_seq', 'GET');
    $mp_seq = get_request_param('mp_seq', 'GET');

    $type_value = "";

    //파라미터 체크
    if (empty($type) || !in_array($type, array_keys($config['site']['config']['terms_'.$page_type]))) {
        $type = key($config['site']['config']['terms_'.$page_type]);
        $type_value = current($config['site']['config']['terms_'.$page_type]);
    } else {
        $type_value = $config['site']['config']['terms_'.$page_type][$type];
    }

    //변수 정리
    $table          = 'site_config';
    $category       = 'terms';
    $mode           = 'reg';
    $str_mode       = '';
    $_db_content    = '';

    //DB
    $sql = "
        SELECT
            *
        FROM {$table}
        WHERE 1
            AND category = '{$category}'
            AND type = :type
            AND PAGE_TYPE = :page_type
        LIMIT 1
    ";
    //_p($sql);
    $clefResult = $mysqldb->get($sql, [':type' => $type, ':page_type' => $page_type]);

    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }
    $data = $clefResult->getResultSet();

    if (!empty($data)) {
        $mode = 'mod';

        //DB 변수 정리
        $_db_content = _check_var($data['content']);
    }
    $str_mode   = ($mode) ? $config['proc']['type'][$mode] : '';

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
            <div class="wrapper wrapper-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox ">
                            <div class="float-e-margins">
                                <p>
                                    <?php if ($mode == 'mod') : ?>
                                        <button type="button" class="btn btn-danger float-right ml-1 w60" onclick="javascript:termsDel();">삭제</button>
                                    <?php endif; ?>

                                    <button type="button" class="btn btn-primary float-right w60" onclick="javascript:termsProc('<?=$mode;?>');"><?=$str_mode;?></button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5><?=$type_value?></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-wrench"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-user">
                                        <?php
                                            if (is_array($config['site']['config']['terms_'.$page_type])) {
                                                foreach ($config['site']['config']['terms_'.$page_type] as $key => $val) {
                                                    echo <<<LI
                                                                <li>
                                                                    <a href="javascript:void(0);" class="dropdown-item" onclick="javascript:$('#type').val('{$key}'); $('#frm').submit();">{$val}</a>
                                                                </li>
                                                            LI;
                                                }
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div>

                            <div class="ibox-content no-padding">
                                <form id="frm" method="get" action="<?=$_SERVER['PHP_SELF'];?>">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode;?>"/>
                                    <input type="hidden" id="type" name="type" value="<?=$type;?>"/>
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>"/>
                                </form>

                                <div>
                                    <textarea id="editor" name="editor"><?=$_db_content;?></textarea>
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
    $(function () {
        //CKEDITOR
        CKEDITOR.editorConfig = function(config) {
            config.colorButton_foreStyle = {
                element: 'font',
                attributes: {
                    'color': '#(color)'
                }
            };
        }
        CKEDITOR.replace('editor', {
            height: 600,
            editorplaceholder: '내용을 입력해주세요.',
            allowedContent: true
        });
        CKEDITOR.instances['editor'].setData($("#editor").val());
    });

    //del
    function termsDel() {
        if (confirm("정말 삭제하시겠습니까?")) {
            $("#mode").val("del");
            let formData = new FormData($("#frm")[0]);
            formData.append("editor", $("#editor").val());

            $.ajax({
                url: "../program/<?=$category;?>.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);

                    //성공
                    alert(json.msg);
                    if (json.code == 200) {
                        location.reload();
                    }
                },
                beforeSend: function() {
                    $(".wrap-loading").removeClass("display-none");
                },
                complete: function() {
                    $(".wrap-loading").addClass("display-none");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }

    //proc
    function termsProc() {
        if (confirm("<?=$str_mode;?>하시겠습니까?")) {
            let formData = new FormData($("#frm")[0]);
            formData.append("editor", CKEDITOR.instances['editor'].getData());

            $.ajax({
                url: "../program/<?=$category;?>.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    let json = JSON.parse(data);

                    //성공
                    alert(json.msg);
                    if (json.code == 200) {
                        location.reload();
                    }
                },
                beforeSend: function() {
                    $(".wrap-loading").removeClass("display-none");
                },
                complete: function() {
                    $(".wrap-loading").addClass("display-none");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }
</script>