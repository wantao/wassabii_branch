<?php 

	require_once '../../unity/self_error_code.php';
	require_once '../../unity/self_account.php';
	require_once '../../unity/self_platform_define.php';
	require_once '../../unity/self_log.php';

	$ret_result = array();
	//判断是否设置了相应的登陆参数
	if (!is_param_right($_REQUEST)) {
		return;
	}
	
	$Time = time();
	$WasabiiKey = urldecode($_REQUEST['WasabiiKey']);
	$UserID = urldecode($_REQUEST['UserID']);
	$Cellphone_os_type = urldecode($_REQUEST['cellphone_os_type']);
	$GameID = 67;
	$OEMType = 1;
	$platform_id = PLATFORM::HONGXINGLAJIAO;
	
	//client 端还未接入sdk，相关参数不能得到，登录验证暂时取消
	
	//向红星辣椒平台发起登陆验证请求
	//$curl_param = "http://start.wasabii.com.tw/MobileFunction/mobile/LoginUINofity.aspx?Time=$Time&WasabiiKey=$WasabiiKey&UserID=$UserID&GameID=$GameID&OEMType=$OEMType";
	$curl_param = "http://start.wasabii.com.tw/MobileFunction/mobile/LoginUINofity.aspx?Time=$Time&WasabiiKey=$WasabiiKey&UserID=$UserID&GameID=$GameID&OEMType=$OEMType";
	//$url = "http://start.wasabii.com.tw/MobileFunction/mobile/LoginUINofity.aspx";
	//$param_data = "Time=$Time&WasabiiKey=$WasabiiKey&UserID=$UserID&GameID=$GameID&OEMType=$OEMType";
	//初始化
	$ch = curl_init();
	//设置选项，包括URL
	curl_setopt($ch, CURLOPT_URL, $curl_param);
	//curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $param_data);
	
	//执行并获取HTML文档内容
	$output = curl_exec($ch);
	curl_close($ch);
	$output_tmp = json_decode($output);
	//print_r($output);
	if (!isset($output_tmp->resultCode)) {
		writeLog("login_auth.php not set resultCode,return:".$output, LOG_NAME::ERROR_LOG_FILE_NAME);
		return;
	}
	if (!isset($output_tmp->resultDesc)) {
		writeLog("login_auth.php not set resultDesc,return:".$output, LOG_NAME::ERROR_LOG_FILE_NAME);
		return;
	}
	if (1 != $output_tmp->resultCode) {
		$ret_result['error_code'] = ErrorCode::ERROR_HONGXINGLAJIAO_LOGIN_AUTH_ERROR;	
		$ret_result['error_desc'] = $output_tmp->resultDesc; 
		echo json_encode($ret_result);
		writeLog("login_auth.php auth error resultCode:".$output_tmp->resultCode." errorDesc:".$output_tmp->resultDesc." url:".$curl_param, LOG_NAME::ERROR_LOG_FILE_NAME);
		return;
	}
	//writeLog("url:".$curl_param, LOG_NAME::ERROR_LOG_FILE_NAME);

	$session_key_arr = array();
	$oa = new AccountOperation();
    if (!$oa->update_account_info(mysql_escape_string($UserID),mysql_escape_string($WasabiiKey),$platform_id,$Cellphone_os_type)) {
    	make_return_err_code_and_des(ErrorCode::DB_OPERATION_FAILURE,get_err_desc(ErrorCode::DB_OPERATION_FAILURE));	
    	return;
    }
    
    $token_result = $oa->getTokenInfo(mysql_escape_string($platform_id.'_'.$UserID));
	
    $session_key_arr["error_code"] = ErrorCode::SUCCESS;
    $session_key_arr["session_key"] = $oa->get_session_key(mysql_escape_string($UserID), mysql_escape_string($WasabiiKey), $platform_id);
    $session_key_arr["is_accept_license"] = $token_result["is_accept_license"];
    $ret_result = json_encode($session_key_arr);
    print_r(urldecode($ret_result));
	//print_r($output);
	$account = $platform_id."_".mysql_escape_string($UserID);
	$oa->save_player_cellphone_os_type_every_time($account,$Cellphone_os_type);
	
	function is_param_right($request)
	{
		if (!isset($request)) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_LOGIN_AUTH_PARAMS,get_err_desc(ErrorCode::ERROR_NOT_SET_LOGIN_AUTH_PARAMS));	
			return false;
		}
		if (!isset($request['WasabiiKey'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_PLATE_KEY,get_err_desc(ErrorCode::ERROR_NOT_SET_PLATE_KEY));	
			return false;
		}
		if (!isset($request['UserID'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_UID,get_err_desc(ErrorCode::ERROR_NOT_SET_UID));	
			return false;	
		}
		if (!isset($request['platform_id'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_PING_TAI_ID,get_err_desc(ErrorCode::ERROR_NOT_SET_PING_TAI_ID));	
			return false;	
		}
		if (!isset($request['cellphone_os_type'])) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_CELLPHONE_OS_TYPE,get_err_desc(ErrorCode::ERROR_NOT_SET_CELLPHONE_OS_TYPE));	
			return false;	
		}
		return true;
	}
?>