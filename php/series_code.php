<?php
/**
 * 파일명 : series_code.php
 * 내용 : 시리즈 리스트 페이지
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

    function getList_ArtistSeries() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );
  
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $PAGE = PAGE;

            $CATEGORY1_SEQ = get_request_param('seq', 'GET');

            $arrValue = array();
            $arrValue[':CATEGORY1_SEQ'] = $CATEGORY1_SEQ;
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT CATEGORY1_SEQ
                      , CATEGORY2_SEQ
                      , TITLE AS MAIN_TITLE
                      , '' AS CATEGORY3_SEQ
                      , '' AS SUB_TITLE
                      , '' AS CATEGORY1_NAME
                      , ORDER_NUMBER AS MAIN_ORDER
                      , 9999 AS SUB_ORDER
                      , 'MAIN' AS TYPE_MODE
                      , '' AS ATTACH_FILE_ID
                      , reg_date
                   FROM CATEGORY2
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND CATEGORY1_SEQ = :CATEGORY1_SEQ
                    AND PAGE_TYPE = :PAGE_TYPE
                  UNION ALL
                 SELECT M.CATEGORY1_SEQ
                      , M.CATEGORY2_SEQ
                      , M.TITLE AS MAIN_TITLE
                      , D.CATEGORY3_SEQ
                      , D.TITLE AS SUB_TITLE
                      , (SELECT TITLE FROM CATEGORY1 A WHERE A.CATEGORY1_SEQ = M.CATEGORY1_SEQ) AS CATEGORY1_NAME
                      , M.ORDER_NUMBER AS MAIN_ORDER
                      , D.ORDER_NUMBER AS SUB_ORDER
                      , 'SUB' AS TYPE_MODE
                      , D.ATTACH_FILE_ID
                      , M.reg_date
                   FROM CATEGORY2 M, CATEGORY3 D
                  WHERE 1
                    AND M.CATEGORY1_SEQ = D.CATEGORY1_SEQ
                    AND M.CATEGORY2_SEQ = D.CATEGORY2_SEQ
                    AND M.MAIN_YN = 'Y'
                    AND D.MAIN_YN = 'Y'
                    AND M.CATEGORY1_SEQ = :CATEGORY1_SEQ
                    AND M.PAGE_TYPE = :PAGE_TYPE
                  ORDER BY MAIN_ORDER DESC, reg_date DESC, MAIN_TITLE, SUB_ORDER DESC";
  
            $name_sql = "시리즈,작품 트리 2계층 리스트";
            $clefResult = $mysqldb->select($sql,  $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();
            $currentMain = null;

            foreach ($list as $key => $data) {
                if ($data['TYPE_MODE'] === 'MAIN') {
                    if (!is_null($currentMain)) {
                        echo '    </ul>';
                        echo '</section>';
                    }
                    
                    echo '<section id="series' . $data['MAIN_ORDER'] . '">';
                    echo '  <div class="section_title">' . $data['MAIN_TITLE'] . '</div>';
                    echo '      <ul>';
                    
                    $currentMain = $data['CATEGORY2_SEQ'];
                } else if ($data['TYPE_MODE'] === 'SUB') {
                    $_db_MAIN_ATTACH_FILE_ID = '';

                    if (!empty($data['ATTACH_FILE_ID'])) {
                        $file_list = gfn_file_upload("S", '', $data['ATTACH_FILE_ID'], 1);

                        if (!empty($file_list)) {
                            foreach ($file_list as $data2) {
                                $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                            }
                        }
                    }

                    echo '<li>';
                    echo '    <a href="' . artFoldName . '/shop/detail.php?seq=' . $data['CATEGORY3_SEQ'] . '">';
                    echo '        <div class="img_box"><img src="' . $_db_MAIN_ATTACH_FILE_ID . '"></div>';
                    echo '            <div class="txt_box">';
                    echo '            <div class="art_title">' . $data['SUB_TITLE'] . '</div>';
                    echo '            <div class="art_info">' . $data['CATEGORY1_NAME'] . '</div>';
                    //echo '            <div class="more_btn">MORE VIEW</div>';
                    echo '        </div>';
                    echo '    </a>';
                    echo '</li>';
                }
            }

            if (!is_null($currentMain)) {
                echo '    </ul>';
                echo '</section>';
            }


        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>