<?php 


 class Ccc_Order_Block_Adminhtml_Cart_Payment extends Mage_Core_Block_Template
 {
    protected $cart = null;
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

   public function getPayemntMethodTitle()
    {
    	$methods = Mage::getModel('payment/config');
    	$activemethod = $methods->getActiveMethods();
    	unset($activemethod['paypal_billing_agreement']);
    	unset($activemethod['checkmo']);
    	unset($activemethod['free']);
    	return $activemethod;
    }
 }