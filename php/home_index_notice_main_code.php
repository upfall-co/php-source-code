<?php
/**
 * 파일명 : home_index_notice_main_code.php
 * 내용 : index_notice 내역 code 
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

        $arrValue = array();
        $arrValue[':COM_TYPE'] = "COL012";

        $table_COM = 'ZCMCOMMON'; // 공통테이블

        $sql = "
             SELECT COM_TYPE
                  , COM_CD
                  , COM_CD_NM
                  , TH1_THEM_CD
                  , TH1_THEM_COMMENT
                  , COM_ORDER
               FROM {$table_COM}
              WHERE COM_TYPE = :COM_TYPE
              ORDER BY COM_ORDER";

        $name_sql = "HOME_INDEX_NOTICE 리스트";
        $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();

        $div_html = "";
        $count = 1;

        if (!empty($list)) {
            foreach ($list as $data) {
                $_db_COM_TYPE = _check_var($data['COM_TYPE']); // 데이터 타입
                $_db_COM_CD = _check_var($data['COM_CD']); // 벨류 코드 
                $_db_COM_CD_NM = _check_var($data['COM_CD_NM']); // 벨류 이름
                $_db_TH1_THEM_CD = _check_var($data['TH1_THEM_CD']); // TH1 참조값
                $_db_TH1_THEM_COMMENT = _check_var($data['TH1_THEM_COMMENT']); // TH1 설명
                $_db_COM_ORDER = _check_var($data['COM_ORDER']); // 벨류 정렬

                $div_html .= <<<DIV
                                    <div class="hr-line-dashed"></div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label"> {$count}번 라인 제목</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="COM_NM{$count}" name="COM_NM{$count}" value="{$_db_COM_CD_NM}" placeholder="제목을 입력해주세요." maxlength="50">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 text-right col-form-label"> {$count}번 라인 내용</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" id="TH1_NM{$count}" name="TH1_NM{$count}" value="{$_db_TH1_THEM_CD}" placeholder="내용을 입력해주세요." maxlength="50">
                                        </div>
                                        <div class="col-sm-3 custom_btn3" style = "transform: translateY(-90%);">
                                            <button class="btn btn-success dim" type="button" title="내용저장" onclick="javascript:ScodeINS('{$_db_COM_CD}', '{$count}');"><i class="fa fa-upload"></i></button>
                                            <button class="btn btn-warning dim" type="button" title="내용삭제" onclick="javascript:ScodeDel('{$_db_COM_CD}', '{$count}');"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                               DIV;

                $count++;
            }
        }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
 ?>