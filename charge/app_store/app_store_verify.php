<?php
	require_once '../../unity/self_config.php';
	require_once '../../unity/self_error_code.php';	
	require_once '../../unity/self_log.php';
	require_once '../../unity/self_platform_define.php';
	require_once '../../unity/self_pay.php';
	require_once 'config.php';
	require_once 'receipt_validator.php';
	require_once 'receipt_fields.php';
	
	if (!is_param_right($_REQUEST)) {
		exit;		
	}

	$platform_id = urldecode($_REQUEST['platform_id']);
	$user_id = urldecode($_REQUEST['user_id']);
	$server_id = urldecode($_REQUEST['ServerID']);
	//writeLog("server_id:".$server_id, LOG_NAME::ERROR_LOG_FILE_NAME);
	$order_source_platform_id = ORDER_SOURCE_PLAT_FORM::APP_STORE;
	
	if (!is_numeric($platform_id) || !is_numeric($server_id)) {
		exit;	
	}
	
	$receipt  = urldecode($_REQUEST['receipt']);
	$endpoint = itunesReceiptValidator::SANDBOX_URL;
	//$endpoint = itunesReceiptValidator::PRODUCTION_URL;
	try {
	    $rv = new itunesReceiptValidator($endpoint, $receipt);
	    $info = $rv->validateReceipt();
	    $receipt_fileds = new ReceiptFileds($info);
	    $product_id = $receipt_fileds->get_product_id();
	    $plat_transfer_code = $receipt_fileds->get_transaction_id();
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
		$plat_transfer_code = $receipt_fileds->get_transaction_id();
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
	    
	}
	catch (Exception $ex) {
	    writeLog($ex->getMessage(), LOG_NAME::ERROR_LOG_FILE_NAME);
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
		if (!isset($request['receipt'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_RECEIPT,get_err_desc(ErrorCode::ERROR_NOT_SET_RECEIPT));	
			return false;	
		}
		return true;
	}
?>