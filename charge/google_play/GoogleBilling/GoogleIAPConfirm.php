<?php

        require_once '_google/utils/google-api-php-client/Google_Client.php';
        require_once '_google/utils/google-api-php-client/contrib/Google_AndroidpublisherService.php';

		

		$m_ResultCodeKey = 'result_code';
		$m_ResultCodeValue = 400; // 成功 200 失敗 400
		$m_ResultMessageKey = 'result_message';
		$m_ResultMessageValue = 'ok';
		$m_ResultOrderKey = 'orders';

		$resultJson = array();
		$resultJson[$m_ResultCodeKey] = $m_ResultCodeValue;
		$resultJson[$m_ResultMessageKey] = $m_ResultMessageValue;
		//print_r($_REQUEST);
		//$purchase_data= json_decode($_REQUEST['purchase_data']);
		//$order_info = get_object_vars($purchase_data);
		//print_r($order_info);
		
		$empty = $post = array();
		/*$post['PackageName'] = $order_info['packageName']; 
		$post['ProductID'] = $order_info['productId'];
		$post['PurchaseToken'] = $order_info['purchaseToken'];*/
		foreach ($_REQUEST as $varname => $varvalue) 
		{
			//if (empty($varvalue)) 
			if ((isset($varvalue) == FALSE) || ($varvalue == ""))
			{
				$empty[$varname] = $varvalue;
			}
			else 
			{
				$post[$varname] = $varvalue;
			}
		}
		//print_r($post);
		$theParameterInvalid = FALSE;
		if (array_key_exists('PackageName', $post) == FALSE) 
		{
			$theParameterInvalid = TRUE;
			$m_ResultMessageValue = "The 'PackageName' element is in the array.";
		}		
		if (array_key_exists('ProductID', $post) == FALSE) 
		{
			$theParameterInvalid = TRUE;
			$m_ResultMessageValue = "The 'ProductID' element is in the array.";
		}		
		if (array_key_exists('PurchaseToken', $post) == FALSE) 
		{
			$theParameterInvalid = TRUE;
			$m_ResultMessageValue = "The 'PurchaseToken' element is in the array.";
		}		
		
		if ($theParameterInvalid == TRUE)
		{
			$resultJson[$m_ResultCodeKey] = $m_ResultCodeValue;
			$resultJson[$m_ResultMessageKey] = $m_ResultMessageValue;
			echo json_encode($resultJson);
			
			return;
		}
		
		$package_name = $post['PackageName'];
		$product_id = $post['ProductID'];
		$purchase_token = $post['PurchaseToken'];
		
		//echo "package_name:".$package_name." product_id:".$product_id." purchase_token:".$purchase_token;
		
		//$api_client_id = '70702179833-vv2enkrii2dqr2k1jodd1k1bvj6a1310.apps.googleusercontent.com';
		//$api_client_secret = 'a09GE5HliXV4_3EZO0qzBUZV';
		//$api_json_access_token = '
	    //{
		//"access_token" : "ya29.AHES6ZSieRM6xKDm0tWbWaqmDF90tvyJaIdp59zH-05Y3yDx-95-",
		//"token_type" : "Bearer",
		//"expires_in" : 3600,
		//"refresh_token" : "1/N8oq7sYWYd3So3iQ93UpUsCYOEcFCT4pGPSS9Go8CXc"
		//}';
		
		$api_client_id = '680654114236-fgkriach4cpb8fnlgna3g0pm0t6mqu9h.apps.googleusercontent.com';
		$api_client_secret = 'OuMhPq4QH8mS5ozq-dk4bnH1';
		$api_json_access_token = '
		{
		"access_token" : "ya29.1.AADtN_WrM2lKtMMo_daLu1UIEtEhmhQ_BlakviYXoG1bcUHM1fMu9bgTDvWjlAhX8Ho-gtYY",
		"token_type" : "Bearer",
		"expires_in" : 3600,
		"refresh_token" : "1/4WztCEoj9BviF0LqEjA1BK1964qydXFcAAMvrN0zAU4MEudVrK5jSpoR30zcRFq6"
		}';
		
		
		$client = new Google_Client();
		if ($client == null)
		{
			$resultJson[$m_ResultCodeKey] = $m_ResultCodeValue;
			$resultJson[$m_ResultMessageKey] = 'new Google_Client() Fail.';
			echo json_encode($resultJson);
			
			return;
		}
	
        $client->setClientId($api_client_id);
        $client->setClientSecret($api_client_secret);
        $client->setAccessToken($api_json_access_token);
        $service = new Google_AndroidpublisherServiceV11($client);
        
        $inappPurchasesResult = array();
		try
        {   //echo "package_name:".$package_name." product_id:".$product_id." purchase_token:".$purchase_token;					  
            $inappPurchasesResult = $service->inappPurchases->get($package_name,$product_id,$purchase_token);
        }
        catch(Exception $e)
        {
			//echo "1111";
			$resultJson[$m_ResultCodeKey] = $m_ResultCodeValue;
			$resultJson[$m_ResultMessageKey] = $e->getMessage();
			echo json_encode($resultJson);
		
			return;
        }

		
        $new_access_token = $client->getAccessToken();
        if($new_access_token != $api_json_access_token)
		{
			// 更新
		}
		
		$resultJson[$m_ResultCodeKey] = 200;
		$resultJson[$m_ResultMessageKey] = 'ok';

		$resultJson[$m_ResultOrderKey] = $inappPurchasesResult;
		$inappPurchasesResultJson = json_encode($resultJson); // , JSON_FORCE_OBJECT
		echo $inappPurchasesResultJson;

?>