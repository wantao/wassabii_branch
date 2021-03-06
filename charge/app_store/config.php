<?php 
	$app_store_shop_goods_config = array(
	//普通充值(shop_type=0)
	'hs_ios_60_stone' => array('cash'=>30,'yuanbao'=>60,'extra_yuanbao'=>0,'shop_type'=>0,'item_id'=>3101),
	'hs_ios_315_stone' => array('cash'=>150,'yuanbao'=>300,'extra_yuanbao'=>15,'shop_type'=>0,'item_id'=>3102),
	'hs_ios_660_stone' => array('cash'=>300,'yuanbao'=>600,'extra_yuanbao'=>60,'shop_type'=>0,'item_id'=>3103),
	'hs_ios_1820_stone' => array('cash'=>790,'yuanbao'=>1580,'extra_yuanbao'=>240,'shop_type'=>0,'item_id'=>3104),
	'hs_ios_3600_stone' => array('cash'=>1490,'yuanbao'=>2980,'extra_yuanbao'=>620,'shop_type'=>0,'item_id'=>3105),
	'hs_ios_7500_stone' => array('cash'=>2990,'yuanbao'=>5980,'extra_yuanbao'=>1520,'shop_type'=>0,'item_id'=>3106),
	/*//推荐充值(shop_type=2)
	'tj_dg_30cash' => array('cash'=>30,'yuanbao'=>300,'extra_yuanbao'=>300,'shop_type'=>2,'item_id'=>106),
	'tj_dg_128cash' => array('cash'=>128,'yuanbao'=>1280,'extra_yuanbao'=>1280,'shop_type'=>2,'item_id'=>107),
	'tj_dg_328cash' => array('cash'=>328,'yuanbao'=>3280,'extra_yuanbao'=>3280,'shop_type'=>2,'item_id'=>108),
	'tj_dg_648cash' => array('cash'=>648,'yuanbao'=>6480,'extra_yuanbao'=>6480,'shop_type'=>2,'item_id'=>109),
	*/
	//月卡(shop_type=1)
	'hs_ios_vip_card' => array('cash'=>300,'yuanbao'=>600,'extra_yuanbao'=>0,'shop_type'=>1,'item_id'=>3201),
	);
	
	require_once '../../unity/self_log.php';
	
	function get_product_info_by_product_id($productId) {
		global $app_store_shop_goods_config;
		if (!isset($app_store_shop_goods_config[$productId])) {
			writeLog("get_yuanbao_by_product_id not find productId:".$productId,LOG_NAME::ERROR_LOG_FILE_NAME);
			return false;			
		}
		return $app_store_shop_goods_config[$productId];
	}
	function get_product_list() {
		$procduct_arry = array();
		global $app_store_shop_goods_config;
		foreach($app_store_shop_goods_config as $product_key=>$product_value) {
			array_push($procduct_arry, array('name'=>$product_key,'cash'=>$product_value['cash']));	
		}
		return $procduct_arry;
	}
?>