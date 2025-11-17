<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    try {
        $cate1 = get_request_param('cate1', 'GET');
        $cate2 = get_request_param('cate2', 'GET');

        $_db_CATEGORY1_SEQ = "";
        $_db_CATEGORY1_NAME = "";
        $_db_CATEGORY2_SEQ = "";
        $_db_CATEGORY2_TITLE = "";
        $_db_CATEGORY3_SEQ = "";
        $_db_CATEGORY3_TITLE = "";

        if ($cate1 == "NEW" || $cate1 == "SALE") {
            $_db_CATEGORY1_SEQ = $cate1;

            if ($cate1 == "SALE") {
                $_db_CATEGORY1_NAME = "Sale";
            } else {
                $_db_CATEGORY1_NAME = "Piknic Edition";
            }

            $_db_CATEGORY2_SEQ = "";
            $_db_CATEGORY2_TITLE = "";

            $_db_CATEGORY3_TITLE = $_db_CATEGORY1_NAME;
        } else {
            $arrValue = array();
            $where = "";

            if (!empty($cate1)) {
                $arrValue[':CATEGORY1_SEQ'] = $cate1;
                $where = " AND M.CATEGORY1_SEQ = :CATEGORY1_SEQ";
            }

            if (!empty($cate2)) {
                $arrValue[':CATEGORY2_SEQ'] = $cate2;
                $where = " AND D.CATEGORY2_SEQ = :CATEGORY2_SEQ";
            }
            
            if (!empty($cate1) && !empty($cate2)) {
                dieAndErrorMove('잘못된 접근입니다.');
            }

            if (empty($cate1) && empty($cate2)) {
                dieAndErrorMove('잘못된 접근입니다.');
            }

            $table = 'CATEGORY2'; // 작품 테이블

            $sql = "
                 SELECT M.CATEGORY1_SEQ
                      , M.TITLE AS CATEGORY1_NAME
                      , (SELECT A.CATEGORY2_SEQ
                           FROM CATEGORY1 D, CATEGORY2 A
                          WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                            AND M.CATEGORY1_SEQ = A.CATEGORY1_SEQ
                            AND M.MAIN_YN = 'Y'
                            AND D.MAIN_YN = 'Y'
                          ORDER BY A.ORDER_NUMBER DESC LIMIT 1) AS CATEGORY2_SEQ
                      , D.TITLE AS CATEGORY2_TITLE
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , D.ORDER_NUMBER AS SUB_ORDER
                      , M.reg_date
                   FROM CATEGORY1 M, CATEGORY2 D
                 WHERE M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                   AND M.MAIN_YN = 'Y'
                   AND D.MAIN_YN = 'Y'
                   {$where}
                 ORDER BY SUB_ORDER DESC";

            $name_sql = "카테고리 리스트";
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

            if (!empty($cate1)) {
                $_db_CATEGORY2_SEQ = "";
                $_db_CATEGORY2_TITLE = "";
                $_db_CATEGORY3_TITLE = $_db_CATEGORY1_NAME;
            }

            if (!empty($cate2)) {
                $_db_CATEGORY3_TITLE = _check_var($data['CATEGORY2_TITLE']); // 카테고리2명
            }
        }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>