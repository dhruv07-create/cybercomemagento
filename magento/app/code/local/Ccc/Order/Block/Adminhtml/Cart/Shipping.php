<?php 


 class Ccc_Order_Block_Adminhtml_Cart_Shipping extends Mage_Core_Block_Template
 {
 	public function __construct()
 	{
 		parent::__construct();
 	}

   public function getShippingAddress()
 	{
 		$cart_address = Mage::getModel('order1/cart')->getCartShippingAddress();
 		if($cart_address->getData())
 		{
 			return $cart_address;
 		}

 		$customer_address = Mage::getModel('customer/customer')->load(Mage::getModel('order1/session')->getCustomerId())->getDefaultShippingAddress();

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