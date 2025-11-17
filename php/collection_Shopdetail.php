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

        $sql = "
             SELECT CATEGORY3_SEQ
                  , CATEGORY1_SEQ
                  , (SELECT D.TITLE
                       FROM CATEGORY1 D
                      WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ) AS CATEGORY1_NAME
                  , CATEGORY2_SEQ
                  , (SELECT D.TITLE
                       FROM CATEGORY2 D
                      WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                        AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ) AS SERIES_TITLE
                  , (SELECT D.ORDER_NUMBER
                       FROM CATEGORY2 D
                      WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                        AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ) AS SERIES_ORDER_NUMBER
                  , TITLE
                  , QUANTITY
                  , FRAME
                  , CONTENT_TEXT
                  , ATTACH_FILE_ID
               FROM {$table} M
              WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ
                AND MAIN_YN = 'Y'";

        $name_sql = "작품 상세정보 리스트";
        $clefResult = $mysqldb->get($sql, [':CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 작가 시퀀스
        $_db_CATEGORY1_NAME = _check_var($data['CATEGORY1_NAME']); // 작가명
        $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 시리즈 시퀀스
        $_db_SERIES_TITLE = _check_var($data['SERIES_TITLE']); // 시리즈명
        $_db_SERIES_ORDER_NUMBER = _check_var($data['SERIES_ORDER_NUMBER']); // 시리즈 정렬값
        $_db_TITLE = _check_var($data['TITLE']); // 작품명
        $_db_QUANTITY = _check_var($data['QUANTITY']); // 수량
        $_db_FRAME = _check_var($data['FRAME']); // 프레임
        $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
        $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

        $file_html = "";

        $back_url = artFoldName. "/shop/series.php?seq=". $_db_CATEGORY1_SEQ. "#series". $_db_SERIES_ORDER_NUMBER;

        if (!empty($_db_ATTACH_FILE_ID)) {
            $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 3);

            if (!empty($file_list)) {
                foreach ($file_list as $list) {
                    $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                    $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                    $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                    $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;

                    $file_html .= <<<DIV
                                        <div class="swiper-slide img_box">
                                            <img src="{$path_File}">
                                        </div>
                                    DIV;
                }
            }
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
        $clefResult = $mysqldb->select($sql, [':CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

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

                if ($SOLD_YN == "Y") {
                    $SOLD = "(품절)";
                }

                $OP_html .= <<<OPTION
                                    <option value="{$OPTION_SEQ}" data-val="{$PRICE}" data-price="{$PRICE_TEXT}">{$OPTION_NAME}{$SOLD}</option>
                            OPTION;
            }
        }

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
            $CATEGORY3_SEQ = get_request_param('seq', 'GET');

            $PAGE = PAGE;
            $arrValue = array();
            $arrValue[':PAGE_TYPE'] = $PAGE;
            $arrValue[':CATEGORY3_SEQ'] = $CATEGORY3_SEQ;

            $table = 'CATEGORY3'; // 작품 테이블

            $sql = "
                 SELECT CATEGORY3_SEQ
                      , CATEGORY1_SEQ
                      , CATEGORY2_SEQ
                      , TITLE
                      , ATTACH_FILE_ID
                   FROM {$table} M
                  WHERE CATEGORY3_SEQ != :CATEGORY3_SEQ
                    AND CATEGORY2_SEQ = (SELECT CATEGORY2_SEQ FROM CATEGORY3 WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ)
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                  ORDER BY ORDER_NUMBER DESC";

            $name_sql = "작시리즈 관련 작품";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $artFoldName = artFoldName;

            if (!empty($list)) {
                echo <<<SECTION
                            <section class="secRelation">
                                <div class="wrapper">
                                    <div class="relation_title">관련작품</div>
                                    <div class="relation_prd">
                                        <div class="swiper-button-prev"><img src="{$artFoldName}/img/shop/relation_prev.png" alt="이전"></div>
                                        <div class="swiper relationSwiper">
                                            <div class="swiper-wrapper">
                        SECTION;
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 작품 시퀀스 
                    $_db_TITLE = _check_var($data['TITLE']); // 작품명
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

                    $url =  artFoldName . '/shop/detail.php?seq=' . $_db_CATEGORY3_SEQ;

                    echo <<<DIV
                                <div class="swiper-slide">
                                    <a href="{$url}"><img src="{$_db_MAIN_ATTACH_FILE_ID}"></a>
                                    <p class="prd_name">{$_db_TITLE}</p>
                                </div>
                            DIV;
                }

                echo <<<SECTION
                                                </div>
                                            </div>
                                            <div class="swiper-button-next"><img src="{$artFoldName}/img/shop/relation_next.png" alt="다음"></div>
                                        </div>
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