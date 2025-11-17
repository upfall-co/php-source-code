<?php
/**
 * 파일명 : recruit_code.php
 * 내용 : RECRUIT 리스트 
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

    global $request_list;
    global $limit;
    global $offset;
    global $scale;
    global $total;
    global $page;
    global $where;
    global $arrValue;

    $page = get_request_param('page', 'GET');
    $CATE = get_request_param('cate', 'GET');
    $CATE2 = get_request_param('cate2', 'GET');

    if (!is_numeric($page)) {
        $page = 1;
    }

    $limit = 9;
    $scale = 10;
    $total = 0;

    if (!empty($CATE)) { // 카테고리 구분
        $where .= " AND CATEGORY1_SEQ = :CATE";
        $arrValue[':CATE'] = $CATE;
    }

    if (!empty($CATE2)) { // 분류 구분
        $where .= " AND CATEGORY2_SEQ = :CATE2";
        $arrValue[':CATE2'] = $CATE2;
    }

    $arrParams = array( // 페이징 파라미터 처리
          'cate' => $CATE
        , 'cate2' => $CATE2
    );

    /**
     * name :getList_RECRUIT
     * comment : RECRUIT 리스트
     */
    function getList_RECRUIT() {
        global $where;
        global $arrValue;

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $table = 'CATEGORY1'; //테이블
            $PAGE = PAGE;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT CATEGORY1_SEQ
                      , TITLE
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    AND CATEGORY1_SEQ LIKE 'RECRU%'
                    {$where}
                  ORDER BY ORDER_NUMBER DESC, reg_date DESC";

            $name_sql = "RECRUIT 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();



            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 제목

                        echo <<<LI
                                    <li data-seq="{$_db_CATEGORY1_SEQ}">{$_db_TITLE}</li>
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
     * name :getList_MO_RECRUIT
     * comment : RECRUIT 리스트
     */
    function getList_MO_RECRUIT() {
        global $where;
        $arrValue = array();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $table = 'CATEGORY1'; //테이블
            $PAGE = PAGE;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT CATEGORY1_SEQ
                      , TITLE
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    AND CATEGORY1_SEQ LIKE 'RECRU%'
                    {$where}
                  ORDER BY ORDER_NUMBER DESC, reg_date DESC";

            $name_sql = "RECRUIT 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();



            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 제목

                        echo <<<LI
                                    <option value="{$_db_CATEGORY1_SEQ}">{$_db_TITLE}</option>
                                LI;
                }
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>