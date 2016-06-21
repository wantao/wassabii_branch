<?php
	if($_SERVER['REQUEST_METHOD'] != 'GET'){
		echo "Forbidden. Only GET request is allowed.";
		exit;
	}
	require_once '../../unity/self_config.php';
	require_once '../../unity/self_platform_define.php';
	require_once '../../unity/self_log.php';
	require_once '../../unity/self_pay.php';
	require_once '../../unity/self_error_code.php';
	require_once 'config.php';
	if (!is_param_right($_REQUEST)) {
		exit;	
	}
	
	$user_id = $_GET['user_id'];
	$cash = $_GET['cash'];
	$plat_transfer_code = $_GET['Transfer'];
	$plate_key = $_GET['WasabiiKey'];
	$server_id = $_GET['ServerID'];
	
	if (!is_numeric($server_id)) {
		exit;
	}
	
	$platform_id = PLATFORM::HONGXINGLAJIAO;
	$order_source_platform_id = ORDER_SOURCE_PLAT_FORM::HONGXINGLAJIAO;
	
	if (!is_WasabiiKey_right($user_id,$cash,$plat_transfer_code,$plate_key)) {
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);	
		exit;
	}
	//判断订单是否已经存在
	$op = new OrderOperation();
	$order_status = $op->get_order_status($plat_transfer_code,$order_source_platform_id);
	if (ErrorCode::PROCESSED_ORDER == $order_status) {
		$ret_result['ReturnCode'] = 1;
		echo json_encode($ret_result);	
		writeLog("error:order_source_platform_id:".$order_source_platform_id." tranfser_code:".$plat_transfer_code." has been proccessed!", LOG_NAME::ERROR_LOG_FILE_NAME);
		exit;	
	}
	if (ErrorCode::NOT_FIND_ORDER != $order_status) {
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);	
		exit;	
	}
	$product_id = $cash;
	$product_info = get_product_info_by_product_id($product_id);
	if (!$product_info) {
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);	
		exit;	
	}
	
	$cash = $product_info['cash'];
	$yuanbao = $product_info['yuanbao'];
	
	//万一合了区，区号发生了变化，找到该区对应的最新区号
	$login_server_info = get_login_server_info($server_id);
	if (empty($login_server_info)) {
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);	
		exit;	
	}
	$server_id = $login_server_info['current_code'];
	
	$digitid = get_player_digit_id_by_uid($user_id,$platform_id,$server_id);
	$ret_result = array();
	if (!$digitid) {
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);
		exit;			
	}
	
	//商品类型
	$shop_type = $product_info['shop_type'];
	$item_id = $product_info['item_id'];
	/*//推荐充值月卡特殊处理
	if (2 == $shop_type || 1 == $shop_type) {
		//如果已经存在该产品记录，则直接返回1	
		$res_product = $op->get_db_product_info($digitid,$product_id);
		if (ErrorCode::SUCCESS != $res_product['error_code'] || ErrorCode::ERROR_NOT_FIND_THE_PRODUCT_INFO != $res_product['error_code']) {
			$ret_result['ReturnCode'] = 1;
			echo json_encode($ret_result);
			writeLog("not putong chongzhi shangping error:order_source_platform_id:".$order_source_platform_id." tranfser_code:".$plat_transfer_code." account:".$platform_id.'_'.$user_id." shop_type:".$shop_type." product_id:".$product_id." error_desc:".get_err_desc($res_product['error_code']), LOG_NAME::ERROR_LOG_FILE_NAME);
			exit;	
		}
	}*/

	$db_key = 'default';
	$db_link = my_connect_mysql($db_key);
	if (!$db_link) {
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);	
		exit;
	}
	mysql_query("BEGIN");
	$insert_tbl_recharge_order_sql = "insert into `tbl_recharge_order` (`digitid`, `areaid`, `money`, `yuanbao`,`pay_ok`) values($digitid,$server_id,$cash,$yuanbao,1)";
	if (!mysql_query($insert_tbl_recharge_order_sql,$db_link)) {
		writeLog("error:".mysql_error($db_link)." sql:".$insert_tbl_recharge_order_sql, LOG_NAME::ERROR_LOG_FILE_NAME);
		mysql_query("ROLLBACK");
		mysql_close($db_link);
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);	
		exit;			
	}
	$order_id = mysql_insert_id($db_link);
	$insert_tbl_recharge_sql = "insert into `tbl_recharge` (`Id`, `playerid`, `area_no`, `money`, `yuanbao`, `orderid`, `ping_tai`, `shop_type`, `item_id`, `order_ping_tai`, `product_id`) values
	($order_id,$digitid,$server_id,$cash,$yuanbao,'".mysql_real_escape_string($plat_transfer_code,$db_link)."',$platform_id,$shop_type,$item_id,$order_source_platform_id,'$product_id')";
	if (!mysql_query($insert_tbl_recharge_sql,$db_link)) {
		writeLog("error:".mysql_error($db_link)." sql:".$insert_tbl_recharge_sql, LOG_NAME::ERROR_LOG_FILE_NAME);
		mysql_query("ROLLBACK");
		mysql_close($db_link);
		$ret_result['ReturnCode'] = 0;
		echo json_encode($ret_result);	
		exit;	
	}
	mysql_query("COMMIT");
	mysql_close($db_link);
	$ret_result['ReturnCode'] = 1;
	echo json_encode($ret_result);
	
	writeLog("server_id:".$server_id." player_account:".$platform_id.'_'.$user_id." cost lajiaodian:".$cash." then get yuanbao:".$yuanbao,LOG_NAME::CHARGE_SUCCESS_LOG_FILE_NAME);
	
	
	
	function is_param_right($request)
	{
		if (!isset($request)) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_CHARGE_NOTIFY_PARAMS,get_err_desc(ErrorCode::ERROR_NOT_SET_CHARGE_NOTIFY_PARAMS));	
			return false;
		}
		if (!isset($request['user_id'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_UID,get_err_desc(ErrorCode::ERROR_NOT_SET_UID));	
			return false;	
		}
		if (!isset($request['cash'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_CASH,get_err_desc(ErrorCode::ERROR_NOT_SET_CASH));	
			return false;	
		}
		if (!isset($request['Transfer'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_PLATE_TRANSFER_CODE,get_err_desc(ErrorCode::ERROR_NOT_SET_PLATE_TRANSFER_CODE));	
			return false;
		}
		if (!isset($request['WasabiiKey'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_PLATE_KEY,get_err_desc(ErrorCode::ERROR_NOT_SET_PLATE_KEY));	
			return false;
		}
		if (!isset($request['ServerID'])) {
			make_return_err_code_and_des(ErrorCode::URL_HAS_NO_SERVER_CODE,get_err_desc(ErrorCode::URL_HAS_NO_SERVER_CODE));	
			return false;	
		}
		return true;
	}
	
	function is_WasabiiKey_right($user_id,$cash,$plat_transfer_code,$WasabiiKey)
	{
		$static_key = 'd2e08f1175c4a858fe9d1fbae7bfb0d0';
		$local_WasabiiKey = md5($user_id.$cash.$plat_transfer_code.$static_key);
		if ($local_WasabiiKey != $WasabiiKey) {
			writeLog("is_WasabiiKey_right auth WasabiiKey false,local_WasabiiKey.".$local_WasabiiKey." plate WasabiiKey:".$WasabiiKey, LOG_NAME::ERROR_LOG_FILE_NAME);
			return false;
		}
		return true;
	}
?>