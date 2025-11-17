<?php
/**
 * 파일명 : rent.php
 * 내용 : 대관신청 (등록, 수정, 삭제)
 * 최초작성날짜 : 2023/08/04
 * 최초작성자 : 전상범
 * ------------------------------------
 * name       date        comment
 * 전상범    2023/08/04     V1.0
 * 김민성    2023/11/10    shop 기능추가
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    $arrRtn = array(
          'code' => 500
        , 'msg'  => ''
        , 'mode' => ''
        , 'url' => ''
    );

    try {
        //파라미터 정리
        $mode = get_request_param('mode');
        $arrRes = array();

        switch ($mode) {
            case 'INS' :
                $arrRes = row_insert();
                break;
            case 'AD_DEL' :
                $arrRes = row_delete();
                break;
            default :
                throw new Exception('잘못된 접근 입니다.');
        }

        if ($arrRes['code'] != 200) {
            throw new Exception($arrRes['msg'], $arrRes['code']);
        }

        $arrRtn['code'] = $arrRes['code'];
        $arrRtn['msg'] = $arrRes['msg'];

        if ($mode == 'AD_DEL') {
            $m_seq = get_request_param('m_seq');
            $mp_seq = get_request_param('mp_seq');
            $page_type = get_request_param('page_type');
            $M_COMPANY = get_request_param('M_COMPANY');
            $M_AGENCY = get_request_param('M_AGENCY');
            $M_TYPE_CD = get_request_param('M_TYPE_CD');

            $arrParams = array(
                  'm_seq' => $m_seq
                , 'mp_seq' => $mp_seq
                , 'page_type' => $page_type
                , 'COMPANY' => $M_COMPANY
                , 'AGENCY' => $M_AGENCY
                , 'TYPE_CD' => $M_TYPE_CD
            );

            $query_string = http_build_query($arrParams);

            $url = "../adm/board/rental_main.php?".$query_string;

            $arrRtn['url'] = $url;

            dieAndMsgReplaceMove($arrRtn['url'], $arrRtn['msg']);
        } else {
            echo json_encode($arrRtn);
        }
    } catch (Exception $e) {
        $arrRtn['code'] = $e->getCode();
        $arrRtn['msg'] = $e->getMessage();
        echo json_encode($arrRtn);
    }

    /**
     * name :row_insert
     * comment : 등록
     */
    function row_insert() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            //트랜잭션
            $mysqldb->link->beginTransaction();

            $page_type = get_request_param('page_type'); // 페이지 구분
            $TYPE_CD = get_request_param('TYPE_CD'); // 문의 구분
            $COMPANY = get_request_param('COMPANY'); // 회사(단체)명
            $AGENCY = get_request_param('AGENCY'); // 대행사명
            $TITLE = get_request_param('TITLE'); // 행사명
            $HSDATE = get_request_param('HSDATE'); // 희망기간 - 시작일
            $HEDATE = get_request_param('HEDATE'); // 희망기간 - 종료일
            $CONTENT_TEXT = get_request_param('CONTENT_TEXT'); // 내용
            $NAME = get_request_param('NAME'); // 담당자명 (직함)
            $PHONE1 = get_request_param('PHONE1'); // 휴대폰 앞자리
            $PHONE2 = get_request_param('PHONE2'); // 휴대폰 중간자리
            $PHONE3 = get_request_param('PHONE3'); // 휴대폰 뒷자리
            $EMAIL_ID = get_request_param('EMAIL_ID'); // 이메일 아이디
            $EMAIL_TEXT = get_request_param('EMAIL_TEXT'); // 이메일 도메인
            $EMAIL_CD = get_request_param('EMAIL_CD'); // 이메일 코드
            $MOBILE = "";
            $FORMAT_MOBILE = "";
            $EMAIL = "";

            if (empty($COMPANY)) {
                gfn_isValidation(301, $COMPANY, '회사(단체)명');
            }

            if (empty($HSDATE)) {
                gfn_isValidation(302, $HSDATE, '희망기간 - 시작일');
            }

            if (empty($HEDATE)) {
                gfn_isValidation(302, $HEDATE, '희망기간 - 종료일');
            }

            if (empty($PHONE1) && empty($PHONE2) && empty($PHONE3)) {
                gfn_isValidation(301, '', '연락처');
            } else {
                $MOBILE = $PHONE1.$PHONE2.$PHONE3;
                $FORMAT_MOBILE = formatPhoneNumber($MOBILE);
            }

            if (empty($EMAIL_ID) && empty($EMAIL_TEXT) && empty($EMAIL_CD)) {
                gfn_isValidation(301, '', '이메일');
            } else {
                $EMAIL = $EMAIL_ID.'@'.$EMAIL_TEXT;
            }

            $seq_name = "RINQ";

            $sql = "
                 SELECT nextval('{$seq_name}') as seq";

            $name_sql = "대관문의 시퀀스";
            $clefResult = $mysqldb->get($sql, null, $name_sql);
            $data = $clefResult->getResultSet();

            $ip = "";

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            $table = 'RENTAL_INQUIRY';
            $type = 'TYPE';
            $dir = UPLOAD_DIR ."/{$table}/{$type}/". date('Ymd');
            $key_value = '';
            $ATTACH_FILE_ID = "";

            if (is_array($_FILES)) {
                if (!empty($key_val)) {
                    $key_val = json_decode($key_val, true);
                }

                foreach ($_FILES as $key => $val) {
                    
                    if (isset($_FILES[$key]['name']) !== false && !empty($_FILES[$key]['name'])) { // 파일업로드
                        $ATTACH_FILE_ID = 'ATTACH_'. $data['seq'];

                        $key_value = $key;
                        $arrRes = json_decode(one_file_upload($dir, $key), true);

                        if ($arrRes['code'] != 200) {
                            throw new Exception($arrRes['msg'], $arrRes['code']);
                        }

                        foreach ($arrRes['file'] as $key => $val) {
                            if ($key_value == "attachFile") {
                                $idx = 1;
                                $ATTACH_GROUP = 1;
                            }
                            gfn_file_upload("I", $dir, $ATTACH_FILE_ID, $ATTACH_GROUP, $idx, $val, $COMPANY, $ip);
                        }
                    }
                }
            }

            $values = array(
                  'RENTAL_SEQ' => $data['seq'] //시퀀스 값
                , 'PAGE_TYPE' => $page_type // 페이지 타입
                , 'TYPE_CD' => $TYPE_CD // 문의 구분
                , 'COMPANY' => $COMPANY // 회사(단체)명
                , 'AGENCY' => $AGENCY // 대행사명
                , 'TITLE' => $TITLE // 행사명
                , 'NAME' => $NAME // 담당자명 (직함)
                , 'HSDATE' => $HSDATE // 희망기간 - 시작일
                , 'HEDATE' => $HEDATE // 희망기간 - 종료일
                , 'MOBILE' => $MOBILE // 연락처
                , 'EMAIL' => $EMAIL // 이메일
                , 'CONTENT_TEXT' => $CONTENT_TEXT // 내용
                , 'ATTACH_FILE_ID' => $ATTACH_FILE_ID // 파일아이디
                , 'reg_user' => $_SESSION['adm']['name'] // 등록자
                , 'reg_ip' => $ip // 등록자 아이피
                , 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
            );

            $name_sql = "대관신청 등록";
            $clefResult = $mysqldb->insert($table, $values, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(501);
            }

            $TYPE_CD_NM = gfn_getZcmcommonVal('COL013', $TYPE_CD, 'COM_CD_NM');

            $subject = "[piknic] - 대관문의 내용입니다.";

            $email_title = "대관문의";

            $email_txt_wrap_html = "";

            $email_table_html = <<<TR
                                    <tr style="theadtr" id="thead">
                                        <th style="theadth" colspan="2">대관문의 내역</th>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">문의 구분</th>
                                        <td style="bodytd">{$TYPE_CD_NM}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">회사(단체)명</th>
                                        <td style="bodytd">{$COMPANY}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">대행사명</th>
                                        <td style="bodytd">{$AGENCY}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">행사명</th>
                                        <td style="bodytd">{$TITLE}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">희망기간</th>
                                        <td style="bodytd">{$HSDATE} ~ {$HEDATE}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">내용</th>
                                        <td style="bodytd">{$CONTENT_TEXT}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">담당자명 (직함)</th>
                                        <td style="bodytd">{$COMPANY}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">연락처</th>
                                        <td style="bodytd">{$FORMAT_MOBILE}</td>
                                    </tr>
                                    <tr style="bodytr">
                                        <th style="bodyth">이메일</th>
                                        <td style="bodytd">{$EMAIL}</td>
                                    </tr>
                                TR;

            $MAIL_INFO = [
                  'to' => "info@piknic.kr"
                , 'subject' => $subject
                , 'email_title' => $email_title
                , 'email_txt_wrap_html' => $email_txt_wrap_html
                , 'email_table_html' => $email_table_html
                , 'path' => ""
                , 'fileName' => ""
                , 'EMAIL' => SMTP_EMAIL
                , 'PW' => SMTP_PW
                , 'NAME' => 'piknik'
                , 'TYPE' => 'naver'
            ];

            $arrRes = gfn_send_mail($MAIL_INFO);

            if (!$arrRes) {
                gfn_isValidation(999, "", "이메일 발송실패 관리자에게 문의필요");
            }

            //성공
            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '등록되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }

    /**
     * name :row_delete
     * comment : 삭제
     */
    function row_delete() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
            'code' => 500
          , 'msg' => ''
        );

        try {
            $mysqldb->link->beginTransaction();

            $RENTAL_SEQ = get_request_param('SEQ'); // 시퀀스
            $ATTACH_FILE_ID = get_request_param('ATTACH_FILE_ID'); // 시퀀스

            $sql = "
                DELETE FROM ZCMFILEA
                 WHERE ATTACH_FILE_ID = :pk";

            $name_sql = $RENTAL_SEQ." 업로드 삭제 ";
            $clefResult = $mysqldb->delete($sql, [':pk' => $ATTACH_FILE_ID], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $sql = "
                DELETE FROM RENTAL_INQUIRY
                 WHERE RENTAL_SEQ = :pk";

            $name_sql = $RENTAL_SEQ." 대관문의 삭제 ";

            $clefResult = $mysqldb->delete($sql, [':pk' => $RENTAL_SEQ], $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(503);
            }

            $mysqldb->link->commit();
            $arrRtn['code'] = 200;
            $arrRtn['msg'] = '삭제되었습니다.';
        } catch (Exception $e) {
            $mysqldb->link->rollBack();
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
        } finally {
            return $arrRtn;
        }
    }
?>