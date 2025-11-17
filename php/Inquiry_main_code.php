<?php
/**
 * 파일명 : Inquiry_main_code.php
 * 내용 : 1:1 문의 페이지 코드
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/09     V1.0
 * 김민성    2023/11/21    샵기능추가
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
        $TYPE_CD = get_request_param('TYPE_CD','GET'); // 문의타입
        $NAME = get_request_param('NAME', 'GET'); // 이름
        $TITLE = get_request_param('TITLE', 'GET'); // 문의명
        $QUESTION_CD = get_request_param('QUESTION_CD','GET'); // 답변상태
        $start_date = get_request_param('start_date','GET'); // 시작일
        $end_date = get_request_param('end_date','GET'); // 종료일

        $title_name = "1:1 문의"; // Copy, CSV, Excel, Print 제목

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'INQUIRY'; // 관리자 테이블
        $type_gb = '';
        $type_gb2 = '';

        //페이지 타입
        if (!empty($page_type)) {
            $where .= " AND PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        //검색
        if (!empty($TYPE_CD)) { // 문의타입
            $where .= " AND TYPE_CD = :TYPE_CD";
            $arrValue[':TYPE_CD'] = $TYPE_CD;

            $type_gb2 = $TYPE_CD;
        }

        if (!empty($NAME)) { // 이름
            $where .= " AND NAME LIKE :NAME";
            $arrValue[':NAME'] = "%{$NAME}%";
        }

        if (!empty($TITLE)) { // 문의명
            $where .= " AND TITLE LIKE :TITLE";
            $arrValue[':TITLE'] = "%{$TITLE}%";
        }

        if (!empty($QUESTION_CD)) { // 답변상태
            $where .= " AND QUESTION_CD = :QUESTION_CD";
            $arrValue[':QUESTION_CD'] = $QUESTION_CD;

            $type_gb = $QUESTION_CD;
        }

        //날짜 검색
        if (!empty($start_date)) { // 시작일
            $where .= " AND DATE(reg_date) >= :sdate";
            $arrValue[':sdate'] = $start_date;
        }

        if (!empty($end_date)) { // 종료일
            $where .= " AND DATE(reg_date) <= :edate";
            $arrValue[':edate'] = $end_date;
        }

        $sql = "
             SELECT INQUIRY_SEQ
                  , ZCM_COM_NM('COL002', TYPE_CD) AS TYPE_CD_NM
                  , NAME
                  , TITLE
                  , ZCM_COM_NM('AD008', QUESTION_CD) AS QUESTION_CD_NM
                  , reg_date
               FROM {$table}
              WHERE 1
               {$where}
              ORDER BY MAIN_YN DESC, reg_date DESC";

        $name_sql = "1:1문의 리스트";
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
            , 'NAME' => $NAME
            , 'TITLE' => $TITLE
            , 'QUESTION_CD' => $QUESTION_CD
            , 'TYPE_CD' => $TYPE_CD
            , 'start_date' => $start_date
            , 'end_date' => $end_date
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
                $_db_INQUIRY_SEQ = _check_var($data['INQUIRY_SEQ']); // 시퀀스
                $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 문의타입
                $_db_NAME = _check_var($data['NAME']); // 이름
                $_db_TITLE = _check_var($data['TITLE']); // 문의제목
                $_db_QUESTION_CD_NM = _check_var($data['QUESTION_CD_NM']); // 답변상태
                $_db_reg_date = _check_var($data['reg_date']); // 등록일

                $url = "../board/Inquiry_details.php?mode=MOD&{$query_string}";

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_TYPE_CD_NM}</td>
                                <td>{$_db_NAME}</td>
                                <td><a href="{$url}&seq={$_db_INQUIRY_SEQ}">{$_db_TITLE}</a></td>
                                <td>{$_db_QUESTION_CD_NM}</td>
                                <td>{$_db_reg_date}</td>
                            </tr>
                        TR;
            }
        }
    }
?>

