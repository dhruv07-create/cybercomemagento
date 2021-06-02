<?php
class Ccc_Order_Block_Adminhtml_Order_Edit_Order_Status extends Mage_Core_Block_Template
{
       protected $status = [
        'Placed'=>1,
        'Pending'=>2,
        'Hold'=>2,
        'Success'=>3,
        'Failed'=>3
    ];

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


    public function getStatusOptions()
    {
        return $this->status;
    }
}