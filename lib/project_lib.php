<?php

use Clef\Pdo7 as Pdo7;
use Clef\ClefResult as ClefResult;

/////////////////// 해당 프로젝트에서만 사용하는 함수들 모음 /////////////////////////////////////////

   /**
    * name : gfn_page_chk
    * comment : 페이지 접근권한 확인
    */
    function gfn_page_chk() {
        if (isset($_SERVER['REQUEST_URI'])) {
            $url = $_SERVER['REQUEST_URI'];

            // URL 파싱
            $parsedUrl = parse_url($url);

            // 경로를 슬래시('/')로 분할
            $pathSegments = explode('/', $parsedUrl['path']);

            // 'collection'이라는 값이 있는지 확인
            if (in_array(PAGE1, $pathSegments)) {
                define('PAGE', PAGE1);
                $_SESSION['FoldName'] = artFoldName;

                $INFORURL_CHK = array_search('mypage', $pathSegments);

                if ($INFORURL_CHK !==2) {
                    $_SESSION['INFOR']['URL'] = $url;

                    $INFORURL_CHK = array_search('order', $pathSegments);
        
                    if ($INFORURL_CHK === 2) {
                        $_SESSION['INFOR']['LOGIN_CHK'] = true;
                    } else {
                        $INFORURL_CHK = array_search('shop', $pathSegments);
        
                        if ($INFORURL_CHK === 2) {
                            $INFORURL_CHK = array_search('detail.php', $pathSegments);
        
                            if ($INFORURL_CHK === 3) {
                                $_SESSION['INFOR']['LOGIN_CHK'] = true;
                            } else {
                                $_SESSION['INFOR']['LOGIN_CHK'] = false;
                            }
                        } else {
                            $_SESSION['INFOR']['LOGIN_CHK'] = false;
                        }
                    }
                }

                if (strpos($_SERVER['PHP_SELF'], '/'.PAGE1.'/index.php') === false) {
                    $index = array_search(PAGE1, $pathSegments);

                    // 'collection'이 위치한 인덱스가 1인지 확인
                    if ($index === 1) {
                        if (!isset($_SESSION['SECRETCHK'])) {
                            dieAndMsgReplaceMove('/'. PAGE1 .'/', '잘못된 접근입니다.[시크릿 코드 누락]');
                        } else {
                            if (empty($_SESSION['SECRETCHK'])) {
                                dieAndMsgReplaceMove('/'. PAGE1 .'/', '잘못된 접근입니다.[시크릿 코드 누락]');
                            }
                        }

                        if (strpos($_SERVER['PHP_SELF'], '/'.PAGE1.'/mypage/login.php') === false && // 로그인페이지 
                            strpos($_SERVER['PHP_SELF'], '/'.PAGE1.'/mypage/join.php') === false && // 회원가입
                            strpos($_SERVER['PHP_SELF'], '/'.PAGE1.'/mypage/find_id_popup.php') === false && // 아이디 찾기
                            strpos($_SERVER['PHP_SELF'], '/'.PAGE1.'/mypage/find_pw_popup.php') === false) { // 비밀번호 찾기
                            $index2 = array_search('mypage', $pathSegments);

                            if ($index2 === 2) {
                                if (!isset($_SESSION['MEMBER'])) {
                                    dieAndMsgReplaceMove('/'. PAGE1 .'/mypage/login.php', '로그인 후 이용해주세요.');
                                } else {
                                    if (empty($_SESSION['MEMBER'])) {
                                        dieAndMsgReplaceMove('/'. PAGE1 .'/mypage/login.php', '로그인 후 이용해주세요.');
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $index = array_search(PAGE1, $pathSegments);

                    if ($index !== 1) {
                        unset($_SESSION['SECRETCHK']);
                    }
                }
            } else if (in_array(PAGE2, $pathSegments)) {
                define('PAGE', PAGE2);
                $_SESSION['FoldName'] = shopFoldName;

                unset($_SESSION['SECRETCHK']);

                $INFORURL_CHK = array_search('mypage', $pathSegments);

                if ($INFORURL_CHK !== 2) {
                    $_SESSION['INFOR']['URL'] = $url;

                    $INFORURL_CHK = array_search('order', $pathSegments);
        
                    if ($INFORURL_CHK === 2) {
                        $_SESSION['INFOR']['LOGIN_CHK'] = true;
                    } else {
                        $INFORURL_CHK = array_search('product', $pathSegments);
        
                        if ($INFORURL_CHK === 2) {
                            $INFORURL_CHK = array_search('detail.php', $pathSegments);
        
                            if ($INFORURL_CHK === 3) {
                                $_SESSION['INFOR']['LOGIN_CHK'] = true;
                            } else {
                                $_SESSION['INFOR']['LOGIN_CHK'] = false;
                            }
                        } else {
                            $_SESSION['INFOR']['LOGIN_CHK'] = false;
                        }
                    }
                }

                if (strpos($_SERVER['PHP_SELF'], '/'.PAGE2.'/index.php') === false) {
                    $index = array_search(PAGE2, $pathSegments);

                    // 'shop'이 위치한 인덱스가 1인지 확인
                    if ($index === 1) {
                        if (strpos($_SERVER['PHP_SELF'], '/'.PAGE2.'/mypage/login.php') === false && // 로그인페이지 
                            strpos($_SERVER['PHP_SELF'], '/'.PAGE2.'/mypage/join.php') === false && // 회원가입
                            strpos($_SERVER['PHP_SELF'], '/'.PAGE2.'/mypage/find_id_popup.php') === false && // 아이디 찾기
                            strpos($_SERVER['PHP_SELF'], '/'.PAGE2.'/mypage/find_pw_popup.php') === false) { // 비밀번호 찾기
                            $index2 = array_search('mypage', $pathSegments);

                            if ($index2 === 2) {
                                if (!isset($_SESSION['MEMBER'])) {
                                    dieAndMsgReplaceMove('/'. PAGE2 .'/mypage/login.php', '로그인 후 이용해주세요.');
                                } else {
                                    if (empty($_SESSION['MEMBER'])) {
                                        dieAndMsgReplaceMove('/'. PAGE2 .'/mypage/login.php', '로그인 후 이용해주세요.');
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $index = array_search(PAGE1, $pathSegments);

                    if ($index !== 1) {
                        unset($_SESSION['SECRETCHK']);
                    }
                }
            } else if (in_array(PAGE3, $pathSegments)) {
                define('PAGE', PAGE3);

                $_SESSION['FoldName'] = homeFoldName;
            }else {
                unset($_SESSION['SECRETCHK']);
            }
        } else {
            dieAndErrorMove("잘못된 접근입니다.");
        }

        gfn_TokenSeesion_Time();
    }

   /**
    * name : gfn_page_chk
    * comment : 무통장 기한이 넘을시 주문취소로 자동 변경 collection [미술품]
    */
    function gfn_Option_Collection() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "CALL OPTION_CHANGE_COLLECTION()";

        $name_sql = "무통장 기한 확인";

        $clefResult = $mysqldb->procedure($sql, null, $name_sql);
    }

    function gfn_Option_shop() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "
             SELECT GROUP_CONCAT(M.PURCHASE_SEQ SEPARATOR ',') as SEQS
                  , GROUP_CONCAT(D.CATEGORY3_SEQ SEPARATOR ',') as PRODUCTS
                  , GROUP_CONCAT(D.OPTION_SEQ SEPARATOR ',') as Options
               FROM PURCHASE_ORDER M
               JOIN PURCHASE_OPTION D ON M.PURCHASE_SEQ = D.PURCHASE_SEQ
              WHERE M.PAGE_TYPE = 'shop'
                AND D.STATE_CD IN ('01')
                AND DATE(M.ORDER_DATE) <= DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY)";

        $name_sql = "샵 무통장 자동취소 자동완료";
        $clefResult = $mysqldb->get($sql, "", $name_sql);

        $data = $clefResult->getResultSet();

        if (!empty($data)) {
            $SEQS = _check_var($data['SEQS']);
            $PRODUCTS = _check_var($data['PRODUCTS']);
            $Options = _check_var($data['Options']);

            if (!empty($SEQS)) {
                $SEQrray = explode(',', $SEQS);
                $PRODUCTArray = explode(',', $PRODUCTS);
                $optionArray = explode(',', $Options);
    
                $ip = "";

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                    $ip = $_SERVER['HTTP_X_REAL_IP'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
    
                $table = "PURCHASE_OPTION";
                $table2 = "PURCHASE_ORDER";
                $table3 = 'CATEGORY_OPTION';
    
                for ($i = 0; $i < count($optionArray); $i++) {
                    $PURCHASE_SEQ = $SEQrray[$i];
                    $CATEGORY3_SEQ = $PRODUCTArray[$i];
                    $OPTION_SEQ = $optionArray[$i];
    
                    $values = array(
                          'STATE_CD' => "42"
                        , 'mod_user' => '자동변경' // 등록자
                        , 'mod_ip' => $ip // 등록자 아이피
                        , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                    );
    
                    $pkvalues = array (
                          'PURCHASE_SEQ' => $PURCHASE_SEQ
                        , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                        , 'OPTION_SEQ' => $OPTION_SEQ
                    );
    
                    $name_sql = "작품 옵션 개별 주문상태 상태변경";
                    $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);
    
                    $arrValue = array();
                    $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;
    
                    $sql = "
                         SELECT GROUP_CONCAT(CATEGORY3_SEQ SEPARATOR ',') as PRODUCTS
                              , GROUP_CONCAT(OPTION_SEQ SEPARATOR ',') as Options
                           FROM CATEGORY_OPTION
                          WHERE OPTION_SEQ IN (SELECT OPTION_SEQ
                                                 FROM PURCHASE_OPTION
                                                WHERE PURCHASE_SEQ = :PURCHASE_SEQ)";
    
                    $name_sql = "카테고리3 옵션 값 추출";
                    $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);
    
                    if (!$clefResult->getResult()) {
                        gfn_isValidation(800);
                    }
    
                    $cata3op = $clefResult->getResultSet();
    
                    if (!empty($cata3op)) {
                        $CATA_PRODUCTS = _check_var($cata3op['PRODUCTS']);
                        $CATA_Options = _check_var($cata3op['Options']);
    
                        $CATA_PRODUCTArray = explode(',', $CATA_PRODUCTS);
                        $CATA_optionArray = explode(',', $CATA_Options);
    
                        for ($j = 0; $j < count($CATA_optionArray); $j++) {
                            $CATA_seq = $CATA_PRODUCTArray[$j];
                            $CATA_code = $CATA_optionArray[$j];
                            
                            $BOBY_INFO = array(
                                  'CATEGORY3_SEQ' => $CATA_seq
                                , 'OPTION_SEQ' => $CATA_code
                                , 'VAL' => 'QUANTITY'
                            );
    
                            $CATA_QUANTITY = gfn_OPTION_QUANTITY($BOBY_INFO);
    
                            $BOBY_INFO = array(
                                  'CATEGORY3_SEQ' => $CATA_seq
                                , 'OPTION_SEQ' => $CATA_code
                                , 'VAL' => 'SOLD_YN'
                            );
    
                            $CATA_SOLD_YN = gfn_OPTION_QUANTITY($BOBY_INFO);
    
                            $BOBY_INFO = array(
                                  'PURCHASE_SEQ' => $PURCHASE_SEQ
                                , 'CATEGORY3_SEQ' => $CATA_seq
                                , 'OPTION_SEQ' => $CATA_code
                                , 'VAL' => 'QUANTITY'
                            );
                            
                            $ORDER_QUANTITY = gfn_ORDER_OPTION_QUANTITY($BOBY_INFO);
    
                            $option_values = array();
                            $option_pkvalues = array();
    
                            $COUNT_QUANTITY = intval($CATA_QUANTITY) + intval($ORDER_QUANTITY);
                            
                            $option_values['QUANTITY'] = $COUNT_QUANTITY;
                            
                            if ($COUNT_QUANTITY == 0) {
                                $option_values['SOLD_YN'] = 'Y';
                            } else if ($CATA_QUANTITY == 0 && $CATA_SOLD_YN == 'Y') {
                                if ($COUNT_QUANTITY != 0) {
                                    $option_values['SOLD_YN'] = 'N';
                                }
                            }
    
                            $option_values['mod_user'] = '자동변경';
                            $option_values['mod_ip'] = $ip;
                            $option_values['mod_date'] = date('Y-m-d H:i:s');
    
                            $option_pkvalues['OPTION_SEQ'] = $CATA_code;
                            $option_pkvalues['CATEGORY3_SEQ'] = $CATA_seq;
                            
                            $name_sql = "수량 수정";
                            $clefResult = $mysqldb->update($table3, $option_values, $option_pkvalues, $name_sql);
    
                            if (!$clefResult->getResult()) {
                                gfn_isValidation(502);
                            }
                        }
                    }
    
                    $STATE = "";
    
                    $sql = "
                         SELECT (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '01') AS STATE_COUNT01
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '21') AS STATE_COUNT21
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '30') AS STATE_COUNT30
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '31') AS STATE_COUNT31
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '32') AS STATE_COUNT32
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '41') AS STATE_COUNT41
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '42') AS STATE_COUNT42
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '51') AS STATE_COUNT51
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '52') AS STATE_COUNT52
                              , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ) AS TOTAL_STATE_COUNT
                           FROM PURCHASE_ORDER M
                          WHERE PURCHASE_SEQ = :PURCHASE_SEQ";
    
                    $name_sql = "주문상태 카운트값";
                    $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);
    
                    if (!$clefResult->getResult()) {
                        gfn_isValidation(800);
                    }
    
                    $data = $clefResult->getResultSet();
    
                    if (empty($data)) {
                        dieAndErrorMove("잘못된 접근입니다.");
                    }
    
                    $STATE_COUNT01 = _check_var($data['STATE_COUNT01']); // 주문접수
                    $STATE_COUNT21 = _check_var($data['STATE_COUNT21']); // 결제완료
                    $STATE_COUNT30 = _check_var($data['STATE_COUNT30']); // 배송준비중
                    $STATE_COUNT31 = _check_var($data['STATE_COUNT31']); // 배송중
                    $STATE_COUNT32 = _check_var($data['STATE_COUNT32']); // 배송완료
                    $STATE_COUNT41 = _check_var($data['STATE_COUNT41']); // 주문취소요청
                    $STATE_COUNT42 = _check_var($data['STATE_COUNT42']); // 주문취소
                    $STATE_COUNT51 = _check_var($data['STATE_COUNT51']); // 환불요청
                    $STATE_COUNT52 = _check_var($data['STATE_COUNT52']); // 환불승인
                    $TOTAL_STATE_COUNT = _check_var($data['TOTAL_STATE_COUNT']); // 토탈값
    
                    $STATE_COUNTS = [
                        '52' => $STATE_COUNT52,
                        '51' => $STATE_COUNT51,
                        '42' => $STATE_COUNT42,
                        '41' => $STATE_COUNT41,
                        '32' => $STATE_COUNT32,
                        '31' => $STATE_COUNT31,
                        '30' => $STATE_COUNT30,
                        '21' => $STATE_COUNT21,
                        '01' => $STATE_COUNT01
                    ];
    
                    $STATE = determineStateCD($TOTAL_STATE_COUNT, $STATE_COUNTS);
    
                    $values = array(
                          'STATE_CD' => $STATE
                        , 'mod_user' => '자동변경' // 등록자
                        , 'mod_ip' => $ip // 등록자 아이피
                        , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                    );
    
                    $pkvalues = array (
                          'PURCHASE_SEQ' => $PURCHASE_SEQ
                    );
    
                    $name_sql = "작품 주문상태 상태변경";
                    $clefResult = $mysqldb->update($table2, $values, $pkvalues, $name_sql);
    
                    if (!$clefResult->getResult()) {
                        gfn_isValidation(502);
                    }
                }
            }
        }
    }

   /**
    * name : gfn_Option_DLVY_END
    * comment : 5일 이상 배송중인 제품 자동으로 배송완료로 변경
    */
    function gfn_Option_DLVY_END() {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $sql = "
             SELECT GROUP_CONCAT(M.PURCHASE_SEQ SEPARATOR ',') as SEQS
                  , GROUP_CONCAT(D.CATEGORY3_SEQ SEPARATOR ',') as PRODUCTS
                  , GROUP_CONCAT(D.OPTION_SEQ SEPARATOR ',') as Options
               FROM PURCHASE_ORDER M
               JOIN PURCHASE_OPTION D ON M.PURCHASE_SEQ = D.PURCHASE_SEQ
              WHERE M.PAGE_TYPE = 'shop'
                AND D.STATE_CD IN ('31')
                AND DATE(M.ORDER_DATE) <= DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY)";

        $name_sql = "배송중 자동완료";
        $clefResult = $mysqldb->get($sql, "", $name_sql);

        $data = $clefResult->getResultSet();

        if (!empty($data)) {
            $SEQS = _check_var($data['SEQS']);
            $PRODUCTS = _check_var($data['PRODUCTS']);
            $Options = _check_var($data['Options']);

            if (!empty($SEQS)) {
                $SEQrray = explode(',', $SEQS);
                $PRODUCTArray = explode(',', $PRODUCTS);
                $optionArray = explode(',', $Options);
    
                $ip = "";

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                    $ip = $_SERVER['HTTP_X_REAL_IP'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
   
                $table = "PURCHASE_OPTION";
                $table2 = "PURCHASE_ORDER";
   
               for ($i = 0; $i < count($optionArray); $i++) {
                   $PURCHASE_SEQ = $SEQrray[$i];
                   $CATEGORY3_SEQ = $PRODUCTArray[$i];
                   $OPTION_SEQ = $optionArray[$i];
   
                   $values = array(
                         'STATE_CD' => "32"
                       , 'mod_user' => '자동변경' // 등록자
                       , 'mod_ip' => $ip // 등록자 아이피
                       , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                   );
   
                   $pkvalues = array (
                         'PURCHASE_SEQ' => $PURCHASE_SEQ
                       , 'CATEGORY3_SEQ' => $CATEGORY3_SEQ
                       , 'OPTION_SEQ' => $OPTION_SEQ
                   );
   
                   $name_sql = "작품 옵션 개별 주문상태 상태변경";
                   $clefResult = $mysqldb->update($table, $values, $pkvalues, $name_sql);
   
                   $arrValue = array();
                   $arrValue[':PURCHASE_SEQ'] = $PURCHASE_SEQ;
   
                   $STATE = "";
   
                   $sql = "
                        SELECT (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '01') AS STATE_COUNT01
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '21') AS STATE_COUNT21
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '30') AS STATE_COUNT30
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '31') AS STATE_COUNT31
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '32') AS STATE_COUNT32
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '41') AS STATE_COUNT41
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '42') AS STATE_COUNT42
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '51') AS STATE_COUNT51
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ AND STATE_CD = '52') AS STATE_COUNT52
                             , (SELECT COUNT(*) FROM PURCHASE_OPTION WHERE M.PURCHASE_SEQ = PURCHASE_SEQ) AS TOTAL_STATE_COUNT
                          FROM PURCHASE_ORDER M
                         WHERE PURCHASE_SEQ = :PURCHASE_SEQ";
   
                   $name_sql = "주문상태 카운트값";
                   $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);
   
                   if (!$clefResult->getResult()) {
                       gfn_isValidation(800);
                   }
   
                   $data = $clefResult->getResultSet();
   
                   if (empty($data)) {
                       dieAndErrorMove("잘못된 접근입니다.");
                   }
   
                   $STATE_COUNT01 = _check_var($data['STATE_COUNT01']); // 주문접수
                   $STATE_COUNT21 = _check_var($data['STATE_COUNT21']); // 결제완료
                   $STATE_COUNT30 = _check_var($data['STATE_COUNT30']); // 배송준비중
                   $STATE_COUNT31 = _check_var($data['STATE_COUNT31']); // 배송중
                   $STATE_COUNT32 = _check_var($data['STATE_COUNT32']); // 배송완료
                   $STATE_COUNT41 = _check_var($data['STATE_COUNT41']); // 주문취소요청
                   $STATE_COUNT42 = _check_var($data['STATE_COUNT42']); // 주문취소
                   $STATE_COUNT51 = _check_var($data['STATE_COUNT51']); // 환불요청
                   $STATE_COUNT52 = _check_var($data['STATE_COUNT52']); // 환불승인
                   $TOTAL_STATE_COUNT = _check_var($data['TOTAL_STATE_COUNT']); // 토탈값
   
                   $STATE_COUNTS = [
                       '52' => $STATE_COUNT52,
                       '51' => $STATE_COUNT51,
                       '42' => $STATE_COUNT42,
                       '41' => $STATE_COUNT41,
                       '32' => $STATE_COUNT32,
                       '31' => $STATE_COUNT31,
                       '30' => $STATE_COUNT30,
                       '21' => $STATE_COUNT21,
                       '01' => $STATE_COUNT01
                   ];
   
                   $STATE = determineStateCD($TOTAL_STATE_COUNT, $STATE_COUNTS);
   
                   $values = array(
                         'STATE_CD' => $STATE
                       , 'mod_user' => '자동변경' // 등록자
                       , 'mod_ip' => $ip // 등록자 아이피
                       , 'mod_date' => date('Y-m-d H:i:s') // 등록날짜
                   );
   
                   $pkvalues = array (
                         'PURCHASE_SEQ' => $PURCHASE_SEQ
                   );
   
                   $name_sql = "작품 주문상태 상태변경";
                   $clefResult = $mysqldb->update($table2, $values, $pkvalues, $name_sql);
   
                   if (!$clefResult->getResult()) {
                       gfn_isValidation(502);
                   }
               }
            }
        }
    }

    /**
     * name :gfn_TokenSeesion_Time
     * comment : 발급 토큰 10분
     */
    function gfn_TokenSeesion_Time() {
        $max_lifetime = 600;

        if (isset($_SESSION['SNSIFNO'])) {
            if (isset($_SESSION['SNSIFNO']['CREATED'])) {
                if (time() - $_SESSION['SNSIFNO']['CREATED'] > $max_lifetime) {
                    // 세션 유효 시간이 초과하면 세션 변수 파기
                    unset($_SESSION['SNSIFNO']);
                }
            }
        }
    }

    /**
     * name :determineStateCD
     * comment : 주문상태 변경 값
     */
    function determineStateCD($TOTAL_STATE_COUNT, $STATE_COUNT) {
        $STATE_CD = "";

        foreach ($STATE_COUNT as $key => $value) {
            ${"STATE_COUNT" . $key} = $value;
        }

        if ($STATE_COUNT52 > 0) {
            $STATE_CD = "52";
            
            if ($TOTAL_STATE_COUNT != $STATE_COUNT52) {
                if ($TOTAL_STATE_COUNT != $STATE_COUNT32) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "90";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "89";
                    } else {
                        $STATE_CD = "63";
                    }
                } else if ($TOTAL_STATE_COUNT != $STATE_COUNT31) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "88";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "87";
                    } else {
                        $STATE_CD = "62";
                    }
                } else if ($TOTAL_STATE_COUNT != $STATE_COUNT30) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "86";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "85";
                    } else {
                        $STATE_CD = "61";
                    }
                } else if ($TOTAL_STATE_COUNT != $STATE_COUNT21) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "84";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "83";
                    } else {
                        $STATE_CD = "60";
                    }
                } else if ($TOTAL_STATE_COUNT != $STATE_COUNT01) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "82";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "81";
                    }
                }
            }
        } else if ($STATE_COUNT51 > 0) {
            $STATE_CD = "51";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT51) {
                if (0 != $STATE_COUNT32) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "90";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "89";
                    } else {
                        $STATE_CD = "63";
                    }
                } else if (0 != $STATE_COUNT31) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "88";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "87";
                    } else {
                        $STATE_CD = "62";
                    }
                } else if (0 != $STATE_COUNT30) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "86";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "85";
                    } else {
                        $STATE_CD = "61";
                    }
                } else if (0 != $STATE_COUNT21) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "84";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "83";
                    } else {
                        $STATE_CD = "60";
                    }
                } else if (0 != $STATE_COUNT01) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "82";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "81";
                    }
                }
            }
        } else if ($STATE_COUNT42 > 0) {
            $STATE_CD = "42";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT42) {
                if (0 != $STATE_COUNT32) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "90";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "89";
                    } else {
                        $STATE_CD = "63";
                    }
                } else if (0 != $STATE_COUNT31) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "88";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "87";
                    } else {
                        $STATE_CD = "62";
                    }
                } else if (0 != $STATE_COUNT30) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "86";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "85";
                    } else {
                        $STATE_CD = "61";
                    }
                } else if (0 != $STATE_COUNT21) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "84";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "83";
                    } else {
                        $STATE_CD = "60";
                    }
                } else if (0 != $STATE_COUNT01) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "82";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "81";
                    }
                }
            }
        } else if ($STATE_COUNT41 > 0) {
            $STATE_CD = "41";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT41) {
                if (0 != $STATE_COUNT32) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "90";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "89";
                    } else {
                        $STATE_CD = "63";
                    }
                } else if (0 != $STATE_COUNT31) {
                    if ($STATE_COUNT52 > 0) {
                        $STATE_CD = "88";
                    } else if ($STATE_COUNT51 > 0) {
                        $STATE_CD = "87";
                    } else {
                        $STATE_CD = "62";
                    }
                } else if (0 != $STATE_COUNT30) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "86";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "85";
                    } else {
                        $STATE_CD = "61";
                    }
                } else if (0 != $STATE_COUNT21) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "84";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "83";
                    } else {
                        $STATE_CD = "60";
                    }
                } else if (0 != $STATE_COUNT01) {
                    if ($STATE_COUNT42 > 0) {
                        $STATE_CD = "82";
                    } else if ($STATE_COUNT41 > 0) {
                        $STATE_CD = "81";
                    }
                }
            }
        } else if ($STATE_COUNT32 > 0) {
            $STATE_CD = "32";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT32) {
                if ($STATE_COUNT52 > 0) {
                    $STATE_CD = "90";
                } else if ($STATE_COUNT51 > 0) {
                    $STATE_CD = "89";
                } else {
                    $STATE_CD = "63";
                }
            }
        } else if ($STATE_COUNT31 > 0) {
            $STATE_CD = "31";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT31) {
                if ($STATE_COUNT52 > 0) {
                    $STATE_CD = "88";
                } else if ($STATE_COUNT51 > 0) {
                    $STATE_CD = "87";
                } else {
                    $STATE_CD = "62";
                }
            }
        } else if ($STATE_COUNT30 > 0) {
            $STATE_CD = "30";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT30) {
                if ($STATE_COUNT42 > 0) {
                    $STATE_CD = "86";
                } else if ($STATE_COUNT41 > 0) {
                    $STATE_CD = "85";
                } else {
                    $STATE_CD = "61";
                }
            }
        } else if ($STATE_COUNT21 > 0) {
            $STATE_CD = "21";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT21) {
                if ($STATE_COUNT42 > 0) {
                    $STATE_CD = "84";
                } else if ($STATE_COUNT41 > 0) {
                    $STATE_CD = "83";
                } else {
                    $STATE_CD = "60";
                }
            }
        } else if ($STATE_COUNT01 > 0) {
            $STATE_CD = "01";

            if ($TOTAL_STATE_COUNT != $STATE_COUNT01) {
                if ($STATE_COUNT42 > 0) {
                    $STATE_CD = "82";
                } else if ($STATE_COUNT41 > 0) {
                    $STATE_CD = "81";
                }
            }
        }

        if ($STATE_COUNT52 != 0 && $STATE_COUNT42 !=0) {
            if ($STATE_COUNT52 + $STATE_COUNT42 == $TOTAL_STATE_COUNT) {
                $STATE_CD = "52";
            }
        }

        return $STATE_CD;
    }

    /**
     * name :generateRandomPassword
     * comment : 비밀번호 임시 발급
     */
    function generateRandomPassword($length = 8) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specialCharacters = '!@#$%^&*()';
    
        $password = '';
        $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialCharacters[random_int(0, strlen($specialCharacters) - 1)];
    
        for ($i = strlen($password); $i < $length; $i++) {
            $randomSet = random_int(0, 2);
            if ($randomSet === 0) {
                $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            } else if ($randomSet === 1) {
                $password .= $numbers[random_int(0, strlen($numbers) - 1)];
            } else {
                $password .= $specialCharacters[random_int(0, strlen($specialCharacters) - 1)];
            }
        }
    
        return str_shuffle($password);
    }

    /**
     * name : formatKoreanPhoneNumber
     * comment : +82등 +82로 연락처가 시작일시 0으로 변경
     */
    function formatKoreanPhoneNumber($number) {
        // +82 10을 010으로 변경
        $formattedNumber = preg_replace('/^\+82 10/', '010', $number);
        
        $areaCodes = [
              '2'   => '02'
            , '31'  => '031'
            , '32'  => '032'
            , '33'  => '033'
            , '41'  => '041'
            , '42'  => '042'
            , '43'  => '043'
            , '44'  => '044'
            , '51'  => '051'
            , '52'  => '052'
            , '53'  => '053'
            , '54'  => '054'
            , '55'  => '055'
            , '61'  => '061'
            , '62'  => '062'
            , '63'  => '063'
            , '64'  => '064'
        ];
        
        foreach ($areaCodes as $intl => $local) {
            $formattedNumber = preg_replace('/^\+82 '.$intl.'/', $local, $formattedNumber);
        }
    
        return $formattedNumber;
    }

    /**
     * name : gfn_getDELIVERY
     * comment : 배송비 가져오기
     */
    function gfn_getDELIVERY($total_price) {
        $price = gfn_getZcmcommonVal("COL010", "PRICE", "TH1_THEM_CD"); // 배송비

        $if_price = gfn_getZcmcommonVal("COL010", "IFPRICE", "TH1_THEM_CD"); // 조건 금액

        if ($total_price >= $if_price) {
            $price = 0;
        }
        
        return $price;
    }

    /**
     * name : gfn_OPTION_QUANTITY
     * comment : 옵션 값 가져오기
     */
    function gfn_OPTION_QUANTITY($BOBY_INFO) {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $table = 'CATEGORY_OPTION'; // 카테고리3 옵션

            $arrValue = array();
            $where = '';

            if (empty($BOBY_INFO['OPTION_SEQ'])) {
                gfn_isValidation(999, "", "카테고리3 옵션 누락");
            } else {
                $where .= " AND OPTION_SEQ = :OPTION_SEQ";
                $arrValue[':OPTION_SEQ'] = $BOBY_INFO['OPTION_SEQ'];
            }

            if (empty($BOBY_INFO['CATEGORY3_SEQ'])) {
                gfn_isValidation(999, "", "카테고리3 누락");
            } else {
                $where .= " AND CATEGORY3_SEQ = :CATEGORY3_SEQ";
                $arrValue[':CATEGORY3_SEQ'] = $BOBY_INFO['CATEGORY3_SEQ'];
            }

            if (empty($BOBY_INFO['VAL'])) {
                $VAL = 'OPTION_NAME';
            }  else {
                $VAL = $BOBY_INFO['VAL'];
            }

            $sql = "
                 SELECT {$VAL} AS TEMP
                   FROM {$table}
                  WHERE 1
                     {$where}";
            
            $name_sql = "옵션값 검색";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

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
     * name : gfn_ORDER_OPTION_QUANTITY
     * comment : 옵션 값 가져오기
     */
    function gfn_ORDER_OPTION_QUANTITY($BOBY_INFO) {
        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        try {
            $table = 'PURCHASE_OPTION'; // 주문 카테고리3 옵션

            $arrValue = array();
            $where = '';

            if (empty($BOBY_INFO['PURCHASE_SEQ'])) {
                gfn_isValidation(999, "", "시퀀스 옵션 누락");
            } else {
                $where .= " AND PURCHASE_SEQ = :PURCHASE_SEQ";
                $arrValue[':PURCHASE_SEQ'] = $BOBY_INFO['PURCHASE_SEQ'];
            }

            if (empty($BOBY_INFO['OPTION_SEQ'])) {
                gfn_isValidation(999, "", "카테고리3 옵션 누락");
            } else {
                $where .= " AND OPTION_SEQ = :OPTION_SEQ";
                $arrValue[':OPTION_SEQ'] = $BOBY_INFO['OPTION_SEQ'];
            }

            if (empty($BOBY_INFO['CATEGORY3_SEQ'])) {
                gfn_isValidation(999, "", "카테고리3 누락");
            } else {
                $where .= " AND CATEGORY3_SEQ = :CATEGORY3_SEQ";
                $arrValue[':CATEGORY3_SEQ'] = $BOBY_INFO['CATEGORY3_SEQ'];
            }

            if (empty($BOBY_INFO['VAL'])) {
                $VAL = 'OPTION_NAME';
            } else {
                $VAL = $BOBY_INFO['VAL'];
            }

            $sql = "
                 SELECT {$VAL} AS TEMP
                   FROM {$table}
                  WHERE 1
                     {$where}";
            
            $name_sql = "주문 옵션값 검색";
            $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);
            
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
     * name : gfn_TOKEN_DELETE
     * comment : 소셜 토큰 연결 해제
     */
    function gfn_TOKEN_DELETE($TYPE, $TOKEN) {
        if ($TYPE == "KAKAO") {
            //연결 해제(탈퇴시)
            // cURL 초기화
            $ch = curl_init();

            $unlink_url = 'https://kapi.kakao.com/v1/user/unlink';

            $access_token = $TOKEN;

            // cURL 옵션 설정
            curl_setopt($ch, CURLOPT_URL, $unlink_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('target_id_type' => 'user_id')));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // cURL 실행 및 응답 얻기
            $response = curl_exec($ch);

            // cURL 에러 체크
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            }

            // cURL 세션 종료
            curl_close($ch);

            // 응답 확인
            $result = json_decode($response, true);

            // 연동 해제가 성공했는지 확인
            if (isset($result['id'])) {
                echo '카카오 사용자 연동 성공';
            } else {
                echo '카카오 사용자 연동 실패';
            }

            return $result;
        } else if ($TYPE == "NAVER") {
            $unlink_api_url = 'https://nid.naver.com/oauth2.0/token';

            $client_id = NAVER_LOGIN_CLIENT_ID;
            $client_secret = NAVER_LOGIN_CLIENT_SECRET;

            $access_token = $TOKEN;

            // service_provider 생성
            $service_provider = base64_encode($client_id . ":" . $client_secret);

            // cURL 초기화
            $ch = curl_init();

            // cURL 옵션 설정
            curl_setopt($ch, CURLOPT_URL, $unlink_api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'access_token' => $access_token,
                'grant_type' => 'delete',
                'service_provider' => $service_provider  // service_provider 추가
            )));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // cURL 실행 및 응답 얻기
            $response = curl_exec($ch);

            // cURL 에러 체크
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch);
            }

            // cURL 세션 종료
            curl_close($ch);

            // 응답 확인
            $result = json_decode($response, true);

            return $result;
        }
    }

    /**
     * name : multiple_file_upload
     * comment : 다중 파일 업로드
     *           $idx_nm : 업로드 할 파일의 배열값
     *           $prefix : 유니크 ID를 생성에 필요한 이름 ex) 'hello_' -> hello_61553f16824ff
     *           $file_size : 파일크기
     */
    function pik_multiple_file_upload($dir, $idx_nm, $prefix = '') {
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

                    if (!copy($_FILES[$idx_nm]['tmp_name'][$key], $target_file)) {
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
    function pik_one_file_upload($dir, $idx_nm, $prefix = '') {
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
                //@unlink($target_file);

                if (file_exists($target_file)) {
                    gfn_isValidation(403, "", "이미 같은 이름의 파일이 존재합니다.");
                }

                if (!copy($_FILES[$idx_nm]['tmp_name'], $target_file)) {
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
?>