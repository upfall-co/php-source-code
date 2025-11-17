<?php
/**
 * 파일명 : writer_main_code.php
 * 내용 : 카테고리1 메인 페이지 코드
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
 * 김민성    2023/11/09    shop 기능추가
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
        global $query_string;

        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $TITLE = get_request_param('TITLE', 'GET'); // 작가명, 1뎁스
        $MAIN_YN = get_request_param('MAIN_YN', 'GET'); // 노출여부
        $sub_type = isset($common_type) ? $common_type : '';  // home 페이지 구분

        $title_name = ""; // Copy, CSV, Excel, Print 제목
        $title_val = "";

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'CATEGORY1'; // 관리자 테이블

        // 페이지 타입
        if (!empty($page_type)) {
            $where .= " AND PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        //검색
        if (!empty($TITLE)) { // 작품
            $where .= " AND TITLE LIKE :TITLE";
            $arrValue[':TITLE'] = "%{$TITLE}%";
        }

        if (!empty($MAIN_YN)) { // 노출여부
            $where .= " AND MAIN_YN = :MAIN_YN";
            $arrValue[':MAIN_YN'] = $MAIN_YN;
        }

        if (!empty($sub_type)) {
            $where .= " AND SUB_TYPE = :SUB_TYPE";
            $arrValue[':SUB_TYPE'] = $sub_type;
        }

        if ($page_type == PAGE1) {
            $title_name = "작가"; // Copy, CSV, Excel, Print 제목
            $title_val = "작가명";
        } else if ($page_type == PAGE2) {
            $title_name = "카테고리"; // Copy, CSV, Excel, Print 제목
            $title_val = "카테고리명";
        } else if ($page_type == PAGE3) {
            if ($sub_type == SUB_PAGE1) {
                $title_name = "업종"; // Copy, CSV, Excel, Print 제목
                $title_val = "업종명";
                $where .= " AND CATEGORY1_SEQ LIKE 'RECRU%'";
            } else if ($sub_type == SUB_PAGE2) {
                $title_name = "작가"; // Copy, CSV, Excel, Print 제목
                $title_val = "작가명";
                $where .= " AND CATEGORY1_SEQ LIKE 'PROGRAM%'";
            }
        }

        $sql = "
             SELECT CATEGORY1_SEQ
                  , TITLE
                  , ZCM_COM_NM('AD002', MAIN_YN) AS MAIN_YN_NM
                  , ORDER_NUMBER
                  , reg_date
                  , reg_user
               FROM {$table}
              WHERE 1
               {$where}
              ORDER BY MAIN_YN DESC, ORDER_NUMBER DESC, reg_date DESC";

        $name_sql = "카테고리1 리스트";
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
            , 'sub_type' => $sub_type
        );

        $INS_query_string = http_build_query($INS_arrParams);

        $arrParams = array(
              'm_seq' => $m_seq
            , 'mp_seq' => $mp_seq
            , 'page_type' => $page_type
            , 'sub_type' => $sub_type
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

        if (!empty($Main_list_arry)) {
            foreach ($Main_list_arry as $data) {
                $_db_CATEGORY1_SEQ = _check_var($data['CATEGORY1_SEQ']); // 시퀀스
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_TITLE = _check_var($data['TITLE']); // 제목(작가명) , 카테고리명
                $_db_MAIN_YN_NM = _check_var($data['MAIN_YN_NM']); // 노출여부
                $_db_reg_date = _check_var($data['reg_date']); // 등록일
                $_db_reg_user = _check_var($data['reg_user']); // 등록자

                $url = "../board/writer_details.php?mode=MOD&{$query_string}";

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td><a href="{$url}&seq={$_db_CATEGORY1_SEQ}">{$_db_TITLE}</a></td>
                                <td>{$_db_MAIN_YN_NM}</td>
                                <td>{$_db_ORDER_NUMBER}</td>
                                <td>{$_db_reg_user}</td>
                                <td>{$_db_reg_date}</td>
                            </tr>
                        TR;
            }
        }
    }
?>

