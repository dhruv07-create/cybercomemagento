<?php
class Ccc_Order_Block_Adminhtml_Cart_AccountInfo extends Mage_Core_Block_Template
{
	public function __construct()
 	{
 		parent::__construct();
 	}

 	public function getCustomer()
 	{   $customer = Mage::getModel('order1/session')->getCustomerId();
 		return Mage::getModel('customer/customer')->load($customer);
 	}
}