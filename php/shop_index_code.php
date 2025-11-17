<?php
/**
 * 파일명 : shop_index_code.php
 * 내용 : 샵 메인페이지
 * 최초작성날짜 : 2023/11/03
 * 최초작성자 : 이보경
 * ------------------------------------
 * name       date        comment
 * 이보경    2023/11/03    V1.0
 * 김민성    2023/11/13    shop 기능추가
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

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

            $arrValue = array();
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT M.CATEGORY1_SEQ
                      , M.CATEGORY2_SEQ
                      , M.TITLE AS MAIN_TITLE
                      , D.CATEGORY3_SEQ
                      , D.BADGE_CO
                      , D.BRAND
                      , D.TITLE AS SUB_TITLE
                      , (SELECT TITLE FROM CATEGORY1 A WHERE A.CATEGORY1_SEQ = M.CATEGORY1_SEQ) AS CATEGORY1_NAME
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , D.ORDER_NUMBER AS SUB_ORDER
                      , D.SALE_YN
                      , FORMAT(D.OID_PRICE, '') AS OID_PRICE
                      , D.SALE_PERCENT
                      , FORMAT(D.PRICE, '') AS PRICE
                      , D.ATTACH_FILE_ID
                      , M.reg_date
                   FROM CATEGORY2 M, CATEGORY3 D
                  WHERE 1
                    AND M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                    AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ
                    AND M.MAIN_YN = 'Y'
                    AND D.MAIN_YN = 'Y'
                    AND M.PAGE_TYPE = :PAGE_TYPE
                    AND D.TYPE_CD LIKE '%NEW%'
                  ORDER BY MAIN_ORDER DESC, reg_date DESC, MAIN_TITLE, SUB_ORDER DESC
                  LIMIT 0, 10";

            $name_sql = "NEW 제품 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue , $name_sql);

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
                    $_db_BADGE_CO = _check_var($data['BADGE_CO']); // 뱃지
                    $_db_M_PRICE = _check_var($data['PRICE']); // 금액
                    $_db_M_SALE_YN = _check_var($data['SALE_YN']); // 할인금액 사용 여부
                    $_db_M_OID_PRICE = _check_var($data['OID_PRICE']); // 원가 (할인 전 금액)
                    $_db_M_SALE_PERCENT = _check_var($data['SALE_PERCENT']); // 할인율
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

                    $BADGE_CO_html = "";
                    $BADGE_CO = "";

                    if (!empty($_db_BADGE_CO)){
                        switch (trim($_db_BADGE_CO)) { // trim() 함수로 문자열 앞뒤 공백 제거
                            case 'NEW':
                                $BADGE_CO = 'new';
                                break;
                            case 'SALE':
                                $BADGE_CO = 'sale';
                                break;
                            case 'BEST':
                                $BADGE_CO = 'best';
                                break;
                            case 'SOLDOUT':
                                $BADGE_CO = 'sold out';
                                break;
                            default:
                                // 해당하는 id가 없는 경우에 대한 처리
                                break;
                        }

                        $BADGE_CO_html = '<div class="prdBadge"><span>'.$BADGE_CO.'</span></div>';
                    }

                    $prdSalePercent_html = "";
                    $prdprdSalePrice_html = "";

                    if ($_db_M_SALE_YN == "Y") {
                        $prdSalePercent_html = '<div class="prdSalePercent"><span>'. $_db_M_SALE_PERCENT .'</span>%</div>';
                        $prdprdSalePrice_html = '<div class="prdSalePrice"><span>'.$_db_M_OID_PRICE. '</span></div>';
                    }

                    $URL = shopFoldName . '/product/detail.php?seq=' . $_db_CATEGORY3_SEQ;

                    echo <<<DIV
                                <div class="swiper-slide">
                                    <a href="{$URL}" class="prdThumbnail">
                                        {$BADGE_CO_html}
                                        <img src="{$_db_MAIN_ATTACH_FILE_ID}">
                                    </a>
                                    <a href="{$URL}" class="prdBrand">{$_db_BRAND}</a>
                                    <a href="{$URL}" class="prdName">{$_db_TITLE}</a>
                                    {$prdSalePercent_html}
                                    <div class="prdPrice"><span>{$_db_M_PRICE}</span></div>
                                    {$prdprdSalePrice_html}
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

            $url = shopFoldName . '/product/list.php?cata2=' . $data['CATEGORY2_SEQ'];

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
?>