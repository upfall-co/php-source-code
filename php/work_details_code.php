<?php
/**
 * 파일명 : work_details_code.php
 * 내용 : 작품 관리 코드
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/07     V1.0
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
        $sub_type = get_request_param('sub_type', 'GET');
        $mode = get_request_param('mode', 'GET');
        $MULTI = get_request_param('MULTI', 'GET');
        $CATEGORY3_SEQ = get_request_param('seq', 'GET');
        $M_PROGRAM_CD = get_request_param('PROGRAM_CD', 'GET');
        $M_CATEGORY1_SEQ = get_request_param('CATEGORY1_SEQ', 'GET');
        $M_CATEGORY2_SEQ = get_request_param('CATEGORY2_SEQ', 'GET');
        $M_TITLE = get_request_param('TITLE', 'GET');
        $M_TITLE_YN = get_request_param('TITLE_YN','GET');
        $M_MAIN_YN = get_request_param('MAIN_YN', 'GET');
        $M_start_date = get_request_param('start_date', 'GET');
        $M_end_date = get_request_param('end_date', 'GET');

        $_db_PROGRAM_CD = '';
        $_db_CATEGORY3_SEQ = '';
        $_db_CATEGORY2_SEQ = '';
        $_db_CATEGORY1_SEQ = '';
        $_db_OPTION = '';
        $TYPE_CHK1 = "";
        $TYPE_CHK2 = "";
        $TYPE_CHK3 = "";
        $TYPE_CHK4 = "";
        $TYPE_CHK5 = "";
        $TYPE_CHK6 = "";
        $TYPE_CHK7 = "";
        $TYPE_CHK8 = "";
        $_db_TITLE = '';
        $_db_SUB_TITLE = "";
        $_db_FRAME = '';
        $_db_BRAND = "";
        $_db_M_PRICE = 0;
        $_db_SALE_YN = "";
        $_db_OID_PRICE = 0;
        $_db_SALE_PERCENT = 0;
        $_db_QUANTITY = 0;
        $_db_BADGE_CO = "";
        $_db_CONTENT_TEXT = '';
        $_db_ORDER_NUMBER = 0;
        $_db_SEARCH_TEXT = '';
        $_db_LINK_URL = '';
        $_db_CONTENT_TITLE = '';
        $_db_ATTACH_FILE_ID = "";

        $_db_OPTION_NAME = '';
        $_db_PRICE = 0;
        $_db_OP_MAIN_YN = 'checked';
        $_db_OP_SOLD_YN = '';
        $_db_OP_QUANTITY = 1;
        $_db_OP_ORDER_NUMBER = 0;

        $_db_SDATE = date('Y-m-d');
        $_db_EDATE = date('Y-m-d', strtotime('+1 days'));

        $_db_MAIN_ATTACH_FILE_ID = ""; // 메인 이미지 파일 경로
        $_db_MAIN_ATTACH4_FILE_ID = ""; // 호버 이미지 파일 경로
        $disabled = '';

        $checked = ''; // 타이틀 노출여부
        $checked2 = 'checked'; // 노출여부
        $checked3 = ''; // 노출여부
        $checked4 = ''; // 할인여부

        $table = 'CATEGORY3'; // 관리자 테이블
        $table_OP = 'CATEGORY_OPTION'; // 옵션 관리자 테이블

        $category1_name = "";
        $category1_val = "";
        $title_name = "";
        $input_title_name = "";

        if ($page_type == PAGE1) {
            $category1_name = "작가";
            $category1_val = "작가명";
            $category2_name = "시리즈";
            $category2_val = "시리즈명";
            $title_name = "작품"; // Copy, CSV, Excel, Print 제목
            $input_title_name = "작품";
            $title_val = "작품명";

            $op_price_readonly = "readonly";
        } else if ($page_type == PAGE2) {
            $category1_name = "카테고리";
            $category1_val = "카테고리명";
            $category2_name = "분류";
            $category2_val = "분류명";
            $title_name = "상품"; // Copy, CSV, Excel, Print 제목
            $input_title_name = "상품";
            $title_val = "상품명";

            $op_price_readonly = "";
        } else if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE2) {
                $category1_name = "카테고리";
                $category1_val = "카테고리명";
                $category2_name = "분류";
                $category2_val = "분류명";
                $title_name = "상세"; // Copy, CSV, Excel, Print 제목
                $input_title_name = "제목 첫 번째 줄"; // Copy, CSV, Excel, Print 제목
                $title_val = "제목";

                $op_price_readonly = "";
            }
        }

        $file_json = "";

        if ($mode == 'MOD') {
            $arrValue = array();
            $arrValue[':CATEGORY3_SEQ'] = $CATEGORY3_SEQ;
            $arrValue[':PAGE_TYPE'] = $page_type;

            $disabled = 'disabled';

            $sql = "
                 SELECT CATEGORY3_SEQ
                      , CATEGORY2_SEQ
                      , CATEGORY1_SEQ
                      , PROGRAM_CD
                      , TYPE_CD
                      , MAIN_YN
                      , INDEX_YN
                      , SDATE
                      , EDATE
                      , LINK_URL
                      , CONTENT_TITLE
                      , TITLE
                      , SUB_TITLE
                      , FRAME
                      , BRAND
                      , FORMAT(PRICE, '') AS PRICE
                      , SALE_YN
                      , FORMAT(OID_PRICE, '') AS OID_PRICE
                      , SALE_PERCENT
                      , BADGE_CO
                      , QUANTITY
                      , CONTENT_TEXT
                      , SEARCH_TEXT
                      , MAIN_YN
                      , TITLE_YN
                      , ORDER_NUMBER
                      , ATTACH_FILE_ID
                   FROM {$table}
                  WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ
                    AND PAGE_TYPE = :PAGE_TYPE";

            $name_sql = "카테고리3 상세정보 리스트";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $data = $clefResult->getResultSet();

            if (empty($data)) {
                dieAndErrorMove('잘못된 접근입니다.');
            }

            $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 작가, 카테고리 시퀀스
            $_db_CATEGORY2_SEQ = _check_var($data['CATEGORY2_SEQ']); // 시리즈, 분류 시퀀스
            $_db_PROGRAM_CD = _check_var($data['PROGRAM_CD']); // 중분류
            $_db_MAIN_YN = _check_var($data['MAIN_YN']); // 노출여부
            $_db_TITLE_YN = _check_var($data['TITLE_YN']); // 메인노출여부
            $_db_INDEX_YN = _check_var($data['INDEX_YN']); // 메인화면 노출여부
            $_db_SDATE = _check_var($data['SDATE']); // 시작일
            $_db_EDATE = _check_var($data['EDATE']); // 종료일
            $_db_LINK_URL = _check_var($data['LINK_URL']); // 외부링크
            $_db_CONTENT_TITLE = _check_var($data['CONTENT_TITLE']); // 상세 제목
            $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
            $_db_TYPE_CD = _check_var($data['TYPE_CD']); // 구분
            $_db_TITLE = _check_var($data['TITLE']); // 작품, 상품명
            $_db_SUB_TITLE = _check_var($data['SUB_TITLE']); // 상품 서브내용
            $_db_FRAME = _check_var($data['FRAME']); // 프레임
            $_db_BRAND = _check_var($data['BRAND']); // 브랜드명
            $_db_M_PRICE = _check_var($data['PRICE']); // 금액
            $_db_SALE_YN = _check_var($data['SALE_YN']); // 할인금액 사용여부
            $_db_OID_PRICE = _check_var($data['OID_PRICE']); // 세일전금액
            $_db_SALE_PERCENT = _check_var($data['SALE_PERCENT']); // 퍼센트
            $_db_BADGE_CO = _check_var($data['BADGE_CO']); // 퍼센트
            $_db_SEARCH_TEXT = _check_var($data['SEARCH_TEXT']); // 검색 내용
            $_db_QUANTITY = _check_var($data['QUANTITY']); // 수량
            $_db_CONTENT_TEXT = _check_var($data['CONTENT_TEXT']); // 내용
            $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디

            $file_list = '';
            $file_list3 = '';

            $file_html3 = '';

            if ($_db_TITLE_YN == "Y") {
                $checked = 'checked';
            }

            if ($_db_MAIN_YN == "N") {
                $checked2 = '';
            }

            if ($_db_INDEX_YN == "Y") {
                $checked3 = 'checked';
            }

            if ($_db_SALE_YN == "Y") {
                $checked4 = 'checked';
            }

            if (!empty($_db_TYPE_CD)){
                $variables = explode(",", $_db_TYPE_CD);

                // 배열 순회 및 처리
                foreach ($variables as $variable) {
                    switch (trim($variable)) { // trim() 함수로 문자열 앞뒤 공백 제거
                        case 'NEW':
                            $TYPE_CHK1 = 'checked';
                            break;
                        case 'BEST':
                            $TYPE_CHK2 = 'checked';
                            break;
                        case 'RECOMMENDED':
                            $TYPE_CHK3 = 'checked';
                            break;
                        case 'SALE':
                            $TYPE_CHK4 = 'checked';
                            break;
                        default:
                            // 해당하는 id가 없는 경우에 대한 처리
                            break;
                    }
                }
            }

            if (!empty($_db_BADGE_CO)){
                switch (trim($_db_BADGE_CO)) { // trim() 함수로 문자열 앞뒤 공백 제거
                    case 'NEW':
                        $TYPE_CHK5 = 'checked';
                        break;
                    case 'SALE':
                        $TYPE_CHK6 = 'checked';
                        break;
                    case 'BEST':
                        $TYPE_CHK7 = 'checked';
                        break;
                    case 'SOLDOUT':
                        $TYPE_CHK8 = 'checked';
                        break;
                    default:
                        // 해당하는 id가 없는 경우에 대한 처리
                        break;
                }
            }

            if (!empty($_db_CATEGORY1_SEQ)) {
                $type_gb = $_db_CATEGORY1_SEQ;
            }

            if (!empty($_db_CATEGORY2_SEQ)) {
                $type_gb2 = $_db_CATEGORY2_SEQ;
            }

            if (!empty($_db_ATTACH_FILE_ID)) {
                $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);
                $file_list3 = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 3);
                $file_list4 = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 4);

                if (!empty($file_list)) { // 메인 이미지
                    foreach ($file_list as $list) {
                        $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                        $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로
                        $_db_MAIN_ATTACH_FILE_ID = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                    }
                }

                if (!empty($file_list3)) { // 작품 상세 멀티 이미지
                    foreach ($file_list3 as $list3) {
                        $_db_attach_file_temp_name = _check_var($list3['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                        $_db_attach_file_real_name = _check_var($list3['ATTACH_FILE_REAL_NAME']); // 파일실제이름
                        $_db_attach_file_path = _check_var($list3['ATTACH_FILE_PATH']); // 경로 
                        $_db_attach_file_group = _check_var($list3['ATTACH_GROUP']); // pk 1
                        $_db_attach_file_group_count = _check_var($list3['ATTACH_GROUP_COUNT']); // pk2
                        $_db_attach_file_size = _check_var($list3['ATTACH_FILE_SIZE']); // 파일사이즈
                        $_db_attach_file_type = _check_var($list3['ATTACH_FILE_TYPE']); // 파일타입
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

                if (!empty($file_list4)) {
                    foreach ($file_list4 as $list) {
                        $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                        $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로
                        $_db_MAIN_ATTACH4_FILE_ID = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                    }
                }
            }

            $sql = "
                SELECT OPTION_SEQ
                     , OPTION_NAME
                     , FORMAT(PRICE, '') AS PRICE
                     , MAIN_YN AS OP_MAIN_YN
                     , SOLD_YN AS OP_SOLD_YN
                     , QUANTITY AS OP_QUANTITY
                     , ORDER_NUMBER AS OP_ORDER_NUMBER
                  FROM {$table_OP}
                 WHERE CATEGORY3_SEQ = :CATEGORY3_SEQ";

            $name_sql = "카테고리3 옵션 상세정보 리스트";
            $clefResult = $mysqldb->select($sql, [':CATEGORY3_SEQ' => $CATEGORY3_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $OP_data = $clefResult->getResultSet();
            $OP_html = '';

            if (!empty($OP_data)) {

                foreach ($OP_data as $OP){
                    $OPTION_SEQ = _check_var($OP['OPTION_SEQ']); // 옵션시퀀스
                    $OPTION_NAME = _check_var($OP['OPTION_NAME']); // 옵션명
                    $PRICE = _check_var($OP['PRICE']); // 금액
                    $OP_MAIN_YN = _check_var($OP['OP_MAIN_YN']); // 옵션 노출여부
                    $OP_SOLD_YN = _check_var($OP['OP_SOLD_YN']); // 옵션 품절여부
                    $OP_QUANTITY = _check_var($OP['OP_QUANTITY']); // 옵션 수량 [해당 기능은 수량 관리할때 이후 오픈 - 김민성]
                    $OP_ORDER_NUMBER = _check_var($OP['OP_ORDER_NUMBER']); // 옵션 정렬값

                    $OP_CK = "";
                    $OP_CK2 = "";

                    if ($OP_MAIN_YN == "Y") {
                        $OP_CK = "checked";
                    }

                    if ($OP_SOLD_YN == "Y") {
                        $OP_CK2 = "checked";
                    }

                    $OP_html .= <<<DIV
                                    <div id="OPTION_CK" data-type="U" data-seq="{$OPTION_SEQ}">
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">* 옵션</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" name="OPTION_NAME[]" value="{$OPTION_NAME}" placeholder="옵션명을 입력해주세요." maxlength="100">
                                            </div>
                                            <label class="col-sm-1 text-right col-form-label">노출</label>
                                            <div class="col-sm-2 m-t-xs">
                                                <div class="i-checks">
                                                    <label class=""> 
                                                        <div class="icheckbox_square-green"  style="position: relative;">
                                                            <input type="checkbox" name="OP_MAIN_YN[]" value="Y" style="position: absolute; opacity: 0;" {$OP_CK}>
                                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                        </div> 
                                                        <i></i><font _mstmutation="1" _msttexthash="9770748" _msthash="240"> 노출여부 </font>
                                                    </label>
                                                </div>
                                            </div>
                                            <label class="col-sm-1 text-right col-form-label">품절</label>
                                            <div class="col-sm-2 m-t-xs">
                                                <div class="i-checks">
                                                    <label class=""> 
                                                        <div class="icheckbox_square-green"  style="position: relative;">
                                                            <input type="checkbox" name="OP_SOLD_YN[]" value="Y" style="position: absolute; opacity: 0;" {$OP_CK2}>
                                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                        </div> 
                                                        <i></i><font _mstmutation="1" _msttexthash="9770748" _msthash="240"> 품절여부 </font>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 text-right col-form-label">* 수량</label>
                                            <div class="col-sm-3">
                                                <input type="number" class="form-control" name="OP_QUANTITY[]" value="{$OP_QUANTITY}" placeholder="작품 수량을 입력해주세요." maxlength="20" {$op_price_readonly}>
                                            </div>
                                            <label class="col-sm-1 text-right col-form-label">* 가격</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="PRICE[]" value="{$PRICE}" onkeyup="javascript:formatNumber(this, 20)" placeholder="작품 금액을 입력해주세요." maxlength="20">
                                            </div>
                                            <label class="col-sm-1 text-right col-form-label">정렬</label>
                                            <div class="col-sm-2">
                                                <input class="touchspin1 form-control" type="text" name="OP_ORDER_NUMBER[]" value="{$OP_ORDER_NUMBER}">
                                            </div>
                                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                                <button type="button" class="btn btn-danger float-right" onclick="javascript:optionDel($(this));">삭제</button>
                                            </div>
                                        </div>
                                        <div class="hr-line-solid"></div>
                                    </div>
                    DIV;
                }
            }
        }

        $arrValue2 = array();
        $arrValue2[':PAGE_TYPE'] = $page_type;
        $arrValue2[':SUB_TYPE'] = $sub_type;

        $sql = "
            SELECT TITLE
                 , CATEGORY1_SEQ
                 , PROGRAM_CD
              FROM CATEGORY1
             WHERE PAGE_TYPE = :PAGE_TYPE
               AND SUB_TYPE = :SUB_TYPE
               AND MAIN_YN = 'Y'
             ORDER BY ORDER_NUMBER DESC";

        $name_sql = "카테고리1 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue2, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setARTISTComboList($combo_list);

        $sql = "
            SELECT CATEGORY2_SEQ as COM_CD
                 , TITLE as COM_CD_NM
                 , CATEGORY1_SEQ as TH1_THEM_CD
              FROM CATEGORY2
             WHERE PAGE_TYPE = :PAGE_TYPE
               AND SUB_TYPE = :SUB_TYPE
               AND MAIN_YN = 'Y'
             ORDER BY TITLE";

        $name_sql = "카테고리2 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue2, $name_sql);
        
        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list = $clefResult->getResultSet();

        setSERIESComboList($combo_list);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'PROGRAM_CD' => $M_PROGRAM_CD
            , 'CATEGORY1_SEQ' => $M_CATEGORY1_SEQ
            , 'CATEGORY2_SEQ' => $M_CATEGORY2_SEQ
            , 'TITLE' => $M_TITLE
            , 'TITLE_YN' => $M_TITLE_YN
            , 'MAIN_YN' => $M_MAIN_YN
            , 'start_date' => $M_start_date
            , 'end_date' => $M_end_date
        );

        $query_string = http_build_query($arrParams);

        if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE2) {
                $back_url = "program2_main.php?{$query_string}";
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

     /**
     * name :setSERIESComboList
     * comment : 콤보박스에 사용하는 값을 전역변수에 저장
     */
    function setSERIESComboList($data) {
        global $combo_list_arry2;
        $combo_list_arry2 = $data;
    }

    /**
     * name :getSERIESComboList
     * comment : 병원명
     */
    function getSERIESComboList() {
        global $combo_list_arry2;
        global $type_gb2;

        $COM_CD = '';
        $COM_CD_NM = '';
        $selected = '';

        foreach($combo_list_arry2 as $array_val) {
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

            if (!empty($type_gb2)) {
                $selected = ($type_gb2 == $COM_CD) ? 'selected="selected"' : '';
            }
            
            echo <<<OPTION
                        <option value="{$COM_CD}" $data_code $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    }
?>


