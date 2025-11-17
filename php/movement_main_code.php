<?php
/**
 * 파일명 : category_details_code.php
 * 내용 : home 카테고리 상세 페이지 코드
 * 최초작성날짜 : 2023/11/29
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/11/29     V1.0
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

        $CATEGORY3_SEQ = '';
        $_db_CATEGORY2_SEQ = 'SHOP';
        $_db_CATEGORY1_SEQ = 'SHOP';
        $_db_TITLE = '';
        $_db_ATTACH_FILE_ID = '';

        $_db_MAIN_ATTACH_FILE_ID = '';

        $table = 'CATEGORY3'; // 관리자 테이블

        $sql = "
            SELECT CATEGORY3_SEQ
              FROM {$table}
             WHERE CATEGORY1_SEQ = 'SHOP'
               AND CATEGORY2_SEQ = 'SHOP'
               AND PAGE_TYPE = '{$page_type}'";

        $name_sql = "카테고리3-SHOP 갯수"; 
        $clefResult = $mysqldb->get($sql, null, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            $mode = "INS";
        } else {
            $mode = "MOD";
            $CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']);
        }

        if ($mode == 'MOD') {
            $sql = "
                 SELECT CATEGORY3_SEQ
                      , CATEGORY1_SEQ
                      , CATEGORY2_SEQ
                      , TITLE
                      , ATTACH_FILE_ID
                   FROM {$table}
                  WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "카테고리3 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 분류 시퀀스
                $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 카테고리 시퀀스
                $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 분류
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 썸네일 이미지

                if ($page_type == PAGE3) {
                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 1);

                        foreach ($file_list as $data2) {
                            $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }
                    }
                }
            }
        }
  } catch (Exception $e) {
      $arrRtn['code'] = $e->getCode();
      $arrRtn['msg'] = $e->getMessage();

      echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
  }
