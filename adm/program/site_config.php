<?php
require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

//관리자 체크
_check_admin();

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

$arrRtn = array(
    'code'  => 500,
    'msg'   => '',
    'mode'  => '',
    'url'   => '',
);

try {
    //파라미터 정리
    $mode = get_request_param('mode');
    $menu_active = get_request_param('menu_active');

    //변수 정리
    $arrRtn['mode'] = $mode;
    $arrRes = array();
    $table = 'site_config';

    switch ($mode) {
        case 'reg' :
            $arrRes = row_insert();
            break;
        default :
            throw new Exception('잘못된 접근입니다.');
    }

    if ($arrRes['code'] != 200) {
        throw new Exception($arrRes['msg'], $arrRes['code']);
    }

    $m_seq = get_request_param('m_seq');
    $mp_seq = get_request_param('mp_seq');
    $page_type = get_request_param('page_type');

    $arrParams = array(
          'm_seq' => $m_seq
        , 'mp_seq' => $mp_seq
        , 'page_type' => $page_type
    );

    $query_string = http_build_query($arrParams);

    //성공
    $arrRtn['code'] = $arrRes['code'];
    $arrRtn['msg']  = $arrRes['msg'];
    $arrRtn['url']  = "../board/{$table}.php?menu_active={$menu_active}&{$query_string}";
    dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();
    dieAndErrorMove($arrRtn['msg']);

}

//등록
function row_insert() {

    $mysqldb    = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn     = array(
        'code'  => 500,
        'msg'   => ''
    );

    try {
        //트랜잭션
        $mysqldb->link->beginTransaction();

        //파라미터 정리
        //타이틀
        $title_seq          = get_request_param('title_seq');
        $title              = get_request_param('title');

        //파비콘
        $favicon_seq        = get_request_param('favicon_seq');
        $favicon_img_val    = get_request_param('favicon_img_val');

        //메타태그
        $meta_seq           = get_request_param('meta_seq');
        $meta_title         = get_request_param('meta_title');
        $meta_keywords      = get_request_param('meta_keywords');
        $meta_description   = get_request_param('meta_description');
        $og_title           = get_request_param('og_title');
        $og_site_name       = get_request_param('og_site_name');
        $og_description     = get_request_param('og_description');
        $og_img_val         = get_request_param('og_img_val');

        //푸터
        $footer_seq = get_request_param('footer_seq');
        $footer_company_name = get_request_param('footer_company_name');
        $footer_rep_name = get_request_param('footer_rep_name');
        $footer_addr = get_request_param('footer_addr');
        $footer_mobile = get_request_param('footer_mobile');
        $footer_email = get_request_param('footer_email');
        $footer_number = get_request_param('footer_number');
        $footer_company_rep_num = get_request_param('footer_company_rep_num');

        //SNS
        $sns_seq = get_request_param('sns_seq');
        $sns_youtube = get_request_param('sns_youtube');
        $sns_facebook = get_request_param('sns_facebook');
        $sns_instargram = get_request_param('sns_instargram');

        //페이지 타입
        $page_type      = get_request_param('page_type');

        //변수 정리
        $ip = "";

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $table = 'site_config';
        $dir = UPLOAD_DIR ."/{$table}";
        $json_flags = JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_HEX_QUOT|JSON_HEX_APOS;

        /********** 타이틀 ***********/

        //trim
        $title      = trim($title);

        $values = array(
            'content'   => $title,
            'PAGE_TYPE' => $page_type,
            'category'  => 'title',
        );

        $code = "";

        //DB
        if (empty($title_seq)) {
            $values['reg_user'] = SE_ADM_NAME;
            $values['reg_ip']   = $ip;

            $clefResult = $mysqldb->insert($table, $values);

            $code = 501;
        } else {
            $values['mod_user'] = SE_ADM_NAME;
            $values['mod_ip']   = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');

            $clefResult = $mysqldb->update($table, $values, ['seq' => $title_seq]);
            $code = 502;
        }
        if (!$clefResult->getResult()) {
            gfn_isValidation($code);
        }

        /********** 타이틀 ***********/

        /********** 파비콘 ***********/

        //변수 정리
        $favicon_dir        = "{$dir}/favicon";
        $arrFavicon         = array();

        if (isset($_FILES['favicon_img']['name']) && !empty($_FILES['favicon_img']['name'])) {
            $return_msg = file_upload_proc($favicon_dir, $_FILES['favicon_img'], true);

            if ($return_msg[0] == false) {
                gfn_isValidation(999, "", "{$return_msg[1]}");
            }

            $arrFavicon['favicon']['img'] = $return_msg[0];
            $arrFavicon['favicon']['img_name']   = $return_msg[1];
        } else {
            $arrImgVal = explode('||', $favicon_img_val);

            $arrFavicon['favicon']['img']        = $arrImgVal[0];
            $arrFavicon['favicon']['img_name']   = (!empty($arrImgVal[1])) ? $arrImgVal[1] : '';
        }
        $json_favicon   = json_encode($arrFavicon, $json_flags);

        unset($values);

        $values = array(
            'content'   => $json_favicon,
            'page_type' => $page_type,
            'category'  => 'favicon',
        );

        //DB
        $code = "";

        if (empty($favicon_seq)) {
            $values['reg_user'] = SE_ADM_NAME;
            $values['reg_ip']   = $ip;

            $code = 501;

            $clefResult = $mysqldb->insert($table, $values);
        } else {
            $values['mod_user'] = SE_ADM_NAME;
            $values['mod_ip']   = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');

            $code = 502;

            $clefResult = $mysqldb->update($table, $values, ['seq' => $favicon_seq]);
        }
        if (!$clefResult->getResult()) {
            gfn_isValidation($code);
        }

        /********** 파비콘 ***********/

        /********** 메타태그 ***********/

        //trim
        $meta_title = trim($meta_title);
        $meta_keywords = trim($meta_keywords);
        $meta_description = trim($meta_description);
        $og_title = trim($og_title);
        $og_site_name = trim($og_site_name);
        $og_description = trim($og_description);

        //변수 정리
        $meta_dir = "{$dir}/meta_tag";
        $arrMetaTag = array(
            'meta'  => array(
                'title' => $meta_title,
                'PAGE_TYPE' => $page_type,
                'keywords' => $meta_keywords,
                'description' => $meta_description,
            ),
            'og'    => array(
                'title' => $og_title,
                'PAGE_TYPE' => $page_type,
                'site_name' => $og_site_name,
                'description' => $og_description,
            )
        );

        if (isset($_FILES['og_img']['name']) && !empty($_FILES['og_img']['name'])) {
            $return_msg = file_upload_proc($meta_dir, $_FILES['og_img'], true);

            if ($return_msg[0] == false) {
                gfn_isValidation(999, "", "{$return_msg[1]}");
            }

            $arrMetaTag['og']['img'] = $return_msg[0];
            $arrMetaTag['og']['img_name'] = $return_msg[1];
        } else {
            $arrImgVal = explode('||', $og_img_val);

            $arrMetaTag['og']['img'] = $arrImgVal[0];
            $arrMetaTag['og']['img_name'] = (!empty($arrImgVal[1])) ? $arrImgVal[1] : '';
        }
        $json_meta_tag      = json_encode($arrMetaTag, $json_flags);

        unset($values);

        $values = array(
            'content' => $json_meta_tag,
            'PAGE_TYPE' => $page_type,
            'category' => 'meta_tag',
        );

        //DB
        $code = "";

        if (empty($meta_seq)) {
            $values['reg_user'] = SE_ADM_NAME;
            $values['reg_ip']   = $ip;

            $code = 501;

            $clefResult = $mysqldb->insert($table, $values);
        } else {
            $values['mod_user'] = SE_ADM_NAME;
            $values['mod_ip']   = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');

            $code = 502;

            $clefResult = $mysqldb->update($table, $values, ['seq' => $meta_seq]);
        }
        if (!$clefResult->getResult()) {
            gfn_isValidation($code);
        }

        /********** 메타태그 ***********/

        /********** footer ***********/

        unset($values);

        $footer_company_name = trim($footer_company_name);
        $footer_rep_name = trim($footer_rep_name);
        $footer_addr = trim($footer_addr);
        $footer_mobile = trim($footer_mobile);
        $footer_email = trim($footer_email);
        $footer_number = trim($footer_number);
        $footer_company_rep_num = trim($footer_company_rep_num);

        $arrFooter = array(
              'company_name' => $footer_company_name
            , 'rep_name' => $footer_rep_name
            , 'addr' => $footer_addr
            , 'mobile' => $footer_mobile
            , 'email' => $footer_email
            , 'number' => $footer_number
            , 'company_rep_num' => $footer_company_rep_num
        );

        $json_footer = json_encode($arrFooter, $json_flags);

        $values = array(
            'content' => $json_footer,
            'PAGE_TYPE' => $page_type,
            'category' => 'footer',
        );

        //DB
        $code = "";

        if (empty($footer_seq)) {
            $values['reg_user'] = SE_ADM_NAME;
            $values['reg_ip'] = $ip;

            $code = 501;

            $clefResult = $mysqldb->insert($table, $values);
        } else {
            $values['mod_user'] = SE_ADM_NAME;
            $values['mod_ip'] = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');

            $code = 502;

            $clefResult = $mysqldb->update($table, $values, ['seq' => $footer_seq]);
        }
        if (!$clefResult->getResult()) {
            gfn_isValidation($code);
        }

        /********** footer ***********/

        /********** sns ***********/

        unset($values);

        $sns_seq             = trim($sns_seq);
        $sns_youtube      = trim($sns_youtube);
        $sns_facebook      = trim($sns_facebook);
        $sns_instargram      = trim($sns_instargram);

        $arrsns          = array(
            'youtube'          => $sns_youtube,
            'facebook'     => $sns_facebook,
            'instargram'       => $sns_instargram,
        );
        $json_sns        = json_encode($arrsns, $json_flags);

        $values = array(
            'content'   => $json_sns,
            'PAGE_TYPE' => $page_type,
            'category'  => 'sns',
        );

        //DB
        $code = "";

        if (empty($sns_seq)) {
            $values['reg_user'] = SE_ADM_NAME;
            $values['reg_ip']   = $ip;

            $code = 501;

            $clefResult = $mysqldb->insert($table, $values);
        } else {
            $values['mod_user'] = SE_ADM_NAME;
            $values['mod_ip']   = $ip;
            $values['mod_date'] = date('Y-m-d H:i:s');

            $code = 502;

            $clefResult = $mysqldb->update($table, $values, ['seq' => $sns_seq]);
        }
        if (!$clefResult->getResult()) {
            gfn_isValidation($code);
        }

        /********** sns ***********/
        //성공
        $mysqldb->link->commit();
        $arrRtn['code'] = 200;
        $arrRtn['msg']  = '등록되었습니다.';

    } catch (Exception $e) {
        $mysqldb->link->rollBack();
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg']  = $e->getMessage();

    } finally {
        return $arrRtn;
    }
}