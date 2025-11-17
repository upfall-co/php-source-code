<?php
/**
 * 파일명 : gmenu_code.php
 * 내용 : 메뉴단 / 상단
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/07    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $login_chk = false;

    if (isset($_SESSION['MEMBER'])) {
        if (!empty($_SESSION['MEMBER'])) {
            $login_chk = true;
        }
    }

    /**
     * name :getList_Gmenu
     * comment : 작가 / 시리즈 트리 2계층 구조
     */
    function getList_Gmenu() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $PAGE = PAGE;
        $arrValue = array();
        $arrValue[':PAGE_TYPE'] = $PAGE;

        if (PAGE == PAGE1) {
            $SUB_SEQ = "'' AS CATEGORY2_SEQ";
        } else if (PAGE == PAGE2) {
            $SUB_SEQ = "(SELECT D.CATEGORY2_SEQ FROM CATEGORY2 D WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ AND D.MAIN_YN = 'Y' ORDER BY D.ORDER_NUMBER DESC LIMIT 1) AS CATEGORY2_SEQ";
        }
  
        $sql = "
             SELECT CATEGORY1_SEQ
                  , TITLE AS CATEGORY1_NAME
                  , '' AS CATEGORY2_SEQ
                  , '' AS TITLE
                  , ORDER_NUMBER AS MAIN_ORDER
                  , 9999 AS SUB_ORDER
                  , 'MAIN' AS TYPE_MODE
                  , reg_date
               FROM CATEGORY1 M
              WHERE 1
                AND MAIN_YN = 'Y'
                AND PAGE_TYPE = :PAGE_TYPE
              UNION ALL
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
                AND M.PAGE_TYPE = :PAGE_TYPE
              ORDER BY MAIN_ORDER DESC, reg_date DESC, CATEGORY1_NAME, SUB_ORDER DESC";
  
        $name_sql = "작가(카테고리1),시리즈(카테고리2) 트리 2계층 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);
     
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();
        $currentMain = null;

        if ($PAGE == PAGE1) {
            foreach ($list as $key => $data) {
                if ($data['TYPE_MODE'] === 'MAIN') {
                    // 1트리가 변경되는 지점에서 새로운 <ul> 태그를 오픈
                    if ($currentMain !== $data['CATEGORY1_SEQ']) {
                        if (!is_null($currentMain)) {
                            // 이전 1트리가 끝났으므로 </ul> 태그를 닫음
                            echo '</ul>';
                        }
    
                        // 새로운 1트리를 시작
                        echo '<ul>';
                        $currentMain = $data['CATEGORY1_SEQ'];
                    }
    
                    // 1트리에 해당하는 <li> 태그를 출력
                    echo '<li class="depth1"><a href="' . artFoldName . '/shop/artist.php?seq=' . $data['CATEGORY1_SEQ'] . '">' . $data['CATEGORY1_NAME'] . '</a></li>';
                } else if ($data['TYPE_MODE'] === 'SUB') {
                    // 2트리가 시작하는 <li class="depth2"> 태그를 출력
                    if ($key === 0 || $list[$key - 1]['TYPE_MODE'] !== 'SUB' || $list[$key - 1]['CATEGORY1_SEQ'] !== $data['CATEGORY1_SEQ']) {
                        echo '<li class="depth2"><ul>';
                    }
    
                    // 2트리에 해당하는 <li> 태그를 출력
                    echo '<li><a href="' . artFoldName . '/shop/series.php?seq=' . $data['CATEGORY1_SEQ'] . '#series' . $data['SUB_ORDER'] . '">' . $data['TITLE'] . '</a></li>';
    
                    // 다음 데이터가 2트리가 아니거나 다음 2트리가 다른 1트리에 속하는 경우에는 </ul> 태그를 닫아줌
                    if (!isset($list[$key + 1]) || ($list[$key + 1]['TYPE_MODE'] !== 'SUB' || $list[$key + 1]['CATEGORY1_SEQ'] !== $data['CATEGORY1_SEQ'])) {
                        echo '</ul></li>';
                    }
                }
            }
    
            // 마지막 1트리를 닫아줌
            if (!is_null($currentMain)) {
                echo '</ul>';
            }
        } else if ($PAGE == PAGE2) {
            foreach ($list as $key => $data) {
                if ($data['TYPE_MODE'] === 'MAIN') {
                    // 1트리가 변경되는 지점에서 새로운 <li> 태그를 오픈
                    if ($currentMain !== $data['CATEGORY1_SEQ']) {
                        if (!is_null($currentMain)) {
                            // 이전 1트리가 끝났으므로 </li> 태그를 닫음
                            echo '</li>';
                        }
                    }

                    // 새로운 1트리를 시작
                    echo '<li class="depth1" data-seq="' . $data['CATEGORY1_SEQ'] . '">';
                    // 1트리에 해당하는 링크 출력
                    echo '<div class="depth_open_btn"><a href="' . shopFoldName . '/product/list.php?cate1=' . $data['CATEGORY1_SEQ'] . '">' . $data['CATEGORY1_NAME'] . '</a><div class="mo_arrow"><img src="' . shopFoldName . '/img/sidenav_arrow.svg" alt="토글"></div></div>';
                    // 2트리 시작
                    $currentMain = $data['CATEGORY1_SEQ'];
                } else if ($data['TYPE_MODE'] === 'SUB') {
                    // 2트리가 시작하는 <li class="depth2"> 태그를 출력
                    if ($key === 0 || $list[$key - 1]['TYPE_MODE'] !== 'SUB' || $list[$key - 1]['CATEGORY1_SEQ'] !== $data['CATEGORY1_SEQ']) {
                        echo '<ul class="depth2">'; // 2트리 열기
                    }
                    // 2트리에 해당하는 <li> 태그를 출력
                    echo '<li><a href="' . shopFoldName . '/product/list.php?cate2=' . $data['CATEGORY2_SEQ'] . '">' . $data['TITLE'] . '</a></li>';
        
                    // 다음 데이터가 2트리가 아니거나 다음 2트리가 다른 1트리에 속하는 경우에는 </ul></li> 태그를 닫아줌
                    if (!isset($list[$key + 1]) || ($list[$key + 1]['TYPE_MODE'] !== 'SUB' || $list[$key + 1]['CATEGORY1_SEQ'] !== $data['CATEGORY1_SEQ'])) {
                        echo '</ul>'; // 2트리 닫기
                        echo '</li>'; // 1트리 닫기
                    }
                }
            }
            
            // 마지막 1트리를 닫아줌
            if (!is_null($currentMain)) {
                echo '</li>'; // depth1 닫기
            }
        }
    }

    function getCart_Count() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $PAGE = PAGE;

            $ID = "";
            $unique_id = "";

            $arrValue = array();
            $where = '';
            $where2 = '';

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
                }
            } else {
                $unique_id = session_id();

                $where .= " AND A.SESSION = :SESSION";
                $arrValue[':SESSION'] = $unique_id;
            }

            $table = 'PRODUCT_CART'; // 작품 테이블
            $table_OP = 'PRODUCT_OPTION_CART'; // 옵션 관리자 테이블

            $sql = "
                 SELECT IFNULL(COUNT(*), 0) AS CART_COUNT
                   FROM PRODUCT_SEQ_CART A, {$table} M, {$table_OP} D
                  WHERE A.PRODUCT_CART_SEQ = M.PRODUCT_CART_SEQ
                    AND A.PRODUCT_CART_SEQ = D.PRODUCT_CART_SEQ
                    AND M.CATEGORY3_SEQ = D.CATEGORY3_SEQ
                    AND M.PAGE_TYPE = '{$PAGE}'
                    {$where}
                  ORDER BY M.TITLE, D.ORDER_NUMBER DESC";
     
           $name_sql = "장바구니 수량";
           $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);
        
           if (!$clefResult->getResult()) {
               gfn_isValidation(800);
           }

           $data = $clefResult->getResultSet();

           $COUNT = $data['CART_COUNT'];

           echo <<<SPAN
                        <span id="cartCount">
                            {$COUNT}
                        </span>
                    SPAN;

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
 ?>