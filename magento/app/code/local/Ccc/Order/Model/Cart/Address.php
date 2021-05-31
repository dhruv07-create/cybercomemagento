<?php
class Ccc_Order_Model_Cart_Address extends Mage_Core_Model_Abstract
{
	protected $cart = null;
	protected function _construct()
    {
        $this->_init('order1/cart_address');
    }

    public function setCart(Ccc_Order_Model_Cart $model)
    {
    	$this->cart = $model;
    	return $this;
    }

    public function getCart()
    {
    	if($this->cart)
    	{
    		return $cart;
    	}

    	if(!$this->getCartId())
    	{
    		return null;
    	}

    	$cart = Mage::getModel('order1/cart')->load($this->getCartId());
    	$this->setCart($cart);

    	return $cart;
    }

}