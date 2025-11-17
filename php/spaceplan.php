<?php
/**
 * 파일명 : spaceplan.php
 * 내용 : space 도면
 * 최초작성날짜 : 2024/03/14
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2024/03/14    V1.0
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

        $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID');
        $GROUP = get_request_param('GROUP');
        $mode = get_request_param('mode');
        $msg = get_request_param('msg');

        $count = 1;

        if ($mode == 'del') {
            gfn_file_upload("D", '', $ATTACH_FILE_ID, $GROUP, $count);
        } else {
            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'SPACE';
            $type = 'PLAN';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $key_value = "";

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }
                
                foreach ($_FILES as $key => $val) {
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드

                        $key_value = $key;
                        
                        if ($key_value == "ATTACH") { // B1
                            $idx = $count;
                            $ATTACH_GROUP = 1;
                        } else if ($key_value == "ATTACH2") { // 1F
                            $idx = $count;
                            $ATTACH_GROUP = 2;
                        } else if ($key_value == "ATTACH3") { // 2F
                            $idx = $count;
                            $ATTACH_GROUP = 3;
                        } else if ($key_value == "ATTACH4") { // 3F
                            $idx = $count;
                            $ATTACH_GROUP = 4;
                        } else if ($key_value == "ATTACH5") { // 4F
                            $idx = $count;
                            $ATTACH_GROUP = 5;
                        } else if ($key_value == "ATTACH6") { // 별관
                            $idx = $count;
                            $ATTACH_GROUP = 6;
                        }

                        if ($GROUP != $ATTACH_GROUP) {
                            continue;
                        }

                        $arrRes = json_decode(one_file_upload($dir, $key), true);

                        if ($arrRes['code'] != 200) {
                            throw new Exception($arrRes['msg'], $arrRes['code']);
                        }

                        if (is_array($arrRes['file'])) {
                            foreach ($arrRes['file'] as $key => $val) {
                                $FIND_FILE = gfn_file_upload("T", '', $ATTACH_FILE_ID, $ATTACH_GROUP);

                                if ($FIND_FILE > 0) {
                                    gfn_file_upload("U", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                } else {
                                    gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $_SESSION['adm']['name'], $ip);
                                }
                            }
                        }
                    }
                }
            }
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