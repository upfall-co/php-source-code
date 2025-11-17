<?php
/**
 * 파일명 : space_main_code.php
 * 내용 : 공간 메인페이지 코드
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

    $combo_list_arry = array();

    try {
        global $query_string;

        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $TYPE_CD = get_request_param('TYPE_CD', 'GET'); // 층수구분
        $TITLE = get_request_param('TITLE', 'GET'); // 공간
        $MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부

        $title_name = "공간";

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'SPACE'; // 공간 테이블
        $type_gb = '';

        //페이지타입
        if (!empty($page_type)) {
            $where .= " AND PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        //검색
        if (!empty($TYPE_CD)) { // 층수구분
            $where .= " AND TYPE_CD = :TYPE_CD";
            $arrValue[':TYPE_CD'] = $TYPE_CD;
        }

        if (!empty($TITLE)) { // 제목
            $where .= " AND TITLE LIKE :TITLE";
            $arrValue[':TITLE'] = "%{$TITLE}%";
        }

        if (!empty($MAIN_YN)) { // 노출여부
            $where .= " AND MAIN_YN = :MAIN_YN";
            $arrValue[':MAIN_YN'] = $MAIN_YN;
        }

        $sql = "
             SELECT SPACE_SEQ
                  , ZCM_COM_NM('COL011', TYPE_CD) AS TYPE_CD_NM
                  , TITLE
                  , ATTACH_FILE_ID
                  , ORDER_NUMBER
                  , ZCM_COM_NM('AD002', MAIN_YN) AS MAIN_YN_NM
                  , reg_date
                  , reg_user
               FROM {$table} 
              WHERE 1
               {$where}
              ORDER BY ORDER_NUMBER DESC, reg_date DESC";

        $name_sql = "공간 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();
        setMain_List($list);

        $INS_arrParams = array( // 초기화 및 등록
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
        );

        $INS_query_string = http_build_query($INS_arrParams);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'TYPE_CD' => $TYPE_CD
            , 'TITLE' => $TITLE
            , 'MAIN_YN' => $MAIN_YN
        );

        $query_string = http_build_query($arrParams);

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }

    /**
     * name :setMain_List
     * comment : 메인 데이터 저장
     */
    function setMain_List($data) {
        global $Main_list_arry;
        $Main_list_arry = $data;
    }

    /**
     * name :getMain_List
     * comment : 메인 총 데이터
     */
    function getMain_List() {
        global $Main_list_arry;
        global $query_string;

        $page_type = get_request_param('page_type', 'GET');

        if (!empty($Main_list_arry)) {
            foreach ($Main_list_arry as $data) {
                $_db_SPACE_SEQ = _check_var($data['SPACE_SEQ']); // 시퀀스
                $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 층수구분
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_MAIN_YN_NM = _check_var($data['MAIN_YN_NM']); // 노출여부
                $_db_reg_date = _check_var($data['reg_date']); // 등록일
                $_db_reg_user = _check_var($data['reg_user']); // 등록자

                $path_File = "";

                $url = "../board/space_details.php?mode=MOD&{$query_string}";

                if (!empty($_db_ATTACH_FILE_ID)) {
                    $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1, "", "", "", "", "ATTACH_GROUP_COUNT DESC");

                    if (!empty($file_list)) {
                        foreach ($file_list as $list) {
                            $_db_attach_file_temp_name = _check_var($list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                            $_db_attach_file_path = _check_var($list['ATTACH_FILE_PATH']); // 경로 
                            $path_File = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                        }
                    }
                }

                echo <<<TR
                        <tr>
                            <td class="simple_numbers"></td>
                            <td>{$_db_TYPE_CD_NM}</td>
                            <td><a href="{$url}&seq={$_db_SPACE_SEQ}">{$_db_TITLE}</td>
                            <td>
                                <div class="lightBoxGallery">
                                    <img src="{$path_File}" style="height: 100px;" alt="썸네일 이미지">
                                </div>
                            </td>
                            <td>{$_db_ORDER_NUMBER}</td>
                            <td>{$_db_MAIN_YN_NM}</td>
                            <td>{$_db_reg_user}</td>
                            <td>{$_db_reg_date}</td>
                        </tr>
                    TR;
            }
        }
    }
?>

