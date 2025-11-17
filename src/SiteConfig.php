<?php

namespace Clef;

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

class SiteConfig
{
    /**
     * title_data
     *
     * @return string
     */
    public static function title_data($page_type): string {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "
             SELECT content
               FROM site_config
              WHERE 1
                AND category = 'title'
                AND PAGE_TYPE = '{$page_type}'
              LIMIT 1
        ";

        $name_sql = "타이틀 이름";
        $clefResult = $mysqldb->get($sql, null, $name_sql);
        $data = $clefResult->getResultSet();
        $_db_title = isset($data['content']) ? $data['content'] : '';

        return $_db_title;
    }

    /**
     * favicon_data
     *
     * @return array
     */
    public static function favicon_data($page_type): array {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "
             SELECT content
               FROM site_config
              WHERE 1
                AND category = 'favicon'
                AND PAGE_TYPE = '{$page_type}'
              LIMIT 1
        ";

        $name_sql = "타이틀 로고";
        $clefResult = $mysqldb->get($sql, null, $name_sql);
        $data = $clefResult->getResultSet();
        $_db_content = isset($data['content']) ? $data['content'] : '';
        $content = json_decode($_db_content, true);
        $content['favicon']['img'] = $content['favicon']['img']??'';

        if (!empty($content['favicon']['img'])) {
            $content['favicon']['img'] = UPLOAD_DIR ."/site_config/favicon/{$content['favicon']['img']}";
        }

        return $content;
    }


    /**
     * meta_tag
     *
     * @return array
     */
    public static function meta_tag($page_type): array {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "
             SELECT content
               FROM site_config
              WHERE 1
                AND category = 'meta_tag'
                AND PAGE_TYPE = '{$page_type}'
              LIMIT 1
        ";

        $name_sql = "메타 태그";
        $clefResult = $mysqldb->get($sql, null, $name_sql);
        $data = $clefResult->getResultSet();
        $_db_content = isset($data['content']) ? $data['content'] : '';
        $content = json_decode($_db_content, true);

        //META
        $content['meta']['title'] = $content['meta']['title']??'';
        $content['meta']['keywords'] = $content['meta']['keywords']??'';
        $content['meta']['description'] = $content['meta']['description']??'';

        //OG
        $content['og']['title'] = $content['og']['title']??'';
        $content['og']['description'] = $content['og']['description']??'';
        $content['og']['img'] = $content['og']['img']??'';

        if (!empty($content['og']['img'])) {
            $content['og']['img'] = UPLOAD_DIR ."/site_config/meta_tag/{$content['og']['img']}";
        }

        return $content;
    }

    /**
     * terms_data
     *
     * @return array
     */
    public static function terms_data($page_type) {
        global $config;

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrData = array();
        $str_terms  = "'". implode("','", array_keys($config['site']['config']['terms_'.$page_type])) ."'";

        //초기화
        if (is_array($config['site']['config']['terms_'.$page_type])) {
            foreach ($config['site']['config']['terms_'.$page_type] as $key => $val) {
                $arrData[$key] = '';
            }
        }

        $sql = "
             SELECT content
                  , type
               FROM site_config
              WHERE 1
                AND category = 'terms'
                AND PAGE_TYPE = '{$page_type}'
                AND type IN ({$str_terms})
        ";
        
        $clefResult = $mysqldb->select($sql);
        $list = $clefResult->getResultSet();

        if (!empty($list)) {
            foreach ($list as $data) {
                $content = $data['content']??'';
                $type = $data['type']??'';

                $arrData[$type] = $content;
            }
        }

        return $arrData;
    }

    /**
     * footer_data
     *
     * @return array
     */
    public static function footer_data($page_type): array {
        $mysqldb    = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "
            SELECT content
              FROM site_config
             WHERE 1
               AND category = 'footer'
               AND PAGE_TYPE = '{$page_type}'
             LIMIT 1
        ";

        $name_sql = "하단 데이터";
        $clefResult = $mysqldb->get($sql, null, $name_sql);
        $data = $clefResult->getResultSet();
        $_db_content = isset($data['content']) ? $data['content'] : '';
        $content = array();

        if (!empty($_db_content)) {
            $content = json_decode($_db_content, true);
        }

        return $content;
    }

    /**
     * sns_data
     *
     * @return array
     */
    public static function sns_data($page_type): array {
        $mysqldb    = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "
             SELECT content
               FROM site_config
              WHERE 1
                AND category = 'sns'
                AND PAGE_TYPE = '{$page_type}'
              LIMIT 1
        ";

        $name_sql = "SNS 데이터";
        $clefResult = $mysqldb->get($sql, null, $name_sql);
        $data = $clefResult->getResultSet();
        $_db_content = isset($data['content']) ? $data['content'] : '';
        $content = array();

        if (!empty($_db_content)) {
            $content    = json_decode($_db_content, true);
        }

        return $content;
    }
}