<?php
/**
 * 파일명 : contact_code.php
 * 내용 : contact
 * 최초작성날짜 : 2024/03/18
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2024/03/18    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    /**
     * name :getList_HOME_CONTACT
     * comment : CONTACT 리스트
     */
    function getList_HOME_CONTACT() {
        global $where;

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $table = 'CONTACT'; //테이블
            $PAGE = PAGE;
            $arrValue = array();

            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT CONTACT_SEQ
                      , TITLE
                      , TITLE_EN
                      , LINK_URL
                      , MOBILE
                      , EMAIL
                      , DATE_VALUE
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                    {$where}
                  ORDER BY ORDER_NUMBER DESC,reg_date DESC";

            $name_sql = "CONTACT 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_CONTACT_SEQ = _check_var($data['CONTACT_SEQ']); // 시리즈 시퀀스
                    $_db_TITLE = _check_var($data['TITLE']); // 제목
                    $_db_TITLE_EN = _check_var($data['TITLE_EN']); // 제목 - 영문 
                    $_db_LINK_URL = _check_var($data['LINK_URL']); // 링크
                    $_db_MOBILE = _check_var($data['MOBILE']); // 연락처
                    $_db_EMAIL = _check_var($data['EMAIL']); // 이메일
                    $_db_DATE_VALUE = _check_var($data['DATE_VALUE']); // 날짜정보

                    echo <<<DIV
                                <div class="cont__s1_contents_wrap">
                                    <a href="{$_db_LINK_URL}" class="title_wrap">
                                        <p class="title">{$_db_TITLE}</p>
                                        <p class="subtitle">{$_db_TITLE_EN}</p>
                                    </a>
                                    <div class="info_container">
                                        <ul class="info_wrap">
                                            <li>
                                                <a href="mailto:{$_db_EMAIL}">{$_db_EMAIL}</a>
                                            </li>
                                            <li class="separator">|</li>
                                            <li>
                                            <a href="tel:{$_db_MOBILE}">{$_db_MOBILE}</a>
                                            </li>
                                        </ul>
                                        <ul class="date_wrap">
                                            <li>{$_db_DATE_VALUE}</li>
                                        </ul>
                                    </div>
                                </div>
                            DIV;
                }
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>