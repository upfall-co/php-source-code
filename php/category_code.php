<?php
/**
 * 파일명 : category_code.php
 * 내용 : CATEGORY 리스트 
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
    global $search_text;
    global $arrValue;

    $page = get_request_param('page', 'GET');
    $CATE = get_request_param('cate', 'GET');
    $CATE2 = get_request_param('cate2', 'GET');
    $search_text = get_request_param('search_text', 'GET');
    $PROGRAM_CD = get_request_param('PROGRAM_CD', 'GET');

    if (!is_numeric($page)) {
        $page = 1;
    }

    $limit = 9;
    $scale = 10;
    $total = 0;

    if (!empty($search_text)) {
        $where .= " AND TRIM(TITLE) LIKE CONCAT(:search_text)";
        $arrValue[':search_text'] = "%{$search_text}%";
    } else {
        if (!empty($CATE)) { // 카테고리 구분
            $where .= " AND CATEGORY1_SEQ = :CATE";
            $arrValue[':CATE'] = $CATE;
        }

        if (!empty($CATE2)) { // 분류 구분
            $where .= " AND CATEGORY2_SEQ = :CATE2";
            $arrValue[':CATE2'] = $CATE2;
        }

        if (!empty($PROGRAM_CD)) { // 분류 구분
            $where .= " AND PROGRAM_CD = :PROGRAM_CD";
            $arrValue[':PROGRAM_CD'] = $PROGRAM_CD;
        }
    }

    $arrParams = array( // 페이징 파라미터 처리
          'cate' => $CATE
        , 'cate2' => $CATE2
        , 'search_text' => $search_text
        , 'PROGRAM_CD' => $PROGRAM_CD
    );

    /**
     * name :getList_CATEGORY
     * comment : 카테고리 리스트
     */
    function getList_CATEGORY() {
        global $limit;
        global $offset;
        global $total;
        global $request_list;
        global $page;
        global $where;
        global $search_text;
        global $arrValue;

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $table = 'CATEGORY3'; //테이블
            $PAGE = PAGE;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT *
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                   {$where}";

            $name_sql = "카테고리 개수 확인";
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
                 SELECT CATEGORY3_SEQ
                      , CATEGORY1_SEQ
                      , CATEGORY2_SEQ
                      , PROGRAM_CD
                      , TITLE
                      , SUB_TITLE
                      , DATE_FORMAT(SDATE, '%Y.%m.%d') AS SDATE
                      , DATE_FORMAT(EDATE, '%Y.%m.%d') AS EDATE
                      , ATTACH_FILE_ID
                      ,CASE WHEN CURDATE() BETWEEN DATE(SDATE) AND DATE(EDATE) THEN 'O'
                            ELSE 'X'
                        END AS DateStatus
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    {$where}
                  ORDER BY ORDER_NUMBER DESC, reg_date DESC
                  LIMIT {$offset}, {$limit}";

            $name_sql = "카테고리 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            $no = $total - $offset;

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 시퀀스
                    $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 카텐고리
                    $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 분류
                    $_db_PROGRAM_CD = _check_var($data['PROGRAM_CD']); // 중분류 코드
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 서브제목
                    $_db_SDATE = _check_var($data['SDATE']); // 시작일
                    $_db_EDATE = _check_var($data['EDATE']); // 종료일
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                    $_db_DateStatus = _check_var($data['DateStatus']); // on / off 확인

                    $homeFoldName = homeFoldName;
                    $url = $homeFoldName.'/include/board_view.php?SEQ='.$_db_CATEGORY3_SEQ.'&PROGRAM_CD='.$_db_PROGRAM_CD.'&page='.$page.'&search_text='.$search_text;
                    $DATE = "";
                    $path_File = "";
                    $data_html = "";

                    if (!empty($_db_SDATE) && !empty($_db_EDATE)) {
                        $DATE = $_db_SDATE."—".$_db_EDATE;
                    }

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);
            
                        if (!empty($file_list)) {
                            foreach ($file_list as $list2) {
                                $_db_attach_file_temp_name = _check_var($list2['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_real_name = _check_var($list2['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                                $_db_attach_file_path = _check_var($list2['ATTACH_FILE_PATH']); // 경로 
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    if (!empty($search_text && $_db_CATEGORY1_SEQ == "EXHIBITION")) {
                        $data_html = "data-type='EX'";
                    }

                    $date_state = "closed";

                    if ($_db_DateStatus == "O") {
                        $date_state = "now on";
                    }

                    if (empty($search_text) && $_db_CATEGORY1_SEQ == "EXHIBITION") {
                        echo <<<LI
                                    <li onmouseenter="viewInfoEnter(this, '.info_container')" onmouseleave="viewInfoLeave(this, '.info_container')">
                                        <a href="{$url}" class="thumb_container">
                                            <figure>
                                                <!-- ↓ 기획전 섬네일 -->
                                                <img src="{$path_File}" alt="">
                                            </figure>
                                            <div class="info_container">
                                                <div class="title_wrap">
                                                    <!-- ↓ 기획전 타이틀 -->
                                                    <p class="title">{$_db_TITLE}</p>
                                                    <!-- ↓ 기획전 서브타이틀 -->
                                                    <p class="subtitle">{$_db_SUB_TITLE}</p>
                                                </div>
                                                <div class="date_wrap">
                                                    <!-- ↓ 기획전 날짜 -->
                                                    <p class="date">{$DATE}</p>
                                                </div>
                                                <div class="btn_wrap">
                                                    <!-- ↓ 기획전 링크-->
                                                    <!-- ※ 기획전 기간중일 때 data-state의 값을 now on로 할당 요청-->
                                                    <!-- ※ 기획전 기간중이 아닐 때 data-state의 값을 closed로 할당 요청-->
                                                    <p data-state="{$date_state}"></p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                LI;
                    } else {
                        echo <<<LI
                                    <li {$data_html}>
                                        <a href="{$url}" class="thumb_container">
                                        <figure>
                                            <img src="{$path_File}" alt="">
                                        </figure>
                                        <div class="title_wrap">
                                            <p>{$_db_TITLE}</p>
                                        </div>
                                        </a>
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