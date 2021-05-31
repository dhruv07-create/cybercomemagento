<?php
class Ccc_Order_Model_Cart extends Mage_Core_Model_Abstract
{
    protected $billingAddress = null;
    protected $shippingAddress = null;
    protected $customer = null;
    protected $items = null;
	
    protected function _construct()
    {
        $this->_init('order1/cart');
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

    public function setCartItems($items)
    {
        $this->items = $items;
        return $this; 
    }
    public function getCartItems()
    {
    	$collection = Mage::getModel('order1/cart_item')
        ->getCollection()
        ->addFieldToFilter('cart_id',['eq'=>$this->getId()]);
      
        $this->setCartItems($collection);

    	return $collection;
    }

    public function setCartBillingAddress(Ccc_Order_Model_Cart_Address $address)
    {
        $this->billingAddress = $address;
        return $this; 
    }

    public function getCartBillingAddress()
    {   
        if($this->billingAddress)
        {
            return $this->billingAddress;
        } 

        if(!$this->getCartId())
        {
            return null;
        }

        $cart_id = $this->getCartId();
        $address = Mage::getModel('order1/cart_address')
        ->getCollection() 
        ->addFieldToFilter('address_type',['eq'=>'billing'])
        ->addFieldToFilter('cart_id',['eq'=>$cart_id])
        ->getFirstItem();

        $this->setCartBillingAddress($address);
        
        return $address;       
    }

    public function setCartShippingAddress(Ccc_Order_Model_Cart_Address $address)
    {
         $this->shippingAddress = $address;
         return $this; 
    }

   public function getCartShippingAddress()
    {   
      if($this->shippingAddress)
       {
         return $this->shippingAddress;
       } 

        if(!$this->getCartId())
        {
            return null;
        }

        $cart_id = $this->getCartId();
        $address = Mage::getModel('order1/cart_address')
        ->getCollection() 
        ->addFieldToFilter('address_type',['eq'=>'shipping'])
        ->addFieldToFilter('cart_id',['eq'=>$cart_id])
        ->getFirstItem();
        
        $this->setCartShippingAddress($address);

        return $address;             
    }

    public function updateCartQuantity()
      {
           
           $cartItems = $this->getCartItems();
              
              $item_quantity=0; 
              $base_grand_total=0;
              foreach ($cartItems as  $cartItem) {
                
                 $item_quantity+=$cartItem->getQuantity();
                 $base_grand_total+=($cartItem->getPrice()*$cartItem->getQuantity());

              }

              $cart = $this->setItemQuantity($item_quantity)
              ->setBaseGrandTotal($base_grand_total)
              ->save() 
              ;
      }
      
}