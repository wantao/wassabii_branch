<?php 
	//红星辣椒点=>我们的游戏币（元宝）
	//1红星辣椒点=1台币
	$hongxin_lajiao_shop_goods_config = array(
	//普通充值(shop_type=0)
	'dg_cay_105_stone' => array('cash'=>50,'yuanbao'=>100,'extra_yuanbao'=>5,'shop_type'=>0,'item_id'=>1101),
	'dg_cay_800_stone' => array('cash'=>350,'yuanbao'=>700,'extra_yuanbao'=>100,'shop_type'=>0,'item_id'=>1102),
	'dg_cay_1150_stone' => array('cash'=>500,'yuanbao'=>1000,'extra_yuanbao'=>150,'shop_type'=>0,'item_id'=>1103),
	'dg_cay_1880_stone' => array('cash'=>800,'yuanbao'=>1600,'extra_yuanbao'=>280,'shop_type'=>0,'item_id'=>1104),
	'dg_cay_2400_stone' => array('cash'=>1000,'yuanbao'=>2000,'extra_yuanbao'=>400,'shop_type'=>0,'item_id'=>1105),
	'dg_cay_4500_stone' => array('cash'=>1800,'yuanbao'=>3600,'extra_yuanbao'=>900,'shop_type'=>0,'item_id'=>1106),
	'dg_cay_7800_stone' => array('cash'=>3000,'yuanbao'=>6000,'extra_yuanbao'=>1800,'shop_type'=>0,'item_id'=>1107),
	/*//推荐充值(shop_type=2)
	'tj_dg_30cash' => array('cash'=>30,'yuanbao'=>300,'extra_yuanbao'=>300,'shop_type'=>2,'item_id'=>106),
	'tj_dg_128cash' => array('cash'=>128,'yuanbao'=>1280,'extra_yuanbao'=>1280,'shop_type'=>2,'item_id'=>107),
	'tj_dg_328cash' => array('cash'=>328,'yuanbao'=>3280,'extra_yuanbao'=>3280,'shop_type'=>2,'item_id'=>108),
	'tj_dg_648cash' => array('cash'=>648,'yuanbao'=>6480,'extra_yuanbao'=>6480,'shop_type'=>2,'item_id'=>109),
	*/
	//月卡(shop_type=1)
	'dg_cay_vipcard' => array('cash'=>200,'yuanbao'=>300,'extra_yuanbao'=>0,'shop_type'=>1,'item_id'=>1201),
	);
	
	require_once '../../unity/self_log.php';
	
	function get_product_info_by_product_id($productId) {
		global $hongxin_lajiao_shop_goods_config;
		if (!isset($hongxin_lajiao_shop_goods_config[$productId])) {
			writeLog("get_yuanbao_by_product_id not find productId:".$productId,LOG_NAME::ERROR_LOG_FILE_NAME);
			return false;			
		}
		return $hongxin_lajiao_shop_goods_config[$productId];
	}
?>