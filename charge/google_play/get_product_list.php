<?php 
	require_once '../../unity/self_config.php';
	require_once '../../unity/self_platform_define.php';
	require_once '../../unity/self_error_code.php';
	require_once '../../unity/self_log.php';
	require_once '../../unity/self_pay.php';
	require_once '../../unity/self_common.php';
	require_once 'config.php';
	
	
	$cip = get_remote_ip();
	if ($cip != '127.0.0.1') {
		exit("you have no permission to visit");
	}
	echo json_encode(get_product_list());
	
?>

