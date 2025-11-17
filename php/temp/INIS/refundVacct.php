<?php
	// 가상계좌 결제 취소 [전체 취소]
	header('Content-Type:text/html; charset=utf-8');

	require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
	require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

	use Clef\Pdo7 as Pdo7;
	use Clef\ClefResult as ClefResult;

	function executeRefundVacctCode() {
		//step1. 요청을 위한 파라미터 설정
		$key = INIS_KEY;
		$iv = INIS_IV;
		$mid = INIS_MID;
		$type = "refund";
		$timestamp = date("YmdHis");
		$clientIp = SERVERID;
		
		$refundAcctNum = $_SESSION['INIS']['ACCT_NUM'];;  //환불계좌번호
		$encData = base64_encode(openssl_encrypt($refundAcctNum, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv));
		
		$postdata = array();
		$postdata["mid"] = $mid;
		$postdata["type"] = $type;
		$postdata["timestamp"] = $timestamp;
		$postdata["clientIp"] = $clientIp;
		
		//// Data 상세
		$detail = array();
		$detail["tid"] = $_SESSION['INIS']['TID']; // 취소요청할 승인 TID값
		$detail["msg"] = $_SESSION['INIS']['INIS_MSG']; // 취소요청 사유
		$detail["refundAcctNum"] = $encData;
		$detail["refundBankCode"] = $_SESSION['INIS']['ACCT_BANKCODE']; // 은행코드
		$detail["refundAcctName"] = $_SESSION['INIS']['ACCT_NAME']; // 계좌주명
		
		$postdata["data"] = $detail;
		
		$details = str_replace('\\/', '/', json_encode($detail, JSON_UNESCAPED_UNICODE));
		
		//// Hash Encryption
		$plainTxt = $key.$mid.$type.$timestamp.$details;
		$hashData = hash("sha512", $plainTxt);
		
		$postdata["hashData"] = $hashData;
		
		echo "plainTxt : ".$plainTxt."<br/><br/>" ; 
		echo "hashData : ".$hashData."<br/><br/>" ; 
		
		
		$post_data = json_encode($postdata, JSON_UNESCAPED_UNICODE);
		
		echo "**** 요청전문 **** <br/>" ; 
		echo str_replace(',', ',<br>', $post_data)."<br/><br/>" ; 
		
		
		//step2. 요청전문 POST 전송
		
		$url = "https://iniapi.inicis.com/v2/pg/refund/vacct";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		
		//step3. 결과출력
		
		echo "**** 응답전문 **** <br/>" ;
		echo str_replace(',', ',<br>', $response)."<br><br>";
	}
	
?>