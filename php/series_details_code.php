<?php
/**
 * 파일명 : writer_details_code.php
 * 내용 : 작가 관리
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
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
        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $mode = get_request_param('mode', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $sub_type = get_request_param('sub_type', 'GET');
        $CATEGORY2_SEQ = get_request_param('seq', 'GET');
        $M_PROGRAM_CD = get_request_param('PROGRAM_CD', 'GET');
        $M_CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET');
        $M_MAIN_YN = get_request_param('MAIN_YN', 'GET');

        $_db_PROGRAM_CD = '';
        $_db_CATEGORY2_SEQ = '';
        $_db_CATEGORY1_SEQ = '';
        $_db_TITLE = '';
        $_db_SUB_TITLE = '';
        $_db_ORDER_NUMBER = 0;
        $_db_ATTACH_FILE_ID = '';
        $_db_CONTENT_TEXT = '';

        $disabled = '';
        $_db_MAIN_ATTACH_FILE_ID = '';
        $_db_MAIN_ATTACH2_FILE_ID = '';
        $_db_MAIN_ATTACH3_FILE_ID = '';

        $checked = 'checked'; // 노출여부
        $table = 'CATEGORY2'; // 관리자 테이블
        $where = '';

        if ($page_type == PAGE1) {
            $category1_name = "작가";
            $category1_val = "작가명";
            $title_name = "시리즈"; // Copy, CSV, Excel, Print 제목
            $title_val = "시리즈명";
        } else if ($page_type == PAGE2) {
            $category1_name = "카테고리";
            $category1_val = "카테고리명";
            $title_name = "분류"; // Copy, CSV, Excel, Print 제목
            $title_val = "분류명";
        } else if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE1) {
                $category1_name = "업종";
                $category1_val = "업종명";
                $title_name = "세부 업종"; // Copy, CSV, Excel, Print 제목
                $title_val = "세부 업종명";
                $where .= " AND CATEGORY1_SEQ LIKE 'RECRU%'";
            } else if ($sub_type == SUB_PAGE2) {
                $category1_name = "카테고리";
                $category1_val = "카테고리명";
                $title_name = "분류"; // Copy, CSV, Excel, Print 제목
                $title_val = "분류명";
                $where .= " AND CATEGORY1_SEQ LIKE 'PROGRAM%'";
            }
        }

        if ($mode == 'MOD') {
            $disabled = 'disabled';

            $sql = "
                 SELECT PROGRAM_CD
                      , CATEGORY2_SEQ
                      , CATEGORY1_SEQ
                      , TITLE
                      , SUB_TITLE
                      , MAIN_YN
                      , CONTENT_TEXT
                      , ORDER_NUMBER
                      , ATTACH_FILE_ID
                   FROM {$table}
                  WHERE CATEGORY2_SEQ = :CATEGORY2_SEQ
                    AND PAGE_TYPE = '{$page_type}'
                    AND SUB_TYPE = '{$sub_type}'";

            $name_sql = "카테고리2 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':CATEGORY2_SEQ' => $CATEGORY2_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_PROGRAM_CD = _check_var($data['PROGRAM_CD']); // 중분류 구분 값
                $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 시리즈 시퀀스
                $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 작가 시퀀스
                $_db_TITLE = _check_var($data['TITLE']); // 시리즈명
                $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 추가명
                $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
                $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 호버 이미지

                if (!empty($_db_CATEGORY1_SEQ)) {
                    $type_gb = $_db_CATEGORY1_SEQ;
                }

                if ($_db_MAIN_YN == "N") {
                    $checked = '';
                }
                if ($page_type == PAGE1 || $sub_type == SUB_PAGE2) {
                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 4);

                        foreach ($file_list as $data2) {
                            $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }

                        $file_list2 = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 2);

                        foreach ($file_list2 as $data2) {
                            $_db_MAIN_ATTACH2_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }

                        $file_list3 = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 1);

                        foreach ($file_list3 as $data2) {
                            $_db_MAIN_ATTACH3_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }
                    }
                }
            }
        }

        $sql = "
            SELECT TITLE
                 , CATEGORY1_SEQ
                 , PROGRAM_CD
              FROM CATEGORY1
             WHERE PAGE_TYPE = '{$page_type}'
               AND MAIN_YN = 'Y'
               {$where}
             ORDER BY ORDER_NUMBER DESC, TITLE";

        $name_sql = "카테고리1명 리스트";
        $clefResult = $mysqldb->select($sql, null, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setARTISTComboList($combo_list);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'sub_type' => $sub_type
            , 'PROGRAM_CD' => $M_PROGRAM_CD
            , 'CATEGORY1_SEQ' => $M_CATEGORY1_SEQ
            , 'TITLE' => $M_TITLE
            , 'MAIN_YN' => $M_MAIN_YN
        );

        $query_string = http_build_query($arrParams);

        if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE1) {
                $back_url = "recruit2_main.php?{$query_string}";
            } else if ($sub_type == SUB_PAGE2) {
                $back_url = "program_main.php?{$query_string}";
            }
        } else {
            $back_url = "series_main.php?{$query_string}";
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

     /**
     * name :setARTISTComboList
     * comment : 콤보박스에 사용하는 값을 전역변수에 저장
     */
    function setARTISTComboList($data) {
        global $combo_list_arry;
        $combo_list_arry = $data;
    }

    /**
     * name :getARTISTComboList
     * comment : 콤보박스에 사용하는 값을 콤보박스 값으로 출력
     */
    function getARTISTComboList() {
        global $combo_list_arry;
        global $type_gb;

        $COM_CD = '';
        $COM_CD_NM = '';
        $selected = '';

        foreach($combo_list_arry as $array_val) {
            foreach($array_val as $key => $val) {
                if ($key == 'CATEGORY1_SEQ') {
                    $COM_CD = $val;
                } else if ($key == 'TITLE') {
                    $COM_CD_NM = $val;
                } else if ($key == 'PROGRAM_CD') {
                    $TH1_THEM_CD = $val;
                }
            }

            if (!empty($type_gb)) {
                $selected = ($type_gb == $COM_CD) ? 'selected="selected"' : '';
            }

            echo <<<OPTION
                        <option value="{$COM_CD}" data-code1="{$TH1_THEM_CD}" $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }
?>


