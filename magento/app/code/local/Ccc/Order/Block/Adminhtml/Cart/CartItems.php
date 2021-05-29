<?php 


 class Ccc_Order_Block_Adminhtml_Cart_CartItems extends Mage_Core_Block_Template
 {
 	public function __construct()
 	{
 		parent::__construct();
 	}

 	public function getCartItems()
 	{
 		 return Mage::getModel('order1/cart')->load(Mage::getModel('order1/session')->getCartId())->getCartItems();
 	}
 }