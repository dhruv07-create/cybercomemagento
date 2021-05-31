<?php
class Ccc_Order_Block_Adminhtml_Cart_AccountInfo extends Mage_Core_Block_Template
{
	public $cart = null;
	public function __construct()
 	{
 		parent::__construct();
 	}

 	public function setCart(Ccc_Order_Model_Cart $cart)
 	{
 		$this->cart = $cart;
 		return $this;
 	}

 	public function getCart()
 	{
 		if($this->cart)
 		{
 			return $this->cart;
 		}

 		return null;
 	}
}