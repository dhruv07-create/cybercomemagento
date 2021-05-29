<?php
class Ccc_Order_Model_Order extends Mage_Core_Model_Abstract
{
	protected function _construct()
    {
        $this->_init('order1/order');
    }

    public function getCustomer()
    {
    	return Mage::getModel('customer/customer')->load($this->getCustomerId());
    }

    public function getOrderItems()			
    {
    	$collection = Mage::getModel('order1/order_item')->getCollection()->addFieldToFilter('order_id',['eq'=>$this->getId()]);

    	return $collection;
    }

    public function getOrderBillingAddress()
    {
    	$ans = "SELECT address_id FROM order_address WHERE address_type ='billing' AND order_id = {$this->getId()}; ";
        $address_id = Mage::getModel('core/resource')->getConnection('core_read')->fetchOne($ans);
    	$model = Mage::getModel('order1/order_address')->load($address_id);
    	return $model;
    }

    public function getOrderShippingAddress()
    {
    	$ans = "SELECT address_id FROM order_address WHERE address_type ='shipping' AND order_id = {$this->getId()}; ";
        $address_id = Mage::getModel('core/resource')->getConnection('core_read')->fetchOne($ans);
    	$model = Mage::getModel('order1/order_address')->load($address_id);
    	return $model;
    }

}