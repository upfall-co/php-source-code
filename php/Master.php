<?php
/**
* 파일명 : Master.php
* 내용 : 마스터 페이지
* 최초작성날짜 : 2023/06/20
* 최초작성자 : 김민성
* ------------------------------------
* name       date        comment
* 김민성    2023/06/20     V1.0
*/

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;
    use Clef\SiteConfig;
    

    $arrRtn = array(
          'code' => 500
        , 'msg'  => ''
        , 'mode' => ''
        , 'url' => ''
    );

    global $emails;
    global $_title_data;


    try {
        //파라미터 정리
        $mode = get_request_param('mode');

        $emails = $config['Mcontact']['email'];

        $_title_data = SiteConfig::title_data();

        //변수 정리
        $arrRtn['mode'] = $mode;
        $arrRes = array();

        switch ($mode) {
            case 'LOGIN' :
                $arrRes = getMasterLogin();
                break;
            case 'CHK' :
                $arrRes = getMasterChk();
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }

        if ($arrRes['code'] != 200) {
            throw new Exception($arrRes['msg'], $arrRes['code']);
        }

        //성공
        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];

        if ($mode == "LOGIN") {
            $arrRtn['url'] = '/adm/Masterpage/index.php';
        } else if ($mode == "CHK") {
            $arrRtn['url'] = '/adm/Masterpage/UserInfo.php';
        } 

        dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        dieAndErrorMove($arrRtn['msg']);
    }

    /**
     * name :getMasterLogin
     * comment : 프로젝트 키 확인
     */
    function getMasterLogin() {
        try {
            $SUBKEY = get_request_param('subkey'); //프로젝트키

            if (gfn_ChkprojectKey($SUBKEY)) {
                $_SESSION['Master'] = 'Master';
            } else {
                gfn_isValidation(999, "", "프로젝트키가 다릅니다.");
            }

            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '확인되었습니다.';
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :getMasterChk
     * comment : 계정 확인
     */
    function getMasterChk() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            global $emails;
            global $_title_data;

            $MASTERKEY = get_request_param('masterkey'); // 마스터키
            $SUBKEY = get_request_param('subkey'); // 프로젝트키
            $ID = get_request_param('id'); // 아이디
    
            $table = 'adm';
    
            $ID = trim($ID);
    
            if (!empty(gfn_getMasterChkKey($MASTERKEY))) {
                if (gfn_getChkProjectKey($SUBKEY)) {
                    $sql = "
                         SELECT name
                              , department
                              , position
                              , mobile
                              , email
                              , GETDECRYPT(pw, :key) as pw
                           FROM {$table} 
                          WHERE 1
                            AND id = :id";
                
                    $name_sql = "관리자 계정 확인";
                    $clefResult = $mysqldb->get($sql, [':id' => $ID , ':key' => $SUBKEY], $name_sql);
                
                    if (!$clefResult->getResult()) {
                        gfn_isValidation(800);
                    }
                
                    $data = $clefResult->getResultSet();
                
                    if (empty($data)) {
                        gfn_isValidation(999, "", "조회되지 않는 아이디 입니다.");
                    }

                    $count = count($emails);

                    $pw = gfn_decrypted($data['pw'], $SUBKEY);

                    if ($count > 0) {
                        for ($i = 0; $i < $count; $i++) {
                            $to = $emails[$i];
                            
                            $subject = "[$_title_data] 계정 관리";
                            $path = "";
                            $fileName = "";

                            $body = <<<HTML
                                            <table border="1"  style=" width:450px;" >
                                                <tr>
                                                    <th colspan="2" style="text-align:center; background-color:darkgray;">계정 정보 내역</th>
                                                </tr>

                                                <tr>
                                                    <td style="text-align:center; width:20%;">이름</td>
                                                    <td style="padding-left:5px">{$data['name']}</td>
                                                </tr>

                                                <tr>
                                                    <td style="text-align:center; width:20%;">부서</td>
                                                    <td style="padding-left:5px">{$data['department']}</td>
                                                </tr>

                                                <tr>
                                                    <td style="text-align:center; width:20%;">직위</td>
                                                    <td style="padding-left:5px">{$data['position']}</td>
                                                </tr>

                                                <tr>
                                                    <td style="text-align:center; width:20%;">연락처</td>
                                                    <td style="padding-left:5px">{$data['mobile']}</td>
                                                </tr>

                                                <tr>
                                                    <td style="text-align:center; width:20%;">이메일</td>
                                                    <td style="padding-left:5px">{$data['email']}</td>
                                                </tr>

                                                <tr>
                                                    <td style="text-align:center; width:20%;">아이디</td>
                                                    <td style="padding-left:5px">{$ID}</td>
                                                </tr>

                                                <tr>
                                                    <td style="text-align:center; width:20%;">비밀번호</td>
                                                    <td style="padding-left:5px">{$pw}</td>
                                                </tr>
                                            </table>
                                        HTML;

                            $arrRes =  gfn_adm_send_mail($to, $subject, $body, $path, $fileName);

                            if ($arrRes) {
                                $arrRtn['code'] = 200;
                                $arrRtn['msg'] = '발송되었습니다.';
                            } else {
                                throw new Exception('발송 실패. ' . $to);
                            }
                        }
                    }
                } else {
                    gfn_isValidation(999, "", "프로젝트키가 다릅니다.");
                }
            }
        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }


    }
?>