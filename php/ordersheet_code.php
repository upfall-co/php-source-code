<?php
/**
 * 파일명 : ordersheet_code.php
 * 내용 : 주문 페이지
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

    $terms = SiteConfig::terms_data(PAGE1); //약관

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
          'code' => 500
        , 'msg' => ''
    );

    try {
        global $_db_TOTAL_COUNT;
        global $_db_TOTAL_PRICE;
        global $_db_TOTAL_PRICE_TEXT;
        global $REAL_DLVY_PRICE;
        global $DELIVERY_PRICE;
        global $DELIVERY_IF_PRICE;
        global $DELIVERY;
        global $PRODUCTS;
        global $OPTIONS;
        global $_INIS_PRICE;
        global $_INIS_GOODNAME;

        $NAME = '';
        $MOBILE = '';
        $EMAIL = '';

        $PRODUCT_TEMP_SEQ = get_request_param('SEQ', 'GET');

        if (isset($_SESSION['ORDER'][$PRODUCT_TEMP_SEQ])) {
            if ($PRODUCT_TEMP_SEQ != $_SESSION['ORDER'][$PRODUCT_TEMP_SEQ]) {
                dieAndErrorMove('잘못된 접근입니다.');
            }
        } else {
            dieAndErrorMove('잘못된 접근입니다.');
        }
        
        if (isset($_SESSION['MEMBER'])) {
            if (!empty($_SESSION['MEMBER'])) {
                $table = 'MEMBER';

                $arrValue = array();
                $arrValue[':ID'] = $_SESSION['MEMBER']['ID'];

                $sql = "
                     SELECT NAME
                          , MOBILE
                          , EMAIL
                       FROM {$table}
                      WHERE ID = :ID";

                $name_sql = "계정값";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $data = $clefResult->getResultSet();

                $NAME = _check_var($data['NAME']);
                $MOBILE = _check_var($data['MOBILE']);
                $EMAIL = _check_var($data['EMAIL']);

                if (!empty($MOBILE)) {
                    $MOBILE = formatPhoneNumber($MOBILE);
                }

                $sql = "SELECT CART_TEMP_DEL() as temp_del";
                $name_sql = "TEMP 삭제 함수";
                $clefResult = $mysqldb->get($sql, null, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(505);
                }
            }
        }

        $DELIVERY_PRICE = gfn_getZcmcommonVal("COL010", "PRICE", "TH1_THEM_CD"); // 배송비
        $DELIVERY_IF_PRICE = gfn_getZcmcommonVal("COL010", "IFPRICE", "TH1_THEM_CD"); // 조건 금액
        $DELIVERY = "무료";

        $TYPE_MONITOR = "";
        
        if (get_is_mobile()) {
            $TYPE_MONITOR = 'MO'; // 모바일
        } else {
            $TYPE_MONITOR = 'PC'; // PC
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }


    function getOrderList() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            global $_db_TOTAL_COUNT;
            global $_db_TOTAL_PRICE;
            global $_db_TOTAL_PRICE_TEXT;
            global $REAL_DLVY_PRICE;
            global $PRODUCTS;
            global $OPTIONS;
            global $DELIVERY;
            global $_INIS_PRICE;
            global $_INIS_GOODNAME;

            $PAGE = PAGE;

            $PRODUCT_TEMP_SEQ = get_request_param('SEQ', 'GET');

            $arrValue = array();
            $arrValue[':PRODUCT_TEMP_SEQ'] = $PRODUCT_TEMP_SEQ;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $values = array(
                'PAGE_TYPE' => $PAGE
            );

            $json_str = json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $sql = "
                SELECT OPTION_PRICE_CHANGE('$json_str') as temp";

            $name_sql = "금액 변경 시퀀스";
            $clefResult = $mysqldb->get($sql, null, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(505);
            }
    
            $table = 'PRODUCT_TEMP'; // 작품 테이블
            $table_OP = 'PRODUCT_OPTION_TEMP'; // 옵션 관리자 테이블
    
            $sql = "
                 SELECT M.PRODUCT_TEMP_SEQ
                      , M.ATTACH_FILE_ID
                      , M.CATEGORY3_SEQ
                      , M.TITLE
                      , D.OPTION_NAME
                      , M.FRAME
                      , M.PRICE AS MPRICE
                      , D.QUANTITY
                      , D.PRICE
                      , D.OPTION_SEQ
                      , FORMAT(D.PRICE, 0) AS PRICE_TEXT
                      , (SELECT SUM(A.QUANTITY*(M.PRICE + A.PRICE))
                           FROM PRODUCT_OPTION_TEMP A
                          WHERE M.PRODUCT_TEMP_SEQ = A.PRODUCT_TEMP_SEQ
                            AND D.OPTION_SEQ = A.OPTION_SEQ) AS OPTION_PRICE
                      , FORMAT((SELECT SUM(A.QUANTITY*(M.PRICE + A.PRICE))
                                  FROM PRODUCT_OPTION_TEMP A
                                 WHERE M.PRODUCT_TEMP_SEQ = A.PRODUCT_TEMP_SEQ
                                   AND D.OPTION_SEQ = A.OPTION_SEQ), 0) AS OPTION_PRICE_TEXT
                      , (SELECT COUNT(*)
                           FROM PRODUCT_OPTION_TEMP D
                          WHERE M.PRODUCT_TEMP_SEQ = D.PRODUCT_TEMP_SEQ
                            AND M.PAGE_TYPE = :PAGE_TYPE) AS TOTAL_COUNT
                      , (SELECT SUM(D.QUANTITY*(D.PRICE + E.PRICE))
                           FROM PRODUCT_OPTION_TEMP D, PRODUCT_TEMP E
                          WHERE M.PRODUCT_TEMP_SEQ = D.PRODUCT_TEMP_SEQ
                            AND M.PRODUCT_TEMP_SEQ = E.PRODUCT_TEMP_SEQ
                            AND E.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                            AND M.PAGE_TYPE = :PAGE_TYPE) AS TOTAL_PRICE
                      , FORMAT((SELECT SUM(D.QUANTITY*(D.PRICE + E.PRICE))
                                  FROM PRODUCT_OPTION_TEMP D, PRODUCT_TEMP E
                                 WHERE M.PRODUCT_TEMP_SEQ = D.PRODUCT_TEMP_SEQ
                                   AND M.PRODUCT_TEMP_SEQ = E.PRODUCT_TEMP_SEQ
                                   AND E.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                                   AND M.PAGE_TYPE = :PAGE_TYPE), 0) AS TOTAL_PRICE_TEXT
                      , (SELECT GROUP_CONCAT(CATEGORY3_SEQ SEPARATOR ',')
                           FROM PRODUCT_TEMP
                          WHERE M.PRODUCT_TEMP_SEQ = PRODUCT_TEMP_SEQ) AS PRODUCTS
                      , (SELECT GROUP_CONCAT(D.OPTION_SEQ SEPARATOR ',')
                           FROM PRODUCT_OPTION_TEMP D
                          WHERE M.PRODUCT_TEMP_SEQ = D.PRODUCT_TEMP_SEQ) AS OPTIONS
                   FROM {$table} M, {$table_OP} D
                  WHERE M.PRODUCT_TEMP_SEQ = D.PRODUCT_TEMP_SEQ
                    AND M.CATEGORY3_SEQ = D.CATEGORY3_SEQ 
                    AND M.PRODUCT_TEMP_SEQ = :PRODUCT_TEMP_SEQ
                  ORDER BY M.TITLE, D.ORDER_NUMBER DESC";
    
            $name_sql = "주문 작품 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);
    
            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }
    
            $list = $clefResult->getResultSet();
    
            if (empty($list)) {
                dieAndErrorMove('잘못된 접근입니다.');
            }
    
            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_TITLE = _check_var($data['TITLE']); // 작품명
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 작품 시퀀스
                    $_db_OPTION_NAME = _check_var($data['OPTION_NAME']); // 옵션명
                    $_db_FRAME = _check_var($data['FRAME']); // 프레임
                    $_db_QUANTITY = _check_var($data['QUANTITY']); // 수량
                    $_db_OPTION_PRICE_TEXT = _check_var($data['OPTION_PRICE_TEXT']); // 옵션별 토탈 금액
                    $_db_OPTION_SEQ = _check_var($data['OPTION_SEQ']); // 옵션시퀀스
                    $_db_TOTAL_COUNT = _check_var($data['TOTAL_COUNT']); // 토탈 개수
                    $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 토탈 금액 
                    $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 토탈 금액 
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                    $PRODUCTS = _check_var($data['PRODUCTS']); // 필요값 [작품 시퀀스]
                    $OPTIONS = _check_var($data['OPTIONS']); // 필요값 [옵션]

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);
            
                        if (!empty($file_list)) {
                            foreach ($file_list as $list) {
                                $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                                $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    if (PAGE == PAGE1) {
                        echo <<<LI
                                    <li class="tbody">
                                        <ul class="table_column">
                                            <li class="td_img">
                                                <div class="shop_img"><img src="{$path_File}" alt="작품 이미지"></div>
                                            </li>
                                            <li class="td_name">{$_db_TITLE}</li>
                                            <li class="td_option">{$_db_OPTION_NAME}</li>
                                            <li class="td_frame">{$_db_FRAME}</li>
                                            <li class="td_count">{$_db_QUANTITY}</li>
                                            <li class="td_price"><span>{$_db_OPTION_PRICE_TEXT}</span> 원</li>
                                        </ul>
                                    </li>
                                LI;
                    } else if (PAGE == PAGE2) {
                        $url = shopFoldName. "/product/detail.php?seq=". $_db_CATEGORY3_SEQ;

                        echo <<<LI
                                    <li class="tbody">
                                        <ul class="table_column">
                                            <li class="td_img">
                                                <a href="{$url}" class="prdThumbnail"><img src="{$path_File}" alt="상품 이미지"></a>
                                            </li>
                                            <li class="td_prd_info">
                                                <a href="{$url}" class="td_name"><p class="prdName">{$_db_TITLE}</p></a>
                                                <div class="td_row">
                                                    <div class="td_option"><div class="prd_option">{$_db_OPTION_NAME}</div></div>
                                                    <div class="td_count">수량 <span class="option_count">{$_db_QUANTITY}</span>개</div>
                                                </div>
                                            </li>
                                            <li class="td_price"><span class="prdPrice" id="option_price{$_db_OPTION_SEQ}">{$_db_OPTION_PRICE_TEXT}</span></li>
                                        </ul>
                                    </li>
                                LI;
                    }
                }

                $sql = "
                     SELECT CONCAT(IF(COUNT(*) > 1, 
                            CONCAT(MIN(M.TITLE), ' 외 '), 
                            GROUP_CONCAT(DISTINCT M.TITLE SEPARATOR ', '))) AS TOTAL_NAME
                       FROM {$table} M, {$table_OP} D
                      WHERE M.PRODUCT_TEMP_SEQ = D.PRODUCT_TEMP_SEQ
                        AND M.CATEGORY3_SEQ = D.CATEGORY3_SEQ 
                        AND M.PRODUCT_TEMP_SEQ = :PRODUCT_TEMP_SEQ
                        AND M.PAGE_TYPE = :PAGE_TYPE
                      ORDER BY M.TITLE, D.ORDER_NUMBER DESC";
        
                $name_sql = "주문 토탈명";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }
        
                $data = $clefResult->getResultSet();

                $_INIS_GOODNAME = _check_var($data['TOTAL_NAME']); // 토탈명

                $frist_del = gfn_getDELIVERY($_db_TOTAL_PRICE);

                $DELIVERY = "";
                $REAL_DLVY_PRICE = 0;

                if (PAGE == PAGE2) {
                    if ($frist_del > 0) {
                        $_db_TOTAL_PRICE = $_db_TOTAL_PRICE +  $frist_del;
                        $_db_TOTAL_PRICE_TEXT = number_format($_db_TOTAL_PRICE);
                        $DELIVERY = number_format($frist_del). '원';

                        $REAL_DLVY_PRICE = $frist_del;
                    } else {
                        $DELIVERY = "무료";
                    }
                }

                $_INIS_PRICE = $_db_TOTAL_PRICE;
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>