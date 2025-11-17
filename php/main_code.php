<?php
/**
 * 파일명 : main_code.php
 * 내용 : 메인 페이지
 * 최초작성날짜 : 2023/08/30
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/30    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    function getList_Image_PC() {
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

            $sql = "
                 SELECT LINK_URL
                      , TITLE
                      , ATTACH_FILE_ID
                   FROM IMAGE
                  WHERE MAIN_YN = 'Y'
                   AND PAGE_TYPE = :PAGE_TYPE
                   ORDER BY ORDER_NUMBER DESC, reg_date DESC
                   LIMIT 0, 36";

            $name_sql = "시리즈 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_LINK_URL = _check_var($data['LINK_URL']); // 링크
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 2);
    
                        foreach ($file_list as $data2) {
                            $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }
    
                        $file_list2 = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 4);
    
                        foreach ($file_list2 as $data2) {
                            $_db_MAIN_ATTACH_FILE_ID2 = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }
                    }

                    echo <<<LI
                                <li class="main_real">
                                    <a href="{$_db_LINK_URL}">
                                        <div class="thumb_box">
                                            <img src="{$_db_MAIN_ATTACH_FILE_ID}">
                                            <p>{$_db_TITLE}</p>
                                        </div>
                                        <div class="zoom_img"><img src="{$_db_MAIN_ATTACH_FILE_ID2}"></div>
                                    </a>
                                </li>
                            LI;
                }
            }

            $artFoldName = artFoldName;
            $noimage = $artFoldName. "/img/no_image.jpg";

            for ($i = 0; $i < 36 - count($list); $i++) {
                echo <<<LI
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="thumb_box">
                                        <img src="{$noimage}">
                                        <p></p>
                                    </div>
                                </a>
                            </li>
                        LI;
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    function getList_Image_MO() {
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

            $sql = "
                 SELECT LINK_URL
                      , TITLE
                      , ATTACH_FILE_ID
                   FROM IMAGE
                  WHERE MAIN_YN = 'Y'
                   AND PAGE_TYPE = :PAGE_TYPE
                   ORDER BY ORDER_NUMBER DESC, reg_date DESC
                   LIMIT 0, 36";

            $name_sql = "시리즈 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_LINK_URL = _check_var($data['LINK_URL']); // 링크
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 5);

                        $_db_MAIN_ATTACH_FILE_ID = "";
    
                        foreach ($file_list as $data2) {
                            $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }

                        if (!empty($_db_MAIN_ATTACH_FILE_ID)) {  
                            echo <<<DIV
                                        <div class="swiper-slide">
                                            <a href="{$_db_LINK_URL}">
                                                <img src="{$_db_MAIN_ATTACH_FILE_ID}">
                                            </a>
                                        </div>
                                    DIV;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    function getFirst_Series() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $PAGE = PAGE;

            $sql = "
                 SELECT M.CATEGORY1_SEQ
                      , M.TITLE AS CATEGORY1_NAME
                      , D.CATEGORY2_SEQ
                      , D.TITLE AS CATEGORY2_NAME
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , D.ORDER_NUMBER AS SUB_ORDER
                      , 'SUB' AS TYPE_MODE
                      , M.reg_date
                   FROM CATEGORY1 M, CATEGORY2 D
                 WHERE 1
                   AND M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                   AND M.MAIN_YN = 'Y'
                   AND D.MAIN_YN = 'Y'
                   AND M.PAGE_TYPE = '{$PAGE}'
                 ORDER BY MAIN_ORDER DESC, reg_date DESC, CATEGORY2_NAME, SUB_ORDER DESC
                 LIMIT 1";

            $name_sql = "첫번째 정렬 시리즈 값 조회";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            $url = artFoldName . '/shop/series.php?seq=' . $data['CATEGORY1_SEQ'] . '#series' . $data['SUB_ORDER'];

            echo <<<A
                        <a href="{$url}" class="more_btn">
                            MORE VIEW
                        </a>
                    A;


        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>