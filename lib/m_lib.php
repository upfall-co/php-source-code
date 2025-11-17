<?php
require_once __DIR__ . '/../lib/config.php';

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

/**
 * name : dieAndErrorMove
 * comment : alert 후 세션 기록의 바로 뒤 페이지로 이동, history.back은 history.go(-1)와 동일
 */
function dieAndErrorMove($msg) {
    die("<script type='text/javascript'>alert('" . $msg . "');history.back();</script>");
}

/**
 * name : dieAndMsgReplaceMove
 * comment : alert 후  원하는 URL 이동 (히스토리에 현재 페이지의 URL이 기록이 하지 않아서 이동 후 뒤로가기로 이동 불가)
 */
function dieAndMsgReplaceMove($url, $msg) {
    die("<script type='text/javascript'>alert('" . $msg . "');location.replace('" . $url . "');</script>");
}

/**
 * name : dieNoMsgReplaceMove
 * comment : 원하는 URL 이동 (히스토리에 현재 페이지의 URL이 기록이 하지 않아서 이동 후 뒤로가기로 이동 불가)
 */
function dieNoMsgReplaceMove($url) {
    die("<script type='text/javascript'>location.replace('" . $url . "');</script>");
}

/**
 * name : dieAndMsgReload
 * comment : 현재 접속중인 페이지를 다시 불러오는 역활
 */
function dieAndMsgReload($msg) {
    die("<script>alert('{$msg}'); location.reload();</script>");
}

/**
 * name : dieAndMsgWindowClose
 * comment : 브라우저 창이나 탭을 닫음
 */
function dieAndMsgWindowClose($msg) {
    die("<script>alert('{$msg}'); window.close();</script>");
}

/**
 * name : _br2nl
 * comment : br 태그를 단넘김으로 변경 개행처리
 */
function _br2nl($str) {
    $str = str_replace('/(<br>|<br\/>|<br \/>)/g', "\r\n", $str);

    return $str;
}

/**
 * name : get_request_param
 * comment : 해당 prarm값을 가져옴
 *           $val_name : 가져올 변수이름
 *           $method : POST , GET 설정
 *           $index : 가져올 값이 배열일때
 *           $strip_tags : 특정한 특수 문자를 hmtl로 변환값
 */
function get_request_param($val_name, $method = "POST", $index = 0, $strip_tags = "ON") {
    $return_val = null;

    if ($val_name !== "") {
        if ($method === "POST") {
            if (isset($_POST[$val_name])) {
                $return_val = $_POST[$val_name];
            } else {
                $return_val = null;
            }
        } else if (($method === 'GET') && isset($_GET[$val_name])) {
            $return_val = $_GET[$val_name];
        } else {
            $return_val = null;
        }
    }

    if (is_array($return_val) && !empty($return_val[$index])) {
        $return_val = trim($return_val[$index]) !== '' ? $return_val[$index] : null;
    } else if (is_array($return_val) && empty($return_val[$index])) {
        return null;
    }

    if ($strip_tags === 'ON') {
        $return_val = htmlspecialchars(strip_tags($return_val));
    }

    /*
     * secure code 시작
     */

    /*
     *  secure code 종료
     */

    return $return_val;
}

/**
 * name : get_is_mobile
 * comment : 모바일 접속인지 PC로 접속했는지 체크
 */
function get_is_mobile(): bool {
    $is_mobile = false;
    $mobilechk = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/i';

    $userAgent = "";

    if (isset($_SERVER['HTTP_X_CUSTOM_USER_AGENT'])) {
        // 사용자 정의 User-Agent 값
        $userAgent = $_SERVER['HTTP_X_CUSTOM_USER_AGENT'];
    } else if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
    } else {
        $userAgent = '';
    }
    
    if (preg_match($mobilechk, $userAgent)) {
        $is_mobile = true;
    }

    return $is_mobile;
}

/**
 * name : create_dir
 * comment : 디렉토리가 없을시에 해당 경로에 디렉토리 생성 권한은 755로 생성해줌
 */
function create_dir($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

/**
 * name : c_error_log
 * comment : 에러 로그 등록
 *           $path : 경로 
 *           $file : 파일이름 ex) eee.log
 *           $print : log에 넣을 값 ex) '로그 값입니다.'
 */
function c_error_log($path, $file, $print) {
    create_dir($path); //디렉토리 여부

    ini_set('log_errors', 1);
    ini_set('error_log', "{$path}/{$file}");

    error_log($print);
}

/**
 * name : file_upload_proc
 * comment : 업로드 할 파일 체크
 *           $upload_dir : 업로드 할 파일 경로
 *           $file : 업로드할 파일 이름
 *           $realname : 업로드하는 파일의 실제이름
 *           $prefix : 유니크 ID를 생성에 필요한 이름 ex) 'hello_' -> hello_61553f16824ff
 *           $file_size : 파일크기
 */
function file_upload_proc($upload_dir, $file, $realname = false, $prefix = '', $file_size = 20) {
    $uploadOk = 1;

    $path = $_SERVER['DOCUMENT_ROOT']. $upload_dir; 
    create_dir($path); //디렉토리 생성 혹은 확인

    $target_file = $_SERVER['DOCUMENT_ROOT']. $upload_dir. '/'. uniqid($prefix, false);
    @unlink($target_file);

    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $denyfile = array("sh", "exe", "php", "php3", "exe", "cgi", "phtml", "html", "htm", "pl", "asp", "jsp", "inc", "dll", "py", "py3");
    $msg = "";

    if (in_array($fileType, $denyfile)) {
        $uploadOk = 0;
        $msg = '업로드 할수 없는 파일 확장자입니다.';
    }

    $target_file .= "." . $fileType;

    if (file_exists($target_file)) {
        $uploadOk = 0;
        $msg = '이미 같은 이름의 파일이 존재합니다.';
    }

    if ($file["size"] > $file_size * 10 * 100 * 1000) {
        $uploadOk = 0;
        $msg = '파일크기가 20MB를 넘습니다. 파일크기 : ' . $file["size"];
    }

    if ($uploadOk === 0) {
        $return_msg = array();
        $return_msg[] = false;
        $return_msg[] = $msg;

        return $return_msg;
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        if ($realname) {
            $return = array();
            $return[] = basename($target_file);
            $return[] = $file["name"];
            $return[] = $fileType;
            $return[] = $file["size"];

            return $return;
        } else {
            $return = array();
            $return[] = basename($target_file);
            $return[] = basename($target_file);
            $return[] = $fileType;
            $return[] = $file["size"];

            return $return;
        }
    } else {
        $return = array();
        $msg = "파일업로드에 문제가 발생하였습니다.";
        $return[] = false;
        $return[] = $msg;

        return $return;
    }
}

/**
 * name : _p
 * comment : 미리 정의된 형식의 텍스트를 보여주며 출력 (쿼리 혹은 데이터 확인할때 좋음)
 */
function _p($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

/**
 * name : _check_var
 * comment : 값이 없을시에 null 처리 (int형은 0, string형은 '')
 */
function _check_var($var) {
    if (is_numeric($var)) { //num type
        $var = !empty($var) ? $var : 0;
    } else { //string type
        $var = isset($var) ? (string) $var : '';
    }

    return $var;
}

/**
 * name : _check_referer
 * comment : 방문기록 확인을 하여 접근 확인을 하고 해당 사실이없으면
 *           alert 생성 후 메인화면으로 돌아감
 */
function _check_referer($url) {
    if (strpos($_SERVER['HTTP_REFERER'], $url) === false) {
        dieAndMsgReplaceMove('/', '잘못된 접근입니다.');
    }
}

/**
 * name : _check_referer_ajax
 * comment : 방문기록 확인을 하여 접근 확인을 하고 해당 사실이없으면
 *           alert 생성 후 해당 이전화면으로 다시 돌아감
 */
function _check_referer_ajax($url) {
    if (strpos($_SERVER['HTTP_REFERER'], $url) === false) {
        gfn_isValidation(999, "", "잘못된 접근입니다.");
    }
}

/**
 * name : _date_gap
 * comment : 원하는 날짜부터 끝나는 날짜까지의 총 날짜들을 구함
 *           $sdate : 시작날짜
 *           $edate : 끝나는날짜
 */
function _date_gap($sdate, $edate) {
    $date = array();
    $sdate = str_replace('-', '', $sdate);
    $edate = str_replace('-', '', $edate);

    for ($i = $sdate; $i <= $edate; $i++) {
        $year = substr($i, 0, 4);
        $month = substr($i, 4, 2);
        $day = substr($i, 6, 2);

        if (checkdate($month, $day, $year)) {
            $date[$year. '-'. $month. '-'. $day] = $year. '-'. $month. '-'. $day;
        }
    }

    return $date;
}

/**
 * name : _log_login
 * comment : 로그인 로그
 *           $arrParams : 로그인 로그에 필요한 해당 유저의 pk값과 이름 
 *                        ex)$arrParams = array('mem_seq' => $_db_seq, 'name' => $_db_name);
 */
function _log_login($arrParams) {
    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');

    $mysqldb = new Clef\Pdo7();
    $clefResult = new Clef\ClefResult();

    $arrRtn = array(
          'code' => 500
        , 'msg' => ''
    );

    try {
        //변수 정리
        $values = array();
        $table = 'log_login_' . date('Y', time());

        $ip = "";

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        //DB
        $sql = "SHOW TABLES LIKE '{$table}'";
        $clefResult = $mysqldb->get($sql);
        $data = $clefResult->getResultSet();

        if (empty($data)) {
            $sql = "
                create table {$table} (
                       seq int unsigned auto_increment comment '시퀀스' primary key
                     , mem_id varchar(100) default '' not null comment '회원 아이디'
                     , reg_user varchar(30)  default '' not null comment '등록자'
                     , reg_ip varchar(30)  default '' not null comment '등록ip'
                     , reg_date timestamp default current_timestamp() null comment '등록일시'
                     , index (mem_id)
                     , index (reg_date)
                ) comment '접속확인 로그' charset = utf8";
                
            $mysqldb->query($sql);
        }

        $values = array(
                'mem_id' => $arrParams['mem_id']
              , 'reg_user' => $arrParams['name']
              , 'reg_ip' => $ip
        );

        $mysqldb->insert($table, $values);
    } catch (Exception $e) {

    }
}

/**
 * name : _search_log_login
 * comment : 사용자 접속기록 로그 값 조회
 */
function _search_log_login($mem_id) {
    require_once ($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');

    $mysqldb = new Clef\Pdo7();
    $clefResult = new Clef\ClefResult();

    try {
        //변수 정리
        $year = date('Y', time());
        $range = range(date('Y'), $year);
        $reg_date = '';

        foreach ($range as $val) {
            $table = "log_login_{$val}";

            // 사용자 접속기록 확인
            $sql = "
                 SELECT MAX(reg_date) AS MAX_REG_DATE
                   FROM {$table}
                  WHERE 1
                    AND mem_id = :mem_id";

            $name_sql = "사용자 접속기록";
            $clefResult = $mysqldb->get($sql, [':mem_id' => $mem_id], $name_sql);
            $data = $clefResult->getResultSet();
            $_db_reg_date = _check_var($data['MAX_REG_DATE']);

            if (empty($_db_reg_date)) {
                continue;
            }

            $reg_date = $_db_reg_date;
        }
    } catch (Exception $e) {

    }

    return $reg_date;
}

/**
 * name : _check_admin
 * comment : 관리자 체크
 */
function _check_admin() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'); //autoload

    $mysqldb = new Clef\Pdo7();
    $clefResult = new Clef\ClefResult();

    if (!ADM_IS_LOGIN) {
        dieAndMsgReplaceMove('../program/logout.php', '로그인 후 이용해주세요.');
    }

    $sql = "
         SELECT *
           FROM adm
          WHERE 1
            AND id = :id
          LIMIT 1";

    $name_sql = "관리자 계정 확인";
    $clefResult = $mysqldb->get($sql, [':id' => $_SESSION['adm']['id']], $name_sql);
    $data = $clefResult->getResultSet();

    if (empty($data)) {
        dieAndMsgReplaceMove('/adm/program/logout.php', '조회되지 않는 관리자입니다.');
    }

    //DB 변수 정리
    $_db_id = _check_var($data['id']);
    $_db_name = _check_var($data['name']);
    $_db_type = _check_var($data['member_type']);

    //세션 정리
    $_SESSION['adm']['id'] = $_db_id;
    $_SESSION['adm']['name'] = $_db_name;
    $_SESSION['adm']['type'] = $_db_type;

    _check_admtype($_SESSION['adm']['type']);
}

function _check_admtype($type) {
    if ($type == "MEM") {
        $_SESSION['check_type'] = false;
    } else {
        $_SESSION['check_type'] = true;
    }
}

/**
 * name : _generator_pw
 * comment : 임시 비밀번호
 */
function _generator_pw($length = 12) {
    $password = "";
    $arrStr = array();
    $counter = ceil($length / 4);
    $counter = ($counter > 0) ? $counter : 1;

    //문자열 배열
    $arrCharList = array(
          array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0")
        , array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z")
        //array("!", "@", "#", "%", "^", "&", "*")
    );

    $char_cnt = count($arrCharList);

    for ($i = 0; $i < $counter; $i++) {
        for ($j = 0; $j < $char_cnt; $j++) {
            $list = $arrCharList[$j];
            $char = $list[array_rand($list)];
            $pattern = '/^[a-z]$/';

            if (preg_match($pattern, $char)) { //a-z일 경우에는 새로운 문자를 하나 선택 후 배열에 넣는다.
                array_push($arrStr, strtoupper($list[array_rand($list)]));
            }

            array_push($arrStr, $char);
        }
        
        shuffle($arrStr); //배열의 순서를 바꿔준다.

        for ($j = 0; $j < count($arrStr); $j++) { //password에 붙인다.
            $password .= $arrStr[$j];
        }
    }

    return substr($password, 0, $length); //길이 조정 후 return
}

/**
 * name : multiple_file_upload
 * comment : 다중 파일 업로드
 *           $idx_nm : 업로드 할 파일의 배열값
 *           $prefix : 유니크 ID를 생성에 필요한 이름 ex) 'hello_' -> hello_61553f16824ff
 *           $file_size : 파일크기
 */
function multiple_file_upload($dir, $idx_nm, $prefix = '') {
    $arrRtn = array(
            'code' => 500
          , 'msg' => ''
          , 'file' => array()
    );

    try {
        $arrFile = array();
        $allowed_ext = array("sh", "exe", "php", "php3", "exe", "cgi", "phtml", "html", "htm", "pl", "asp", "jsp", "inc", "dll", "py", "py3");
        $str_allowed_ext = implode(', ', $allowed_ext);

        foreach ($_FILES[$idx_nm]['name'] as $key => $val) { //파일 체크
            if ($_FILES[$idx_nm]['size'][$key] > 0) {
                $path = $_SERVER['DOCUMENT_ROOT']. $dir;
                create_dir($path); //파일 디렉토리 체크

                if ($_FILES[$idx_nm]['size'][$key] > 20 * 1024 * 1024) { //파일 사이즈 체크
                    gfn_isValidation(401, "", "파일 용량은 20MB 까지만 가능합니다.");
                }

                $file_info = pathinfo($_FILES[$idx_nm]['name'][$key]);
                $ext = strtolower($file_info['extension']);

                if (in_array($ext, $allowed_ext)) { //파일 확장자 체크
                    gfn_isValidation(402, "", "첨부 파일은 {$str_allowed_ext} 확장자만 가능합니다.");
                }

                $target_file = $_SERVER['DOCUMENT_ROOT']. $dir. '/'. uniqid($prefix, false). '.'. $ext;
                @unlink($target_file);

                if (file_exists($target_file)) {
                    gfn_isValidation(403, "", "이미 같은 이름의 파일이 존재합니다.");
                }

                if (!move_uploaded_file($_FILES[$idx_nm]['tmp_name'][$key], $target_file)) {
                    gfn_isValidation(404);
                }

                $arrFile[] = array(
                        'tmp_name' => basename($target_file)
                      , 'name' => $_FILES[$idx_nm]['name'][$key]
                      , 'ext' => $ext
                      , 'size' => $_FILES[$idx_nm]['size'][$key]
                );
            }
        }

        $arrRtn['code'] = 200; //성공
        $arrRtn['file'] = $arrFile;
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();
    } finally {
        return json_encode($arrRtn);
    }
}

/**
 * name : one_file_upload
 * comment : 단일 파일 업로드
 *           $idx_nm : 업로드 할 파일의 배열값
 *           $prefix : 유니크 ID를 생성에 필요한 이름 ex) 'hello_' -> hello_61553f16824ff
 *           $file_size : 파일크기
 */
function one_file_upload($dir, $idx_nm, $prefix = '') {
    $arrRtn = array(
            'code' => 500
          , 'msg' => ''
          , 'file' => array()
    );

    try {
        $arrFile = array();
        $allowed_ext = array("sh", "exe", "php", "php3", "exe", "cgi", "phtml", "html", "htm", "pl", "asp", "jsp", "inc", "dll", "py", "py3");
        $str_allowed_ext = implode(', ', $allowed_ext);

        if ($_FILES[$idx_nm]['size'] > 0) {
            $path = $_SERVER['DOCUMENT_ROOT']. $dir;
            create_dir($path); //파일 디렉토리 체크

            if ($_FILES[$idx_nm]['size'] > 20 * 1024 * 1024) { //파일 사이즈 체크
                gfn_isValidation(401, "", "파일 용량은 20MB 까지만 가능합니다.");
            }

            $file_info = pathinfo($_FILES[$idx_nm]['name']);
            $ext = strtolower($file_info['extension']);

            if (in_array($ext, $allowed_ext)) { //파일 확장자 체크
                gfn_isValidation(402, "", "첨부 파일은 {$str_allowed_ext} 확장자만 가능합니다.");
            }

            $target_file = $_SERVER['DOCUMENT_ROOT']. $dir. '/'. uniqid($prefix, false). '.'. $ext;
            @unlink($target_file);

            if (file_exists($target_file)) {
                gfn_isValidation(403, "", "이미 같은 이름의 파일이 존재합니다.");
            }

            if (!move_uploaded_file($_FILES[$idx_nm]['tmp_name'], $target_file)) {
                gfn_isValidation(404);
            }

            $arrFile[] = array(
                       'tmp_name' => basename($target_file)
                    , 'name' => $_FILES[$idx_nm]['name']
                    , 'ext' => $ext
                    , 'size' => $_FILES[$idx_nm]['size']
            );
        }

        $arrRtn['code'] = 200; //성공
        $arrRtn['file'] = $arrFile;
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();
    } finally {
        return json_encode($arrRtn);
    }
}

/**
 * name : curl_f
 * comment : 내가 원하는 주소의 페이지에 임의의 값을 전달하고 리턴 값을 받아오는 역할
 *           $url : 경로
 *           $method : POST, GET
 *           $arrHeaders : hearder값의 tpye 지정 배열값 ex) array('application/x-www-form-urlencoded;charset=utf-8');
 *           $arrFields : 해당 url에 지정할 파라미터값들 ex ) $arrParams = array('client_id' => NAVER_LOGIN_CLIENT_ID);
 *           $type : nuul [사용하지 않음]
 */
function curl_f($url, $method, $arrHeaders, $arrFields, $type = '') {
    $curl_data = array();
    $str_postfields = http_build_query($arrFields, '', '&');

    $ch = curl_init(); //초기화

    curl_setopt($ch, CURLOPT_URL, $url); //URL 지정
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaders); //header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //요청 결과를 문자열로 반환
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //connection timeout 5초
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //curl 실행 timeout 10초
    curl_setopt($ch, CURLOPT_NOSIGNAL, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true); //true 시 post 전송
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str_postfields); //POST data
    } else {

    }

    $response = curl_exec($ch);
    $get_info = curl_getinfo($ch, CURLINFO_HTTP_CODE); //http 상태 코드

    if (curl_error($ch)) {
        $curl_data = null;
        $get_info = null;
    } else {
        $curl_data[0] = $get_info;
        $curl_data[1] = $response;
    }

    curl_close($ch);

    return $curl_data;
}

/**
 * name : _file_size_print
 * comment : 해당 경로에 있는 파일의 사이즈값을 return 시킴
 */
function _file_size_print($path) {
    $file_size = filesize($path);

    if ($file_size < 1024) {
        $file_size = floor($file_size). 'B';
    } else if ($file_size < 1024 * 1024) {
        $file_size = floor(($file_size / 1024)). 'KB';
    } else if ($file_size < 1024 * 1024 * 1024) {
        $file_size = floor(($file_size / (1024 * 1024))). 'MB';
    } else {
        $file_size = floor(($file_size / (1024 * 1024 * 1024))). 'G';
    }

    return $file_size;
}

/**
 * name : _common_code_list
 * comment : 공통 코드 리스트
 */
function _common_code_list($code_type) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

    $mysqldb = new Clef\Pdo7();
    $clefResult = new Clef\ClefResult();

    $arrRtn = array(
            'code' => 500
          , 'msg' => ''
          , 'list' => array()
    );

    try {
        if (empty($code_type)) { //파라미터 체크
            gfn_isValidation(999, "", "코드 타입 값이 없습니다.");
        }

        $sql = "
             SELECT *
			   FROM ZCMCOMMON
			  WHERE COM_TYPE = :code_type";

        $clefResult = $mysqldb->select($sql, [':code_type' => $code_type]);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $list = $clefResult->getResultSet();

        $arrRtn['code'] = 200; //성공
        $arrRtn['list'] = $list;
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();
    } finally {
        return $arrRtn;
    }
}

/**
 * name : gfn_query_log
 * comment : 현 selet, delete문의 규칙은 이걸로 적용이 가능하며 실행되는 쿼리를 console.log에 출력해줌
 *           $name_sql : 쿼리 이름 ex) 프로젝트 조회
 *           $sql : 쿼리문(작성한 쿼리문을 추가해주면됨) 
 *           $params : 해당 쿼리문의 value값 :sql등
 */
function gfn_query_log($name_sql = null, $sql, $params = null) {
    $query_log = $_SERVER['SCRIPT_FILENAME']. " : ";

    if ($name_sql) {
        $query_log .= $name_sql. " : ";
    } else {
        $query_log .= "name_sql 지정 필요 : ";
    }

    $sql_value = str_replace("\r\n", "", $sql);
    $sql_value = preg_replace('/\s+/', ' ', $sql_value);
    $sql_value = trim($sql_value);
    $query_log .= $sql_value . ";";

    if ($params) {
        foreach ($params as $key => $value) {
            $query_log = preg_replace('/('. $key.')/', "'". $value. "'", $query_log, 1);
        }
    }

    gfn_sql_log($query_log);
}

/**
 * name : gfn_query_log2
 * comment : 현 insert, update문의 규칙은 이걸로 적용이 가능하며 실행되는 쿼리를 console.log에 출력해줌
 *           $name_sql : 쿼리 이름 ex) 프로젝트 조회
 *           $sql : 쿼리문(작성한 쿼리문을 추가해주면됨) 
 *           $params : 해당 쿼리문의 value값 :sql등
 */
function gfn_query_log2($name_sql = null, $sql, $params = null, $pkParam = null) {
    $query_log = $_SERVER['SCRIPT_FILENAME']. " : ";

    if ($name_sql) {
        $query_log .= $name_sql. " : ";
    } else {
        $query_log .= "name_sql 지정 필요 : ";
    }

    $sql_value = str_replace("\r\n", "", $sql);
    $sql_value = preg_replace('/\s+/', ' ', $sql_value);
    $sql_value = trim($sql_value);
    $query_log .= $sql_value . ";";

    if ($params) {
        foreach ($params as $key => $value) {
            $query_log = preg_replace('/(:'. $key.')/', "'". $value. "'", $query_log, 1);
        }
    }

    if ($pkParam) {
        foreach ($pkParam as $key => $value) {
            $query_log = preg_replace('/(:'. $key.')/', "'". $value. "'", $query_log, 1);
        }
    }

    gfn_sql_log($query_log);
}

/**
 * name : gfn_sql_log
 * comment : sql 로그 보관
 */
function gfn_sql_log($query_log) {
    if (strpos($_SERVER['PHP_SELF'], '/Masterpage/UserInfo.php') === false &&
        strpos($_SERVER['PHP_SELF'], '/Masterpage/index.php') === false &&
        strpos($_SERVER['PHP_SELF'], '/php/Master.php') === false) {
        $log_path = $_SERVER['DOCUMENT_ROOT']. '/lib/log';

        $date = date("Ymd");
        $log_file = 'sql_check'. $date. '.log';
    
        $query_log .= "\n------------------------------------------------------------------------------------end-----------------------------------------------------------------------";
        c_error_log($log_path, $log_file, $query_log);
    }
}

/**
 * name : Console_log
 * comment : php에서 log 사용
 */
function Console_log($data){
    echo "<script>console.log('$data');</script>";
}

/**
 * name :gfn_getZcmcommonVal
 * comment : 공통코드에서 원하는 값 추출
 *           $COM_TYPE : 공통코드 번호 ex> 'AD0001'
 *           $COM_CD : $COM_TYPE에 해당하는 COM_CD값 번호 ex> '001'
 *           $VAL : SELECT 문에서 원하는 값 추출 ex) TH2_THEM_CD
 *           $LANG_CD : 언어구분값(기본값 = KOR)
 */
function gfn_getZcmcommonVal($COM_TYPE, $COM_CD, $VAL=NULL, $LANG_CD=NULL) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    try {
        $table_COM = 'ZCMCOMMON'; // 공통테이블

        $where = '';

        if (empty($COM_CD)) {
            gfn_isValidation(999, "", "COM_CD 누락");
        }

        if (empty($VAL)) {
            $VAL = 'COM_CD_NM';
        }

        if (empty($LANG_CD)) {
            $LANG_CD = 'KOR';
        }

        if (!empty($LANG_CD)) {
            $where .= "AND LANG_CD = '{$LANG_CD}'";
        }

        if (!empty($COM_CD)) {
            $where .= "AND COM_CD = '{$COM_CD}'";
        }

        $sql = "
             SELECT {$VAL} AS TEMP
              FROM {$table_COM}
             WHERE COM_TYPE = '{$COM_TYPE}'
               AND COM_CD = '{$COM_CD}'
               {$where}";
        
        $name_sql = "공통 값 검색";
        $clefResult = $mysqldb->get($sql, null, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        return $data['TEMP'];
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
}

/**
 * name :gfn_getComboList
 * comment : 콤보박스에 사용하는 값을 콤보박스 값으로 출력
 *           $name_sql : sql name
 *           $COM_TYPE : 공통코드 번호 ex> 'AD0001'
 *           $type_gb : 셀렉박스의 selected를 진행할때 사용
 *           $CHOICE : 콤보리스트 첫번째 행의 기본 옵션 'A' : 전체, 'S' : '선택' , 그 외의값 EX) '언어구분' 이런식으로 작성시 초기값을 바로 셋팅해줌
 *           $LANG_CD : 언어구분값(기본값 = KOR)
 *           $ORDER : ORDER BY기준이 다른경우 작성 EX) COM_CD DESC,COM_TYPE
 *           $TH1_THEM_CD : TH1_THEM_CD의 해당 공통코드의 관련코드를 입력하여 필요한데이터만 가져올수있음
 *           $TH2_THEM_CD : TH2_THEM_CD의 해당 공통코드의 관련코드를 입력하여 필요한데이터만 가져올수있음
 *           $TH3_THEM_CD : TH3_THEM_CD의 해당 공통코드의 관련코드를 입력하여 필요한데이터만 가져올수있음
 */
function gfn_getComboList($name_sql=NULL, $COM_TYPE, $type_gb=NULL ,$CHOICE=NULL, $LANG_CD=NULL, $ORDER=NULL, $TH1_THEM_CD=NULL, $TH2_THEM_CD=NULL, $TH3_THEM_CD=NULL) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    try {
        if (empty($COM_TYPE)) {
            return;
        }

        $table_COM = 'ZCMCOMMON'; // 공통테이블

        $where = '';
        $order = 'ORDER BY COM_ORDER';

        if (empty($LANG_CD)) {
            $LANG_CD = 'KOR';
        }

        if (!empty($LANG_CD)) {
            $where .= "AND LANG_CD = '{$LANG_CD}'";
        }

        if (!empty($TH1_THEM_CD)) {
            $where .= "AND TH1_THEM_CD LIKE '%{$TH1_THEM_CD}%'";
        }

        if (!empty($TH2_THEM_CD)) {
            $where .= "AND TH2_THEM_CD LIKE '%{$TH2_THEM_CD}%'";
        }

        if (!empty($TH3_THEM_CD)) {
            $where .= "AND TH3_THEM_CD LIKE '%{$TH3_THEM_CD}%'";
        }

        if (!empty($ORDER)) {
            $order = "ORDER BY {$ORDER}";
        }

        $sql = "
             SELECT COM_CD
                  , COM_CD_NM
                  , TH1_THEM_CD
                  , TH2_THEM_CD
                  , TH3_THEM_CD
              FROM {$table_COM}
             WHERE COM_TYPE = '{$COM_TYPE}'
               {$where}
               {$order}";

        $clefResult = $mysqldb->select($sql, null, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list_arry = $clefResult->getResultSet();

        $COM_CD = '';
        $COM_CD_NM = '';
        $choice_val = '';
        $selected = '';

        if (!empty($CHOICE)) {
            if ($CHOICE == "A") {
                $choice_val = "전체";
            } else if ($CHOICE == "S") {
                $choice_val = "선택";
            } else {
                $choice_val = $CHOICE;
            }

            echo <<<OPTION
                        <option value="" >{$choice_val}</option>
                    OPTION;
        }

        foreach ($combo_list_arry as $array_val) {
            $data_code = '';

            foreach ($array_val as $key => $val) {
                if ($key == 'COM_CD') {
                    $COM_CD = $val;
                } else if ($key == 'COM_CD_NM') {
                    $COM_CD_NM = $val;
                } else if ($key == 'TH1_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code1='$val' ";
                    }
                } else if ($key == 'TH2_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code2='$val' ";
                    }
                } else if ($key == 'TH3_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code3='$val' ";
                    }
                }
            }

            if (!empty($type_gb)) {
                $selected = ($type_gb == $COM_CD) ? 'selected="selected"' : '';
            }
            
            echo <<<OPTION
                        <option value="{$COM_CD}" $data_code $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
}

/**
 * name :gfn_getComboListCustom
 * comment : 콤보박스에 사용하는 값을 콤보박스 값으로 출력 (value값과 보이는값을 원하는데로 변경가능)
 *           $name_sql : sql name
 *           $COM_TYPE : 공통코드 번호 ex> 'AD0001'
 *           $type_gb : 셀렉박스의 selected를 진행할때 사용
 *           $CHOICE : 콤보리스트 첫번째 행의 기본 옵션 'A' : 전체, 'S' : '선택' , 그 외의값 EX) '언어구분' 이런식으로 작성시 초기값을 바로 셋팅해줌
 *           $LANG_CD : 언어구분값(기본값 = KOR)
 *           $ORDER : ORDER BY기준이 다른경우 작성 EX) COM_CD DESC,COM_TYPE
 *           $TH1_THEM_CD : TH1_THEM_CD의 해당 공통코드의 관련코드를 입력하여 필요한데이터만 가져올수있음
 *           $TH2_THEM_CD : TH2_THEM_CD의 해당 공통코드의 관련코드를 입력하여 필요한데이터만 가져올수있음
 *           $TH3_THEM_CD : TH3_THEM_CD의 해당 공통코드의 관련코드를 입력하여 필요한데이터만 가져올수있음
 *           $COM_CD_VAL : value값을 다른 공통의 컬럼값으로 사용가능
 *           $COM_CD_NM_VAL : 원하는 값으로 콤보리스트에 보여지는값을 설정가능
 */
function gfn_getComboListCustom($name_sql=NULL, $COM_TYPE, $type_gb=NULL ,$CHOICE=NULL, $LANG_CD=NULL, $ORDER=NULL, $TH1_THEM_CD=NULL, $TH2_THEM_CD=NULL, $TH3_THEM_CD=NULL, $COM_CD_VAL=NULL, $COM_CD_NM_VAL=NULL) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    try {
        if (empty($COM_TYPE)) {
            return;
        }

        $table_COM = 'ZCMCOMMON'; // 공통테이블

        $where = '';
        $order = 'ORDER BY COM_ORDER';

        $COM_CD = "COM_CD";
        $COM_CD_NM = "COM_CD_NM";

        if (empty($LANG_CD)) {
            $LANG_CD = 'KOR';
        }

        if (!empty($LANG_CD)) {
            $where .= "AND LANG_CD = '{$LANG_CD}'";
        }

        if (!empty($TH1_THEM_CD)) {
            $where .= "AND TH1_THEM_CD LIKE '%{$TH1_THEM_CD}%'";
        }

        if (!empty($TH2_THEM_CD)) {
            $where .= "AND TH2_THEM_CD LIKE '%{$TH2_THEM_CD}%'";
        }

        if (!empty($TH3_THEM_CD)) {
            $where .= "AND TH3_THEM_CD LIKE '%{$TH3_THEM_CD}%'";
        }

        if (!empty($ORDER)) {
            $order = "ORDER BY {$ORDER}";
        }


        if (!empty($COM_CD_VAL)) {
            $COM_CD = $COM_CD_VAL. " AS COM_CD";
        }

        if (!empty($COM_CD_NM_VAL)) {
            $COM_CD_NM = $COM_CD_NM_VAL. " AS COM_CD_NM";
        }

        $sql = "
             SELECT {$COM_CD}
                  , {$COM_CD_NM}
                  , TH1_THEM_CD
                  , TH2_THEM_CD
                  , TH3_THEM_CD
              FROM {$table_COM}
             WHERE COM_TYPE = '{$COM_TYPE}'
               {$where}
               {$order}";

        $clefResult = $mysqldb->select($sql, null, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $combo_list_arry = $clefResult->getResultSet();

        $COM_CD = '';
        $COM_CD_NM = '';
        $choice_val = '';
        $selected = '';

        if (!empty($CHOICE)) {
            if ($CHOICE == "A") {
                $choice_val = "전체";
            } else if ($CHOICE == "S") {
                $choice_val = "선택";
            } else {
                $choice_val = $CHOICE;
            }

            echo <<<OPTION
                        <option value="" >{$choice_val}</option>
                    OPTION;
        }

        foreach ($combo_list_arry as $array_val) {
            $data_code = '';

            foreach ($array_val as $key => $val) {
                if ($key == 'COM_CD') {
                    $COM_CD = $val;
                } else if ($key == 'COM_CD_NM') {
                    $COM_CD_NM = $val;
                } else if ($key == 'TH1_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code1='$val' ";
                    }
                } else if ($key == 'TH2_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code2='$val' ";
                    }
                } else if ($key == 'TH3_THEM_CD') {
                    if (!empty($val)) {
                        $data_code .= "data-code3='$val' ";
                    }
                }
            }

            if (!empty($type_gb)) {
                $selected = ($type_gb == $COM_CD) ? 'selected="selected"' : '';
            }
            
            echo <<<OPTION
                        <option value="{$COM_CD}" $data_code $selected>{$COM_CD_NM}</option>
                    OPTION;
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
}

/**
 * name : gfn_file_upload
 * comment : ZCMFILEA 테이블에 파일 데이터 저장
 *           $dir : 파일경로
 *           $ATTACH_FILE_ID : 파일아이디
 *           $GROUP : 파일업로드당 파일업로드 아이디 값 ex) 하나의 파일업로일시는 1부터 시작하여 input이 두개일시는 1,2 등등
 *           $GROUP_COUNT : 파일업로드당 파일업로드 아이디가 가지고잇는 카운트 ex) 파일의 개수라 생각하면됨
 *           $val : 파일 데이터가 들어있는 값 data_type이 "I" 와 "U"일때 사용
 *           $user : 추가 및 수정한 사람의 사용자 아이디
 *           $ip : 추가한 위치의 IP
 *           $data_type : ["I"  -> 파일업로드 추가]
 *                        ["U"  -> 파일업로드 수정]
 *                        ["D"  -> 파일업로드 삭제]
 *                        ["T"  -> 파일업로드 토탈 개수]
 *                        ["S"  -> 파일업로드 조회]
 *           $ORDER : ORDER BY기준이 다른경우 작성 EX) COM_CD DESC,COM_TYPE
 */
function gfn_file_upload($data_type, $dir=NULL, $ATTACH_FILE_ID, $GROUP=NULL, $GROUP_COUNT=NULL, $val=NULL, $user=NULL, $ip=NULL, $ORDER=NULL) {
    if (empty($ATTACH_FILE_ID) ||  empty($data_type)) {
        return;
    }

    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    try {
        if ($data_type == "I") {
            if (empty($dir) || empty($val)) {
                return;
            }

            if (!isset($GROUP) || is_null($GROUP) || $GROUP === "") {
                $GROUP = 1;
            }

            if (!isset($GROUP_COUNT) || is_null($GROUP_COUNT) || $GROUP_COUNT === "") {
                $GROUP_COUNT = 0;
            }
            
            $values = array (
                    'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 유지보수 시퀀스 값
                  , 'ATTACH_GROUP' => $GROUP // 파일업로드 아이디 값 파일업로드당
                  , 'ATTACH_GROUP_COUNT' => $GROUP_COUNT // 해당 파일업로드의 파일 개수
                  , 'ATTACH_FILE_TEMP_NAME' => $val['tmp_name'] // 파일업로드 가상이름
                  , 'ATTACH_FILE_REAL_NAME' => $val['name'] // 파일업로드하는 실제파일이름
                  , 'ATTACH_FILE_SIZE' => $val['size'] // 파일 사이즈
                  , 'ATTACH_FILE_PATH' => $dir // 파일 경로
                  , 'ATTACH_FILE_TYPE' => $val['ext'] // 파일 타입
                  , 'reg_user' => $user // 등록자
                  , 'reg_ip' => $ip // 등록자 아이피
                  , 'reg_date' => date('Y-m-d H:i:s') // 등록 날짜
            );

            $name_sql = $ATTACH_FILE_ID. " 파일업로드 추가";
            $clefResult = $mysqldb->insert('ZCMFILEA', $values, $name_sql);
        } else if ($data_type == "U") {
            if (empty($dir) || empty($val)) {
                return;
            }

            if (!isset($GROUP) || is_null($GROUP) || $GROUP === "") {
                $GROUP = 1;
            }

            if (!isset($GROUP_COUNT) || is_null($GROUP_COUNT) || $GROUP_COUNT === "") {
                $GROUP_COUNT = 0;
            }

            $values = array (
                    'ATTACH_FILE_TEMP_NAME' => $val['tmp_name'] // 파일업로드 가상이름
                  , 'ATTACH_FILE_REAL_NAME' => $val['name'] // 파일업로드하는 실제파일이름
                  , 'ATTACH_FILE_SIZE' => $val['size'] // 파일 사이즈
                  , 'ATTACH_FILE_PATH' => $dir // 파일 경로
                  , 'ATTACH_FILE_TYPE' => $val['ext'] // 파일 타입
                  , 'mod_user' => $user // 등록자
                  , 'mod_ip' => $ip // 등록자 아이피
                  , 'mod_date' => date('Y-m-d H:i:s') // 등록 날짜
            );

            $pkvalues = array (
                    'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 유지보수 시퀀스 값
                  , 'ATTACH_GROUP' => $GROUP // 파일업로드 아이디 값 파일업로드당
                  , 'ATTACH_GROUP_COUNT' => $GROUP_COUNT // 해당 파일업로드의 파일 개수
            );
            
            $name_sql = $ATTACH_FILE_ID. " 파일업로드 수정";
            $clefResult = $mysqldb->update('ZCMFILEA', $values, $pkvalues, $name_sql);
        } else if ($data_type == "D") {
            if (!isset($GROUP) || is_null($GROUP) || $GROUP === "") {
                gfn_isValidation(999, "", "GROUP 누락");
            }

            if (!isset($GROUP_COUNT) || is_null($GROUP_COUNT) || $GROUP_COUNT === "") {
                gfn_isValidation(999, "", "GROUP_COUNT 누락");
            }

            $sql = "
                 DELETE FROM ZCMFILEA
                  WHERE ATTACH_FILE_ID = '{$ATTACH_FILE_ID}'
                    AND ATTACH_GROUP = '{$GROUP}'
                    AND ATTACH_GROUP_COUNT = :pk";
       
            $name_sql = $ATTACH_FILE_ID. " 파일업로드 삭제 ";
            $clefResult = $mysqldb->delete($sql, [':pk' => $GROUP_COUNT], $name_sql);
        } else if ($data_type == "T") {
            $where = '';

            if (!empty($GROUP)) {
                $where .= "AND ATTACH_GROUP = '{$GROUP}'";
            }

            if (!empty($GROUP_COUNT)) {
                $where .= "AND ATTACH_GROUP_COUNT = '{$GROUP_COUNT}'";
            }

            $sql = " 
                 SELECT *
                   FROM ZCMFILEA
                  WHERE ATTACH_FILE_ID = :ATTACH_FILE_ID
                    {$where}";

            $name_sql = $ATTACH_FILE_ID. " 파일확인 개수 확인";
            $clefResult = $mysqldb->count($sql, [':ATTACH_FILE_ID' => $ATTACH_FILE_ID], $name_sql);
        } else if ($data_type == "S") {
            $where = '';

            if (!empty($GROUP)) {
                $where .= "AND ATTACH_GROUP = '{$GROUP}'";
            }

            if (!empty($GROUP_COUNT)) {
                $where .= "AND ATTACH_GROUP_COUNT = '{$GROUP_COUNT}'";
            }

            $order = 'ORDER BY ATTACH_FILE_ID, ATTACH_GROUP, ATTACH_GROUP_COUNT';

            if (!empty($ORDER)) {
                $order = "ORDER BY {$ORDER}";
            }
            
            $sql = "
                 SELECT ATTACH_FILE_ID
                      , ATTACH_GROUP
                      , ATTACH_GROUP_COUNT
                      , ATTACH_FILE_TEMP_NAME
                      , ATTACH_FILE_REAL_NAME
                      , ATTACH_FILE_SIZE
                      , ATTACH_FILE_PATH
                      , ATTACH_FILE_TYPE
                   FROM ZCMFILEA
                  WHERE ATTACH_FILE_ID = :ATTACH_FILE_ID
                    {$where}
                    {$order}";
        
            $name_sql = $ATTACH_FILE_ID. " 파일업로드 조회";
            $clefResult = $mysqldb->select($sql, [':ATTACH_FILE_ID' => $ATTACH_FILE_ID], $name_sql);
        }

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        if ($data_type == "T") {
            $total = $clefResult->getCount();

            return $total;
        } else if ($data_type == "S") {
            $file_list = $clefResult->getResultSet();
          
            return $file_list;
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
}

/**
 * name : gfn_excel_Download
 * comment : 엑셀 다운로드
 *           $headers : 엑셀 헤드부분 (데이터의 제목) 
 *                      ex) ['번호', '작성일', '이름'];
 *           $arrData : 데이터 내용 배열로 가져옴 
 *                      ex) [[1, '2021-10-10', 'tester1'],[1, '2021-10-10', 'tester1']] 해당 식으로 데이터가 보관되어야함
 *           $column : 엑셀의 시작위치를 지정 ex) 'A' OR 'B'등
 *           $cells :  엑셀 시작위치의 열을 지정 ex) 1 or 2등
 *           $rowNum : 엑셀 데이터의 시작 행을 지정 ex) 2 or 3등
 *           $sheet_name : 시트명
 */
function gfn_excel_Download($total, $ex_TITLE) {
    $js_code = '
        //엑셀 다운로드
        function excelDownload() {
            if ('.$total.' > 0) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "/php/temp/excel_Download.php", true);
                xhr.responseType = "arraybuffer";
                xhr.onload = function() {
                    if (this.status === 200) {
                        var blob = new Blob([this.response], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
                        var link = document.createElement("a");
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "'.$ex_TITLE.'";
                        link.click();
                    }
                };
                xhr.send();
            } else {
                alert("해당 데이터가 존재하지 않습니다.");
                return;
            }
        }

    ';

    return $js_code;
}

/**
 * name : formatPhoneNumber
 * comment : 연락처 php로 처리
 */
function formatPhoneNumber($phoneNumber) {
    // 앞 2자리를 추출하여 지역번호를 확인합니다.
    $areaCode = substr($phoneNumber, 0, 2);
  
    // 지역번호에 따라 전화번호 형식을 다르게 설정합니다.
    switch ($areaCode) {
      case "02":
        // 서울 지역번호인 경우
        return substr($phoneNumber, 0, 2) . "-" . substr($phoneNumber, 2, 4) . "-" . substr($phoneNumber, 6);
      case "031":
        // 경기도 부천, 성남, 안양, 수원 등 지역번호인 경우
        return substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 3) . "-" . substr($phoneNumber, 6);
      case "032":
        // 인천 지역번호인 경우
        return substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7);
      case "042":
        // 대전 지역번호인 경우
        return substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 3) . "-" . substr($phoneNumber, 6);
      case "051":
        // 부산 지역번호인 경우
        return substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7);
      default:
        // 그 외 지역번호인 경우
        return substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7);
    }
}

/**
 * name : gfn_alimtalk_token
 * comment : 알리고 문자 토큰 발급
 *          $apikey : 발급받은 key (발급키)
 *          $userid : 사용자[회사명] (Identifier)
 */
function gfn_alimtalk_token($apikey, $userid) {
    $_apiURL = 'https://kakaoapi.aligo.in/akv10/token/create/30/s/';
    $_hostInfo = parse_url($_apiURL);
    $_port =   (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables = array(
          'apikey' => $apikey
        , 'userid' => $userid
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // 리턴 JSON 문자열 확인
    //print_r($ret . PHP_EOL);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    $token = $retArr->token;
    return $token;
}

/**
 * name : gfn_alimtalk_send
 * comment : 알리고 알림톡 발송
 *          $Info_value : key값모음 및 필요값
 *              apikey : 발급받은 key (발급키) [필수 값]
 *              userid : 사용자[회사명] (Identifier) [필수 값]
 *              token : gfn_alimtalk_token로 발급받은 토큰값 [필수 값]
 *              senderkey : 카카오에서 발급받은 key (Senderkey) [필수 값]
 *              tpl_code : 작성한 템플릿 (템플릿 관리 -> 템플릿코드) [필수 값]
 * 
 *          $Body_value : 문자 내용 및 수신자,발신자 내역
 *              sender : 수신자 [필수 값]
 *              senddate : 예약 알림톡시간
 *              moblie : 발신자 연락처 [필수 값]
 *              name : 발신자명 
 *              title : 알림톡 제목 [필수 값]
 *              emtitle : 알림톡 강조표기형일때 제목부분 
 *              message : 템플릿 내용의 변수값 모음 json처리로 발송 [필수 값]
 *                      ex) $message = array("이벤트명" => $EVENT_NAME, "고객명" => $NAME, "고객연락처" => $MOBILE, "신청일자" => date('Y-m-d H:i:s'));
 *                          해당 내용은 템플릿 tpl_code에 해당하는 변수명으로 작성이 필요함
 *              button : 해당 내용은 템플릿 tpl_code에 해당하는 버튼변수 모음 json 처리로 발송
 *                      ex) {"button":[{"name":"테스트 버튼","linkType":"DS"}]}
 *              failover : 대체문자 여부 (기본값은 N)
 *              testMode : 테스트 기능 (기본값은 N)
 */
function gfn_alimtalk_send($Info_value, $Body_value) {
    /* 
    -----------------------------------------------------------------------------------
    알림톡 전송
    -----------------------------------------------------------------------------------
    버튼의 경우 템플릿에 버튼이 있을때만 버튼 파라메더를 입력하셔야 합니다.
    버튼이 없는 템플릿인 경우 버튼 파라메더를 제외하시기 바랍니다.
    */
    $content = gfn_alimtalk_template($Info_value);

    $templtTitle =  $content->templtTitle;
    $emtitle = $templtTitle;

    if (!empty($templtTitle)) {
        foreach ($Body_value['emtitle'] as $key => $value) {
            $emtitle = preg_replace('/#\{' . preg_quote($key, '/') . '\}/', $value, $emtitle);
        }
    }

    $templtContent = $content->templtContent;
    $message = $templtContent;

    foreach ($Body_value['message'] as $key => $value) {
        $message = preg_replace('/#\{' . preg_quote($key, '/') . '\}/', $value, $message);
    }

    $_apiURL =   'https://kakaoapi.aligo.in/akv10/alimtalk/send/';
    $_hostInfo =   parse_url($_apiURL);
    $_port =   (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables =   array(
          'apikey' => $Info_value['apikey']
        , 'userid' => $Info_value['userid']
        , 'token' => $Info_value['token']
        , 'senderkey' => $Info_value['senderkey']
        , 'tpl_code' => $Info_value['tpl_code']
        , 'sender' => $Body_value['sender']
        , 'senddate' => $Body_value['senddate']//date("YmdHis", strtotime("+10 minutes"))
        , 'receiver_1' => $Body_value['moblie']
        , 'recvname_1' => $Body_value['name']
        , 'subject_1' => $Body_value['title']
        , 'emtitle_1' => $emtitle
        , 'message_1' => $message
        //, 'button_1' => json_encode($Body_value['button'], JSON_UNESCAPED_UNICODE) // 템플릿에 버튼이 없는경우 제거하시기 바랍니다. ex){"button":[{"name":"테스트 버튼","linkType":"DS"}]} 추후오픈
        , 'failover' => $Body_value['failover']
        , 'fsubject_1' => $Body_value['title'] // 대체문자 여부가 Y이고 실패시 문자로 발송 제목
        , 'fmessage_1' => $message // 대체문자 여부가 Y이고 실패시 문자로 발송 내용
        , 'testMode' => $Body_value['testMode']
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);
}

/**
 * name : gfn_alimtalk_template
 * comment : 알리고 템플릿 조회
 *          $Info_value : key값모음 및 필요값
 *              apikey : 발급받은 key (발급키)
 *              userid : 사용자[회사명] (Identifier)
 *              token : gfn_alimtalk_token로 발급받은 토큰값
 *              senderkey : 카카오에서 발급받은 key (Senderkey)
 *              tpl_code : 작성한 템플릿 (템플릿 관리 -> 템플릿코드)
 */
function gfn_alimtalk_template($Info_value) {
    /*
    -----------------------------------------------------------------------------------
    등록된 템플릿 리스트
    -----------------------------------------------------------------------------------
    등록된 템플릿 목록을 조회합니다. 템플릿 코드가 D 나 P 로 시작하는 경우 공유 템플릿이므로 삭제 불가능 합니다.
    */

    $_apiURL      =   'https://kakaoapi.aligo.in/akv10/template/list/';
    $_hostInfo   =   parse_url($_apiURL);
    $_port         =   (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;
    $_variables   =   array(
          'apikey' => $Info_value['apikey']
        , 'userid' => $Info_value['userid']
        , 'token' => $Info_value['token']
        , 'senderkey' => $Info_value['senderkey']
        , 'tpl_code' => $Info_value['tpl_code']
    );

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($_variables));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // 리턴 JSON 문자열 확인
    //print_r($ret . PHP_EOL);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    // 결과값 출력
    $template = $retArr->list[0];
    return $template;
}

/**
 * name : gfn_aligoMessage_send
 * comment : 알리고 문자 발송
 *          $Info_value : key값모음 및 필요값
 *              apikey : 발급받은 key (발급키)
 *              userid : 사용자[회사명] (Identifier)
 * 
 *          $Body_value : 문자 내용 및 수신자,발신자 내역
 *              msg : 메세지 내용 [필수 값]
 *                    euc-kr로 치환이 가능한 문자열만 사용하실 수 있습니다. (이모지 사용불가능)
 *              receiver : 수신인 %고객명% 치환
 *              rdate : 예약일자 - 20161004 : 2016-10-04일기준
 *              rtime : 예약시간 - 1930 : 오후 7시30분
 *              subject : LMS, MMS 제목 (미입력시 본문중 44Byte 또는 엔터 구분자 첫라인)
 *              msg_type : SMS, LMS, MMS등 메세지 타입을 지정
 *              testmode_yn : Y 인경우 실제문자 전송X , 자동취소(환불) 처리
 */
function gfn_aligoMessage_send ($Info_value, $Body_value) {
    /** 문자전송하기 예제 필독항목
      * 동일내용의 문자내용을 다수에게 동시 전송하실 수 있습니다
      * 대량전송시에는 반드시 컴마분기하여 1천건씩 설정 후 이용하시기 바랍니다. (1건씩 반복하여 전송하시면 초당 10~20건정도 발송되며 컨텍팅이 지연될 수 있습니다.)
      * 전화번호별 내용이 각각 다른 문자를 다수에게 보내실 경우에는 send 가 아닌 send_mass(예제:curl_send_mass.html)를 이용하시기 바랍니다.
     **/

    $sms_url = "https://apis.aligo.in/send/"; // 전송요청 URL
    $sms['key'] = $Info_value['apikey'];
    $sms['user_id'] = $Info_value['userid'];
    $sms['msg'] = stripslashes($Body_value['msg']);
    $sms['receiver'] = $Body_value['receiver'];
    $sms['destination'] = $Body_value['destination'];
    $sms['sender'] = $Body_value['sender'];
    $sms['rdate'] = $Body_value['rdate'];
    $sms['rtime'] = $Body_value['rtime'];
    $sms['title'] = $Body_value['subject'];
    $sms['msg_type'] = $Body_value['msg_type'];
    $sms['testmode_yn'] = empty($Body_value['testmode_yn']) ? '' : $Body_value['testmode_yn'];

    $host_info = explode("/", $sms_url);
    $port = $host_info[0] == 'https:' ? 443 : 80;
    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $port);
    curl_setopt($oCurl, CURLOPT_URL, $sms_url);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
    $ret = curl_exec($oCurl);
    curl_close($oCurl);

    $retArr = json_decode($ret); // 결과배열

    return $retArr;
}

/**
 * name : gfn_Bizalimtalk_token
 * comment : 비즈뿌리오 토큰 발급
 */
function gfn_Bizalimtalk_token($userid, $password) {
    $_apiURL =	'https://api.bizppurio.com/v1/token';
    $_hostInfo = parse_url($_apiURL);
    $_port = (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;

    $base64_auth_string = base64_encode("{$userid}:{$password}");

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_HTTPHEADER, array(
        "Authorization: Basic {$base64_auth_string}"
    ));
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // 리턴 JSON 문자열 확인
    //print_r($ret . PHP_EOL);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    //_p($ret);
    //_p($retArr);
    // 결과값 출력
    $token = $retArr->accesstoken;
    //_p($token);
    return $token;
}

/**
 * name : gfn_Bizalimtalk_send
 * comment : 비즈뿌리오 문자/알림톡 발송
 *          $Info_value : key값모음 및 필요값
 *              userid : 비즈뿌리오 계정 [필수 값]
 *              userpw : 비즈뿌리오 계정 비밀번호 [필수 값]
 *              apikey : URL API연동으로 진행된 계정의 apikey [필수 값]
 *              senderkey : 카카오에서 발급받은 발신프로필 Key (Senderkey) [필수 값]
 *              tpl_code : 작성한 템플릿 (메시지 관리 -> 카카오톡 관리 -> 알림톡 템플릿 관리 -> 템플릿코드) [필수 값]
 * 
 *          $Body_value : 문자 내용 및 수신자,발신자 내역
 *              type : 메시지 발송 구분 [sms, lms, mms
 *                      , at : 알림톡
 *                      , al : 알림톡 이미지
 *                      , ft : 친구톡 텍스트
 *                      , fi : 친구톡 이미지
 *                      , fw : 친구톡 와이드 이미지
 *                      , fl : 친구톡 와이드 아이템
 *                      , fc : 친구톡 캐러셀 피드]
 *              from : 발신번호  [필수 값]
 *              to : 수신번호 [필수 값]
 *              name : 발신자명 
 *              emtitle : 알림톡 강조표기형일때 제목부분 
 *              message : 템플릿 내용의 변수값 모음 json처리로 발송 [필수 값]
 *                        type이 sms, lms, mms 인경우는 메시지내용을 작성
 *                        하단의 예시는 템플릿을 사용하는 경우이므로 at, al, ft, fw, fl, fc 인 경우만 사용가능
 *                      ex) $message = array("이벤트명" => $EVENT_NAME, "고객명" => $NAME, "고객연락처" => $MOBILE, "신청일자" => date('Y-m-d H:i:s'));
 *                          해당 내용은 템플릿 tpl_code에 해당하는 변수명으로 작성이 필요함
 *              button : 해당 내용은 템플릿 tpl_code에 해당하는 버튼변수 모음
 *                       하단의 내용을array() 방식으로 작성 나중에 한번에 json처리로 발송
 *                      ex) {"button":[{"name":"테스트 버튼","linkType":"DS"}]}
 *              item : 해당 내용은 템플릿 tpl_code에 해당하는 아이템 모음
 *                       하단의 내용을array() 방식으로 작성 나중에 한번에 json처리로 발송
 *                      ex) {"item": {"list": [{"title": "타이틀", "description": "디스크립션"},
 *                                             {"title": "타이틀2", "description": "디스크립션2"}],
 *                                             "summary": {"title": "요약 타이틀", "description": "$100,000원"}}}
 *              link : 테스트 기능 (기본값은 N)
 *              resend : 대체문자를 어떤것으로 보낼것인지 type 값 기준
 *              subject : type이 lms인경우 제목
 *              file : type가 mms인경우 첨부파일 
 *                       하단의 내용을array() 방식으로 작성 나중에 한번에 json처리로 발송
 *                      ex) {"file":[{"type":"파일유형","key":"파일키"}]}
 */
function gfn_Bizalimtalk_send($Info_value, $Body_value) {
    if ($Body_value['type'] == "sms") { // sms

        $message = $Body_value['message'];

        $content = array(
            $Body_value['type'] => array(
                  "message" => $message
            )
        );
    } else if ($Body_value['type'] == "lms") { // lms
        $message = $Body_value['message'];

        $content = array(
            $Body_value['type'] => array(
                  "subject" => $Body_value['subject']
                , "message" => $message
            )
        );
    } else if ($Body_value['type'] == "mms") { // mms
        $message = $Body_value['message'];
        
        $content = array(
            $Body_value['type'] => array(
                  "subject" => $Body_value['subject']
                , "message" => $message
                , "file" => $Body_value['file']
            )
        );
    } else if ($Body_value['type'] == "at" || $Body_value['type'] == "ai" ) { // 카카오 알림톡
        $content = gfn_Bizalimtalk_template($Info_value);

        $templateName =  $content->templateTitle;
        $emtitle = $templateName;
    
        if (!empty($templateName)) {
            foreach ($Body_value['emtitle'] as $key => $value) {
                $emtitle = preg_replace('/#\{' . preg_quote($key, '/') . '\}/', $value, $emtitle);
            }
        }
    
        $templateContent = $content->templateContent;
        $message = $templateContent;
    
        foreach ($Body_value['message'] as $key => $value) {
            $message = preg_replace('/#\{' . preg_quote($key, '/') . '\}/', $value, $message);
        }

        $content = array(
            $Body_value['type'] => array(
                  "senderkey" => $Info_value['senderkey']
                , "templatecode" => $Info_value['tpl_code']
                , "message" => $message
                , 'title' => $emtitle
                , "button" => $Body_value['button']
            )
            , "item" => $Body_value['item']
            , "link" => $Body_value['link']
        );
    }

    $resend = array("first" => $Body_value['resend']);
    $recontent = array($Body_value['resend'] => array("message" => $message));

    $accessToken = gfn_Bizalimtalk_token($Info_value['userid'], $Info_value['userpw']);
    
    $_apiURL = 'https://api.bizppurio.com/v3/message';
    $_hostInfo = parse_url($_apiURL);
    $_port = (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;

    $_variables = array(
          'account' => $Info_value['userid']
        , 'type' => $Body_value['type']
        , 'from' => $Body_value['from']
        , 'to' => $Body_value['to']
        , 'content' => $content
        , 'refkey' => $Info_value['apikey']
        , 'resend' => $resend
        , 'recontent' => $recontent
    );

    $jsonData = json_encode($_variables);

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Bearer ' . $accessToken
    ]);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // 리턴 JSON 문자열 확인
    //print_r($ret . PHP_EOL);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    $Response_code = $retArr->code;
    return $Response_code;

    //_p($retArr);
}

/**
 * name : gfn_Bizalimtalk_template
 * comment : 비즈뿌리오 알림톡 템플릿 내용 확인 및 내용 return
 */
function gfn_Bizalimtalk_template($Info_value) {
    /*
    -----------------------------------------------------------------------------------
    등록된 템플릿 리스트
    -----------------------------------------------------------------------------------
    등록된 템플릿 목록을 조회합니다. 
    */

    $_apiURL = 'https://kapi.ppurio.com/v3/kakao/template/detail';
    $_hostInfo = parse_url($_apiURL);
    $_port = (strtolower($_hostInfo['scheme']) == 'https') ? 443 : 80;

    $_variables = array(
          'apiKey' => $Info_value['apikey']
        , 'bizId' => $Info_value['userid']
        , 'senderKey' => $Info_value['senderkey']
        , 'templateCode' => $Info_value['tpl_code']
    );

    $jsonData = json_encode($_variables);

    $oCurl = curl_init();
    curl_setopt($oCurl, CURLOPT_PORT, $_port);
    curl_setopt($oCurl, CURLOPT_URL, $_apiURL);
    curl_setopt($oCurl, CURLOPT_POST, 1);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($oCurl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
    ]);

    $ret = curl_exec($oCurl);
    $error_msg = curl_error($oCurl);
    curl_close($oCurl);

    // 리턴 JSON 문자열 확인
    //print_r($ret . PHP_EOL);

    // JSON 문자열 배열 변환
    $retArr = json_decode($ret);

    //_p($ret);
    //_p($retArr);
    //_p($retArr->data);

    // 결과값 출력
    $template = $retArr->data;
    return $template;
}

/**
 * name : gfn_encrypted
 * comment : PHP 암호화
 *           value : 암호화 할 값
 */
function gfn_encrypted ($value) {
    $encrypted_value = openssl_encrypt($value, 'aes-128-ecb', $_SESSION['projectkey']);

    return $encrypted_value;
}

/**
 * name : gfn_ChkprojectKey
 * comment : 프로젝트 키 확인
 *           projectkey : 발급받은 key
 */
function gfn_ChkprojectKey($projectkey) {
    if ($projectkey === $_SESSION['projectkey']) {
        return true;
    }

    return false;
}

/**
 * name : gfn_decrypted
 * comment : PHP 복호화
 *           value : 복호화 할 값
*            key : 발급받은 key
 */
function gfn_decrypted($value, $key) {
    $decrypted_value = openssl_decrypt($value, 'aes-128-ecb', $key);

    return $decrypted_value;
}

/**
 * name : gfn_getDBProjectKey
 * comment : 프로젝트 키 조회
 */
function gfn_getDBProjectKey() {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $sql = "
            SELECT GETPROJECTKEY() as projectkey";

    $name_sql = "프로젝트 키";
    $clefResult = $mysqldb->get($sql, null, $name_sql);
    $data = $clefResult->getResultSet();

    $_SESSION['projectkey'] = $data['projectkey'];
    $_SESSION['projecurl'] = $_SERVER['DOCUMENT_ROOT'];
}

/**
 * name : gfn_getChkProjectKey
 * comment : 프로젝트 키 확인
 */
function gfn_getChkProjectKey($value) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $sql = "
            SELECT CHKPROJECTKEY('{$value}') as Chk_val";

    $name_sql = "프로젝트 키 확인";
    $clefResult = $mysqldb->get($sql, null, $name_sql);
    $data = $clefResult->getResultSet();

    return $data['Chk_val'];
}

/**
 * name : gfn_getEncrypt
 * comment : 2차 암호화 실행 php code 용
 */
function gfn_getEncrypt($value, $key) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $sql = "
            SELECT GETENCRYPT('{$value}', '{$key}') as val";

    $name_sql = "2차 암호화";
    $clefResult = $mysqldb->get($sql, null, $name_sql);
    $data = $clefResult->getResultSet();

    if (!empty($data['val'])) {
        return $data['val'];
    } else {
        dieAndErrorMove('암호화 실패 [해당 키 및 변수값을 확인해주세요].');
    }
}

/**
 * name : gfn_getEncrypt
 * comment : 2차 암호화 실행 ajax용
 */
function gfn_getEncrypt_ajax($value, $key) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $sql = "
            SELECT GETENCRYPT('{$value}', '{$key}') as val";

    $name_sql = "2차 암호화";
    $clefResult = $mysqldb->get($sql, null, $name_sql);
    $data = $clefResult->getResultSet();

    if (!empty($data['val'])) {
        return $data['val'];
    } else {
        gfn_isValidation(600);
    }
}

/**
 * name : gfn_getMasterChkKey
 * comment : 마스터키로 프로젝트 키 조회
 */
function gfn_getMasterChkKey($value) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $sql = "
            SELECT GETMASTERKEY('{$value}') as val";

    $name_sql = "마스터 프로젝트키 확인";
    $clefResult = $mysqldb->get($sql, null, $name_sql);
    $data = $clefResult->getResultSet();

    if (!empty($data['val'])) {
        return $data['val'];
    } else {
        dieAndErrorMove('마스터키를 확인해주세요 [팀장님에게 문의]');
    }
}

//메일 발송
function gfn_adm_send_mail($to, $subject, $body, $path, $fileName) {
    require_once __DIR__ .'/../vendor/autoload.php';

    $arrRtn = array(
        'code' => 500,
        'msg' => ''
    );

    try {
        // Instantiation and passing `true` enables exceptions
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        //인코딩 셋
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        //디버그 off
        $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_OFF;

        $mail->isSMTP();

        $mail->Host = 'smtp.naver.com';
        $mail->SMTPAuth = true;
        $mail->Username = ADM_EMAIL;
        $mail->Password = ADM_PW;
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';

        if (!empty($path) && !empty($fileName)) {
            $mail->addAttachment($_SERVER['DOCUMENT_ROOT'].$path, $fileName);
        }
        //From
        $mail->setFrom(ADM_EMAIL, 'CLEF');
        //To
        $mail->addAddress($to);
        
        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        $mail->send();

        return true; // 메일 전송 성공 시 true 반환
    } catch (Exception $e) {
        return false; // 메일 전송 실패 시 false 반환
    }
}

/**
 * name : gfn_send_mail
 * comment : 이메일 발송 [파일 관련기능은 아직 추가 x 해당 기능은 필요시 추가예정]
 *          $Info : 값모음
 *              to : 받는 사용자의 이메일
 *              subject : 이메일 제목
 *              email_title : 문의제목
 *              email_txt_wrap_html : 설명 html  
 *                  ex) <p>안녕하세요. 클리프입니다.</p>
 *              email_table_html : 테이블내용 html
 *                 ex) <tr style="theadtr" id="thead">
 *                         <th style="theadth" colspan="2">문의자 정보 내역</th>
 *                     </tr>
 *                     <tr style="bodytr">
 *                         <th style="bodyth">문의자 명</th>
 *                         <td style="bodytd">{$data['NAME']}</td>
 *                     </tr>
 *              path : 파일 경로
 *              fileName : 파일명
 *              EMAIL : 보내는 사용자의 이메일
 *              PW : 보내는 사용자의 이메일 암호
 *              NAME : 보내는 사용자의 명칭
 *              TYPE : 이메일 타입 (naver, google---) 
 *                     해당 값은 이메일 보내는곳이 다를때마다 신규로 소스추가
 */
function gfn_send_mail($Info) {
    require_once __DIR__ .'/../vendor/autoload.php';

    try {

        $Host = "";
        $Port = "";
        $SMTPSecure = "";

        if ($Info['TYPE'] == "naver") {
            $Host = "smtp.naver.com";
            $Port = 587;
            $SMTPSecure = "tls";
        }

        // Instantiation and passing `true` enables exceptions
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        //인코딩 셋
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        //디버그 off
        $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_OFF;

        $mail->isSMTP();

        $mail->Host = $Host;
        $mail->SMTPAuth = true;
        $mail->Username = $Info['EMAIL'];
        $mail->Password = $Info['PW'];
        $mail->Port = $Port;
        $mail->SMTPSecure = $SMTPSecure;

        if (!empty($Info['path']) && !empty($Info['fileName'])) {
            $mail->addAttachment($_SERVER['DOCUMENT_ROOT'].$Info['path'], $Info['fileName']);
        }

        //From
        $mail->setFrom($Info['EMAIL'], $Info['NAME']);
        
        //To
        $mail->addAddress($Info['to']);
        
        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT'].'/img/icon/alarm.png', 'alarm');
        $mail->Subject = $Info['subject'];

        $body = file_get_contents(EMAILTEMPURL);
        $body = str_replace('문의제목', $Info['email_title'], $body);
        $body = str_replace('설명', $Info['email_txt_wrap_html'], $body);
        $body = str_replace('테이블내용', $Info['email_table_html'], $body);

        // 테이블 내용 style 적용
        $body = str_replace('style="theadtr"', 'style="margin: 0px; padding: 0px; box-sizing: border-box; color: rgb(17, 17, 17); line-height: normal; border-bottom: 1px solid rgb(230, 230, 230); background: rgb(246, 246, 246);"', $body);
        $body = str_replace('style="theadth"', 'style="margin: 0px; padding: 12px 10px; box-sizing: border-box; color: rgb(17, 17, 17); line-height: normal; border-right: 1px solid rgb(230, 230, 230); text-align: center;"', $body);
        $body = str_replace('style="bodytr"', 'style="margin: 0px; padding: 0px; box-sizing: border-box; color: rgb(17, 17, 17); line-height: normal; border-bottom: 1px solid rgb(230, 230, 230);"', $body);
        $body = str_replace('style="bodyth"', 'style="margin: 0px; padding: 10px; box-sizing: border-box; color: rgb(119, 119, 119); line-height: normal; border-right: 1px solid rgb(230, 230, 230); text-align: center; background: rgb(248, 248, 248); font-weight: 500;"', $body);
        $body = str_replace('style="bodytd"', 'style="margin: 0px; padding: 10px 10px 10px 20px; box-sizing: border-box; color: rgb(17, 17, 17); line-height: normal;"', $body);

        $mail->Body = $body;
        
        $mail->send();

        return true; // 메일 전송 성공 시 true 반환
    } catch (Exception $e) {
        return false; // 메일 전송 실패 시 false 반환
    }
}

/**
 * name : validatePassword
 * comment : 비밀번호에 영문, 숫자, 특수문자 중 requiredCriteria 이상의 조합을 포함하여 requiredLength 자 이상인지 확인
 *          password : 확인할 값
 *          requiredCriteria : 3가지 이상의 조합을 포함 (영문, 숫자, 특수문자)
 *          requiredLength : 최소 길이값
 * return : boolean
 */
function validatePassword($password, $requiredCriteria, $requiredLength) {
    // 비밀번호에 영문, 숫자, 특수문자 중 requiredCriteria 이상의 조합을 포함하여 requiredLength 자 이상인지 확인
    $hasAlpha = preg_match('/[a-zA-Z]/', $password);
    $hasNumber = preg_match('/\d/', $password);
    $hasSpecial = preg_match('/[!@#$%^&*()_+\-=[\]{};:\'\\|,.<>\/?]/', $password);

    // 조건에 맞는 조합의 개수를 계산
    $matchingCriteria = ($hasAlpha ? 1 : 0) + ($hasNumber ? 1 : 0) + ($hasSpecial ? 1 : 0);

    return $matchingCriteria >= $requiredCriteria && strlen($password) >= $requiredLength;
}

/**
 * name :gfn_getIinisVal
 * comment : 이니시스 테이블 값 호출
 *           $INICIS_SEQ : 이니시스 시퀀스 값 ex> 'AD0001'
 *           $VAL : SELECT 문에서 원하는 값 추출 ex) TID
 */
function gfn_getIinisVal($INICIS_SEQ, $VAL=NULL) {
    $mysqldb = new Pdo7();
    $clefResult = new ClefResult();

    $arrRtn = array(
        'code' => 500
      , 'msg' => ''
    );

    try {
        $table = 'PURCHASE_INICIS'; // 공통테이블

        $where = '';

        if (empty($INICIS_SEQ)) {
            gfn_isValidation(999, "", "SEQ 누락");
        }

        if (empty($VAL)) {
            $VAL = 'INICIS_SEQ';
        }
        $sql = "
             SELECT {$VAL} AS TEMP
              FROM {$table}
             WHERE INICIS_SEQ = '{$INICIS_SEQ}'
               {$where}";
        
        $name_sql = "이니시스 검색";
        $clefResult = $mysqldb->get($sql, null, $name_sql);

        if (!$clefResult->getResult()) {
            gfn_isValidation(800);
        }

        $data = $clefResult->getResultSet();

        return $data['TEMP'];
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();

        echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
    }
}

/**
 * name : gfn_isValidation
 * comment : 클리프 에러 목록 코드 및 메시지
 *          code : 지정된 코드 구분값
 *          ID : 필수값 체크시 필요한 값
 *          message : 필수 값 및 기타에 사용할 메세지 내역 ex) 아이디 , 아이디 및 비밀번호가 일치하지 않습니다. 등
 */
function gfn_isValidation($code, $ID=Null, $message=Null) {
    $error_msg = "";

    if ($code == 301) { // 필수값 콤보박스
        if (empty($ID)) {
            $error_msg = "Code({$code}) {$message}을/를 선택해주세요.";
        }
    } else if ($code == 302) { // 필수값 key in
        $ID = trim($ID);

        if (empty($ID)) {
            $error_msg = "Code({$code}) {$message}을/를 입력해주세요.";
        }
    } else if ($code == 303) { // 필수값 int
        $ID = trim($ID);

        if (empty($ID)) {
            $error_msg = "Code({$code}) {$message}을/를 입력해주세요.";
        }
    } else if ($code == 304) { // 필수값 Date
        $ID = trim($ID);

        if (empty($ID)) {
            $error_msg = "Code({$code}) {$message}을/를 입력해주세요.";
        }
    } else if ($code == 305) { // 필수값 hidden
        $ID = trim($ID);

        if (empty($ID)) {
            $error_msg = "Code({$code})". $message;
        }
    } else if ($code == 306) { // 필수값 div 커스텀 입력
        $ID = trim($ID);

        if (empty($ID)) {
            $error_msg = "Code({$code}) " . $message;
        }
    } else if ($code == 307) { // 필수값 파일업로드
        $ID = trim($ID);

        if (empty($ID)) {
            $error_msg = "Code({$code}) {$message}을/를 선택해주세요."; 
        }
    } else if ($code == 401) { // 파일업로드 - 파일용량
        $error_msg = "파일 오류 Code({$code}) ". $message;
    } else if ($code == 402) { // 파일업로드 - 확장자
        $error_msg = "파일 오류 Code({$code}) ". $message;
    } else if ($code == 403) { // 파일업로드 - 동일파일
        $error_msg = "파일 오류 Code({$code}) ". $message;
    } else if ($code == 404) { // 파일업로드 - 용량/파일에러 등
        $error_msg = "파일 업로드 오류 Code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 501) { // sql 등록
        $error_msg = "DB 오류 Code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 502) { // sql 수정
        $error_msg = "DB 오류 Code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 503) { // sql  삭제
        $error_msg = "DB 오류 Code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 504) { // sql 프로시저(PROCEDURE) 
        $error_msg = "DB 오류 Code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 505) { // sql DB 함수 오류
        $error_msg = "DB 오류 Code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 600) { // 암호화 오류
        $error_msg = "Code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 700) { // 잘못된 파라미터 접근
        $error_msg = "Code({$code}) 임의 수정 금지";
    } else if ($code == 800) { // sql 조회오류
        $error_msg = "DB 오류 code({$code}) 관리자에게 문의해주세요.";
    } else if ($code == 999) { // 기타
        $error_msg = "Code({$code}) ". $message;
    }

    if (!empty($error_msg)) {
        throw new Exception($error_msg, $code);
    }
}

// 프로젝트 암호화 기능을 위해 기초 프로젝트 셋팅
// 해당 기능이 없을시 암호화 관련하여 문제 발생
if (isset($_SESSION['projectkey'])) {
    if (!empty($_SESSION['projectkey'])) { // 최초의 한번만 실행하기위해 
        if ($_SESSION['projecurl'] != $_SERVER['DOCUMENT_ROOT']) {
            gfn_getDBProjectKey();
        }
    } else {
        gfn_getDBProjectKey();
    }
} else{
    gfn_getDBProjectKey();
}

/**
 * name : gfn_ip_isValidation
 * comment : IP 비교 및 접근 제한
 *          code : 지정된 코드 구분값
 *          ID : 필수값 체크시 필요한 값
 *          message : 필수 값 및 기타에 사용할 메세지 내역 ex) 아이디 , 아이디 및 비밀번호가 일치하지 않습니다. 등
 */
function gfn_ip_isValidation() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php'); //autoload

    $SESSION_IP = $_SESSION['adm']['menu_access'];
    $SESSION_TYPE = $_SESSION['adm']['member_type_cd'];

    if ($SESSION_TYPE == "SUBADM") {
        if (!ADM_IS_LOGIN || empty($SESSION_IP)) {
            dieAndMsgReplaceMove('../program/logout.php', '로그인 후 이용해주세요.');
        }

        $_arrMenuAccess = explode(',', $_SESSION['adm']['menu_access']);
        $url = $_SERVER['REQUEST_URI'];

        $array = array();
        $result = false;

        // URL 파싱
        $parsedUrl = parse_url($url);

        // 쿼리스트링을 배열로 변환
        parse_str($parsedUrl['query'], $array);

        foreach ($_arrMenuAccess as $val) {
            if (in_array($val, $array)) {
                $result = true; // 일치하는 값이 하나라도 있으면 true 반환
                break; // 반복문을 더이상 실행하지 않도록 중단
            } 
        }

        if (strpos($url, 'adm_details') !== false) {
            $result = true;
        }

        if (!($result)) {
            dieAndErrorMove("잘못된 접근입니다.");
        }
    }
}

require_once __DIR__ . '/../lib/project_lib.php';