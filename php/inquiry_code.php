<?php
/**
 * 파일명 : inquiry_code.php
 * 내용 : 1:1 문의 
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/08    V1.0
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

    $url = $_SERVER['REQUEST_URI'];

    // URL 파싱
    $parsedUrl = parse_url($url);

    // 경로를 슬래시('/')로 분할
    $pathSegments = explode('/', $parsedUrl['path']);

    $index = array_search('mypage', $pathSegments);

    if ($index === 2) {
        if (isset($_SESSION['MEMBER'])) {
            if (!empty($_SESSION['MEMBER'])) {
                $ID = $_SESSION['MEMBER']['ID'];
                $where .= " AND ID = '{$ID}'";
            }
        }
    }

    $limit = 10;
    $scale = 10;
    $total = 0;

    $arrParams = array(); // 페이징 파라미터 처리

    /**
     * name :getList_FAQ
     * comment : FAQ
     */
    function getList_FAQ() {
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
            $page_type = PAGE;

            //페이지타입
            if (!empty($page_type)) {
                $where .= " AND PAGE_TYPE = :page_type";
                $arrValue[':page_type'] = $page_type;
            }

            $table = 'INQUIRY'; //테이블

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE 1
                   {$where}";

            $name_sql = "문의 개수 확인";
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
                 SELECT INQUIRY_SEQ
                      , QUESTION_CD
                      , ZCM_COM_NM('AD008',QUESTION_CD) AS QUESTION_CD_NM
                      , TITLE
                      , reg_user
                      , DATE_FORMAT(reg_date, '%Y. %m. %d') AS reg_date_nm
                      , IF(PASSWORD IS NOT NULL AND PASSWORD != '', 'Y', 'N') AS PASSWORD_YN
                   FROM {$table}
                  WHERE 1
                    {$where}
                  ORDER BY reg_date DESC
                  LIMIT {$offset}, {$limit}";

            $name_sql = "문의 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $no = $total - $offset;

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_INQUIRY_SEQ = _check_var($data['INQUIRY_SEQ']); // 시퀀스
                    $_db_QUESTION_CD = _check_var($data['QUESTION_CD']); // 답변상태 코드
                    $_db_QUESTION_CD_NM = _check_var($data['QUESTION_CD_NM']); // 답변상태명칭
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_reg_user = _check_var($data['reg_user']); // 작성자
                    $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 작성일
                    $_db_PASSWORD_YN = _check_var($data['PASSWORD_YN']); // 비밀번호 여부

                    $QUESTION_CD_html = "";
                    $PASSWORD_html = "";

                    if ($_db_QUESTION_CD == "01")  {
                        $QUESTION_CD_html = "yet";
                    } else if ($_db_QUESTION_CD == "02") {

                    } else if ($_db_QUESTION_CD == "03") {
                        $QUESTION_CD_html = "end";
                    }

                    if ($_db_PASSWORD_YN == "Y") {
                        $PASSWORD_html = "secret";
                    }

                    if (!empty($_db_reg_user)) {
                        $firstChar = mb_substr($_db_reg_user, 0, 1, 'UTF-8');
                        $lastChar = mb_substr($_db_reg_user, -1, 1, 'UTF-8');
                        $maskedPart = str_repeat("*", mb_strlen($_db_reg_user, 'UTF-8') - 2);
                        
                        $maskedName = $firstChar . $maskedPart . $lastChar;
                    }

                    $artFoldName = artFoldName;

                    if (PAGE == PAGE1) {
                        echo <<<LI
                                    <li class="tbody">
                                        <div class="td_num">{$no}</div>
                                        <div class="td_state {$QUESTION_CD_html}">
                                            <div class="state_yet_badge">답변대기</div>
                                            <div class="state_end_badge">답변완료</div>
                                        </div>
                                        <div class="td_title {$PASSWORD_html}" onclick="ufn_view_Chk(this,'{$_db_INQUIRY_SEQ}')">
                                            <div class="secret_icon">
                                                <img src="{$artFoldName}/img/help/secret_icon.png" alt="비밀글">
                                            </div>
                                            <a href="javascript:void(0);">{$_db_TITLE}</a>
                                        </div>
                                        <div class="td_writer">{$maskedName}</div>
                                        <div class="td_date">{$_db_reg_date_nm}</div>
                                    </li>
                                LI;
                    } else if (PAGE == PAGE2) {
                        echo <<<LI
                                    <li class="tbody">
                                        <div class="td_date">{$_db_reg_date_nm}</div>
                                        <div class="td_type">{$_db_QUESTION_CD_NM}</div>
                                        <div class="td_title " onclick="inquiryPop('$_db_INQUIRY_SEQ');">
                                            <a href="javascript:void(0);">{$_db_TITLE}</a>
                                        </div>
                                        <div class="td_state {$QUESTION_CD_html}">
                                            <div class="state_yet_badge">답변대기</div>
                                            <div class="state_end_badge">답변완료</div>
                                        </div>
                                    </li>
                                LI;
                    }

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
     * name :getList_FAQ
     * comment : FAQ
     */
    function getMyList_FAQ() {
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
            $page_type = PAGE;

            //페이지타입
            if (!empty($page_type)) {
                $where .= " AND PAGE_TYPE = :page_type";
                $arrValue[':page_type'] = $page_type;
            }

            $table = 'INQUIRY'; //테이블

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE 1
                   {$where}";

            $name_sql = "문의 개수 확인";
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
                 SELECT INQUIRY_SEQ
                      , QUESTION_CD
                      , ZCM_COM_NM('AD008',QUESTION_CD) AS QUESTION_CD_NM
                      , ZCM_COM_NM('COL002',TYPE_CD) AS TYPE_CD_NM
                      , TITLE
                      , reg_user
                      , DATE_FORMAT(reg_date, '%Y. %m. %d') AS reg_date_nm
                      , IF(PASSWORD IS NOT NULL AND PASSWORD != '', 'Y', 'N') AS PASSWORD_YN
                   FROM {$table}
                  WHERE 1
                    {$where}
                  ORDER BY reg_date DESC
                  LIMIT {$offset}, {$limit}";

            $name_sql = "문의 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $no = $total - $offset;

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_INQUIRY_SEQ = _check_var($data['INQUIRY_SEQ']); // 시퀀스
                    $_db_QUESTION_CD = _check_var($data['QUESTION_CD']); // 답변상태 코드
                    $_db_QUESTION_CD_NM = _check_var($data['QUESTION_CD_NM']); // 답변상태명칭
                    $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 문의분류
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_reg_user = _check_var($data['reg_user']); // 작성자
                    $_db_reg_date_nm = _check_var($data['reg_date_nm']); // 작성일
                    $_db_PASSWORD_YN = _check_var($data['PASSWORD_YN']); // 비밀번호 여부

                    $QUESTION_CD_html = "";
                    $PASSWORD_html = "";

                    if ($_db_QUESTION_CD == "01")  {
                        $QUESTION_CD_html = "yet";
                    } else if ($_db_QUESTION_CD == "02") {

                    } else if ($_db_QUESTION_CD == "03") {
                        $QUESTION_CD_html = "end";
                    }

                    /*if ($_db_PASSWORD_YN == "Y") {
                        $PASSWORD_html = "secret";
                    }*/

                    if (!empty($_db_reg_user)) {
                        $firstChar = mb_substr($_db_reg_user, 0, 1, 'UTF-8');
                        $lastChar = mb_substr($_db_reg_user, -1, 1, 'UTF-8');
                        $maskedPart = str_repeat("*", mb_strlen($_db_reg_user, 'UTF-8') - 2);
                        
                        $maskedName = $firstChar . $maskedPart . $lastChar;
                    }

                    $artFoldName = artFoldName;
                    if (PAGE == PAGE1) {
                        echo <<<LI
                                    <li class="tbody">
                                        <div class="td_num">{$no}</div>
                                        <div class="td_state {$QUESTION_CD_html}">
                                            <div class="state_yet_badge">답변대기</div>
                                            <div class="state_end_badge">답변완료</div>
                                        </div>
                                        <div class="td_title {$PASSWORD_html}" onclick="ufn_view_Chk(this,'{$_db_INQUIRY_SEQ}')">
                                            <div class="secret_icon">
                                                <img src="{$artFoldName}/img/help/secret_icon.png" alt="비밀글">
                                            </div>
                                            <a href="javascript:void(0);">{$_db_TITLE}</a>
                                        </div>
                                        <div class="td_writer">{$maskedName}</div>
                                        <div class="td_date">{$_db_reg_date_nm}</div>
                                    </li>
                                LI;
                    } else if (PAGE == PAGE2) {
                        echo <<<LI
                                    <li class="tbody">
                                        <div class="td_date">{$_db_reg_date_nm}</div>
                                        <div class="td_type">{$_db_TYPE_CD_NM}</div>
                                        <div class="td_title " onclick="inquiryPop('$_db_INQUIRY_SEQ');">
                                            <a href="javascript:void(0);">{$_db_TITLE}</a>
                                        </div>
                                        <div class="td_state {$QUESTION_CD_html}">
                                            <div class="state_yet_badge">답변대기</div>
                                            <div class="state_end_badge">답변완료</div>
                                        </div>
                                    </li>
                                LI;
                    }

                    $no--;
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>