<?php
/**
 * 파일명 : rental_main_code.php
 * 내용 : 대관문의 메인 페이지 코드
 * 최초작성날짜 : 2025/04/09
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준     2025/04/09    V1.0
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
        $COMPANY = get_request_param('COMPANY', 'GET'); // 회사(단체)명
        $AGENCY = get_request_param('AGENCY', 'GET'); // 대행사명
        $TYPE_CD = get_request_param('TYPE_CD','GET'); // 문의 구분

        $title_name = "대관문의"; // Copy, CSV, Excel, Print 제목

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'RENTAL_INQUIRY'; // 관리자 테이블

        //페이지 타입
        if (!empty($page_type)) {
            $where .= " AND PAGE_TYPE = :page_type";
            $arrValue[':page_type'] = $page_type;
        }

        //검색
        if (!empty($COMPANY)) { // 회사(단체)명
            $where .= " AND COMPANY LIKE :COMPANY";
            $arrValue[':COMPANY'] = "%{$COMPANY}%";
        }

        if (!empty($AGENCY)) { // 대행사명
            $where .= " AND AGENCY LIKE :AGENCY";
            $arrValue[':AGENCY'] = "%{$AGENCY}%";
        }

        if (!empty($TYPE_CD)) { // 문의 구분
            $where .= " AND TYPE_CD = :TYPE_CD";
            $arrValue[':TYPE_CD'] = $TYPE_CD;
        }

        $sql = "
             SELECT RENTAL_SEQ
                  , ZCM_COM_NM('COL013', TYPE_CD) AS TYPE_CD_NM
                  , COMPANY
                  , AGENCY
                  , NAME
                  , MOBILE
                  , EMAIL
                  , reg_user
                  , reg_date
               FROM {$table}
              WHERE 1
               {$where}
              ORDER BY reg_date DESC";

        $name_sql = "대관신청 리스트";
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
            , 'COMPANY' => $COMPANY
            , 'AGENCY' => $AGENCY
            , 'TYPE_CD' => $TYPE_CD
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
                $_db_RENTAL_SEQ = _check_var($data['RENTAL_SEQ']); // 시퀀스
                $_db_TYPE_CD_NM = _check_var($data['TYPE_CD_NM']); // 문의 구분
                $_db_COMPANY = _check_var($data['COMPANY']); // 회사(단체)명
                $_db_AGENCY = _check_var($data['AGENCY']); // 대행사명
                $_db_NAME = _check_var($data['NAME']); // 담당자명 (직함)
                $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                $_db_reg_user = _check_var($data['reg_user']); // 등록자
                $_db_reg_date = _check_var($data['reg_date']); // 등록일

                $url = "../board/rental_details.php?mode=MOD&{$query_string}";

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td>{$_db_TYPE_CD_NM}</td>
                                <td><a href="{$url}&seq={$_db_RENTAL_SEQ}">{$_db_COMPANY}</a></td>
                                <td>{$_db_AGENCY}</td>
                                <td>{$_db_NAME}</td>
                                <td>{$_db_MOBILE}</td>
                                <td>{$_db_EMAIL}</td>
                                <td>{$_db_reg_user}</td>
                                <td>{$_db_reg_date}</td>
                            </tr>
                        TR;
            }
        }
    }
?>

