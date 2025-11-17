<?php
/**
 * 파일명 : faq_code.php
 * 내용 : 구매안내
 * 최초작성날짜 : 2023/08/08
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/08    V1.0
 * 김민성    2023/11/09    shop 기능추가
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;


    /**
     * name :getList_FAQ
     * comment : FAQ
     */
    function getList_FAQ() {
        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $table = 'QUESTIONS'; // FAQ 테이블
            $PAGE = PAGE;
            $arrValue = array();
            $arrValue[':PAGE_TYPE'] = $PAGE;

            $sql = "
                 SELECT TYPE_CD
                      , ASKED
                      , ANSWER
                   FROM {$table}
                  WHERE 1
                    AND MAIN_YN = 'Y'
                    AND PAGE_TYPE = :PAGE_TYPE
                  ORDER BY ORDER_NUMBER DESC";

            $name_sql = "FAQ 리스트";
            $clefResult = $mysqldb->select($sql, $arrValue, $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_TYPE_CD = _check_var($data['TYPE_CD']); // FAQ 관련 공통코드 COL002
                    $_db_ASKED = _check_var($data['ASKED']); // 질문
                    $_db_ANSWER = _check_var($data['ANSWER']); // 답변

                    if (!empty($_db_ASKED)) {
                        $_db_ASKED = nl2br($_db_ASKED);
                    }

                    if (!empty($_db_ANSWER)) {
                        $_db_ANSWER = nl2br($_db_ANSWER);
                    }

                    $TYPE_html = "";

                    if (!empty($_db_TYPE_CD)) {
                        if ($_db_TYPE_CD == "PRD") { // collection 제품
                            $TYPE_html = "faq_prd";
                        } else if ($_db_TYPE_CD == "PAY") { // collection 결제
                            $TYPE_html = "faq_pay";
                        } else if ($_db_TYPE_CD == "DVY") { // collection 배송
                            $TYPE_html = "faq_ship";
                        } else if ($_db_TYPE_CD == "SPRD") { // shop 상품
                            $TYPE_html = "product";
                        } else if ($_db_TYPE_CD == "SSHP") { // shop 배송
                            $TYPE_html = "shipping";
                        } else if ($_db_TYPE_CD == "SPYM") { // shop 결제
                            $TYPE_html = "payment";
                        } else if ($_db_TYPE_CD == "SENT") { // shop 입점
                            $TYPE_html = "enter";
                        } else if ($_db_TYPE_CD == "SOPT") { // shop 운영
                            $TYPE_html = "operation";
                        }
                    }

                    if (PAGE == PAGE1) {
                        $artFoldName = artFoldName;

                        echo <<<LI
                                    <li class="{$TYPE_html}">
                                        <div class="q_wrap">
                                            <div class="q_title">
                                                <div class="badge">Q.</div>
                                                <div class="title">{$_db_ASKED}</div>
                                            </div>
                                            <div class="arrow">
                                                <img src="{$artFoldName}/img/help/faq_arrow.png" alt="화살표">
                                            </div>
                                        </div>
                                        <div class="a_wrap">
                                            <p>
                                                {$_db_ANSWER}
                                            </p>
                                        </div>
                                    </li>
                                LI;
                    } else if (PAGE == PAGE2) {
                        $shopFoldName = shopFoldName;

                        echo <<<LI
                                    <li data-faq-category="{$TYPE_html}"> 
                                        <div class="q_row">
                                            <div class="q_lt">
                                                <div class="badge"><span>Q</span></div>
                                                <div class="title">{$_db_ASKED}</div>
                                            </div>
                                            <div class="q_arrow"><img src="{$shopFoldName}/img/faq_arrow.svg" alt="화살표"></div>
                                        </div>
                                        <div class="a_row">
                                        <div class="badge"><span>A</span></div>
                                        <div class="txt">
                                            {$_db_ANSWER}
                                        </div>
                                    </li>
                                LI;
                    }
                    
                }
            }


        } catch (Exception $e) {
            $arrRtn['code'] = $e->getCode();
            $arrRtn['msg'] = $e->getMessage();
    
            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>