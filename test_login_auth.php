<?php
	
	//玩家id
	//test//begin
	$UserID = "225109983";
	$WasabiiKey = "hongxinlajiao";
	$platform_id=1;
	$cellphone_os_type=1;
	//test//end
	//签名
	
	//对url中相关的参数做urlencode
	$UserID = urlencode($UserID);
	$WasabiiKey = urlencode($WasabiiKey);
	//开始发送奖励，下面curl_param中的192.168.1.16中的ip为测试ip，实际情况中需改成游戏web服务器的ip
	$curl_param = "http://203.70.19.146/login_auth/hongxinlajiao/login_auth.php?platform_id=$platform_id&UserID=$UserID&WasabiiKey=$WasabiiKey&cellphone_os_type=$cellphone_os_type";
	//$curl_param = "http://210.66.186.86/login_auth/hongxinlajiao/login_auth.php?platform_id=$platform_id&UserID=$UserID&WasabiiKey=$WasabiiKey";
	//初始化
	$ch = curl_init();
	//设置选项，包括URL
	//curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:20000/?type=gm&account=test1&cmd=reload+py_cpp");
	curl_setopt($ch, CURLOPT_URL, $curl_param);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	//执行并获取HTML文档内容
	$output = curl_exec($ch);
	$output = json_decode($output);
	//echo $curl_param;
	print_r($output);
	//释放curl句柄
	curl_close($ch);
?>