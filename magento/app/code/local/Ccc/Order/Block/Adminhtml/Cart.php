<?php 


 class Ccc_Order_Block_Adminhtml_Cart extends Mage_Core_Block_Template
 {
 	public function __construct()
 	{
 		parent::__construct();
 	}

 	public function getHeaderText()
 	{
 		return 'Cart';
 	}
 }