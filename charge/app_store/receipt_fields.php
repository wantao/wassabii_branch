<?php 
	class ReceiptFileds
	{
		/**
		 * The number of items purchased.
		 * @var string, interpreted as an integer
		 */
		protected $_quantity;
	
		/**
		 * The product identifier of the item that was purchased
		 * @var string
		 */
		protected $_product_id;
	
		/**
		 * The transaction identifier of the item that was purchased
		 * @var string
		 */
		protected $_transaction_id;
	
		/**
		 * For a transaction that restores a previous transaction, the transaction identifier of the original transaction. Otherwise, identical to the transaction identifier.
		 * @var string
		 */
		protected $_original_transaction_id;
	
		/**
		 * The date and time that the item was purchased
		 * @var string, interpreted as an RFC 3339 date
		 */
		protected $_purchase_date;
		
		/**
		 * For a transaction that restores a previous transaction, the date of the original transaction
		 * @var string, interpreted as an RFC 3339 date
		 */
		protected $_original_purchase_date;
	
		/**
		 * The expiration date for the subscription, expressed as the number of milliseconds since January 1, 1970, 00:00:00 GMT.
		 * @var number
		 */
		protected $_expires_date;
		
		protected $_bid;
		/**
		 * A string that the App Store uses to uniquely identify the application that created the transaction.
		 * @var  string
		 */
		protected $_app_item_id;
	
		protected $_bvrs;
	
		/**
		 * @param Object $order
		 */
		public function  __construct($receipt)
		{
			$vars = get_object_vars($receipt);
	
			if(isset($vars["quantity"]))
				$this->_quantity = $vars["quantity"];
			if(isset($vars["product_id"]))
				$this->_product_id = $vars["product_id"];
			if(isset($vars["transaction_id"]))
				$this->_transaction_id = $vars["transaction_id"];
			if(isset($vars["original_transaction_id"]))
				$this->_original_transaction_id = $vars["original_transaction_id"];
			if(isset($vars["purchase_date"]))
				$this->_purchase_date = $vars["purchase_date"];
			if(isset($vars["original_purchase_date"]))
				$this->_original_purchase_date = $vars["original_purchase_date"];
			if(isset($vars["expires_date"]))
				$this->_expires_date = $vars["expires_date"];
			if(isset($vars["bid"]))
				$this->_bid = $vars["bid"];
			if(isset($vars["app_item_id"]))
				$this->_app_item_id = $vars["app_item_id"];
			if(isset($vars["bvrs"]))
				$this->_bvrs = $vars["bvrs"];
		}
	
		public function get_quantity()
		{
			return $this->_quantity;
		}
	
		public function get_product_id()
		{
			return $this->_product_id;
		}
		
		public function get_transaction_id()
		{
			return $this->_transaction_id;
		}
		
		public function get_original_transaction_id()
		{
			return $this->_original_transaction_id;
		}
		
		public function get_purchase_date()
		{
			return $this->_purchase_date;
		}
		
		public function get_original_purchase_date()
		{
			return $this->_original_purchase_date;
		}
		
		public function get_expires_date()
		{
			return $this->_expires_date;
		}
		
		public function get_bid()
		{
			return $this->_bid;
		}
		
		public function get_app_item_id()
		{
			return $this->_app_item_id;
		}
		
		public function get_bvrs()
		{
			return $this->_bvrs;
		}
	
	}
?>