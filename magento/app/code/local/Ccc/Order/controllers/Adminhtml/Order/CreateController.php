<?php
class Ccc_Order_Adminhtml_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
    
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    protected function _initSession()
    {
        /**
         * Identify customer
         */
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int) $customerId);
        }      
    
        return $this;
    }

    public function indexAction()
     {  
        $this->_title($this->__('Order'))->_title($this->__('Orders'))->_title($this->__('New Order'));
        $this->_initSession();
        $this->loadLayout();
        $this->_setActiveMenu('order1/order1')
            ->renderLayout();
       }        

   public function startAction()
    {
    	$this->loadLayout();
    	$this->renderLayout();
        $this->_getSession()->clear();
    }

   public function saveAction()
    { 
        try
         {  

           if($this->getRequest()->getPost('updateitemquantity'))
           {
              $this->updateItemQuantity($this->getRequest()->getPost('cartitem'));
           }
           if($this->getRequest()->getPost('save_cart_bill_address'))
           {
              $this->saveCartBillingAddress($this->getRequest()->getPost('cart')['billing_address']);
           }
           if($this->getRequest()->getPost('save_cart_ship_address'))
           {
              $this->saveCartShippingAddress($this->getRequest()->getPost('cart')['shipping_address']);
           }
           if($this->getRequest()->getPost('payment_methods'))
           {
              $this->saveBillingMathod($this->getRequest()->getPost('billingMethod'));
           }
          if($this->getRequest()->getPost('shipping_methods'))
           {
              $this->saveShippingMathod($this->getRequest()->getPost('shippingMethod'));
           }
           if($this->getRequest()->getPost('checkout'))
           {  
            $sess = Mage::getModel('order1/session');
            $cart_id = $sess->getCartId();
            $cart = Mage::getModel('order1/cart')->load($cart_id);
            $shipping = $cart->getShippingAmount();
            $subTotal = $cart->getBaseGrandTotal();
            $cart->setGrandTotal($shipping+$subTotal);
            $cart->save();  
              $this->_redirect('*/adminhtml_order/save'); 
           }
           

        }catch(Exception $e)
        {
           
        }
    } 

    public function updateItemQuantity($data)
    {
      try{

       foreach ($data as $item_id => $quantity) 
       {
          $save = Mage::getModel('order1/cart_item')->load($item_id)->setQuantity($quantity)->save(); 

          if($save)
          {
            Mage::getSingleton('adminhtml/session')->addSuccess('Item Quantity Updated');
          }else{
            throw new Exception("Some Quantity Not Updated", 1);
            
          }
       }

       $this->updateCartQuantity();

      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }

       $this->_redirect('*/*/');
    }

    public function saveCartBillingAddress($data)
    {  
       try{

       $model_add = Mage::getModel('order1/cart')->getCartBillingAddress();
       $sess = Mage::getModel('order1/session');
      if(!$model_add->getData())
      {
       $model_add->setCartId($sess->getCartId())->setCustomerId($sess->getCustomerId())->addData($data)->setAddressType('billing');
      }else{
        $model_add->addData($data);
      }
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
   
   public function saveCartShippingAddress($data)
    {  
       try{

           
            $model_add = Mage::getModel('order1/cart')->getCartShippingAddress();
    
          if($this->getRequest()->getPost('shipping_as_billing')) 
          {
             $data = Mage::getModel('order1/cart')->getCartBillingAddress()->getData();
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
       $sess = Mage::getModel('order1/session');
       $model_add->setCartId($sess->getCartId())->setCustomerId($sess->getCustomerId())->addData($data)->setAddressType('shipping');
      }else{
        $model_add->addData($data);
      }
        if($model_add->save())
        {
          Mage::getSingleton('adminhtml/session')->addSuccess('Address Stored SuccessFully');
        }else{
           throw new Exception("Error To Store Address", 1);
           
        }
        $this->storeAddToCustomer('Shipping',$model_add->getData());
          


      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addSuccess($e->getMessage());
      }
     
       $this->_redirect('*/*/'); 
    }


    public function SessionAction()
    {
       $ans = "select cart_id from cart WHERE customer_id={$this->getRequest()->getParam('customer_id')} ";
    
       $cart_id = Mage::getModel('core/resource')->getConnection('core_read')->fetchOne($ans);
            if(!$cart_id)
            {
               $cart = Mage::getModel('order1/cart')->setCustomerId($this->getRequest()->getParam('customer_id'))->save();
               $cart_id = $cart->getId();  
            }
        Mage::getModel('order1/session')->setCustomerId($this->getRequest()->getParam('customer_id'))->setCartId($cart_id);

          $this->_redirect('*/*/index',['_current'=>true]);
    }

    public function AddToCartAction()
      {  
      try{

        
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
              $cartID=Mage::getModel('order1/session')->getCartId();
              $q = "select item_id from cart_item WHERE product_id={$product['entity_id']} AND cart_id={$cartID} ";
              $item_id = Mage::getModel('core/resource')->getConnection('core_read')->fetchOne($q);
              if($item_id)
              {
                  $cart_item->load($item_id);
                  $quentity = $cart_item->getQuantity();
                  $cart_item->setQuantity($quentity+1);
              }else{
                $cart_item->setQuantity(1)->setCartId( Mage::getModel('order1/session')->getCartId());
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
               
               $this->updateCartQuantity();
            
          }

    


      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addSuccess($e->getMessage());
      }
       
          $this->_redirect('*/*/index');
      }   

      public function updateCartQuantity()
      {
           $cartItems = Mage::getModel('order1/cart')->load(Mage::getModel('order1/session')->getCartId())->getCartItems();
              $item_quantity=0; 
              $base_grand_total=0;
              foreach ($cartItems as  $cartItem) {
                
                 $item_quantity+=$cartItem->getQuantity();
                 $base_grand_total+=($cartItem->getPrice()*$cartItem->getQuantity());

              }

              $cart = Mage::getModel('order1/cart')->load(Mage::getModel('order1/session')->getCartId())->setItemQuantity($item_quantity)
              ->setBaseGrandTotal($base_grand_total)
              ->save() 
              ;
      }
    
    public function saveBillingMathod($code)
    {
       try{
       
         $save= Mage::getModel('order1/cart')->load(Mage::getModel('order1/session')->getCartId())->setPaymentCode($code)->save();

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
  
   /* public function updateQuantityAction()
    {
       try{

      }catch(Exception $e)
      {
        Mage::getSingleton('adminhtml/session')->addSuccess($e->getMessage());
      }
        print_r($this->getRequest()->getPost());
    }*/


    public function saveShippingMathod($data)
    {
       
       try{
         
               $value = explode('/',$data);
               $name = $value[0];
               $price = $value[1];
      
            $save = Mage::getModel('order1/cart')->load(Mage::getModel('order1/session')->getCartId())->setShippingCode($name)->setShippingAmount($price)->save();

            if($save)
            {
               Mage::getSingleton('adminhtml/session')->addSuccess(' Shipping Mathod  Stored SuccessFully');
            }else{
                throw new Exception('Error To Store Shipping Method', 1);
            }

       }catch(Exception $e)
       {
          $this->_getSession()->addError($e->getMessage());
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

          $this->updateCartQuantity();

      }catch(Exception $d)
      {  
         $this->_getSession()->addError($d->getMeaage());
      }
       
       $this->_redirect("*/adminhtml_order_create/index");
    }

    public function storeAddToCustomer($data,$data0)
    { 
       try{

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
            if(Mage::getModel('customer/customer')->load($sess->getCustomerId())->$method())
            {
                $address_id = Mage::getModel('customer/customer')->load($sess->getCustomerId())->$method()->getId();
                $customer_address->load($address_id);      
            }
            else{
              $customer_address->setParentId($sess->getCustomerId())->setEntityTypeId(2)->$method2('1');
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
        Mage::getSingleton('adminhtml/session')->addSuccess($e->getMessage());
      }

    }
}
