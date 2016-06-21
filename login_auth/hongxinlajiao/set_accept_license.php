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
	$UserID = urldecode($_REQUEST['UserID']);
	$platform_id = urldecode($_REQUEST['platform_id']);
	//$platform_id = PLATFORM::HONGXINGLAJIAO;
	
	$account = $platform_id.'_'.$UserID;
	$oa = new AccountOperation();
	$token_result = $oa->getTokenInfo($account);
	if (empty($token_result)) {
		make_return_err_code_and_des(ErrorCode::ERROR_NOT_FIND_THE_UID,get_err_desc(ErrorCode::ERROR_NOT_FIND_THE_UID));	
		return;	
	}
	if (1 == $token_result["is_accept_license"]) {
		make_return_err_code_and_des(ErrorCode::SUCCESS,get_err_desc(ErrorCode::SUCCESS));	
		return;	
	}
	if (!$oa->update_accept_license_flag(1,$account)) {
		make_return_err_code_and_des(ErrorCode::DB_OPERATION_FAILURE,get_err_desc(ErrorCode::DB_OPERATION_FAILURE));	
		return;	
	}
	make_return_err_code_and_des(ErrorCode::SUCCESS,get_err_desc(ErrorCode::SUCCESS));	
	return;

	function is_param_right($request)
	{
		if (!isset($request)) {
			make_return_err_code_and_des(ErrorCode::ERROR_NOT_SET_LOGIN_AUTH_PARAMS,get_err_desc(ErrorCode::ERROR_NOT_SET_LOGIN_AUTH_PARAMS));	
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
		return true;
	}

?>