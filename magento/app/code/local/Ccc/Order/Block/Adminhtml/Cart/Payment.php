<?php 


 class Ccc_Order_Block_Adminhtml_Cart_Payment extends Mage_Core_Block_Template
 {
 	public function __construct()
 	{
 		parent::__construct();
 	}


   public function getPayemntMethodTitle()
    {
    	$methods = Mage::getModel('payment/config');
    	$activemethod = $methods->getActiveMethods();
    	unset($activemethod['paypal_billing_agreement']);
    	unset($activemethod['checkmo']);
    	unset($activemethod['free']);
    	return $activemethod;
    }

    public function getPaymentMethod()
    {
    	return Mage::getModel('order1/cart')->getPaymentMethod();
    }
 }