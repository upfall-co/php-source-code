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
    $menu_active = get_request_param('menu_active', 'GET');
    $page_type = get_request_param('page_type', 'GET');
    $m_seq = get_request_param('m_seq', 'GET');
    $mp_seq = get_request_param('mp_seq', 'GET');

    //파라미터 체크
    if (empty($menu_active) || !is_numeric($menu_active)) {
        $menu_active= 0;
    }

    //변수 정리
    $arrSiteData    = array(
        'title'     => array('seq' => 0, 'content' => ''),
        'favicon'   => array('seq' => 0, 'content' => ''),
        'meta_tag'  => array('seq' => 0, 'content' => ''),
        'footer'    => array('seq' => 0, 'content' => ''),
        'sns'       => array('seq' => 0, 'content' => ''),
    );
    $arrSiteConfig  = $config['site']['config'];
    $table          = 'site_config';

    //DB
    $sql = "
        SELECT
            *
        FROM {$table}
        WHERE 1
            AND category IN ('title', 'favicon', 'meta_tag', 'footer', 'sns')
            AND PAGE_TYPE = '{$page_type}'
    ";
    $clefResult = $mysqldb->select($sql);
    if (!$clefResult->getResult()) {
        gfn_isValidation(800);
    }
    $site_list  = $clefResult->getResultSet();

    if (!empty($site_list)) {
        foreach ($site_list as $data) {
            //DB 변수 정리
            $_db_seq        = _check_var($data['seq']);
            $_db_content    = _check_var($data['content']);
            $_db_category   = _check_var($data['category']);

            $arrSiteData[$_db_category] = array(
                'seq'       => $_db_seq,
                'content'   => $_db_content,
            );
        }
    }
    //_p($arrSiteData);

    /** favicon **/
    $favicon_content    = json_decode($arrSiteData['favicon']['content'], true);
    $favicon_img_val    = '';
    $favicon_img_path   = '';

    if (!empty($favicon_content['favicon']['img'])) {
        $favicon_img_val    = "{$favicon_content['favicon']['img']}||{$favicon_content['favicon']['img_name']}";
        $favicon_img_path   = UPLOAD_DIR ."/{$table}/favicon/{$favicon_content['favicon']['img']}";
    }
    /** favicon **/

    /** meta OG **/
    $meta_content       = json_decode($arrSiteData['meta_tag']['content'], true);
    $og_img_val         = '';
    $og_img_path        = '';

    if (!empty($meta_content['og']['img'])) {
        $og_img_val     = "{$meta_content['og']['img']}||{$meta_content['og']['img_name']}";
        $og_img_path    = UPLOAD_DIR ."/{$table}/meta_tag/{$meta_content['og']['img']}";
    }
    /** meta OG **/

    /** footer */
    $footer_content     = json_decode($arrSiteData['footer']['content'], true);
    /** footer */

    /** sns */
    $sns_content     = json_decode($arrSiteData['sns']['content'], true);
    /** sns */

    $INS_arrParams = array( // 초기화 및 등록
        'page_type' => $page_type
      , 'mp_seq' => $mp_seq
      , 'm_seq' => $m_seq
    );

    $INS_query_string = http_build_query($INS_arrParams);

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
        <form id="frm" method="post" action="../program/<?=$table;?>.php" enctype="multipart/form-data">
            <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
            <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
            <input type="hidden" id="mode" name="mode" value="reg"/>
            <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>"/>
            <input type="hidden" id="title_seq" name="title_seq" value="<?=$arrSiteData['title']['seq'];?>"/>
            <input type="hidden" id="favicon_seq" name="favicon_seq" value="<?=$arrSiteData['favicon']['seq'];?>"/>
            <input type="hidden" id="meta_seq" name="meta_seq" value="<?=$arrSiteData['meta_tag']['seq'];?>"/>
            <input type="hidden" id="footer_seq" name="footer_seq" value="<?=$arrSiteData['footer']['seq'];?>"/>
            <input type="hidden" id="sns_seq" name="sns_seq" value="<?=$arrSiteData['sns']['seq'];?>"/>
            <input type="hidden" id="menu_active" name="menu_active" value="<?=$menu_active;?>"/>

            <div class="row">
                <div class="col-lg-12">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li><a class="nav-link active" data-toggle="tab" href="#tab-1"> 기본설정</a></li>
                            <li><a class="nav-link" data-toggle="tab" href="#tab-2">메타태그</a></li>
                            <li><a class="nav-link" data-toggle="tab" href="#tab-3">메타태그(OG)</a></li>
                            <li><a class="nav-link" data-toggle="tab" href="#tab-4">푸터정보</a></li>
                            <li><a class="nav-link" data-toggle="tab" href="#tab-5">SNS 주소정보</a></li>
                        </ul>
                        <div class="tab-content custom_detail">
                            <div role="tabpanel" id="tab-1" class="tab-pane active">
                                <div class="panel-body">
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label">타이틀 (title)</label>

                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="title" name="title" value="<?=$arrSiteData['title']['content'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label for="favicon_img" class="col-sm-1 col-form-label text-right">파비콘 이미지</label>

                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="hidden" id="favicon_img_val" name="favicon_img_val" value="<?=$favicon_img_val;?>"/>
                                                    <input id="favicon_img" name="favicon_img" type="file" multiple="" class="custom-file-input" accept=".jpg,.jpeg,.png,.svg,.ico" onchange="javascript:fileupload(this, '#favicon_preview', 'jpg|jpeg|png|svg|ico', 5);">
                                                    <label class="custom-file-label" for="favicon_img">파일 선택</label>
                                                </div>
                                            </div>
                                            <span class="form-text m-b-none text-navy">권장사이즈(192 * 192px) / 5MB 이하 .jpg .jpeg .png .svg .ico<br/>이미지가 깨질 경우 .svg로 업로드해주세요.</span>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row" >
                                        <label for="" class="col-sm-1 col-form-label text-right" >파비콘 이미지 </br> 미리보기</label>

                                        <div class="col-sm-10">
                                            <img src="<?=$favicon_img_path;?>" class="br_img" id="favicon_preview" alt="" width="200"/>
                                            <?php if (!empty($favicon_content['favicon']['img'])) : ?>
                                                <div class="mt-1">
                                                    다운로드 : <a href="<?=$favicon_img_path;?>" download="<?=$favicon_content['favicon']['img_name'];?>"><?=$favicon_content['favicon']['img_name'];?></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" id="tab-2" class="tab-pane">
                                <div class="panel-body">
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label">타이틀 (title)</label>

                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?=$meta_content['meta']['title'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label">키워드 (keywords)</label>

                                        <div class="col-sm-6">
                                            <textarea type="text" class="form-control h-150" id="meta_keywords" name="meta_keywords" rows="5" cols="10" placeholder="- 포털사이트가 홈페이지를 구분/분류하는 데 이용, 관련도 높은 키워드 입력 요망
                                            (관련없는 키워드 입력 시, 홈페이지 신뢰도/정확도가 하락하는 요인이 될 수 있음)
                                            - 콤마(, )로 구분
                                            - 글자수 60자 이내"><?=$meta_content['meta']['keywords'];?></textarea>
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label">설명 </br>(description)</label>

                                        <div class="col-sm-6">
                                            <textarea type="text" class="form-control h-150" id="meta_description" name="meta_description" rows="5" cols="10" placeholder="- 포털사이트 검색결과에 노출될 가능성이 있음
                                        - 글자수 50자 이내"><?=$meta_content['meta']['description'];?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" id="tab-3" class="tab-pane">
                                <div class="panel-body">
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label">타이틀 (title)</label>

                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="og_title" name="og_title" value="<?=$meta_content['og']['title'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label">사이트 이름 (site_name)</label>

                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="og_site_name" name="og_site_name" value="<?=$meta_content['og']['site_name'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label">설명 </br>(description)</label>

                                        <div class="col-sm-6">
                                            <textarea type="text" class="form-control h-150" id="og_description" name="og_description" rows="5" cols="10"><?=$meta_content['og']['description'];?></textarea>
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label for="og_img" class="col-sm-1 col-form-label text-right">OG 이미지</label>

                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="hidden" id="og_img_val" name="og_img_val" value="<?=$og_img_val;?>"/>
                                                    <input id="og_img" name="og_img" type="file" multiple="" class="custom-file-input" accept=".jpg,.jpeg,.png" onchange="javascript:fileupload(this, '#og_preview', 'jpg|jpeg|png', 5);">
                                                    <label class="custom-file-label" for="og_img">파일 선택</label>
                                                </div>
                                            </div>
                                            <span class="form-text m-b-none text-navy">권장사이즈(800 * 399px)<br/>5MB 이하 .jpg .jpeg .png</span>
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row" >
                                        <label for="" class="col-sm-1 col-form-label text-right" >OG 이미지 </br> 미리보기</label>

                                        <div class="col-sm-10">
                                            <img src="<?=$og_img_path;?>" class="br_img" id="og_preview" alt="" width="200"/>
                                            <?php if (!empty($meta_content['og']['img'])) : ?>
                                                <div class="mt-1">
                                                    다운로드 : <a href="<?=$og_img_path;?>" download="<?=$meta_content['og']['img_name'];?>"><?=$meta_content['og']['img_name'];?></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" id="tab-4" class="tab-pane">
                                <div class="panel-body">
                                    <div class="form-group row">
                                        <label for="footer_company_name" class="col-sm-1 text-right col-form-label">사업자명</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="footer_company_name" name="footer_company_name" value="<?=$footer_content['company_name'];?>">
                                        </div>

                                        <label for="footer_rep_name" class="col-sm-1 text-right col-form-label">대표자명</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="footer_rep_name" name="footer_rep_name" value="<?=$footer_content['rep_name'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label for="footer_addr" class="col-sm-1 text-right col-form-label">사업자주소</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="footer_addr" name="footer_addr" value="<?=$footer_content['addr'];?>">
                                        </div>

                                        <label for="footer_mobile" class="col-sm-1 text-right col-form-label">대표번호</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="footer_mobile" name="footer_mobile" value="<?=$footer_content['mobile'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label for="footer_email" class="col-sm-1 text-right col-form-label">이메일</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="footer_email" name="footer_email" value="<?=$footer_content['email'];?>">
                                        </div>

                                        <label for="footer_number" class="col-sm-1 text-right col-form-label">통신판매업</br>신고번호</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="footer_number" name="footer_number" value="<?=$footer_content['number'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label for="footer_company_rep_num" class="col-sm-1 text-right col-form-label">사업자등록번호</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="footer_company_rep_num" name="footer_company_rep_num" value="<?=$footer_content['company_rep_num'];?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" id="tab-5" class="tab-pane">
                                <div class="panel-body">
                                    <!--
                                    <div class="form-group row">
                                        <label for="sns_youtube" class="col-sm-1 text-right col-form-label">youtube 링크</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="sns_youtube" name="sns_youtube" value="<?=$sns_content['youtube'];?>">
                                        </div>
                                    </div>

                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label for="sns_facebook" class="col-sm-1 text-right col-form-label">facebook 링크</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="sns_facebook" name="sns_facebook" value="<?=$sns_content['facebook'];?>">
                                        </div>
                                    </div>
                                            

                                    <div class="hr-line-dashed"></div>-->
                                    <div class="form-group row">
                                        <label for="sns_instargram" class="col-sm-1 text-right col-form-label">instargram 링크</label>

                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="sns_instargram" name="sns_instargram" value="<?=$sns_content['instargram'];?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:reg();">등록</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
    let gfnfile = [];

    //파일업로드
    function fileupload(obj, id, strExt, limitSize) {
        gfnfile = {
          mode : 'I' // [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
        , obj  : obj // input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
        , id   : id // 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
        , strExt : strExt// 확장자 ex) jpg|gif|jpeg|png|pdf|zip
        , limitSize : limitSize // 파일의 사이즈를 확인
        , fileMap : '' // mode가 M인경우 다중파일일 경우 값 저장을 위하여
        , formData_del : '' // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
        , del_count : ''// mode가 M인경우 삭제 pk값보관
        , file_list_row : '' // mode가 M인경우 다중파일의 pk값을 보관
        , row_val : ''//mode가 M인경우  다중파일의  max값을 지정해줌
        , ues : ''// 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile (gfnfile);
    }

    //reg
    function reg() {
        if (confirm("등록하시겠습니까?")) {
            //submit
            $("#frm").submit();
        }
    }
</script>