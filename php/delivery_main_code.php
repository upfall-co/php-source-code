<?php
/**
 * 파일명 : secretCodeManagement_main.php
 * 내용 : 시크릿코드관리 페이지
 * 최초작성날짜 : 2023/08/03
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/03    V1.0
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

        $table_COM = 'ZCMCOMMON'; // 공통테이블

        $sql = "
             SELECT COM_TYPE
                  , COM_CD
                  , TH1_THEM_CD
                  , FORMAT(TH1_THEM_CD, '') AS TH1_THEM_CD_NM
               FROM {$table_COM}
              WHERE COM_TYPE = 'COL010'
              ORDER BY COM_ORDER";

        $name_sql = "배송비";
        $clefResult = $mysqldb->select($sql, null, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $Main_list_arry = $clefResult->getResultSet();

        $count = 1;

        if (!empty($Main_list_arry)) {
            foreach ($Main_list_arry as $data) {
                ${$data['COM_CD']} =  $data['TH1_THEM_CD_NM'];
            }
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
 ?>