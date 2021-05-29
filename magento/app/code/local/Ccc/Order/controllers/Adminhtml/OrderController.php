<?php
class Ccc_Order_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
	   $this->loadLayout();
	 
	   $this->renderLayout();

	}

	public function editAction()
	{
       $this->loadLayout();
       $order_id = $this->getRequest()->getParam('order_id');
	   $order = Mage::getModel('order1/order')->load($order_id);
       $h = $this->getLayout()->getBlock('final')->setOrder($order);
       $this->renderLayout();		
	}

	public function saveAction()
	{  

       try{


		$order_id = $this->getRequest()->getParam('order_id');
		if($this->getRequest()->getPost('save_order_bill_address'))
		{
			$order_billing_address = Mage::getModel('order1/order')->load($order_id)->getOrderBillingAddress();
            $data = $this->getRequest()->getPost('order')['billing_address'];

            $order_billing_address->addData($data);
            if($order_billing_address->save())
            {
            	Mage::getSingleton('adminhtml/session')->addSuccess('Address Updated Successfully');
            }else{
            	throw new Exception("Error Processing Address", 1);
            	
            } 

        $this->_redirect('*/adminhtml_order/edit',['_current'=>true]);

		}elseif ($this->getRequest()->getPost('save_order_ship_address')) 
		{
             
             $order_shipping_address = Mage::getModel('order1/order')->load($order_id)->getOrderShippingAddress();
            $data = $this->getRequest()->getPost('order')['shipping_address'];

            $order_shipping_address->addData($data);
           
            if( $order_shipping_address->save())
            {
            	Mage::getSingleton('adminhtml/session')->addSuccess('Address Updated Successfully');
            }else{
            	throw new Exception("Error Processing Address", 1);
            } 


         $this->_redirect('*/adminhtml_order/edit',['_current'=>true]);

		}else 
		{

	    $id = $this->saveOrderData();
	    if($id)
	    {
	       $this->_getSession()->addSuccess('Checkout Successfully');	
	    }else{
	    	throw new Exception("Checkout Fail", 1);
	    }
		$this->saveOrderShippingAdd($id);
		$this->saveOrderBillingAdd($id);
	    $this->saveOrderItems($id); 
        $session = Mage::getModel('order1/session');
	     $cart = Mage::getModel('order1/cart')->load($session->getCartId());
	     if(!$cart->delete())
	     {
	     	throw new Exception('Error To Process Checkout', 1);
	     	
	     } 

		}

	    }catch(Exception $e)
        {
       	   $this->_getSession()->addError($e->getMessage());
        }	
        $this->_redirect('*/adminhtml_order');
	}

	public function massCancelAction()
	{
		$ids = $this->getRequest()->getPost('order_ids');


		foreach ($ids as  $id) {
	
			if(Mage::getModel('order1/order')->load($id)->setStatus('cancle')->save())
			{
				Mage::getSingleton('adminhtml/session')->addSuccess('Updated Successfully');
			}
		}

		$this->_redirect('*/*/');
	}

	public function saveOrderItems($id)
	{ 
		$session = Mage::getModel('order1/session');
	    $items = Mage::getModel('order1/cart')->load($session->getCartId())->getCartItems();
    

	    foreach ($items as  $item)
	     {
	     	$data = $item->getData();
	     	unset($data['item_id']);
	     	unset($data['cart_id']);
	        $order_item = Mage::getModel('order1/order_item')->setOrderId($id)->addData($data)->save();
  
	     }   

	}

	public function saveOrderData()
	{
		$session = Mage::getModel('order1/session');

		$cart = Mage::getModel('order1/cart')->load($session->getCartId());

		$order = Mage::getModel('order1/order');
        
        $data = $cart->getData();

        $customer = Mage::getModel('customer/customer')->load($session->getCustomerId())->getData();
        $order_data = [];

        $order_data['cart_id']=$data['cart_id'];
        $order_data['customer_id']=$data['customer_id'];
        $order_data['shipping_code']=$data['shipping_code'];
        $order_data['payment_code']=$data['payment_code'];
        $order_data['shipping_amount']=$data['shipping_amount'];
        $order_data['item_quantity']=$data['item_quantity'];
        $order_data['subtotal']=$data['base_grand_total'];
        $order_data['grand_total']=$data['grand_total'];
        $order_data['created_at']=date('Y-m-d');
        $order_data['customer_prefix']=$customer['prefix'];
        $order_data['customer_email']=$customer['email'];
        $order_data['customer_firstname']=$customer['firstname'];
        $order_data['customer_middlename']=$customer['middlename'];
        $order_data['customer_lastname']=$customer['lastname'];
        $order_data['status']=0;
  
        $order->addData($order_data)->setStatus('pending');
        $order->save(); 

        return $order->getId();

	}

   public function saveOrderShippingAdd($id)
   {   
   	  try {
   	  	$session = Mage::getModel('order1/session');
   	   $cart_address = Mage::getModel('order1/cart')->load($session->getCartId())->getCartShippingAddress()->getData();
        
       unset($cart_address['cart_id']);  
       unset($cart_address['address_id']);  
  
   	   $orderAdd = Mage::getModel('order1/order_address')->setOrderId($id)->addData($cart_address);
   	   if(!$orderAdd->save())
   	   {
   	   	  throw new Exception("Error To Process Shipping Address", 1);
   	   	  
   	   }
   	  } catch (Exception $e) {
   	  	  
   	  	  Mage::getModel('adminhtml/session')->addError($e->getMessage());
   	  } 
   	   
       return;
   }

   public function saveOrderBillingAdd($id)
   {
   	  try {
   	  	
   	  	$session = Mage::getModel('order1/session'); 
   	   $cart_address = Mage::getModel('order1/cart')->load($session->getCartId())->getCartBillingAddress()->getData();
        
       unset($cart_address['cart_id']);
       unset($cart_address['address_id']);  
   
   	   $order = Mage::getModel('order1/order_address')->setOrderId($id)->addData($cart_address)->save();
   	   if(!$order)
   	   {
   	   	  throw new Exception("Error Processing Billing Address..", 1);
   	   	  
   	   }

   	  } catch (Exception $e) {
   	  	   
          Mage::getModel('adminhtml/session')->addError($e->getMessage());
   	  }
   	   
      return;
   }
}

