<?php
/**
 *  
 */
class Ccc_Order_Block_Adminhtml_Order_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	
  public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_order_customer';
        $this->_blockGroup='order1';
        $this->_headerText = Mage::helper('sales')->__('customer');
        $this->removeButton('add');
    }
}