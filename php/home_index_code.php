<?php
/**
 * 파일명 : home_index_code.php
 * 내용 : 브랜드 메인페이지
 * 최초작성날짜 : 2023/11/22
 * 최초작성자 : 정우진
 * ------------------------------------
 * name       date        comment
 * 정우진    2023/11/03    V1.0
 * 전상범    2023/11/30    home 개발
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;
    use Clef\SiteConfig;

    $terms = SiteConfig::terms_data(PAGE3);

    /**
     * name :getList_Slide
     * comment : 메인 슬라이드
     */
    function getList_Image() {
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
                      , SUB_TITLE
                      , ATTACH_FILE_ID
                   FROM IMAGE
                  WHERE MAIN_YN = 'Y'
                   AND PAGE_TYPE = :PAGE_TYPE
                   ORDER BY ORDER_NUMBER DESC, reg_date DESC";

            $name_sql = "메인베너 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue , $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!get_is_mobile()){
                $ATTACH_FILE_COUNT = 2;
            } else {
                $ATTACH_FILE_COUNT = 5;
            }

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_LINK_URL = _check_var($data['LINK_URL']); // 링크
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 서브
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, $ATTACH_FILE_COUNT);

                        foreach ($file_list as $data2) {
                            $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }
                    }

                    echo <<<A
                                <a class="swiper-slide" href="{$_db_LINK_URL}">
                                    <img src="{$_db_MAIN_ATTACH_FILE_ID}">
                                    <div class="txt">
                                        <div class="desc">{$_db_SUB_TITLE}</div>
                                        <div class="title">{$_db_TITLE}</div>
                                    </div>
                                </a>
                            A;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getList_New
     * comment : 상품 New 제품 10개 까지만 출력
     */
    function getList_New() {
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
                      , M.CATEGORY2_SEQ
                      , M.TITLE AS MAIN_TITLE
                      , D.CATEGORY3_SEQ
                      , D.BRAND
                      , D.TITLE AS SUB_TITLE
                      , (SELECT TITLE FROM CATEGORY1 A WHERE A.CATEGORY1_SEQ = M.CATEGORY1_SEQ) AS CATEGORY1_NAME
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , D.ORDER_NUMBER AS SUB_ORDER
                      , FORMAT(PRICE, '') AS PRICE
                      , D.ATTACH_FILE_ID
                      , M.reg_date
                   FROM CATEGORY2 M, CATEGORY3 D
                  WHERE 1
                    AND M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                    AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ
                    AND M.MAIN_YN = 'Y'
                    AND D.MAIN_YN = 'Y'
                    AND M.PAGE_TYPE = '{$PAGE}'
                    AND D.TYPE_CD LIKE '%NEW%'
                  ORDER BY MAIN_ORDER DESC, reg_date DESC, MAIN_TITLE, SUB_ORDER DESC
                  LIMIT 0, 10";

            $name_sql = "NEW 제품 리스트";
            $clefResult = $mysqldb->select($sql, null , $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $shopFoldName = shopFoldName;

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 상품 시퀀스
                    $_db_BRAND = _check_var($data['BRAND']); // 브랜드명
                    $_db_TITLE = _check_var($data['SUB_TITLE']); // 작품, 상품명
                    $_db_M_PRICE = _check_var($data['PRICE']); // 금액
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    $_db_MAIN_ATTACH_FILE_ID = '';

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                        if (!empty($file_list)) {
                            foreach ($file_list as $data2) {
                                $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                            }
                        }
                    }

                    $URL = shopFoldName . '/product/detail.php?seq=' . $_db_CATEGORY3_SEQ;

                    echo <<<DIV
                                <div class="swiper-slide">
                                    <a href="{$URL}" class="prdThumbnail"><img src="{$_db_MAIN_ATTACH_FILE_ID}"></a>
                                    <a href="{$URL}" class="prdBrand">{$_db_BRAND}</a>
                                    <a href="{$URL}" class="prdName">{$_db_TITLE}</a>
                                    <div class="prdPrice"><span>{$_db_M_PRICE}</span></div>
                                </div>
                            DIV;
                }
            }


        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getFirst_Category2
     * comment : 카테고리 값 조회
     */
    function getFirst_Category1() {
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
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , M.reg_date
                   FROM CATEGORY1 M
                 WHERE 1
                   AND M.MAIN_YN = 'Y'
                   AND M.PAGE_TYPE = '{$PAGE}'
                 ORDER BY MAIN_ORDER DESC, reg_date DESC";

            $name_sql = "첫번째 정렬 카테고리 값 조회";
            $clefResult = $mysqldb->select($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();


            if (!empty($list)) {
                $frist = "Y";

                foreach ($list as $data) {
                    $selected = "";

                    if ($frist == "Y") {
                        $selected = "selected";

                        $frist = "N";
                    }

                    $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 카테고리 시퀀스
                    $_db_CATEGORY1_NAME = _check_var($data['CATEGORY1_NAME']); // 카테고리명

                    echo <<<LI
                                <li class="{$selected}" data-category="{$_db_CATEGORY1_SEQ}">
                                    {$_db_CATEGORY1_NAME}
                                </li>
                            LI;
                }
            }


        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getFirst_Category2
     * comment : 첫번째 정렬 카테고리 값 조회
     */
    function getFirst_Category2() {
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
                      , D.TITLE
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
                 ORDER BY MAIN_ORDER DESC, reg_date DESC, M.TITLE, SUB_ORDER DESC
                 LIMIT 1";

            $name_sql = "첫번째 정렬 카테고리 값 조회";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            $url = shopFoldName . '/product/list.php?seq=' . $data['CATEGORY2_SEQ'];

            echo <<<A
                        <a href="{$url}">
                            more view
                        </a>
                    A;


        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getHomeList_Image
     * comment : 메인 home 슬라이드
     */
    function getHomeList_Image() {
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
                      , SUB_TITLE
                      , VIDEO_YN
                      , ATTACH_FILE_ID
                      , CASE WHEN DATE(CURDATE()) >= DATE(SDATE) AND DATE(CURDATE()) <= DATE(EDATE) THEN 'Y'
                        ELSE 'N'
                         END AS DATECHECK
                   FROM IMAGE
                  WHERE MAIN_YN = 'Y'
                   AND PAGE_TYPE = :PAGE_TYPE
                   ORDER BY ORDER_NUMBER DESC, reg_date DESC";

            $name_sql = "메인베너 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue , $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!get_is_mobile()){
                $ATTACH_FILE_COUNT = 2;
            } else {
                $ATTACH_FILE_COUNT = 5;
            }

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_LINK_URL = _check_var($data['LINK_URL']); // 링크
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 서브
                    $_db_VIDEO_YN = _check_var($data['VIDEO_YN']); // 비디오 여부
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    $_db_DATECHECK = _check_var($data['DATECHECK']); // 종료일

                    $_db_MAIN_ATTACH_FILE_ID = "";
                    $_db_MAIN_MO_ATTACH_FILE_ID = "";
                    $LINK_URL = "javascript:modalShowing(this, '.modal', 'ex_pre')";

                    if (!empty($_db_LINK_URL)) {
                        $LINK_URL = $_db_LINK_URL;
                    }

                    if ($_db_DATECHECK == "N") {
                        $LINK_URL = "javascript:modalShowing(this, '.modal', 'ex_pre')";
                    }

                    $_V_MAIN_ATTACH_FILE_ID = "";
                    $_chk_ATTACH_FILE_TYPE = "";

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, $ATTACH_FILE_COUNT);

                        foreach ($file_list as $data2) {
                            if ($data2['ATTACH_GROUP'] == 2) {
                                $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                            } else if ($data2['ATTACH_GROUP'] == 5) {
                                $_db_MAIN_MO_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                            } else if ($data2['ATTACH_GROUP'] == 7) {
                                $_V_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                                $_chk_ATTACH_FILE_TYPE =  _check_var($data2['ATTACH_FILE_TYPE']);
                            }
                        }

                        if ($_db_VIDEO_YN == "Y") {
                            $file_list2 = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 7);

                            foreach ($file_list2 as $data3) {
                                if ($data3['ATTACH_GROUP'] == 7) {
                                    $_V_MAIN_ATTACH_FILE_ID = _check_var($data3['ATTACH_FILE_PATH']).'/'._check_var($data3['ATTACH_FILE_TEMP_NAME']);
                                    $_chk_ATTACH_FILE_TYPE =  _check_var($data3['ATTACH_FILE_TYPE']);
                                }
                            }
                        }
                    }

                    $image_html = "";

                    if ($_chk_ATTACH_FILE_TYPE == "mp4" && $_db_VIDEO_YN == "Y") {
                        $image_html = <<<DIV
                                            <div>
                                                <video data-autoplay muted loop autoplay playsinline>
                                                    <source src="{$_V_MAIN_ATTACH_FILE_ID}" type="video/mp4" />
                                                </video>
                                            </div>
                                        DIV;
                    } else {
                        // <!-- ↓ pc 이미지 1920 * 850 -->
                        // <!-- ↓ mo 이미지 960 * 1315 -->
                        // <!-- ↓ 기본 이미지 (pc와 동일) -->
                        $image_html = <<<A
                                        <a href="{$LINK_URL}">
                                            <picture>
                                                <source media="(min-width: 768px)" srcset="{$_db_MAIN_ATTACH_FILE_ID}">
                                                <source media="(min-width: 100px)" srcset="{$_db_MAIN_MO_ATTACH_FILE_ID}">
                                                <img src="{$_db_MAIN_ATTACH_FILE_ID}" alt="">
                                            </picture>
                                        </a>
                                        A;
                    }

                    echo <<<DIV
                                <div class="swiper-slide">
                                    {$image_html}
                                </div>
                            DIV;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getHomeList_EXHIBITION
     * comment : 메인 전시회 이미지
     */
    function getHomeList_EXHIBITION() {
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
                 SELECT TITLE
                      , ATTACH_FILE_ID
                      , CATEGORY3_SEQ
                   FROM CATEGORY3
                  WHERE TITLE_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    AND CATEGORY1_SEQ = 'EXHIBITION'
                   ORDER BY ORDER_NUMBER DESC, reg_date DESC
                   LIMIT 1";

            $name_sql = "전시 메인 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue , $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    $homeFoldName = homeFoldName;

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                        if (!empty($file_list)) {
                            foreach ($file_list as $list2) {
                                $_db_attach_file_temp_name = _check_var($list2['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_path = _check_var($list2['ATTACH_FILE_PATH']); // 경로
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    $URL = $homeFoldName. "/include/board_view.php?SEQ=".$_db_CATEGORY3_SEQ;

                    echo <<<LI
                                <li data-type="EX">
                                    <p class="title">exhibition</p>
                                    <a href="$URL">
                                        <figure>
                                            <img src="{$path_File}" alt="">
                                        </figure>
                                        <p class="name">
                                            {$_db_TITLE}
                                        </p>
                                    </a>
                                </li>
                            LI;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getHomeList_PROGRAM
     * comment : 메인 프로그램 이미지
     */
    function getHomeList_PROGRAM() {
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
                 SELECT TITLE
                      , ATTACH_FILE_ID
                      , CATEGORY3_SEQ
                   FROM CATEGORY3
                  WHERE TITLE_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    AND CATEGORY1_SEQ LIKE '%PROGRAM%'
                   ORDER BY ORDER_NUMBER DESC, reg_date DESC
                   LIMIT 1";

            $name_sql = "프로그램 메인 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    $homeFoldName = homeFoldName;

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                        if (!empty($file_list)) {
                            foreach ($file_list as $list2) {
                                $_db_attach_file_temp_name = _check_var($list2['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_path = _check_var($list2['ATTACH_FILE_PATH']); // 경로
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    $URL = $homeFoldName. "/include/board_view.php?SEQ=".$_db_CATEGORY3_SEQ;

                    echo <<<LI
                                <li>
                                    <p class="title">program</p>
                                    <a href="$URL">
                                        <figure>
                                            <img src="{$path_File}" alt="">
                                        </figure>
                                        <p class="name">
                                            {$_db_TITLE}
                                        </p>
                                    </a>
                                </li>
                            LI;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getHomeList_SHOP
     * comment : 메인 SHOP 이미지
     */
    function getHomeList_SHOP() {
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
                 SELECT TITLE
                      , ATTACH_FILE_ID
                   FROM CATEGORY3
                  WHERE 1
                    AND PAGE_TYPE = :PAGE_TYPE
                    AND CATEGORY1_SEQ = 'SHOP'";

            $name_sql = "샵 메인 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue , $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

                    $shopFoldName = shopFoldName;

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                        if (!empty($file_list)) {
                            foreach ($file_list as $list2) {
                                $_db_attach_file_temp_name = _check_var($list2['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_path = _check_var($list2['ATTACH_FILE_PATH']); // 경로
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    echo <<<LI
                                <li>
                                    <p class="title">shop</p>
                                    <a href="{$shopFoldName}/index.php">
                                        <figure>
                                            <img src="{$path_File}" alt="">
                                        </figure>
                                        <p class="name">
                                            {$_db_TITLE}
                                        </p>
                                    </a>
                                </li>
                            LI;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getHomeList_INFORM
     * comment : 메인 공지사항 내용
     */
    function getHomeList_INFORM() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

      $mysqldb = new Pdo7();
      $clefResult = new ClefResult();

        try {
            $PAGE = PAGE;

            $arrValue = array();
            $arrValue[':COM_TYPE'] = "COL012";

            $table_COM = 'ZCMCOMMON'; // 공통테이블

            $sql = "
                 SELECT COM_TYPE
                      , COM_CD
                      , COM_CD_NM
                      , TH1_THEM_CD
                      , TH1_THEM_COMMENT
                      , COM_ORDER
                   FROM {$table_COM}
                  WHERE COM_TYPE = :COM_TYPE
                  ORDER BY COM_ORDER";

            $name_sql = "HOME_INDEX_NOTICE 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_COM_TYPE = _check_var($data['COM_TYPE']); // 데이터 타입
                    $_db_COM_CD = _check_var($data['COM_CD']); // 벨류 코드
                    $_db_COM_CD_NM = _check_var($data['COM_CD_NM']); // 벨류 이름
                    $_db_TH1_THEM_CD = _check_var($data['TH1_THEM_CD']); // TH1 참조값
                    $_db_TH1_THEM_COMMENT = _check_var($data['TH1_THEM_COMMENT']); // TH1 설명
                    $_db_COM_ORDER = _check_var($data['COM_ORDER']); // 벨류 정렬

                    if (empty($_db_COM_CD_NM) && empty($_db_TH1_THEM_CD)) {
                        continue;
                    }

                    echo <<<LI
                            <li>
                                <p class="name">$_db_COM_CD_NM</p>
                                <p class="contents">{$_db_TH1_THEM_CD}</p>
                            </li>
                            LI;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getHomeList_LOCATION
     * comment : 메인 주소 내용
     */
    function getHomeList_LOCATION() {
        $arrRtn = array(
            'code' => 500
          , 'msg' => ''
      );

      $mysqldb = new Pdo7();
      $clefResult = new ClefResult();

      try {
          $table = 'LOCATION';

          $sql = "
               SELECT M_ADDRESS
                    , M_NAVER_LINK
                    , M_KAKAO_LINK
                    , D_ADDRESS
                    , D_NAVER_LINK
                    , D_KAKAO_LINK
                    , OPERATE
                    , PARKKING
                    , FACILITIES
                    , ATTACH_FILE_ID
                 FROM {$table}
                ORDER BY reg_date DESC";

          $name_sql = 'LOCATION 데이터';
          $clefResult = $mysqldb->select($sql, '', $name_sql);

          if (!$clefResult->getResult()) {
              gfn_isValidation(800);
          }

          $list = $clefResult->getResultSet();

          if (!empty($list)) {
              foreach ($list as $data) {
                  $_db_M_ADDRESS = _check_var($data['M_ADDRESS']); // 제1 주소
                  $_db_M_NAVER_LINK = _check_var($data['M_NAVER_LINK']); // 제1 네이버 맵 링크
                  $_db_M_KAKAO_LINK = _check_var($data['M_KAKAO_LINK']); // 제1 카카오 맵 링크
                  $_db_D_ADDRESS = _check_var($data['D_ADDRESS']); // 제2 주소
                  $_db_D_NAVER_LINK = _check_var($data['D_NAVER_LINK']); // 제2 네이버 맵 링크
                  $_db_D_KAKAO_LINK = _check_var($data['D_KAKAO_LINK']); // 제2 카카오 맵 링크
                  $_db_OPERATE = _check_var($data['OPERATE']); // 운영
                  $_db_PARKKING = _check_var($data['PARKKING']); // 주차
                  $_db_FACILITIES = _check_var($data['FACILITIES']); // 시설
                  $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                  $file_html = "";

                  if (!empty($_db_ATTACH_FILE_ID)) {
                      $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                      if (!empty($file_list)) { // 썸네일
                          foreach ($file_list as $f_list) {
                              $_db_attach_file_temp_name = _check_var($f_list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                              $_db_attach_file_path = _check_var($f_list['ATTACH_FILE_PATH']); // 경로
                              $file_html = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                          }
                      }
                  }

                  echo <<<LI
                            <li>
                                <p class="name">주소</p>
                                <div class="contents_wrap">
                                    <div class="contents">
                                        {$_db_M_ADDRESS}
                                        <a href="{$_db_M_NAVER_LINK}" target="blank">naver</a>
                                        <a href="{$_db_M_KAKAO_LINK}" target="blank">kakao</a>
                                    </div>

                                    <div class="contents">
                                        {$_db_D_ADDRESS}
                                        <a href="{$_db_D_NAVER_LINK}" target="blank">naver</a>
                                        <a href="{$_db_D_KAKAO_LINK}" target="blank">kakao</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <p class="name">운영</p>
                                <p class="contents">{$_db_OPERATE}</p>
                            </li>
                            <li>
                                <p class="name">주차</p>
                                <p class="contents">{$_db_PARKKING}</p>
                            </li>
                            <li>
                                <p class="name">시설</p>
                                <p class="contents">{$_db_FACILITIES}</p>
                            </li>
                        LI;
              }
          }

      } catch (Exception $e) {
          $arrRtn['code'] = $e -> getCode();
          $arrRtn['msg'] = $e -> getMessage();

          echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
      }
    }
?>
