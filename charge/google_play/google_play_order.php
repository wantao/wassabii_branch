<?php 
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * It is also available through the world-wide-web at this URL:
 * https://github.com/MG2Innovations/google-play-license-verification/blob/master/LICENSE
 */

/**
 * A representation of a single order returned by the Google Play In-App Billing service
 *
 * @category   GooglePlay
 * @package    GooglePlay_Licensing
 */
class GooglePlayOrder
{
	/**
	 * @var string
	 */
	protected $_orderId;

	/**
	 * @var string
	 */
	protected $_packageName;

	/**
	 * @var string
	 */
	protected $_productId;

	/**
	 * @var long
	 */
	protected $_purchaseTime;

	/**
	 * @var purchaseState
	 */
	protected $_purchaseState;
	
	/**
	 * @var string
	 */
	protected $_developerPayload;

	/**
	 * @var string
	 */
	protected $_purchaseToken;

	/**
	 * @param Object $order
	 */
	public function  __construct($order)
	{
		$vars = get_object_vars($order);

		if(!isset($vars["orderId"]))
			throw new Exception("No orderId");

		$this->_orderId = $vars["orderId"];

		if(!isset($vars["packageName"]))
			throw new Exception("No packageName");

		$this->_packageName = $vars["packageName"];

		if(!isset($vars["productId"]))
			throw new Exception("No productId");

		$this->_productId = $vars["productId"];

		if(!isset($vars["purchaseTime"]))
			throw new Exception("No purchaseTime");

		$this->_purchaseTime = $vars["purchaseTime"];

		if(!isset($vars["purchaseState"]))
			throw new Exception("No purchaseState");

		$this->_purchaseState = $vars["purchaseState"];
		
		/*if(!isset($vars["developerPayload"]))
			throw new Exception("No developerPayload");

		$this->_developerPayload= $vars["developerPayload"];*/
		
		if(!isset($vars["purchaseToken"]))
			throw new Exception("No purchaseToken");

		$this->_purchaseToken= $vars["purchaseToken"];
	}

	/**
	 * Get the order ID for this order.
	 *
	 * @return string
	 */
	public function getOrderId()
	{
		return $this->_orderId;
	}

	/**
	 * Get the application package name
	 *
	 * @return string
	 */
	public function getPackageName()
	{
		return $this->_packageName;
	}

	/**
	 * Get the product ID
	 *
	 * @return string
	 */
	public function getProductId()
	{
		return $this->_productId;
	}

	/**
	 * Get the purchase time
	 *
	 * @return string
	 */
	public function getPurchaseTime()
	{
		return $this->_purchaseTime;
	}

	/**
	 * Get the purchase state
	 *
	 * @return string
	 */
	public function getPurchaseState()
	{
		return $this->_purchaseState;
	}
	
	/**
	 * Get the developerPayload.
	 *
	 * @return string
	 */
	public function getDeveloperPayload()
	{
		return $this->_developerPayload;
	}
	
	/**
	 * Get the purchaseToken.
	 *
	 * @return string
	 */
	public function getPurchaseToken()
	{
		return $this->_purchaseToken;
	}

}
?>