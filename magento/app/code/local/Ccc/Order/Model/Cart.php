<?php
class Ccc_Order_Model_Cart extends Mage_Core_Model_Abstract
{
	protected function _construct()
    {
        $this->_init('order1/cart');
    }

    public function getCartItems()
    {
    	$collection = Mage::getModel('order1/cart_item')->getCollection()->addFieldToFilter('cart_id',['eq'=>$this->getId()]);
    	return $collection;
    }

    public function getCartBillingAddress()
    {   $cart_id = Mage::getModel('order1/session')->getCartId();
        $ans = "SELECT address_id FROM cart_address WHERE address_type ='billing' AND cart_id = {$cart_id}; ";
        $address_id = Mage::getModel('core/resource')->getConnection('core_read')->fetchOne($ans); 

        return Mage::getModel('order1/cart_address')->load($address_id);       
    }

   public function getCartShippingAddress()
    {   $cart_id = Mage::getModel('order1/session')->getCartId();
        $ans = "SELECT address_id FROM cart_address WHERE address_type ='shipping' AND cart_id = {$cart_id}; ";
        $address_id = Mage::getModel('core/resource')->getConnection('core_read')->fetchOne($ans); 

        return Mage::getModel('order1/cart_address')->load($address_id);       
    }

    public function getPaymentMethod()
    {   
    	$cart_id = Mage::getModel('order1/session')->getCartId();
    	return Mage::getModel('order1/cart')->load($cart_id)->getPaymentCode();
    }

    public function getShippingMethod()
    {   
    	$cart_id = Mage::getModel('order1/session')->getCartId();
    	return Mage::getModel('order1/cart')->load($cart_id);
    }


}