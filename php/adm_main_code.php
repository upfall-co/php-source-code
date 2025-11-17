<?php
/**
 * 파일명 : adm_main_code.php
 * 내용 : 회원관리 메인 페이지 코드
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/08     V1.0
 * 김민성    2023/08/09     소스작성
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
        $ID = get_request_param('ID', 'GET');
        $NAME = get_request_param('NAME', 'GET');
        $MOBILE = get_request_param('MOBILE', 'GET');
        $start_date = get_request_param('start_date','GET');
        $end_date = get_request_param('end_date','GET');

        $title_name = "관리자 계정 정보"; // Copy, CSV, Excel, Print 제목

        $arrValue = array();
        $limit = 10;
        $where = '';
        $table = 'adm'; // 회원 테이블
        $type_gb = '';

        //검색
        if (!empty($ID)) { // 아이디
            $where .= " AND ID LIKE :ID";
            $arrValue[':ID'] = "%{$ID}%";
        }

        if (!empty($NAME)) { // 이름
            $where .= " AND NAME LIKE :NAME";
            $arrValue[':NAME'] = "%{$NAME}%";
        }

        if (!empty($MOBILE)) { // 연락처
            $MOBILE = str_replace('-', '', $MOBILE);

            $where .= " AND MOBILE LIKE :MOBILE";
            $arrValue[':MOBILE'] = "%{$MOBILE}%";
        }

         //날짜 검색
        if (!empty($start_date)) { // 시작일
            $where .= " AND reg_date >= :sdate";
            $arrValue[':sdate'] = $start_date;
        }

        if (!empty($end_date)) { // 종료일
            $where .= " AND reg_date <= :edate";
            $arrValue[':edate'] = $end_date;
        }

        $sql = "
             SELECT ID
                  , NAME
                  , MOBILE
                  , EMAIL
                  , reg_date
               FROM {$table}
              WHERE 1
                AND member_type NOT IN ('SUPADM', 'ADM')
               {$where}
              ORDER BY reg_date DESC";

        $name_sql = "관리자 계정 리스트";
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
            , 'ID' => $ID
            , 'NAME' => $NAME
            , 'MOBILE' => $MOBILE
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
                $_db_ID = _check_var($data['ID']); // 아이디
                $_db_NAME = _check_var($data['NAME']); // 이름
                $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                $_db_reg_date = _check_var($data['reg_date']); // 가입일
                
                $url = "../board/adm_details.php?mode=MOD&{$query_string}";

                if (!empty($_db_MOBILE)) {
                    $_db_MOBILE = formatPhoneNumber($_db_MOBILE);
                }

                echo <<<TR
                            <tr>
                                <td class="simple_numbers"></td>
                                <td><a href="{$url}&seq={$_db_ID}">{$_db_ID}</a></td>
                                <td>{$_db_NAME}</td>
                                <td>{$_db_MOBILE}</td>
                                <td>{$_db_EMAIL}</td>
                                <td>{$_db_reg_date}</td>
                            </tr>
                        TR;
            }
        }
    }
?>

