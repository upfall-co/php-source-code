<?php
/**
 * 파일명 : cart_code.php
 * 내용 : 장바구니 페이지
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
        global $_db_TOTAL_COUNT;
        global $_db_TOTAL_PRICE;
        global $_db_TOTAL_PRICE_TEXT;
        global $DELIVERY_PRICE;
        global $DELIVERY_IF_PRICE;
        global $DELIVERY;

        $_db_TOTAL_COUNT = 0;
        $_db_TOTAL_PRICE = 0;
        $_db_TOTAL_PRICE_TEXT = 0;

        $DELIVERY_PRICE = gfn_getZcmcommonVal("COL010", "PRICE", "TH1_THEM_CD"); // 배송비
        $DELIVERY_IF_PRICE = gfn_getZcmcommonVal("COL010", "IFPRICE", "TH1_THEM_CD"); // 조건 금액
        $DELIVERY = "무료";

        $prd_list_mdoe = "CART";
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }


    function getPrdChkList() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            global $_db_TOTAL_COUNT;
            global $_db_TOTAL_PRICE;
            global $_db_TOTAL_PRICE_TEXT;
            global $DELIVERY;

            $PAGE = PAGE;

            $ID = "";
            $unique_id = "";

            $arrValue = array();
            $where = '';
            $where2 = '';

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

            if (isset($_SESSION['MEMBER'])) {
                if (!empty($_SESSION['MEMBER'])) {
                    $ID = $_SESSION['MEMBER']['ID'];
                    $where .= " AND A.ID = :ID";
                    $arrValue[':ID'] = $ID;

                    $where2 .= " AND A.ID = B.ID ";
                } else {
                    $unique_id = session_id();

                    $where .= " AND A.SESSION = :SESSION";
                    $arrValue[':SESSION'] = $unique_id;

                    $where2 .= " AND A.SESSION = B.SESSION ";
                }
            } else {
                $unique_id = session_id();

                $where .= " AND A.SESSION = :SESSION";
                $arrValue[':SESSION'] = $unique_id;

                $where2 .= " AND A.SESSION = B.SESSION ";
            }

            $table = 'PRODUCT_CART'; // 작품 테이블
            $table_OP = 'PRODUCT_OPTION_CART'; // 옵션 관리자 테이블

            $sql = "
                 SELECT D.CHEK_YN
                      , M.PRODUCT_CART_SEQ
                      , M.CATEGORY3_SEQ
                      , M.ATTACH_FILE_ID
                      , M.TITLE
                      , D.OPTION_NAME
                      , M.FRAME
                      , M.PRICE AS MPRICE
                      , D.QUANTITY
                      , D.PRICE
                      , D.OPTION_SEQ
                      , (SELECT SUM(C.QUANTITY*(M.PRICE + C.PRICE))
                           FROM PRODUCT_OPTION_CART C
                          WHERE M.PRODUCT_CART_SEQ = C.PRODUCT_CART_SEQ
                            AND D.OPTION_SEQ = C.OPTION_SEQ) AS OPTION_PRICE
                      , FORMAT((SELECT SUM(C.QUANTITY*(M.PRICE + C.PRICE))
                                  FROM PRODUCT_OPTION_CART C
                                 WHERE M.PRODUCT_CART_SEQ = C.PRODUCT_CART_SEQ
                                   AND D.OPTION_SEQ = C.OPTION_SEQ), 0) AS OPTION_PRICE_TEXT
                      , (SELECT IFNULL(COUNT(*), 0)
                           FROM PRODUCT_SEQ_CART B, PRODUCT_CART C, PRODUCT_OPTION_CART E
                          WHERE 1
                            {$where2}
                            AND B.PRODUCT_CART_SEQ = C.PRODUCT_CART_SEQ
                            AND B.PRODUCT_CART_SEQ = E.PRODUCT_CART_SEQ
                            AND E.CHEK_YN = 'Y'
                            AND B.PAGE_TYPE = '{$PAGE}') AS TOTAL_COUNT
                      , (SELECT IFNULL(SUM(E.QUANTITY*(C.PRICE + E.PRICE)), 0)
                           FROM PRODUCT_SEQ_CART B, PRODUCT_CART C, PRODUCT_OPTION_CART E
                          WHERE 1
                            {$where2}
                            AND B.PRODUCT_CART_SEQ = C.PRODUCT_CART_SEQ
                            AND B.PRODUCT_CART_SEQ = E.PRODUCT_CART_SEQ
                            AND E.CHEK_YN = 'Y'
                            AND B.PAGE_TYPE = '{$PAGE}') AS TOTAL_PRICE
                      , FORMAT((SELECT IFNULL(SUM(E.QUANTITY*(C.PRICE + E.PRICE)), 0)
                                  FROM PRODUCT_SEQ_CART B, PRODUCT_CART C, PRODUCT_OPTION_CART E
                                 WHERE 1
                                   {$where2}
                                   AND B.PRODUCT_CART_SEQ = C.PRODUCT_CART_SEQ
                                   AND B.PRODUCT_CART_SEQ = E.PRODUCT_CART_SEQ
                                   AND E.CHEK_YN = 'Y'
                                   AND B.PAGE_TYPE = '{$PAGE}'), 0) AS TOTAL_PRICE_TEXT
                   FROM PRODUCT_SEQ_CART A, {$table} M, {$table_OP} D
                  WHERE A.PRODUCT_CART_SEQ = M.PRODUCT_CART_SEQ
                    AND A.PRODUCT_CART_SEQ = D.PRODUCT_CART_SEQ
                    AND M.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                    AND M.PAGE_TYPE = '{$PAGE}'
                    {$where}
                  ORDER BY M.TITLE, D.ORDER_NUMBER DESC";
            
            $name_sql = "장바구니 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                $count = 1;

                foreach ($list as $data) {
                    $_db_PRODUCT_CART_SEQ = _check_var($data['PRODUCT_CART_SEQ']); // 장바구니 시퀀스
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 작품 시퀀스
                    $_db_CHEK_YN = _check_var($data['CHEK_YN']); // 체크확인
                    $_db_TITLE = _check_var($data['TITLE']); // 작품명
                    $_db_OPTION_NAME = _check_var($data['OPTION_NAME']); // 옵션명
                    $_db_FRAME = _check_var($data['FRAME']); // 프레임
                    $_db_MPRICE = _check_var($data['MPRICE']); // 금액
                    $_db_QUANTITY = _check_var($data['QUANTITY']); // 수량
                    $_db_DPRICE = _check_var($data['PRICE']); // 금액
                    $_db_OPTION_PRICE = _check_var($data['OPTION_PRICE']); // 옵션별 토탈 금액
                    $_db_OPTION_PRICE_TEXT = _check_var($data['OPTION_PRICE_TEXT']); // 옵션별 토탈 금액
                    $_db_OPTION_SEQ = _check_var($data['OPTION_SEQ']); // 옵션시퀀스
                    $_db_TOTAL_COUNT = _check_var($data['TOTAL_COUNT']); // 토탈 개수
                    $_db_TOTAL_PRICE = _check_var($data['TOTAL_PRICE']); // 토탈 금액 
                    $_db_TOTAL_PRICE_TEXT = _check_var($data['TOTAL_PRICE_TEXT']); // 토탈 금액 
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

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

                    $checked = "";

                    if ($_db_CHEK_YN == "Y") {
                        $checked = "checked";
                    }

                    if (PAGE == PAGE1) {
                        echo <<<LI
                                    <li class="tbody">
                                        <ul class="table_column">
                                            <li class="td_chk" data-pk="{$_db_PRODUCT_CART_SEQ}" data-seq="{$_db_CATEGORY3_SEQ}" data-code="{$_db_OPTION_SEQ}" data-val="{$_db_OPTION_PRICE}" data-count="{$_db_QUANTITY}">
                                                <input type="checkbox" id="prdChk{$count}" name="prdChk" $checked>
                                                <label for="prdChk{$count}"></label>
                                            </li>
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
                                            <li class="td_chk" id="{$_db_OPTION_SEQ}" data-pk="{$_db_PRODUCT_CART_SEQ}" data-seq="{$_db_CATEGORY3_SEQ}" data-code="{$_db_OPTION_SEQ}" data-val="{$_db_DPRICE}" data-count="{$_db_QUANTITY}" data-mval="{$_db_MPRICE}">
                                                <input type="checkbox" id="prdChk{$count}" name="prdChk" $checked>
                                                <label for="prdChk{$count}"></label>
                                            </li>
                                            <li class="td_img">
                                                <a href="{$url}" class="prdThumbnail"><img src="{$path_File}" alt="상품 이미지"></a>
                                            </li>
                                            <li class="td_prd_info">
                                                <a href="{$url}" class="td_name"><p class="prdName">{$_db_TITLE}</p></a>
                                                <div class="td_option"><div class="prd_option">{$_db_OPTION_NAME}</div></div>
                                                <div class="td_count">
                                                    <div class="count_wrap">
                                                        <input type="button" title="-" id="minusBtn{$_db_OPTION_SEQ}" onclick="countMinus('{$_db_OPTION_SEQ}', 'cart')">
                                                        <input type="text" value="{$_db_QUANTITY}" id="optionCount{$_db_OPTION_SEQ}" class="option_count" onchange="countInput('{$_db_OPTION_SEQ}', 'cart')">
                                                        <input type="button" title="+" id="plusBtn{$_db_OPTION_SEQ}" onclick="countPlus('{$_db_OPTION_SEQ}', 'cart')">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="td_price"><span class="prdPrice" id="option_price{$_db_OPTION_SEQ}">{$_db_OPTION_PRICE_TEXT}</span></li>
                                        </ul>
                                    </li>
                                LI;
                    }
                    

                    $count++;
                }

                $frist_del = gfn_getDELIVERY($_db_TOTAL_PRICE);

                $DELIVERY = "";

                if (PAGE == PAGE2) {
                    if ($frist_del > 0) {
                        $DELIVERY = number_format($frist_del). '원';
                    } else {
                        $DELIVERY = "무료";
                    }
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

?>