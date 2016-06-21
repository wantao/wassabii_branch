<?php 
	$db = array(
		//全局数据库z_all
		'default' => array(
			'hostname' => '172.31.10.11:3306',
			'username' => 'dggame',
			'password' => 'Dh59wQBUiG',
			'database' => 'z_all',
			'dbdriver' => 'mysql',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'autoinit' => TRUE,
			'stricton' => FALSE
		),
		
		//2区game_db和game_db//begin
		'2_game_db' => array(
			'hostname' => '172.31.10.11:3306',
			'username' => 'dggame',
			'password' => 'Dh59wQBUiG',
			'database' => 'z_gamedb_2',
			'dbdriver' => 'mysql',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'autoinit' => TRUE,
			'stricton' => FALSE
		),
		'2_game_log' => array(
			'hostname' => '172.31.10.11:3306',
			'username' => 'dggame',
			'password' => 'Dh59wQBUiG',
			'database' => 'z_gamelog_2',
			'dbdriver' => 'mysql',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'autoinit' => TRUE,
			'stricton' => FALSE
		),	
	);
?>