<?php
	// 일반 결제 취소 [전체 취소]
	header('Content-Type:text/html; charset=utf-8');
	require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
	require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

	use Clef\Pdo7 as Pdo7;
	use Clef\ClefResult as ClefResult;

	function executeRefundCode() {
		//step1. 요청을 위한 파라미터 설정
		$key = INIS_KEY;
		$mid = INIS_MID;
		$type = "refund";
		$timestamp = date("YmdHis");
		$clientIp = SERVERID;
		
		$postdata = array();
		$postdata["mid"] = $mid;
		$postdata["type"] = $type;
		$postdata["timestamp"] = $timestamp;
		$postdata["clientIp"] = $clientIp;
		
		//// Data 상세
		$detail = array();
		$detail["tid"] = $_SESSION['INIS']['TID']; // 취소요청할 승인 TID값
		$detail["msg"] = $_SESSION['INIS']['INIS_MSG']; // 취소요청 사유

		$postdata["data"] = $detail;
		
		$details = str_replace('\\/', '/', json_encode($detail, JSON_UNESCAPED_UNICODE));

		//// Hash Encryption
		$plainTxt = $key.$mid.$type.$timestamp.$details;
		$hashData = hash("sha512", $plainTxt);

		$postdata["hashData"] = $hashData;

		//echo "plainTxt : ".$plainTxt."<br/><br/>"; 
		//echo "hashData : ".$hashData."<br/><br/>"; 


		$post_data = json_encode($postdata, JSON_UNESCAPED_UNICODE);
		
		//echo "**** 요청전문 **** <br/>" ; 
		//echo str_replace(',', ',<br>', $post_data)."<br/><br/>" ; 
		
		//step2. 요청전문 POST 전송
		
		$url = "https://iniapi.inicis.com/v2/pg/refund";
		
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
		
		//echo "**** 응답전문 **** <br/>" ;
		//echo str_replace(',', ',<br>', $response)."<br><br>";

		$resultMap = json_decode($response, true);

		//_p($resultMap);

		try {

			if (strcmp("00", $resultMap["resultCode"]) == 0) {
				$mysqldb = new Pdo7();
				$clefResult = new ClefResult();
		
				$mysqldb->link->beginTransaction();
		
				$ip = "";

				if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
					$ip = $_SERVER['HTTP_X_REAL_IP'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				
				$table = 'INICIS_CANCEL';
		
				$INICIS_SEQ = $_SESSION['INIS']['SEQ'];
				$CANCEL_SEQ = $_SESSION['INIS']['CANCEL_SEQ'];
				$TID = @(in_array($resultMap["tid"] , $resultMap) ? $resultMap["tid"] : $_SESSION['INIS']['TID']); // 전체취소 - 취소일자 cancelDate
				$CANCELDATE = @(in_array($resultMap["cancelDate"] , $resultMap) ? $resultMap["cancelDate"] : ""); // 전체취소 - 취소일자 cancelDate
				$CANCELTIME = @(in_array($resultMap["cancelTime"] , $resultMap) ? $resultMap["cancelTime"] : ""); // 전체취소 - 취소시간 cancelTime
				$CSHRCANCELNUM = @(in_array($resultMap["cshrCancelNum"] , $resultMap) ? $resultMap["cshrCancelNum"] : ""); // 전체취소 - 현금영수증 취소승인번호
				$DETAILRESULTCODE = @(in_array($resultMap["detailResultCode"] , $resultMap) ? $resultMap["detailResultCode"] : ""); // 전체취소 - 취소실패 응답시 상세코드
				$RECEIPTINFO = @(in_array($resultMap["receiptInfo"] , $resultMap) ? $resultMap["receiptInfo"] : ""); // 전체취소 - 특정 가맹점 전용 응답필드
				$PRTCDATE = @(in_array($resultMap["prtcDate"] , $resultMap) ? $resultMap["prtcDate"] : ""); // 부분취소 - 취소일자 prtcDate
				$PRTCTIME = @(in_array($resultMap["prtcTime"] , $resultMap) ? $resultMap["prtcTime"] : ""); // 부분취소 - 취소시간 prtcTime
				$PRTCTID = @(in_array($resultMap["prtcTid"] , $resultMap) ? $resultMap["prtcTid"] : ""); // 취소 승인 거래번호 prtcTid
				$PRTCPRICE = @(in_array($resultMap["prtcPrice"] , $resultMap) ? $resultMap["prtcPrice"] : ""); // 취소금액 prtcPrice
				$PRTCREMAINS = @(in_array($resultMap["prtcRemains"] , $resultMap) ? $resultMap["prtcRemains"] : ""); // 남은금액 prtcRemains
				$PRTCCNT = @(in_array($resultMap["prtcCnt"] , $resultMap) ? $resultMap["prtcCnt"] : ""); // 부분취소 요청 회수
				$PRTCTYPE = @(in_array($resultMap["prtcType"] , $resultMap) ? $resultMap["prtcType"] : ""); // 부분취소 구분 0 : 재승인방식, 1 : 부분취소
				$POINTAMOUNT = @(in_array($resultMap["pointAmount"] , $resultMap) ? $resultMap["pointAmount"] : ""); // 부분취소 시 취소된 포인트 금액
				$DISCOUNTAMOUNT = @(in_array($resultMap["discountAmount"] , $resultMap) ? $resultMap["discountAmount"] : ""); // 부분취소 시 취소된 할인금액
				$CREDITAMOUNT = @(in_array($resultMap["creditAmount"] , $resultMap) ? $resultMap["creditAmount"] : ""); // 부분취소 시 취소된 여신금액
				$CASHRECEIPTAMOUNT = @(in_array($resultMap["cashReceiptAmount"] , $resultMap) ? $resultMap["cashReceiptAmount"] : ""); // 부분취소 후 남은 금액에 대한 현금영수증 발행 대상 금액
				$CURRENCY = $_SESSION['INIS']['CURRENCY']; // WON, USD
				$CANCELTAX = $_SESSION['INIS']['TAX']; // 부가세
				$CANCELTAXFREE = $_SESSION['INIS']['TAXFREE']; // 비과세
				$INIS_CANCEL_NAME = $_SESSION['INIS']['CANCEL_NAME'];
		
				$values = array(
					  'INICIS_SEQ' => $INICIS_SEQ // 이니시스 시퀀스
					, 'CANCEL_SEQ' => $CANCEL_SEQ // 취소 시퀀스
					, 'TID' => $TID // 거래번호
					, 'TYPE_CD' => $type // 취소구분
					, 'CANCELDATE' => $CANCELDATE
					, 'CANCELTIME' => $CANCELTIME
					, 'CSHRCANCELNUM' => $CSHRCANCELNUM
					, 'DETAILRESULTCODE' => $DETAILRESULTCODE
					, 'RECEIPTINFO' => $RECEIPTINFO
					, 'PRTCDATE' => $PRTCDATE
					, 'PRTCTIME' => $PRTCTIME
					, 'PRTCTID' => $PRTCTID
					, 'PRTCPRICE' => $PRTCPRICE
					, 'PRTCREMAINS' => $PRTCREMAINS
					, 'PRTCCNT' => $PRTCCNT
					, 'PRTCTYPE' => $PRTCTYPE
					, 'POINTAMOUNT' => $POINTAMOUNT
					, 'DISCOUNTAMOUNT' => $DISCOUNTAMOUNT
					, 'CREDITAMOUNT' => $CREDITAMOUNT
					, 'CASHRECEIPTAMOUNT' => $CASHRECEIPTAMOUNT
					, 'CURRENCY' => $CURRENCY
					, 'CANCELTAX' => $CANCELTAX
					, 'CANCELTAXFREE' => $CANCELTAXFREE
					, 'reg_user' => $INIS_CANCEL_NAME // 등록자
					, 'reg_ip' => $ip // 등록자 아이피
					, 'reg_date' => date('Y-m-d H:i:s') // 등록날짜
				);
		
				$name_sql = "이니시스 취소내역 추가[전체취소]";
				$clefResult = $mysqldb->insert($table, $values, $name_sql);
		
				if (!$clefResult->getResult()) {
					gfn_isValidation(999, "",'잘못된 접근입니다. 503');
				}
		
				$mysqldb->link->commit();
			} else {
				gfn_isValidation(999, "", $resultMap["resultCode"]. " ". $resultMap["resultMsg"]);
			}
		} catch (Exception $e) {
			gfn_isValidation(999, "", $resultMap["resultMsg"]);
		}
	}
    
?>