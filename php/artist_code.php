<?php
/**
 * 파일명 : artist_code.php
 * 내용 : 작가 시리즈 리스트 페이지
 * 최초작성날짜 : 2023/08/11
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/11    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

     /**
     * name :getList_TitleSeries
     * comment : 시리즈 내역 및 타이틀 작품 이미지
     */
    function getList_TitleSeries() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $PAGE = PAGE;

            $CATEGORY1_SEQ = get_request_param('seq', 'GET');
            $table = 'CATEGORY2'; // 시리즈

            if (empty($CATEGORY1_SEQ)) {
                dieAndErrorMove('잘못된 접근입니다.');
            }

            $sql = "
                 SELECT M.TITLE
                      , (SELECT CONCAT(ATTACH_FILE_PATH, '/', ATTACH_FILE_TEMP_NAME)
                           FROM ZCMFILEA
                          WHERE M.ATTACH_FILE_ID = ATTACH_FILE_ID
                            AND ATTACH_GROUP = '4'
                          ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT LIMIT 1) AS MAIN_ATTACH_FILE_ID
                      , M.ORDER_NUMBER
                   FROM {$table} M
                  WHERE M.MAIN_YN = 'Y'
                    AND M.CATEGORY1_SEQ = :CATEGORY1_SEQ
                    AND M.PAGE_TYPE = '{$PAGE}'
                  ORDER BY M.ORDER_NUMBER DESC";

            $name_sql = "시리즈 및 작품 리스트";
            $clefResult = $mysqldb->select($sql, [':CATEGORY1_SEQ' => $CATEGORY1_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (empty($list)) {
                dieAndErrorMove('잘못된 접근입니다.');
            }

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_TITLE = _check_var($data['TITLE']); // 시리즈명
                    $_db_MAIN_ATTACH_FILE_ID = _check_var($data['MAIN_ATTACH_FILE_ID']); // 파일경로
                    $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 시리즈 번호

                    $artFoldName = artFoldName;

                    echo <<<DIV
                                <div class="wst-img-wrapper">
                                    <a class="wst-img-container" href='{$artFoldName}/shop/series.php?seq={$CATEGORY1_SEQ}#series{$_db_ORDER_NUMBER}'>
                                        <div>{$_db_TITLE}</div>
                                        <div class="wst-overlay-text"><img src="{$_db_MAIN_ATTACH_FILE_ID}"></div>
                                    </a>
                                </div>
                            DIV;
                }
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }

?>