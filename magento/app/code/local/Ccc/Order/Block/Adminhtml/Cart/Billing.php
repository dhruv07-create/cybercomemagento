<?php 


 class Ccc_Order_Block_Adminhtml_Cart_Billing extends Mage_Core_Block_Template
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

 	public function getBillingAddress()
 	{
 		$cart_address = $this->getCart()->getCartBillingAddress();
 		if($cart_address->getId())
 		{
 			return $cart_address;
 		}

 		$customer_address = $this->getCart()->getCustomer()->getDefaultBillingAddress();

       if($customer_address)
       {
 		if($customer_address->getData())
 		{
 			return $customer_address;
 		}
 	   }

 		return $cart_address;
 	}

 	public function getCountryOptions(){
        return Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
    }
 }