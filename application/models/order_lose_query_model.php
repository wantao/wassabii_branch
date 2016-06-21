<?php 
class Order_lose_query_model extends CI_Model {
	public function __construct(){
		parent::__construct();
	}
	public function search($trans_id,$order_ping_tai){
		$this->load->database("default");
		$tbl_name = "tbl_recharge";
		$sql = "select A.*,B.name as server_name from $tbl_name as A,`tbl_server` as B where A.`orderid` = ".$this->db->escape($trans_id)." and A.order_ping_tai=$order_ping_tai and A.area_no = B.id";
		$query = $this->db->query($sql);
		$nums = $query->num_rows();
	
		if ($nums > 0) {	
			$result = $query->result();
			$this->db->close();
			return $result;
		} else {
			$tbl_name = "tbl_all_recharge";
			$sql = "select A.*,B.name as server_name from $tbl_name as A,`tbl_server` as B where A.`orderid` = ".$this->db->escape($trans_id)." and A.order_ping_tai=$order_ping_tai and A.area_no = B.id";
			$query = $this->db->query($sql);
			$result = $query->result();	
			$this->db->close();
			return $result;	
		}
	}
	
}
?>