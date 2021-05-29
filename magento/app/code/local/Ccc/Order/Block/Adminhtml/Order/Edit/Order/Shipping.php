<?php
class Ccc_Order_Block_Adminhtml_Order_Edit_Order_Shipping extends Mage_Core_Block_Template
{
	public function __construct()
 	{
 		parent::__construct();
 	}

 	public $order = null;
 	 public function setOrder(Ccc_Order_Model_Order $order)
     {
         $this->order = $order;
         return $this;         
     } 

     public function getOrder()
     {
     	 if($this->order)
     	 {
     	 	return $this->order;
     	 }

     	 return null;
     }

     public function getCountryOptions(){
        return Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
    }
}