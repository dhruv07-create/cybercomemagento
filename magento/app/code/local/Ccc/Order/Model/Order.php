<?php
class Ccc_Order_Model_Order extends Mage_Core_Model_Abstract
{
    protected $customer=null;
    protected $billingAddress = null;
    protected $shippingAddress = null;
    protected $items = null;

	protected function _construct()
    {
        $this->_init('order1/order');
    }

    public function setCustomer(Mage_Customer_Model_Customer $customer)  
    {
         $this->customer=$customer;
         return $this;
    }

    public function getCustomer()
    {
        if($this->customer)
        {
            return $this->customer;
        } 

        if(!$this->getCustomerId())
        {
            return false;
        }
        
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        $this->setCustomer($customer);           
        return $this->customer;
    }

    public function setOrderItems($items)
    {
        $this->items = $items;
        return $this;
    }

    public function getOrderItems()
    {
        $collection = Mage::getModel('order1/order_item')
        ->getCollection()
        ->addFieldToFilter('order_id',['eq'=>$this->getId()]);
        $this->setOrderItems($collection);
        return $collection;
    }

    public function setOrderBillingAddress(Ccc_Order_Model_Order_Address $address)
    {
        $this->billingAddress = $address;
        return $this; 
    }

    public function getOrderBillingAddress()
    {   
        if($this->billingAddress)
        {
            return $this->billingAddress;
        } 

        if(!$this->getOrderId())
        {
            return null;
        }

        $order_id = $this->getOrderId();
        $address = Mage::getModel('order1/order_address')
        ->getCollection() 
        ->addFieldToFilter('address_type',['eq'=>'billing'])
        ->addFieldToFilter('order_id',['eq'=>$order_id])
        ->getFirstItem();

        $this->setOrderBillingAddress($address);
        
        return $address;       
    }

    public function setOrderShippingAddress(Ccc_Order_Model_Order_Address $address)
    {
         $this->shippingAddress = $address;
         return $this; 
    }

   public function getOrderShippingAddress()
    {   
      if($this->shippingAddress)
       {
         return $this->shippingAddress;
       } 

        if(!$this->getOrderId())
        {
            return null;
        }

        $order_id = $this->getOrderId();
        $address = Mage::getModel('order1/order_address')
        ->getCollection() 
        ->addFieldToFilter('address_type',['eq'=>'shipping'])
        ->addFieldToFilter('order_id',['eq'=>$order_id])
        ->getFirstItem();
        
        $this->setOrderShippingAddress($address);

        return $address;             
    }
}