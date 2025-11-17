<?php
/**
 * 파일명 : notice_code.php
 * 내용 : 공지사항 리스트 
 * 최초작성날짜 : 2023/08/10
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/10    V1.0
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

    $page = get_request_param('page', 'GET');

    if (!is_numeric($page)) {
        $page = 1;
    }

    $limit = 10;
    $scale = 10;
    $total = 0;

    $arrParams = array(); // 페이징 파라미터 처리

    /**
     * name :getList_INFORM
     * comment : 공지사항 리스트
     */
    function getList_INFORM() {
        global $limit;
        global $offset;
        global $total;
        global $request_list;
        global $page;
        global $where;

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {

            $table = 'INFORM'; //테이블
            $PAGE = PAGE;
            $arrValue = array();
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                   {$where}";

            $name_sql = "공지사항 개수 확인";
            $clefResult = $mysqldb->count($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $total = $clefResult->getCount();

            if ($total <= $limit) {
                $page = 1;
            }

            $request_list .= "page=". $page;

            $offset = ($page - 1) * $limit;

            $sql = "
                 SELECT INFORM_SEQ
                      , TITLE
                      , DATE_FORMAT(reg_date, '%Y. %m. %d') AS reg_date_nm
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    {$where}
                  ORDER BY ORDER_NUMBER DESC, reg_date DESC
                  LIMIT {$offset}, {$limit}";

            $name_sql = "공지사항 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $no = $total - $offset;

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_INFORM_SEQ = _check_var($data['INFORM_SEQ']); // 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 작성일

                    $artFoldName = artFoldName;

                    echo <<<LI
                                <li class="tbody">
                                    <div class="td_num">{$no}</div>
                                    <div class="td_title">
                                        <a href="{$artFoldName}/help/notice_view.php?SEQ={$_db_INFORM_SEQ}">{$_db_TITLE}</a>
                                    </div>
                                    <div class="td_date">{$_db_reg_date_nm}</div>
                                </li>
                            LI;
                    $no--;
                }
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * name :getList_HOME_NOTICE
     * comment : 공지사항 리스트
     */
    function getList_HOME_NOTICE($TYPE_CD) {
        global $where;

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $table = 'INFORM'; //테이블
            $PAGE = PAGE;
            $arrValue = array();

            $arrValue[':PAGE_TYPE'] = $PAGE;
            $arrValue[':TYPE_CD'] = $TYPE_CD;

            $sql = "
                 SELECT INFORM_SEQ
                      , TITLE
                      , CONTENT_TEXT
                      , OPTION_TEXT
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    AND TYPE_CD = :TYPE_CD
                    {$where}
                  ORDER BY reg_date DESC";

            $name_sql = "공지사항 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_INFORM_SEQ = _check_var($data['INFORM_SEQ']); // 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
                    $_db_OPTION_TEXT = _check_var($data['OPTION_TEXT']); // 옵션

                    $CONTENT_TEXT = ""; // 내용 
                    $CONTENT_TEXT_html = ""; // 내용 HTML
                    $OPTION_TEXT = ""; // 옵션
                    $OPTION_TEXT_html = ""; // 옵션 HTML

                    if (!empty($_db_CONTENT_TEXT)) {
                        $CONTENT_TEXT = explode("\n", $_db_CONTENT_TEXT);

                        foreach ($CONTENT_TEXT as $row) {
                            $CONTENT_TEXT_html .= <<<LI
                                                        <li>{$row}</li>
                                                    LI;
                        }
                    }

                    if (!empty($_db_OPTION_TEXT)) {
                        $OPTION_TEXT = explode("\n", $_db_OPTION_TEXT);

                        foreach ($OPTION_TEXT as $row) {
                            $OPTION_TEXT_html .= <<<LI
                                                        <li>+ {$row}</li>
                                                    LI;
                        }
                    }

                    echo <<<LI
                                <div class="noti__s1_contents">
                                    <p class="title">{$_db_TITLE}</p>
                                    <ol>
                                        {$CONTENT_TEXT_html}
                                    </ol>
                                    <ul class="option">
                                        {$OPTION_TEXT_html}
                                    </ul>
                                </div>
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