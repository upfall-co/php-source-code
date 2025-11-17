<?php
/**
 * 파일명 : shop_inquiry_code.php
 * 내용 : 문의하기 [샵전용]
 * 최초작성날짜 : 2023/11/21
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/11/21    V1.0
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
      $NAME = '';
      $MOBILE = '';
      $EMAIL = '';

      $TYPE_CD = get_request_param('TYPE_CD', 'GET');

      if (isset($_SESSION['MEMBER'])) {
          if (!empty($_SESSION['MEMBER'])) {
                $table = 'MEMBER';

                $arrValue = array();
                $arrValue[':ID'] = $_SESSION['MEMBER']['ID'];

                $sql = "
                     SELECT NAME
                          , MOBILE
                          , EMAIL
                       FROM {$table}
                      WHERE ID = :ID";

                $name_sql = "계정값";
                $clefResult = $mysqldb->get($sql, $arrValue, $name_sql);

                if (!$clefResult->getResult()) {
                    gfn_isValidation(800);
                }

                $data = $clefResult->getResultSet();

                if (empty($data)) {
                    dieAndMsgReplaceMove(shopFoldName.'/mypage/login.php', '로그인 후 이용해주세요.');
                }

                $NAME = _check_var($data['NAME']);
                $MOBILE = _check_var($data['MOBILE']);
                $EMAIL = _check_var($data['EMAIL']);

                if (!empty($MOBILE)) {
                    $MOBILE = formatPhoneNumber($MOBILE);
                }
          }
      } else {
        dieAndMsgReplaceMove(shopFoldName.'/mypage/login.php', '로그인 후 이용해주세요.');
      }

  } catch (Exception $e) {
      $arrRtn['code'] = $e->getCode();
      $arrRtn['msg'] = $e->getMessage();

      echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
  }