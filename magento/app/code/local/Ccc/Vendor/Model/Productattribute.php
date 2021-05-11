<?php

 class Ccc_Vendor_Model_Productattribute extends Mage_Eav_Model_Attribute
 {
 
     const MODULE_NAME = 'Ccc_Vendor';

     protected $_eventObject = 'attribute';
   
 	public function _construct()
 	{
 		 $this->_init('vendor/productattribute');
 	}

   public function isScopeGlobal()
   {
   	  return true;
   }
   
 }