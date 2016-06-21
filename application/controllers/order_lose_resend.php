<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'common_func.php';
class Order_lose_resend extends CI_Controller{
	var $CURRENT_PAGE = "丢单补发";
	var $GM_LEVEL = "-1";
	
	public function show($params = array()){
			$this->load->library('session');
			$logged_in = $this->session->userdata('logged_in');		
			if($logged_in == false){
				$this->load->view("main/main_not_logged");
				return;
			}
			$gm_level = $this->session->userdata('gm_level');
			$this->load->model('main_model');
			$pages = $this->main_model->get_pages($gm_level);
			if(!isset($pages)){
				$this->load->view("main/main_not_enough_level");
				$this->load->view("templates/footer");
				return;
			}
			
			$data['current_page'] = $this->CURRENT_PAGE;
			$data['pages'] = $pages;
			
			$uri_string = $this->session->CI->uri->segments[1];
			if (!is_sub_url_string($data['pages'],$uri_string)) {
				$this->load->view("main/main_not_enough_level");
				$this->load->view("templates/footer");
				return;	
			}
			
			$this->load->model('order_lose_resend_model');
			$data['player_id'] = isset($params["player_id"]) ? $params["player_id"] : '';
	
			$data['order_lose_resend_type_list'] = (isset($params["order_lose_resend_type_list"])) ? $params["order_lose_resend_type_list"] : $this->order_lose_resend_model->get_order_lose_resend_type_list(LG_GOOGLE_PLAY_ORDER_LOSE_RESEND);	
			$data['product_list'] = (isset($params["product_list"])) ? $params["product_list"] : $this->_get_product_list($data['order_lose_resend_type_list'][0]['name']);
			$data['trans_id'] = isset($params["trans_id"]) ? $params["trans_id"] : '';
			$this->load->view("templates/header", $data);
			$this->load->view("order_lose_resend/order_lose_resend_show", $data);
			$this->load->view("templates/footer");	
	}

	
	private function _get_product_list($resend_type_name){
		$curl_param = '';
		if (LG_GOOGLE_PLAY_ORDER_LOSE_RESEND == $resend_type_name) {
			//$curl_param = "http://127.0.0.1/wassabii_branch/charge/google_play/get_product_list.php";	
			$curl_param = "http://127.0.0.1/charge/google_play/get_product_list.php";
		} else if (LG_APPSTORE_ORDER_LOSE_RESEND == $resend_type_name) {
			//$curl_param = "http://127.0.0.1/wassabii_branch/charge/app_store/get_product_list.php";	
			$curl_param = "http://127.0.0.1/charge/app_store/get_product_list.php";
		} else {
			exit("not fine resend_type_name:".$resend_type_name);
		}
		
		//初始化
		$ch = curl_init();
		//设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, $curl_param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		//执行并获取HTML文档内容
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output);		
	}
	
	public function execute($digit_id,$resend_type_name,$product_name,$trans_id){
		$curl_param = '';
		if (LG_GOOGLE_PLAY_ORDER_LOSE_RESEND == $resend_type_name) {
			//$curl_param = "http://127.0.0.1/wassabii_branch/charge/google_play/charge_auth_bufa.php";	
			$curl_param = "http://127.0.0.1/charge/google_play/charge_auth_bufa.php";
		} else if (LG_APPSTORE_ORDER_LOSE_RESEND == $resend_type_name) {
			//$curl_param = "http://127.0.0.1/wassabii_branch/charge/app_store/app_store_bufa.php";
			$curl_param = "http://127.0.0.1/charge/app_store/app_store_bufa.php";	
		} else {
			exit("execute not fine resend_type_name:".$resend_type_name);
		}
		$curl_param .= "?digit_id=$digit_id&product_id=$product_name&plat_transfer_code=$trans_id";
		
		//初始化
		$ch = curl_init();
		//设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, $curl_param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		//执行并获取HTML文档内容
		$output = curl_exec($ch);
		curl_close($ch);
		echo $output;	
	}
	
	public function update_product_list($resend_type_name,$player_id,$trans_id) {
		$resend_type_name = $this->_getParam($resend_type_name);
		$player_id = $this->_getParam($player_id);
		$trans_id = $this->_getParam($trans_id);

		$resend_type_name = $this->_to_string(urldecode($resend_type_name));
		$player_id = $this->_to_string(urldecode($player_id));
		$trans_id = $this->_to_string(urldecode($trans_id));
		if ($player_id == '%20') {//非领奖邮件
			$player_id = '';
		}	
		if ($trans_id == '%20') {
			$trans_id = '';	
		}	
		
		$this->load->model('order_lose_resend_model');
		$ret_arry = array(
			"player_id" => $player_id,
			"order_lose_resend_type_list" => $this->order_lose_resend_model->get_order_lose_resend_type_list($resend_type_name,1),
			"product_list" => $this->_get_product_list($resend_type_name),
			"trans_id" => $trans_id,
		);
		$this->show($ret_arry);
		
	}
	
	public function _getParam($param){
		$this->load->model("common_model");
		return $this->common_model->deprefix($param);
	}
	private function _to_string($escaped_string){
		return strtr($escaped_string, array("%3A" => ':', "%2C" => ','));
	}
}

?>

