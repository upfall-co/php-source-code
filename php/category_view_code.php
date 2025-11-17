<?php
/**
 * 파일명 : category_view_code.php
 * 내용 : 카테고리 상세정보
 * 최초작성날짜 : 2023/11/30
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/11/30    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;
    
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
          'code' => 500
        , 'msg' => ''
    );

    try {
        $CATEGORY3_SEQ = get_request_param('SEQ', 'GET');
        $page = get_request_param('page', 'GET');
        $search_text = get_request_param('search_text', 'GET');

        if (empty($page)) {
            $page = 1;
        }

        $table = 'CATEGORY3';

        $sql = "
             SELECT TITLE
                  , CATEGORY1_SEQ
                  , CATEGORY2_SEQ
                  , PROGRAM_CD
                  , SUB_TITLE
                  , SDATE
                  , EDATE
                  , LINK_URL
                  , CONTENT_TITLE
                  , CONTENT_TEXT
                  , RELATED_VALUE
                  , ATTACH_FILE_ID
               FROM {$table} 
              WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ";

        $name_sql = "카테고리 상세정보";
        $clefResult = $mysqldb->get($sql, [':CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 카테고리
        $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 분류
        $_db_PROGRAM_CD = _check_var($data['PROGRAM_CD']); // 구분
        $_db_M_TITLE = _check_var($data['TITLE']); // 제목
        $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 제목설명
        $_db_SDATE = _check_var($data['SDATE']); // 시작일
        $_db_EDATE = _check_var($data['EDATE']); // 종료일
        $_db_LINK_URL = _check_var($data['LINK_URL']); // 외부링큰
        $_db_CONTENT_TITLE = _check_var($data['CONTENT_TITLE']); // 상세제목
        $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
        $_db_RELATED_VALUE = _check_var($data['RELATED_VALUE']); // 관련값
        $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

        $homeFoldName = homeFoldName;
        $backurl = "";
        $DATE = "";
        $file_img = "";
        $banner_img = "";
        $CATEGORY_NAME = "";

        $_db_MAIN_ATTACH_FILE_ID = "";
        $_db_MAIN_MO_ATTACH_FILE_ID = "";

        if (!empty($_db_CATEGORY1_SEQ)) {
            if ($_db_CATEGORY1_SEQ == "EXHIBITION") {
                $backurl = "/home/sub01/exhibition.php?cate={$_db_CATEGORY1_SEQ}";
            // } else if ($_db_CATEGORY1_SEQ == "PROGRAM") {
            } else if (strpos($_db_CATEGORY1_SEQ, "PROGRAM") !== false) {
                $backurl = "/home/sub02/program.php?cate={$_db_CATEGORY1_SEQ}&PROGRAM_CD={$_db_PROGRAM_CD}&page={$page}&search_text={$search_text}";
            } else if ($_db_CATEGORY1_SEQ == "COLLABO") {
                $backurl = "/home/sub03/collabo.php?cate={$_db_CATEGORY1_SEQ}";
            }

            $CATEGORY_NAME = strtolower($_db_CATEGORY1_SEQ);
        }

        if (!empty($_db_ATTACH_FILE_ID)) {
            if (strpos($_db_CATEGORY1_SEQ, "PROGRAM") !== false) {
                $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 3);
            } else {
                $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 2);
            }

            foreach ($file_list as $data2) {
                // if ($data2['ATTACH_GROUP'] == 2) {
                    $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                // } else if ($data2['ATTACH_GROUP'] == 5) {
                //     $_db_MAIN_MO_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                // }

                $banner_img .= <<<DIV
                                    <div class="swiper-slide">
                                        <div>
                                        <picture>
                                            <!-- ↓ pc 이미지 1920 * 850 -->
                                            <source media="(min-width: 768px)" srcset="{$_db_MAIN_ATTACH_FILE_ID}">
                                            <!-- ↓ mo 이미지 960 * 1315 -->
                                            <source media="(min-width: 100px)" srcset="{$_db_MAIN_MO_ATTACH_FILE_ID}">
                                            <!-- ↓ 기본 이미지 (pc와 동일) -->
                                            <img src="{$_db_MAIN_ATTACH_FILE_ID}" alt="">
                                        </picture>
                                        </div>
                                    </div>
                                DIV;
            }
        }

        if (!empty($_db_CONTENT_TEXT)) {
            $_db_CONTENT_TEXT = nl2br($_db_CONTENT_TEXT);
        }

        if (!empty($_db_SDATE) && !empty($_db_EDATE)) {
            $DATE = $_db_SDATE."—".$_db_EDATE;
        }

        $categoriesArray = explode(',', $_db_RELATED_VALUE);

        $quotedCategories = array_map(function($item) {
            return "'".$item."'";
        }, $categoriesArray);

        $quotedCategoriesString = implode(',', $quotedCategories);

        $sql = "
             SELECT CATEGORY3_SEQ
                  , TITLE
                  , ATTACH_FILE_ID
               FROM {$table} 
              WHERE MAIN_YN ='Y'
                AND CATEGORY3_SEQ IN ({$quotedCategoriesString})
                AND CATEGORY1_SEQ NOT IN ('COLLABO')
              ORDER BY ORDER_NUMBER DESC, reg_date DESC
              LIMIT 0, 3";

        $name_sql = "카테고리 상세정보";
        $clefResult = $mysqldb->select($sql, null, $name_sql);

        $data2 = $clefResult->getResultSet();

        if (!empty($data2)) {
            foreach ($data2 as $ImgList) {
                $_db_CATEGORY3_SEQ = _check_var($ImgList['CATEGORY3_SEQ']); // 시퀀스
                $_db_TITLE = _check_var($ImgList['TITLE']); // 제목
                $_db_ATTACH_FILE_ID = _check_var($ImgList['ATTACH_FILE_ID']); // 파일아이디

                $url = $homeFoldName.'/include/board_view.php?SEQ='.$_db_CATEGORY3_SEQ;
                $path_File = "";
                $data_html = "";

                if (!empty($_db_ATTACH_FILE_ID)) {
                    $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                    if (!empty($file_list)) {
                        foreach ($file_list as $list) {
                            $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                            $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                            $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                        }
                    }
                }

                if ($_db_CATEGORY1_SEQ == "EXHIBITION") {
                    $data_html = "data-type='EX'";
                }

                $file_img .= <<<LI
                                    <li {$data_html}>
                                        <a href="{$url}" class="thumb_container">
                                        <figure>
                                            <img src="{$path_File}" alt="">
                                        </figure>
                                        <div class="title_wrap">
                                            <p>{$_db_TITLE}</p>
                                        </div>
                                        </a>
                                    </li>
                                LI;
            }
        }

   if (!$clefResult->getResult()) {
       gfn_isValidation(800);
   }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>