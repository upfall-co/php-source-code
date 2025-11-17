<?php
/**
 * 파일명 : secretCodeManagement.php
 * 내용 : 시크릿코드 추가
 * 최초작성날짜 : 2023/08/07
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/07    V1.0
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
        $TH1_THEM_CD = get_request_param('TH1_THEM_CD');
        $mode = get_request_param('mode');
        $msg = get_request_param('msg');

        if ($mode == 'del') {
            $TH1_THEM_CD = '';

            $values = array(
                'TH1_THEM_CD' => $TH1_THEM_CD
            );
        } else {
            gfn_isValidation(302, $TH1_THEM_CD, "시크릿코드");

            $TH1_THEM_CD = trim($TH1_THEM_CD);
            $TH1_THEM_CD = gfn_getEncrypt_ajax(gfn_encrypted($TH1_THEM_CD), $_SESSION['projectkey']);
        }

        $values = array(
            'TH1_THEM_CD' => $TH1_THEM_CD
        );

        $table = 'ZCMCOMMON';
        
        $pkvalues = array (
              'COM_TYPE' => 'COL001' // 공통 타입
            , 'COM_CD' => $COM_CD // 공통 코드값
        );

        $name_sql = "시크릿코드 관련";
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