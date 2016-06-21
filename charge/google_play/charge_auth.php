<?php 

	require_once '../../unity/self_config.php';
	require_once '../../unity/self_platform_define.php';
	require_once '../../unity/self_error_code.php';
	require_once '../../unity/self_log.php';
	require_once '../../unity/self_pay.php';
	require_once 'config.php';
	require_once 'google_play_order.php';
	
	
	
	if (!is_param_right($_REQUEST)) {
		exit;	
	}
	
	$platform_id = urldecode($_REQUEST['platform_id']);
	$user_id = urldecode($_REQUEST['user_id']);
	$server_id = urldecode($_REQUEST['ServerID']);
	$purchase_data = urldecode($_REQUEST['purchase_data']);
	$purchase_data_signature = urldecode($_REQUEST['signature_data']);

	$data_json = json_decode($purchase_data);
	
	//print_r($data_json);
	if (!is_numeric($platform_id) || !is_numeric($server_id)) {
		exit;	
	}
	
	$order_source_platform_id = ORDER_SOURCE_PLAT_FORM::GOOGLE_PLAY;
	
	//$google_public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgL8vqO4c4rH0sTv767NwBaC2yKPNgptec7Q28jW435L4rOwCWY4lbbszl8oypNOdFtoKLrYGrB5xRgSik8Xj8qDw/tgNggIeY2CZ6n8ujvObadSlcC9THaT/FMOoEuMd4BIrxJYj69AlCTAmObCl2fMRuFyCCgXULvz7P/iWT5/rWd/GSwIQsg5xBxw7WpcS1gmEVkR5eqYdBfQ6QxXi0Rqx2EfRAcMgZR/NYyfdyH8X8mTz11W/bVO31sXVUUqrouxYOyO9M18Mdk755OlqGmJUICUchMBHS75ynYPyyWRlli+Wepf7up6hdDtKmXUQF4osPO3lkU9EsOzuYAXh3wIDAQAB';
	//$pubkey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($google_public_key, 64, "\n") . "-----END PUBLIC KEY-----";
	//$public_key_handle = openssl_get_publickey($pubkey);
	//$result = openssl_verify($purchase_data, base64_decode($purchase_data_signature), $public_key_handle, OPENSSL_ALGO_SHA1);
	
	//if ($result == 0)
	{
		$google_order = NULL;
		try {
			$google_order = new GooglePlayOrder($data_json);
		} catch (Exception $e) {
			writeLog("GooglePlayOrder_error:".$e->getMessage(),LOG_NAME::ERROR_LOG_FILE_NAME);
			make_return_err_code_and_des(ErrorCode::ERROR_ORDER_DATA_STRUTURE_CHANGED,get_err_desc(ErrorCode::ERROR_ORDER_DATA_STRUTURE_CHANGED));
			exit;
		}
		$product_id = $google_order->getProductId();
		$plat_transfer_code = $google_order->getOrderId();
		$package_name = $google_order->getPackageName();
		$purchase_token = $google_order->getPurchaseToken();
		
		//echo "product_id:".$product_id." package_name:".$package_name." purchase_token:".$purchase_token."<br>";
		
		$curl_param = "http://127.0.0.1/charge/google_play/GoogleBilling/GoogleIAPConfirm.php?PackageName=$package_name&ProductID=$product_id&PurchaseToken=$purchase_token";
		//echo $curl_param;
		//初始化
		$ch = curl_init();
		//设置选项，包括URL
		//curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:20000/?type=gm&account=test1&cmd=reload+py_cpp");
		curl_setopt($ch, CURLOPT_URL, $curl_param);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		//执行并获取HTML文档内容
		$google_communicate_result = curl_exec($ch);
		curl_close($ch);
		$google_communicate_result = json_decode($google_communicate_result);
		//print_r($google_communicate_result);
		/*if (!isset($google_communicate_result["result-code"])) {
			echo "111";
			writeLog("GoogleIAPConfirm not set result-code,"."server_id:".$server_id." player_account:".$platform_id.'_'.$user_id." purchase_data:".$purchase_data." signature:".$purchase_data_signature, LOG_NAME::ERROR_LOG_FILE_NAME);
			make_return_err_code_and_des(ErrorCode::ERROR_VERIFY_FAILURE,get_err_desc(ErrorCode::ERROR_VERIFY_FAILURE));
			exit;
		}*/
		//print_r($google_communicate_result->result_message);
		if ($google_communicate_result->result_code != 200) {
			//echo "2222";
			writeLog("GoogleIAPConfirm failure,result_code:".$google_communicate_result->result_code." result_message:".$google_communicate_result->result_message."server_id:".$server_id." player_account:".$platform_id.'_'.$user_id." purchase_data:".$purchase_data." signature:".$purchase_data_signature, LOG_NAME::ERROR_LOG_FILE_NAME);
			make_return_err_code_and_des(ErrorCode::ERROR_VERIFY_FAILURE,get_err_desc(ErrorCode::ERROR_VERIFY_FAILURE));
			exit;
		}
		//判断订单是否已经存在
		$op = new OrderOperation();
		$order_status = $op->get_order_status($plat_transfer_code,$order_source_platform_id);
		if (ErrorCode::PROCESSED_ORDER == $order_status) {
			make_return_err_code_and_des(ErrorCode::PROCESSED_ORDER,get_err_desc(ErrorCode::PROCESSED_ORDER));
			writeLog("error:order_source_platform_id:".$order_source_platform_id." tranfser_code:".$plat_transfer_code." has been proccessed!", LOG_NAME::ERROR_LOG_FILE_NAME);
			exit;	
		}
		if (ErrorCode::NOT_FIND_ORDER != $order_status) {
			make_return_err_code_and_des(Eorder_status,get_err_desc($order_status));
			writeLog("error:order_source_platform_id:".$order_source_platform_id." tranfser_code:".$plat_transfer_code." err_msg:".get_err_desc($order_status), LOG_NAME::ERROR_LOG_FILE_NAME);	
			exit;	
		}
		
		$product_info = get_product_info_by_product_id($product_id);
		//print_r($product_info);	
		if (!$product_info) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_FIND_THE_PRODUCT_INFO,get_err_desc(ErrorCode::ERROR_NOT_FIND_THE_PRODUCT_INFO));
			exit;	
		}
		
		//万一合了区，区号发生了变化，找到该区对应的最新区号
		$login_server_info = get_login_server_info($server_id);
		if (empty($login_server_info)) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_FIND_THE_SERVER_CODE,get_err_desc(ErrorCode::ERROR_NOT_FIND_THE_SERVER_CODE));	
			exit;	
		}
		$server_id = $login_server_info['current_code'];
		
		$digitid = get_player_digit_id_by_uid($user_id,$platform_id,$server_id);
		$ret_result = array();
		if (!$digitid) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_FIND_THE_UID,get_err_desc(ErrorCode::ERROR_NOT_FIND_THE_UID));
			exit;			
		}
	
		$db_key = 'default';
		$db_link = my_connect_mysql($db_key);
		if (!$db_link) {
			make_return_err_code_and_des(ErrorCode::ERROR_DB_CONNECT_FAILURE,get_err_desc(ErrorCode::ERROR_DB_CONNECT_FAILURE));
			exit;
		}
		
		$cash = $product_info['cash'];
		$yuanbao = $product_info['yuanbao'];
		$shop_type = $product_info['shop_type'];
		$item_id = $product_info['item_id'];
		mysql_query("BEGIN");
		$insert_tbl_recharge_order_sql = "insert into `tbl_recharge_order` (`digitid`, `areaid`, `money`, `yuanbao`,`pay_ok`,`shop_type`,`item_id`) values($digitid,$server_id,$cash,$yuanbao,1,$shop_type,$item_id)";
		if (!mysql_query($insert_tbl_recharge_order_sql,$db_link)) {
			writeLog("error:".mysql_error($db_link)." sql:".$insert_tbl_recharge_order_sql, LOG_NAME::ERROR_LOG_FILE_NAME);
			mysql_query("ROLLBACK");
			mysql_close($db_link);	
			make_return_err_code_and_des(ErrorCode::DB_OPERATION_FAILURE,get_err_desc(ErrorCode::DB_OPERATION_FAILURE));
			exit;			
		}
		$order_id = mysql_insert_id($db_link);
		//$plat_transfer_code = $google_order->getOrderId();
		$insert_tbl_recharge_sql = "insert into `tbl_recharge` (`Id`, `playerid`, `area_no`, `money`, `yuanbao`, `orderid`, `ping_tai`,`shop_type`,`item_id`,`order_ping_tai`,`product_id`) values
		($order_id,$digitid,$server_id,$cash,$yuanbao,'".mysql_real_escape_string($plat_transfer_code,$db_link)."',$platform_id,$shop_type,$item_id,$order_source_platform_id,'$product_id')";
		if (!mysql_query($insert_tbl_recharge_sql,$db_link)) {
			writeLog("error:".mysql_error($db_link)." sql:".$insert_tbl_recharge_sql, LOG_NAME::ERROR_LOG_FILE_NAME);
			mysql_query("ROLLBACK");
			mysql_close($db_link);
			make_return_err_code_and_des(ErrorCode::DB_OPERATION_FAILURE,get_err_desc(ErrorCode::DB_OPERATION_FAILURE));
			exit;	
		}
		mysql_query("COMMIT");
		mysql_close($db_link);
	
	    writeLog("server_id:".$server_id." player_account:".$platform_id.'_'.$user_id." charge success,cash:".$cash." yuanbao:".$yuanbao." shop_type:".$shop_type." item_id:".$item_id." product_id:".$product_id,LOG_NAME::CHARGE_SUCCESS_LOG_FILE_NAME);
		make_return_err_code_and_des(ErrorCode::SUCCESS,get_err_desc(ErrorCode::SUCCESS));	
		exit;
	} /*else {
		writeLog("server_id:".$server_id." player_account:".$platform_id.'_'.$user_id." verify error purchase_data:".$purchase_data." signature:".$purchase_data_signature, LOG_NAME::ERROR_LOG_FILE_NAME);
		make_return_err_code_and_des(ErrorCode::ERROR_VERIFY_FAILURE,get_err_desc(ErrorCode::ERROR_VERIFY_FAILURE));	
		exit;
	}*/

	function sign_data($data,$priv_key){
        openssl_sign($data, $signature, $priv_key);
        $signature=base64_encode($signature);
        return $signature;
	}
	
	function is_param_right($request)
	{
		if (!isset($request)) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_CHARGE_NOTIFY_PARAMS,get_err_desc(ErrorCode::ERROR_NOT_SET_CHARGE_NOTIFY_PARAMS));	
			return false;
		}
		if (!isset($request['platform_id'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_PING_TAI_ID,get_err_desc(ErrorCode::ERROR_NOT_SET_PING_TAI_ID));	
			return false;	
		}
		if (!isset($request['user_id'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_UID,get_err_desc(ErrorCode::ERROR_NOT_SET_UID));	
			return false;	
		}
		if (!isset($request['ServerID'])) {
			make_return_err_code_and_des(ErrorCode::URL_HAS_NO_SERVER_CODE,get_err_desc(ErrorCode::URL_HAS_NO_SERVER_CODE));	
			return false;	
		}
		if (!isset($request['purchase_data'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_GOOGLE_PLAY_ORDER_DATA,get_err_desc(ErrorCode::ERROR_NOT_SET_GOOGLE_PLAY_ORDER_DATA));	
			return false;	
		}
		if (!isset($request['signature_data'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_GOOGLE_PLAY_ORDER_DATA_SIGNATURE,get_err_desc(ErrorCode::ERROR_NOT_SET_GOOGLE_PLAY_ORDER_DATA_SIGNATURE));	
			return false;	
		}
		return true;
	}
?>