<?php
/**
 * 파일명 : search_result_code.php
 * 내용 : 검색 페이지
 * 최초작성날짜 : 2023/08/28
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/28    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');
    
    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $search_text = get_request_param('search_text', 'GET');

    if (empty($search_text)) {
        dieAndErrorMove("검색어를 입력해주세요.");
    }

    function getList_Serach() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );
  
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $PAGE = PAGE;

            $VAL = get_request_param('search_text', 'GET');
            $VAL = trim($VAL);

            if (empty($VAL)) {
                dieAndErrorMove("검색어를 입력해주세요");
            }

            $arrValue = array();
            $arrValue[':VAL'] = $VAL;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT CATEGORY3_SEQ
                      , TITLE
                      , (SELECT TITLE FROM CATEGORY1 A WHERE A.CATEGORY1_SEQ = M.CATEGORY1_SEQ) AS ARTIST_NAME
                      , ATTACH_FILE_ID
                   FROM CATEGORY3 M
                  WHERE (
                     TRIM(TITLE) LIKE CONCAT('%', :VAL, '%')
                     OR FIND_IN_SET(:VAL, REPLACE(SEARCH_TEXT, ' ', ''))
                     OR EXISTS ( SELECT 1
                                   FROM CATEGORY1
                                  WHERE TRIM(CATEGORY1.TITLE) LIKE CONCAT('%', :VAL, '%')
                                    AND CATEGORY1.CATEGORY1_SEQ = M.CATEGORY1_SEQ)
                     OR EXISTS (SELECT 1
                                  FROM CATEGORY2
                                 WHERE TRIM(CATEGORY2.TITLE) LIKE CONCAT('%', :VAL, '%')
                                   AND CATEGORY2.CATEGORY1_SEQ = M.CATEGORY1_SEQ
                                   AND CATEGORY2.CATEGORY2_SEQ = M.CATEGORY2_SEQ))
                    AND M.PAGE_TYPE = :PAGE_TYPE
                  ORDER BY reg_date DESC";

            $name_sql = "작가, 시리즈, 작품 검색";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 작품명
                    $_db_ARTIST_NAME = _check_var($data['ARTIST_NAME']); // 작가명
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 시퀀스

                    $_db_MAIN_ATTACH_FILE_ID = '';

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                        if (!empty($file_list)) {
                            foreach ($file_list as $data2) {
                                $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                            }
                        }
                    }

                    $URL = artFoldName . '/shop/detail.php?seq='. $_db_CATEGORY3_SEQ;

                    echo <<<LI
                                <li>
                                    <a href="{$URL}">
                                        <div class="img_box"><img src="{$_db_MAIN_ATTACH_FILE_ID}"></div>
                                        <div class="txt_box">
                                            <div class="art_title">{$_db_TITLE}</div>
                                            <div class="art_info">{$_db_ARTIST_NAME}</div>
                                        </div>
                                    </a>
                                </li>
                            LI;
                } //                                             <div class="more_btn">MORE VIEW</div>
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>