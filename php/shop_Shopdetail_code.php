<?php
/**
 * 파일명 : Shopdetail_code.php
 * 내용 : 작품 상세 정보
 * 최초작성날짜 : 2023/08/17
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/17    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;
    use Clef\SiteConfig;

    $terms = SiteConfig::terms_data(PAGE2); //약관

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
          'code' => 500
        , 'msg' => ''
    );

    try {
        $CATEGORY3_SEQ = get_request_param('seq', 'GET');

        if (empty($CATEGORY3_SEQ)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $login_chk = false;

        if (isset($_SESSION['MEMBER'])) {
            if (!empty($_SESSION['MEMBER'])) {
                $login_chk = true;
            }
        }

        $table = 'CATEGORY3'; // 작품 테이블
        $table_OP = 'CATEGORY_OPTION'; // 옵션 관리자 테이블

        $arrValue = array();
        $arrValue[':CATEGORY3_SEQ'] = $CATEGORY3_SEQ;

        $sql = "
             SELECT CATEGORY3_SEQ
                  , (SELECT D.CATEGORY1_SEQ
                       FROM CATEGORY1 D
                      WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ) AS CATEGORY1_SEQ
                  , (SELECT D.TITLE
                       FROM CATEGORY1 D
                      WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ) AS CATEGORY1_NAME
                  , CATEGORY2_SEQ
                  , (SELECT D.TITLE
                       FROM CATEGORY2 D
                      WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                        AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ) AS CATEGORY2_TITLE
                  , (SELECT D.ORDER_NUMBER
                       FROM CATEGORY2 D
                      WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                        AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ) AS CATEGORY2_ORDER_NUMBER
                  , TITLE
                  , SUB_TITLE
                  , BADGE_CO
                  , BRAND
                  , SALE_YN
                  , OID_PRICE
                  , FORMAT(OID_PRICE, '') AS OID_PRICE_TEXT
                  , SALE_PERCENT
                  , PRICE
                  , FORMAT(PRICE, '') AS PRICE_TEXT
                  , CONTENT_TEXT
                  , ATTACH_FILE_ID
               FROM {$table} M
              WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ
                AND MAIN_YN = 'Y'";

        $name_sql = "작품 상세정보 리스트";
        $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 카테고리1 시퀀스
        $_db_CATEGORY1_NAME = _check_var($data['CATEGORY1_NAME']); // 카테고리1명
        $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 카테고리2 시퀀스
        $_db_CATEGORY2_TITLE = _check_var($data['CATEGORY2_TITLE']); // 카테고리2명
        $_db_TITLE = _check_var($data['TITLE']); // 상품명
        $_db_BADGE_CO = _check_var($data['BADGE_CO']); // 뱃지
        $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 상품설명
        $_db_PRICE = _check_var($data['PRICE']); // 금액
        $_db_PRICE_TEXT = _check_var($data['PRICE_TEXT']); // 금액 문자
        $_db_M_SALE_YN = _check_var($data['SALE_YN']); // 할인금액 사용 여부
        $_db_M_OID_PRICE = _check_var($data['OID_PRICE']); // 원가 (할인 전 금액)
        $_db_M_OID_PRICE_TEXT = _check_var($data['OID_PRICE_TEXT']); // 원가 문자 (할인 전 금액)
        $_db_M_SALE_PERCENT = _check_var($data['SALE_PERCENT']); // 할인율
        $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
        $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

        $file_html = "";

        if (!empty($_db_SUB_TITLE)) {
            $_db_SUB_TITLE = nl2br($_db_SUB_TITLE);
        }

        if (!empty($_db_ATTACH_FILE_ID)) {
            $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 3);

            if (!empty($file_list)) {
                foreach ($file_list as $list) {
                    $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                    $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                    $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                    $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;

                    $file_html .= <<<DIV
                                        <div class="swiper-slide">
                                            <div class="prdThumbnail">
                                                <img src="{$path_File}">
                                            </div>
                                        </div>
                                    DIV;
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

        $sql = "
             SELECT OPTION_SEQ
                  , OPTION_NAME
                  , PRICE
                  , FORMAT(PRICE, 0) AS PRICE_TEXT
                  , MAIN_YN AS OP_MAIN_YN
                  , SOLD_YN AS OP_SOLD_YN
                  , QUANTITY AS OP_QUANTITY
                  , ORDER_NUMBER AS OP_ORDER_NUMBER
               FROM {$table_OP}
              WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ
                AND MAIN_YN = 'Y'";

        $name_sql = "작품 옵션 상세정보 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $OP_data = $clefResult->getResultSet();
        $OP_html = '';

        if (!empty($OP_data)) {
            foreach ($OP_data as $OP){
                $OPTION_SEQ = _check_var($OP['OPTION_SEQ']); // 옵션시퀀스
                $OPTION_NAME = _check_var($OP['OPTION_NAME']); // 옵션명
                $PRICE = _check_var($OP['PRICE']); // 금액
                $PRICE_TEXT = _check_var($OP['PRICE_TEXT']); // 금액 문자
                $SOLD_YN = _check_var($OP['OP_SOLD_YN']); // 금액 문자

                $SOLD = "";
                $PRICE_HTML = "";

                if ($SOLD_YN == "Y") {
                    $SOLD = "(품절)";
                }

                if ($PRICE != 0) {
                    $PRICE_HTML = " (+". $PRICE_TEXT. ")";
                }

                $OP_html .= <<<OPTION
                                    <option value="{$OPTION_SEQ}" data-val="{$PRICE}" data-price="{$PRICE_TEXT}" data-mval="{$_db_PRICE}">{$OPTION_NAME}{$PRICE_HTML}{$SOLD}</option>
                            OPTION;
            }
        }

        $frist_del = gfn_getDELIVERY($_db_PRICE);

        $DELIVERY = "";

        if ($frist_del > 0) {
            $DELIVERY = $frist_del = number_format($frist_del);
        } else {
            $DELIVERY = "무료";
        }

        $DELIVERY_PRICE = gfn_getZcmcommonVal("COL010", "PRICE", "TH1_THEM_CD"); // 배송비
        $DELIVERY_IF_PRICE = gfn_getZcmcommonVal("COL010", "IFPRICE", "TH1_THEM_CD"); // 조건 금액

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

    function getsecRelation_List() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $PAGE = PAGE;

            $CATEGORY3_SEQ = get_request_param('seq', 'GET');

            $arrValue = array();
            $arrValue[':CATEGORY3_SEQ'] = $CATEGORY3_SEQ;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT D.CATEGORY3_SEQ
                       , D.TITLE AS CATEGORY3_NAME
                       , D.BADGE_CO
                       , D.SALE_YN
                       , FORMAT(D.OID_PRICE, '') AS OID_PRICE
                       , D.SALE_PERCENT
                       , FORMAT(D.PRICE, '') AS PRICE
                       , D.ORDER_NUMBER
                       , D.reg_date
                       , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '1' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS MAIN_ATTACH_FILE_ID
                       , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME) FROM ZCMFILEA WHERE D.ATTACH_FILE_ID = ATTACH_FILE_ID AND ATTACH_GROUP = '4' ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS HOVER_ATTACH_FILE_ID
                    FROM CATEGORY2 M, CATEGORY3 D
                   WHERE D.CATEGORY3_SEQ != :CATEGORY3_SEQ
                     AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ
                     AND M.CATEGORY2_SEQ = (SELECT CATEGORY2_SEQ FROM CATEGORY3 WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ)
                     AND M.MAIN_YN = 'Y'
                     AND D.MAIN_YN = 'Y'
                     AND M.PAGE_TYPE = :PAGE_TYPE
                   ORDER BY ORDER_NUMBER DESC";

            $name_sql = "분류 관련 작품";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $shopFoldName = shopFoldName;

            if (!empty($list)) {
                echo <<<SECTION
                                <section class="sec_relation_prd">
                                    <div class="slide_btn_wrap">
                                        <div class="swiper alsoLikeSwiper">
                                            <div class="sub_sec_title">you may also like</div>
                        
                                            <div class="swiper-wrapper">
                        SECTION;
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 작품 시퀀스 
                    $_db_CATEGORY3_NAME = _check_var($data['CATEGORY3_NAME']); // 작품명
                    $_db_BADGE_CO = _check_var($data['BADGE_CO']); // 뱃지
                    $_db_PRICE = _check_var($data['PRICE']); // 금액
                    $_db_M_SALE_YN = _check_var($data['SALE_YN']); // 할인금액 사용 여부
                    $_db_M_OID_PRICE = _check_var($data['OID_PRICE']); // 원가 (할인 전 금액)
                    $_db_M_SALE_PERCENT = _check_var($data['SALE_PERCENT']); // 할인율
                    $_db_MAIN_ATTACH_FILE_ID = _check_var($data['MAIN_ATTACH_FILE_ID']); // 파일아이디
                    $_db_HOVER_ATTACH_FILE_ID = _check_var($data['HOVER_ATTACH_FILE_ID']); // 파일아이디

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

                    $url =  shopFoldName . '/product/detail.php?seq=' . $_db_CATEGORY3_SEQ;

                    echo <<<DIV
                                <div class="swiper-slide">
                                    <a href="{$url}" class="prdThumbnail">
                                        {$BADGE_CO_html}
                                        <img src="{$_db_MAIN_ATTACH_FILE_ID}">
                                        <img src="{$_db_HOVER_ATTACH_FILE_ID}">
                                    </a>
                                    <a href="{$url}" class="prdName">{$_db_CATEGORY3_NAME}</a>
                                    {$prdSalePercent_html}
                                    <div class="prdPrice"><span>{$_db_PRICE}</span></div>
                                    {$prdprdSalePrice_html}
                                </div>
                            DIV;
                }

                echo <<<SECTION
                                            </div>
                                        </div>

                                        <div class="swiper-button-prev"><img src="{$shopFoldName}/img/icon_slide_prev.svg" alt="이전"></div>
                                        <div class="swiper-button-next"><img src="{$shopFoldName}/img/icon_slide_next.svg" alt="다음"></div>
                                    </div>
                                </section>
                        SECTION;
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>