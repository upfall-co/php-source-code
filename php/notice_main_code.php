<?php
/**
 * 파일명 : notice_main_code.php
 * 내용 : 공지사항 메인 페이지 코드
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 이보경 (writer_main_code.php 복붙해 수정)
 * ------------------------------------
 * name       date        comment
 * 이보경    2023/08/08     V1.0
 * 전상범    2023/08/09     개발
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
        global $page_type;

        $m_seq = get_request_param('m_seq', 'GET');
        $mp_seq = get_request_param('mp_seq', 'GET');
        $page_type = get_request_param('page_type', 'GET');
        $TITLE = get_request_param('TITLE', 'GET'); // 제목
        $MAIN_YN = get_request_param('MAIN_YN','GET'); // 노출여부

        $title_name = "공지사항"; // Copy, CSV, Excel, Print 제목

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'INFORM'; // 관리자 테이블
        $type_gb = '';

        //페이지 타입
        if (!empty($page_type)) {
            $where .= " AND PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        //검색
        if (!empty($TITLE)) { // 제목
            $where .= " AND TITLE LIKE :TITLE";
            $arrValue[':TITLE'] = "%{$TITLE}%";
        }

        if (!empty($MAIN_YN)) { // 노출여부
            $where .= " AND MAIN_YN = :MAIN_YN";
            $arrValue[':MAIN_YN'] = $MAIN_YN;

            $type_gb = $MAIN_YN;
        }

        $sql = "
             SELECT INFORM_SEQ
                  , ORDER_NUMBER
                  , TITLE
                  , ZCM_COM_NM('AD002', MAIN_YN) AS MAIN_YN_NM
                  , reg_user
                  , reg_date
               FROM {$table}
              WHERE 1
                AND TYPE_CD = 'NOL'
               {$where}
              ORDER BY MAIN_YN DESC, ORDER_NUMBER DESC, reg_date DESC";

        $name_sql = "공지사항 리스트";
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
        global $page_type;;

        if (!empty($Main_list_arry)) {
            foreach ($Main_list_arry as $data) {
                $_db_INFORM_SEQ = _check_var($data['INFORM_SEQ']); // 시퀀스
                $_db_ORDER_NUMBER = _check_var($data['ORDER_NUMBER']); // 정렬값
                $_db_TITLE = _check_var($data['TITLE']); // 제목
                $_db_MAIN_YN_NM = _check_var($data['MAIN_YN_NM']); // 노출여부
                $_db_reg_user = _check_var($data['reg_user']); // 등록자
                $_db_reg_date = _check_var($data['reg_date']); // 등록일

                $url = "../board/notice_details.php?mode=MOD&{$query_string}";

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td><a href="{$url}&seq={$_db_INFORM_SEQ}">{$_db_TITLE}</a></td>
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

