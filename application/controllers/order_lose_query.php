<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'common_func.php';
class Order_lose_query extends CI_Controller{
	var $CURRENT_PAGE = "丢单查询";
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
		$data['trans_id'] = isset($params['trans_id']) ? $params['trans_id'] : '';
		$this->load->model('order_lose_resend_model');
		$data['order_lose_query_type_list'] = isset($params['order_lose_query_type_list']) ? $params['order_lose_query_type_list'] : $this->order_lose_resend_model->get_order_lose_resend_type_list(LG_GOOGLE_PLAY_ORDER_LOSE_RESEND);
		$this->load->view("templates/header", $data);
		$this->load->view("order_lose_query/order_lose_query_show", $data);
		$this->load->view("templates/footer");
	}
	public function execute($trans_id,$order_ping_tai){
		$this->load->model("order_lose_query_model");
		$result = $this->order_lose_query_model->search($trans_id,$order_ping_tai);
		$data['result'] = $result;
		$this->load->view("order_lose_query/order_lose_query_result", $data);
		return;
	}
}
?>