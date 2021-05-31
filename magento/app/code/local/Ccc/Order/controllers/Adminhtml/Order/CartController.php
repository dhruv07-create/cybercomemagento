<?php
class Ccc_Order_Adminhtml_Order_CartController extends Mage_Adminhtml_Controller_Action
{
    
    protected function _getSession()
    {
        return Mage::getSingleton('order1/session');
    }

/*    public function newAction()
    {
        $this->_redirect('adminhtml/customer/new');
    }
*/
    public function indexAction()
     {  
        $this->_title($this->__('Order'))->_title($this->__('Orders'))->_title($this->__('New Order'));
        $cart = $this->getCart();
       
        $this->loadLayout();
        $this->getLayout()->getBlock('main')->setCart($cart);
        $this->_setActiveMenu('order1/order1')
            ->renderLayout();
       }        

   public function startAction()
    {
    	$this->loadLayout();
    	$this->renderLayout();
        $this->_getSession()->clear();
    }

  public function getCart()
   {   
     $customer_id = (int)$this->getRequest()->getParam('customer_id');
     if($customer_id)
     {
        $this->_getSession()->setCustomerId($customer_id);
     }
     $customer_id = $this->_getSession()->getCustomerId(); 
    
      $model = Mage::getModel('order1/cart')->load($customer_id,'customer_id');

      if($model->getId())
      {
        return $model;
      }  
       
      $model->setCustomerId($customer_id)
      ->setCreatedAt(date('Y-m-d H:i:s'))
      ->save(); 

     return $model;
   }


    public function updateItemQuantityAction()
    {
      try{
       $data=$this->getRequest()->getPost('cartitem');     
       foreach ($data as $item_id => $quantity) 
       {
          $save = Mage::getModel('order1/cart_item')
          ->load($item_id)
          ->setQuantity($quantity)
          ->save(); 

          if($save)
          {
            Mage::getSingleton('adminhtml/session')->addSuccess('Item Quantity Updated');
          }else{
            throw new Exception("Some Quantity Not Updated", 1);
          }
       }

       $this->getCart()->updateCartQuantity();

      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }

       $this->_redirect('*/*/');
    }

    public function saveBillingAddressAction()
    {  
       try{
       $data = $this->getRequest()->getPost('cart')['billing_address'];
       $cart = $this->getCart();
       $model_add = $cart->getCartBillingAddress();

      if(!$model_add->getData())
      {
       $model_add->setCartId($cart->getId())
       ->setCustomerId($cart->getCustomerId())
       ->setAddressType('billing');
      }
        $model_add->addData($data);
      
        if($model_add->save())
        {
           Mage::getSingleton('adminhtml/session')->addSuccess("Address Save SuccessFully");
        }else{
          throw new Exception("Address Is not Save", 1);
        }

        $this->storeAddToCustomer('Billing',$data);

      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }

       $this->_redirect('*/*/'); 
    }
   
   public function saveShippingAddressAction()
    {  
       try{
                
            $cart = $this->getCart();
              $data = $this->getRequest()->getPost('cart')['shipping_address'];
            $model_add = $cart->getCartShippingAddress();
    
          if($this->getRequest()->getPost('shipping_as_billing')) 
          {
             $data = $cart->getCartBillingAddress()->getData();
             if(!$data)
             { 
                throw new Exception("Fill Billing address first", 1);
                
             }
             unset($data['address_id']);
             $model_add->addData($data);
             if($model_add->setAddressType('shipping')->save())
             {
                Mage::getSingleton('adminhtml/session')->addSuccess('Address Stored SuccessFully');
             }else
             {
                throw new Exception("Error To Store Address", 1);
                
             }
             $this->storeAddToCustomer('Shipping',$model_add->getData());
             $this->_redirect('*/*/'); 
             return;
          }

      if(!$model_add->getData())
      {
       $model_add->setCartId($cart->getCartId())
       ->setCustomerId($cart->getCustomerId())
       ->setAddressType('shipping');
      }
        $model_add->addData($data);
      
        if($model_add->save())
        {
          Mage::getSingleton('adminhtml/session')->addSuccess('Address Stored SuccessFully');
        }else{
           throw new Exception("Error To Store Address", 1);
           
        }
        $this->storeAddToCustomer('Shipping',$model_add->getData());
  
      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
     
       $this->_redirect('*/*/'); 
    }

    public function AddToCartAction()
      {  
      try{

         $cart = $this->getCart();
         $Ids = $this->getRequest()->getPost('product');
          foreach ($Ids as  $id) {
              
              $product = Mage::getModel('catalog/product')->load($id)->getData();
      
              $data=[];
              $data['sku']=$product['sku'];
              $data['name']=$product['name'];
              $data['description'] = $product['description'];
              $data['product_id'] =$product['entity_id'];
              $data['created_at'] = date('Y-m-d');
              $cart_item = Mage::getModel('order1/cart_item');

              $item_id = Mage::getModel('order1/cart_item')->getCollection()
              ->addFieldToFilter('product_id',['eq'=>$product['entity_id']])
              ->addFieldToFilter('cart_id',['eq'=>$cart->getId()])
              ->getFirstItem()->getId();

              if($item_id)
              {
                  $cart_item->load($item_id);
                  $quentity = $cart_item->getQuantity();
                  $cart_item->setQuantity($quentity+1);
              
              }else{
                $cart_item->setQuantity(1)
                ->setCartId($cart->getId());
              }

              $cart_item
              ->setPrice($product['price'])
              ->addData($data);
              
              if($cart_item->save())
              {
                Mage::getSingleton('adminhtml/session')->addSuccess('Item add To cart SuccessFully');
              }else{
                throw new Exception('Error To Store Item In Cart', 1);
                
              }
               
               $this->getCart()->updateCartQuantity();            
          }

      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
       
          $this->_redirect('*/*/index');
      }   

    /*  public function updateCartQuantity()
      {
           $cart = $this->getCart();
           $cartItems = $cart->getCartItems();
              
              $item_quantity=0; 
              $base_grand_total=0;
              foreach ($cartItems as  $cartItem) {
                
                 $item_quantity+=$cartItem->getQuantity();
                 $base_grand_total+=($cartItem->getPrice()*$cartItem->getQuantity());

              }

              $cart = $cart->setItemQuantity($item_quantity)
              ->setBaseGrandTotal($base_grand_total)
              ->save() 
              ;
      }*/
    
    public function saveBillingMathodAction()
    {
       try{
       $code = $this->getRequest()->getPost('billingMethod'); 
         $cart = $this->getCart();

         $save= $cart->setPaymentCode($code)
         ->save();

         if($save)
         {
           Mage::getSingleton('adminhtml/session')->addSuccess(' Billing Mathod  Stored SuccessFully');
         }else
         {
            throw new Exception("Error To Store Billing Method", 1);   
         }

      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
        $this->_redirect('*/*/index',['_current'=>true]);
    }
  
    public function saveShippingMathodAction()
    {
       try{ 
            $data = $this->getRequest()->getPost('shippingMethod');
        $cart = $this->getCart();       
               $value = explode('/',$data);
               $name = $value[0];
               $price = $value[1];
      
            $save = $cart->setShippingCode($name)
            ->setShippingAmount($price)
            ->save();

            if($save)
            {
               Mage::getSingleton('adminhtml/session')->addSuccess(' Shipping Mathod  Stored SuccessFully');
            }else{
                throw new Exception('Error To Store Shipping Method', 1);
            }

       }catch(Exception $e)
       {
          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
       }

      $this->_redirect('*/*/index',['_current'=>true]);
    }

    public function deleteItemAction()
    { 

      try{
          $item_id = $this->getRequest()->getParam('id');

          if(Mage::getModel('order1/cart_item')->load($item_id)->delete())
          {
             Mage::getModel('adminhtml/session')->addSuccess('Delete Item From cart SuccessFully');
          }else {
              throw new Exception("Error To Delete Items", 1);
          }

          $this->getCart()->updateCartQuantity();

      }catch(Exception $d)
      {  
         Mage::getSingleton('adminhtml/session')->addError($d->getMeaage());
      }
       
       $this->_redirect("*/*/index");
    }

    public function storeAddToCustomer($data,$data0)
    { 
       try{
          if(!$data0)
          {
            throw new Exception('Fill '.$data.'Address First', 1);
          }
          $cart = $this->getCart();
          if($this->getRequest()->getPost('save_in_address_book'))
         {   
              $sess = Mage::getModel('order1/session');
              $newData['firstname']=$data0['firstname'];
              $newData['middlename']=$data0['middlename'];
              $newData['lastname']=$data0['lastname'];
              $newData['suffix']=$data0['suffix'];
              $newData['street']=$data0['street'];
              $newData['city']=$data0['city'];
              $newData['prefix']=$data0['prefix'];
              $newData['country_id']=$data0['country'];
              $newData['postcode']=$data0['zipcode'];
              $newData['telephone']=$data0['telephone'];
              $newData['region']=$data0['state'];
              $customer_address = Mage::getModel('customer/address');
              $method = "getPrimary".$data."Address";
              $method2 = "setIsDefault".$data;
           
            if($cart->getCustomer()->$method())
            {
                $address_id = $cart->getCustomer()->$method()->getId();
                $customer_address->load($address_id);      
            }
            else{
              $customer_address->setParentId($cart->getCustomer()->getId())->setEntityTypeId(2)->$method2('1');
            }

          $customer_address->addData($newData);
          
          if($customer_address->save())
          {
             Mage::getSingleton('adminhtml/session')->addSuccess('Customer Address Stored SuccessFully');     
          }else{
            throw new Exception("Error To Store Address In Customer", 1);
            
          }

        }
      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }

    }

  public function checkoutAction()
    { 
        try
         { 
            $validate = $this->validate();
            if(!$validate)
            {
               $this->_redirect('*/*/index');
            }else{   
            $cart = $this->getCart();
            $shipping = $cart->getShippingAmount();
            $subTotal = $cart->getBaseGrandTotal();
            $cart->setGrandTotal($shipping+$subTotal);
            $cart->save();  
            $this->_redirect('*/adminhtml_order/save',['cart_id'=>$cart->getId()]); 
           }
        }catch(Exception $e)
        {
           
        }
    } 


    public function validate()
    {
       try {
       $cart = $this->getCart();

       if(!$cart->getCartItems()->getData())
       {
          Mage::getSingleton('adminhtml/session')->addError('Add Items To Cart');
           return false;
       }
       if(!$cart->getCartBillingAddress()->getId())
       {
         Mage::getSingleton('adminhtml/session')->addError('Fill Billing Address');
           return false;
       }
       if(!$cart->getCartShippingAddress()->getId())
       {
        Mage::getSingleton('adminhtml/session')->addError('Fill Shipping Address');
           return false;
       }
       if(!$cart->getPaymentCode())
       {

        Mage::getSingleton('adminhtml/session')->addError('Fill Payment Method');
           return false;
       }
       if(!$cart->getShippingCode()|| !$cart->getShippingAmount())
       {

        Mage::getSingleton('adminhtml/session')->addError('Fill Shipping Method');
           return false;
       }                           

        return true;   

       } catch (Exception $e) {
          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
       }
    }
}
