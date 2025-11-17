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
        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $mode = get_request_param('mode', 'GET');
        $CATEGORY3_SEQ = get_request_param('seq', 'GET');
        $M_CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET');
        $M_CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET');
        $M_TITLE_YN = get_request_param('TITLE_YN', 'GET');
        $M_MAIN_YN = get_request_param('MAIN_YN', 'GET');
        $M_start_date = get_request_param('start_date', 'GET');
        $M_end_date = get_request_param('end_date', 'GET');

        $_db_CATEGORY2_SEQ = '';
        $_db_CATEGORY1_SEQ = $M_CATEGORY1_SEQ;
        $_db_TITLE = '';
        $_db_SUB_TITLE = '';
        $_db_LINK_URL = '';
        $_db_CONTENT_TITLE = '';
        $_db_CONTENT_TEXT = '';
        $_db_RELATED_VALUE = '';
        $_db_ORDER_NUMBER = 0;
        $_db_ATTACH_FILE_ID = '';

        $_db_SDATE = date('Y-m-d');
        $_db_EDATE = date('Y-m-d', strtotime('+1 days'));

        $disabled = '';
        $_db_MAIN_ATTACH_FILE_ID = '';

        $checked = ''; // 메인노출여부
        $checked2 = 'checked'; // 노출여부
        $table = 'CATEGORY3'; // 관리자 테이블

        if ($M_CATEGORY1_SEQ == "EXHIBITION") {
            $title_name = "EXHIBITION"; // Copy, CSV, Excel, Print 제목
            $url_name = strtolower($title_name);
            $back_url = "../board/exhibition_main.php";
        } else if ($M_CATEGORY1_SEQ == "PROGRAM") {
            $title_name = "PROGRAM"; // Copy, CSV, Excel, Print 제목
            $url_name = strtolower($title_name);
            $back_url = "../board/program_main.php";
        } else if ($M_CATEGORY1_SEQ == "COLLABO") {
            $title_name = "COLLABO"; // Copy, CSV, Excel, Print 제목
            $url_name = strtolower($title_name);
            $back_url = "../board/collabo_main.php";
        }

        if ($mode == 'MOD') {
            $sql = "
                 SELECT CATEGORY3_SEQ
                      , CATEGORY1_SEQ
                      , CATEGORY2_SEQ
                      , TITLE_YN
                      , MAIN_YN
                      , TITLE
                      , SUB_TITLE
                      , LINK_URL
                      , CONTENT_TITLE
                      , CONTENT_TEXT
                      , RELATED_VALUE
                      , SDATE
                      , EDATE
                      , ORDER_NUMBER
                      , ATTACH_FILE_ID
                   FROM {$table}
                  WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ
                    AND PAGE_TYPE = '{$page_type}'";

            $name_sql = "카테고리2 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, [':CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (!empty($data)) {
                $_db_CATEGORY3_SEQ = _check_var($data['CATEGORY3_SEQ']); // 분류 시퀀스
                $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 카테고리 시퀀스
                $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 분류
                $_db_TITLE_YN = _check_var($data['TITLE_YN']); // 메인노출여부
                $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 제목 설명
                $_db_LINK_URL = _check_var($data['LINK_URL']); // 외부 링크
                $_db_CONTENT_TITLE = _check_var($data['CONTENT_TITLE']); // 상세 제목
                $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
                $_db_RELATED_VALUE = _check_var($data['RELATED_VALUE']); // 관련값
                $_db_SDATE = _check_var($data['SDATE']); // 시작일
                $_db_EDATE = _check_var($data['EDATE']); // 종료일
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 썸네일 이미지

                $file_html = '';

                if (!empty($_db_CATEGORY2_SEQ)) {
                    $type_gb = $_db_CATEGORY2_SEQ;
                }

                if ($_db_TITLE_YN == "Y") {
                    $checked = 'checked';
                }

                if ($_db_MAIN_YN == "N") {
                    $checked2 = '';
                }

                if ($page_type == PAGE3) {
                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", "", $_db_ATTACH_FILE_ID, 1);
                        $file_list2 = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 2);

                        foreach ($file_list as $data2) {
                            $_db_MAIN_ATTACH_FILE_ID = _check_var($data2['ATTACH_FILE_PATH']).'/'._check_var($data2['ATTACH_FILE_TEMP_NAME']);
                        }

                        if (!empty($file_list2)) { // 작품 상세 멀티 이미지
                            foreach ($file_list2 as $list) {
                                $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_real_name = _check_var($list['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                                $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                                $_db_attach_file_group = _check_var($list['ATTACH_GROUP']); // pk 1
                                $_db_attach_file_group_count = _check_var($list['ATTACH_GROUP_COUNT']); // pk2
                                $_db_attach_file_size = _check_var($list['ATTACH_FILE_SIZE']); // 파일사이즈
                                $_db_attach_file_type = _check_var($list['ATTACH_FILE_TYPE']); // 파일타입
                                $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
    
                                $fileData[] = array(
                                    'ATTACH_FILE_TEMP_NAME' => $_db_attach_file_temp_name
                                    , 'ATTACH_FILE_REAL_NAME' => $_db_attach_file_real_name
                                    , 'ATTACH_GROUP' => $_db_attach_file_group
                                    , 'ATTACH_GROUP_COUNT' => $_db_attach_file_group_count
                                    , 'ATTACH_FILE_SIZE' => $_db_attach_file_size
                                    , 'ATTACH_FILE_TYPE' => $_db_attach_file_type
                                    , 'PATH' => $path_File
                                    , 'data_type' => 'N'
                                );
                            }
                            $file_json = json_encode($fileData);
                        }
                    }
                }
            }
        }

        if ($M_CATEGORY1_SEQ != "COLLABO") {
            $sql = "
                 SELECT CATEGORY2_SEQ as COM_CD
                      , TITLE as COM_CD_NM
                      , CATEGORY1_SEQ as TH1_THEM_CD
                   FROM CATEGORY2
                  WHERE 1
                    AND PAGE_TYPE = '{$page_type}'
                    AND CATEGORY1_SEQ = '{$M_CATEGORY1_SEQ}'
                  ORDER BY ORDER_NUMBER";

            $name_sql = "카테고리2 리스트";
            $clefResult = $mysqldb->select($sql, null, $name_sql);
            
            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $combo_list = $clefResult->getResultSet();

            setCATEGORY2ComboList($combo_list);
        }

        $sql = "
             SELECT CATEGORY3_SEQ AS COM_CD
                  , TITLE as COM_CD_NM
               FROM CATEGORY3
              WHERE 1
                AND PAGE_TYPE = '{$page_type}'
                AND CATEGORY1_SEQ = '{$M_CATEGORY1_SEQ}'
                AND CATEGORY3_SEQ != '{$CATEGORY3_SEQ}'
              ORDER BY ORDER_NUMBER DESC";

        $name_sql = "관련제품 리스트";
        $clefResult = $mysqldb->select($sql, null, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list2 = $clefResult->getResultSet();

        setRELATEDComboList($combo_list2);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'CATEGORY1_SEQ' => $M_CATEGORY1_SEQ
            , 'CATEGORY2_SEQ' => $M_CATEGORY2_SEQ
            , 'TITLE' => $M_TITLE
            , 'TITLE_YN' => $M_TITLE_YN
            , 'MAIN_YN' => $M_MAIN_YN
            , 'start_date' => $M_start_date
            , 'end_date' => $M_end_date
        );

      $query_string = http_build_query($arrParams);
  } catch (Exception $e) {
      $arrRtn['code'] = $e->getCode();
      $arrRtn['msg'] = $e->getMessage();

      echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
  }

      /**
     * name :setCATEGORY2ComboList
     * comment : 콤보박스에 사용하는 값을 전역변수에 저장
     */
    function setCATEGORY2ComboList($data) {
        global $combo_list_arry;
        $combo_list_arry = $data;
    }

    /**
     * name :getCATEGORY2ComboList
     * comment : 콤보박스에 사용하는 값을 콤보박스 값으로 출력
     */
    function getCATEGORY2ComboList() {
        global $combo_list_arry;
        global $type_gb;

        $COM_CD = '';
        $COM_CD_NM = '';
        $selected = '';

        foreach($combo_list_arry as $array_val) {
            $data_code = '';

            foreach ($array_val as $key => $val) {
                if ($key == 'COM_CD') {
                    $COM_CD = $val;
                } else if ($key == 'COM_CD_NM') {
                    $COM_CD_NM = $val;
                } else if ($key == 'TH1_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code1='$val' ";
                    }
                }
            }

            if (!empty($type_gb)) {
                $selected = ($type_gb == $COM_CD) ? 'selected="selected"' : '';
            }
            
            echo <<<OPTION
                        <option value="{$COM_CD}" $data_code $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }

    /**
     * name :setRELATEDComboList
     * comment : 콤보박스에 사용하는 값을 전역변수에 저장
     */
    function setRELATEDComboList($data) {
        global $combo_list_arry2;
        $combo_list_arry2 = $data;
    }

    /**
     * name :getMEMBERComboList
     * comment : 콤보박스에 사용하는 값을 콤보박스 값으로 출력
     */
    function getRELATEDComboList() {
        global $combo_list_arry2;
        global $type_gb2;

        $COM_CD = '';
        $COM_CD_NM = '';
        $selected = '';

        foreach($combo_list_arry2 as $array_val) {
            foreach($array_val as $key => $val) {
                if ($key == 'COM_CD') {
                    $COM_CD = $val;
                } else if ($key == 'COM_CD_NM') {
                    $COM_CD_NM = $val;
                }
            }

            if (!empty($type_gb2)) {
                $selected = ($type_gb2 == $COM_CD) ? 'selected="selected"' : '';
            }

            echo <<<OPTION
                        <option value="{$COM_CD}" $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }