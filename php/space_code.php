<?php
/**
 * 파일명 : space_code.php
 * 내용 : SPACE 리스트
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
    use Clef\SiteConfig;

    $terms = SiteConfig::terms_data(PAGE3); //약관

    $TYPE_CD = "SPF1";

    try {
        $ATTACH_FILE_ID = "ATTACH_SPCPALN";

        $GROUP = gfn_getZcmcommonVal("COL011", $TYPE_CD , "TH1_THEM_CD");

        $file_list = gfn_file_upload("S", "", $ATTACH_FILE_ID, $GROUP);

        $MAIN_ATTACH_FILE_ID = "";

        if (!empty($file_list)) {
            foreach ($file_list as $data) {
                $MAIN_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_PATH']).'/'._check_var($data['ATTACH_FILE_TEMP_NAME']);
            }
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

    /**
     * name :getList_SPACE
     * comment : SPACE 리스트
     */
    function getList_SPACE() {
        global $TYPE_CD;

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $PAGE = PAGE;


            $arrValue = array();
            $arrValue[':PAGE_TYPE'] = $PAGE;
            $arrValue[':TYPE_CD'] = $TYPE_CD;

            $sql = "
                 SELECT SPACE_SEQ
                      , TITLE
                      , DATE_TEXT
                      , MOBILE
                      , EMAIL
                      , ATTACH_FILE_ID
                FROM SPACE
                WHERE 1
                AND MAIN_YN = 'Y'
                AND PAGE_TYPE = :PAGE_TYPE
                AND TYPE_CD = :TYPE_CD
                ORDER BY ORDER_NUMBER DESC";


            $name_sql = "SPACE 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                $count = 1;

                foreach ($list as $data) {
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_DATE_TEXT = _check_var($data['DATE_TEXT']); // 기간
                    $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                    $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    $img_html = "";
                    $li_html = "";
                    $li_html2 = "";

                    $homeFoldName = homeFoldName;

                    if (!empty($_db_MOBILE)) {
                        $li_html = '<li class="separator">|</li>';
                    }

                    if (!empty($_db_EMAIL)) {
                        $li_html2 = '<li class="separator">|</li>';
                    }

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                        if (!empty($file_list)) {
                            foreach ($file_list as $list2) {
                                $_db_attach_file_temp_name = _check_var($list2['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_real_name = _check_var($list2['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                                $_db_attach_file_path = _check_var($list2['ATTACH_FILE_PATH']); // 경로
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;

                                $img_html .=  <<<DIV
                                                <div class="swiper-slide">
                                                    <figure>
                                                    <img src="{$path_File}" alt="">
                                                    </figure>
                                                </div>
                                                DIV;
                            }
                        }
                    }
                    echo <<<DIV
                                    <div class="spac__s2_contents_wrap spac__s2_contents_wrap{$count}">
                                        <div class="spac__s2_slide spac__s2_slide{$count} swiper">
                                            <div class="swiper-wrapper">
                                                {$img_html}
                                            </div>
                                            <div class="spac__s2_nav_container">
                                                <button type="button" class="spac__s2_nav{$count}--prev spac__s2_nav spac__s2_nav--prev">
                                                    <img src="${homeFoldName}/img/slide_prev.svg" alt="">
                                                </button>
                                                <button type="button" class="spac__s2_nav{$count}--next spac__s2_nav spac__s2_nav--next">
                                                    <img src="${homeFoldName}/img/slide_prev.svg" alt="">
                                                </button>
                                            </div>
                                        </div>
                                        <div class="spac__s2_cap">
                                            <p class="title">{$_db_TITLE}</p>
                                            <ul class="info_wrap">
                                                <li>{$_db_DATE_TEXT}</li>
                                                {$li_html}
                                                <li><a href="tel:{$_db_MOBILE}">{$_db_MOBILE}</a></li>
                                                {$li_html2}
                                                <li><a href="mailto:{$_db_EMAIL}">{$_db_EMAIL}</a></li>
                                            </ul>
                                        </div>
                                    </div>
                            DIV;
                    $count++;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>