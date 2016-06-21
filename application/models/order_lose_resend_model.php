<?php 
class Order_lose_resend_model extends CI_Model {
	
	
	public function __construct(){
		parent::__construct();
	}
	
	public function get_order_lose_resend_type_list($resend_type_name,$select=0){
		if (0 == $select) {
			return array(array('id'=>2,'name'=>LG_GOOGLE_PLAY_ORDER_LOSE_RESEND),array('id'=>3,'name'=>LG_APPSTORE_ORDER_LOSE_RESEND));
		} else {
			if (LG_GOOGLE_PLAY_ORDER_LOSE_RESEND == $resend_type_name) {
				return array(array('id'=>2,'name'=>LG_GOOGLE_PLAY_ORDER_LOSE_RESEND),array('id'=>3,'name'=>LG_APPSTORE_ORDER_LOSE_RESEND));
			} else if (LG_APPSTORE_ORDER_LOSE_RESEND == $resend_type_name) {
				return array(array('id'=>3,'name'=>LG_APPSTORE_ORDER_LOSE_RESEND),array('id'=>2,'name'=>LG_GOOGLE_PLAY_ORDER_LOSE_RESEND));	
			}
		}
	} 
	
}
?>