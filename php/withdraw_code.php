<?php
/**
 * 파일명 : withdraw_code.php
 * 내용 : 회원탈퇴
 * 최초작성날짜 : 2023/12/07
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/12/07    V1.0
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
        $ID = $_SESSION['MEMBER']['ID'];

        $TYPE = "DELETE";

        $table = 'MEMBER';

        $sql = "
             SELECT ACCESS_TOKEN_KAKAO
                  , ACCESS_TOKEN_NAVER
               FROM {$table} 
              WHERE ID = :ID";

        $name_sql = "회원 상세정보";
        $clefResult = $mysqldb->get($sql, [':ID' => $ID], $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        if (empty($data)) {
            dieAndErrorMove('잘못된 접근입니다.');
        }

        $_db_ACCESS_TOKEN_KAKAO = _check_var($data['ACCESS_TOKEN_KAKAO']); // 카카오토큰
        $_db_ACCESS_TOKEN_NAVER = _check_var($data['ACCESS_TOKEN_NAVER']); // 네이버토큰

        $DIV_HTML = '';
        $TYPE_HTML = '';

        if (!empty($_db_ACCESS_TOKEN_NAVER)) {
            $DIV_HTML .= '<div class="border_btn w_500" onclick="naverLogin();"><img src="/img/naver_logo.svg" alt="naver" title="naver" /><span>네이버 연동 해제</span></div>';
        }

        if (!empty($_db_ACCESS_TOKEN_KAKAO)) {
            $DIV_HTML .= '<div class="border_btn w_500" onclick="kakaoLogin();"><img src="/img/kakao_logo.svg" alt="kakao" title="kakao" /><span>카카오 연동 해제</span></div>';
        }

        if (empty($_db_ACCESS_TOKEN_NAVER) && empty($_db_ACCESS_TOKEN_KAKAO)) {
            ob_start();
            $Type_ComboList = gfn_getComboList("탈퇴사유", "COL009", "", "S");
            $Type_ComboList = ob_get_clean();

            $TYPE_HTML = <<<DIV
                                <div class="withdraw_reason">
                                    <div class="flex">
                                        <span>탈퇴사유</span>
                                        
                                        <select name="TYPE_CD" id="TYPE_CD">
                                            {$Type_ComboList}
                                        </select>
                                    </div>
                                    <input type="text" id="CONTENT_TEXT" name="CONTENT_TEXT" class="etc_hidden" placeholder="기타사유">
                                </div>
                            DIV;

            $DIV_HTML = '<div class="border_btn w_500" onclick="onWithDrawEnd();">회원 탈퇴</div>';
        }

    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
?>