<?php
/**
 * 파일명 : home_index_location.php
 * 내용 : Main location (등록/ 수정)
 * 최초작성날짜 : 2024/04/25
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준    2024/04/25    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $arrRtn = array(
        'code' => 500
        , 'msg' => ''
    );

    try {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $mysqldb->link->beginTransaction();

        $COM_CD = get_request_param('COM_CD');
        $COM_CD_NM = get_request_param('COM_CD_NM');
        $TH1_THEM_CD = get_request_param('TH1_THEM_CD');
        $mode = get_request_param('mode');
        $msg = get_request_param('msg');

        if ($mode == 'del') {
            $COM_CD_NM = '';
            $TH1_THEM_CD = '';
        };

        $values = array(
              'COM_CD_NM' => $COM_CD_NM
            , 'TH1_THEM_CD' => $TH1_THEM_CD
        );

        $table = 'ZCMCOMMON';

        $pkvalues = array (
              'COM_TYPE' => 'COL013' // 공통 타입
            , 'COM_CD' => $COM_CD // 공통 코드값
        );

        $name_sql = "HOME_INDEX_LOCATION 관련";
        $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(502);
        }

        $mysqldb->link->commit();
        $arrRtn['code'] = 200;
        $arrRtn['msg'] = $msg;
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();
    } finally {
        echo json_encode($arrRtn);
    }
?>