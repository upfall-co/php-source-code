<?php

	class properties {

		function getAuthUrl($idc_name)	{
            $url = "stdpay.inicis.com/api/payAuth";
			switch ($idc_name) {
				case 'fc':
                    $authUrl = "https://fc".$url;
					break;
				case 'ks':
					$authUrl = "https://ks".$url;
					break;
				case 'stg':
					$authUrl = "https://stg".$url;
					break;
				default:
					break;
			}			
			return $authUrl;
		}

		function getAuthUrl_Mo($idc_name)	{
            $url = "mobile.inicis.com/smart/payReq.ini";
			switch ($idc_name) {
				case 'fc':
                    $authUrl = "https://fc".$url;
					break;
				case 'ks':
					$authUrl = "https://ks".$url;
					break;
				case 'stg':
					$authUrl = "https://stg".$url;
					break;
				default:
					break;
			}			
			return $authUrl;
		}

		function getNetCancel_Mo($idc_name)	{
            $url = "mobile.inicis.com/smart/payNetCancel.ini";
			switch ($idc_name) {
				case 'fc':
                    $authUrl = "https://fc".$url;
					break;
				case 'ks':
					$authUrl = "https://ks".$url;
					break;
				case 'stg':
					$authUrl = "https://stg".$url;
					break;
				default:
					break;
			}			
			return $authUrl;
		}

		function getNetCancel($idc_name)	{
            $url = "stdpay.inicis.com/api/netCancel";
			switch ($idc_name) {
				case 'fc':
                    $netCancel = "https://fc".$url;
					break;
				case 'ks':
					$netCancel = "https://ks".$url;
					break;
				case 'stg':
					$netCancel = "https://stg".$url;
					break;
				default:
					break;
			}			
			return $netCancel;
		}
	}

?>