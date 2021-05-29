<?php 


 class Ccc_Order_Block_Adminhtml_Cart_Shipment extends Mage_Core_Block_Template
 {
 	public function __construct()
 	{
 		parent::__construct();
 	}
 	public function getShippingMethods()
 	{
 		$shippingMethods = Mage::getModel('shipping/config')->getActiveCarriers(); 

 		return $shippingMethods;
 	}

 	public function getShippingMethod()
 	{
 		$shippingMethod =  Mage::getModel('order1/cart')->getShippingMethod();
         
         return $shippingMethod->getShippingCode().'/'.$shippingMethod->getShippingAmount(); 
 	}
 }