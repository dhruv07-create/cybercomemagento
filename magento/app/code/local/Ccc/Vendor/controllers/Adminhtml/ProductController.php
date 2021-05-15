<?php

  class Ccc_Vendor_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action
  {
    
    public function indexAction()
    {  
       
        $this->loadLayout();
        $this->_setActiveMenu('vendor');
        $this->_title('VendorProduct Grid');

        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_product'));

        $this->renderLayout();
    }

    public function approveAction()
    {
    	 $productId = $this->getRequest()->getParam('id');

    	 try {

    	 	if(!$productId)
    	 	{
    	 		throw new Exception("Not any product specifed", 1);
    	 	}

    	 	$productModel = Mage::getModel('vendor/product')->load($productId);
            $productModel->setRequest('1');
            $productModel->setRequestStatus('1');
            $productModel->setAdminStatus('Approved');
            date_default_timezone_set('Asia/Kolkata');
            $productModel->setAdminLog(date('j/m/Y'));
            $catalogModel = Mage::getModel('catalog/product');

    	 	if($productModel->getVendorStatus()=='add')
    	 	{
               $data = $productModel->getData();
               $list=['entity_id','entity_type_id','attribute_set_id','increment_id','store_id','created_at','updated_at','is_active','vendor_status','vendor_log','request_status','request','admin_log','admin_status'];
              
                foreach ($list as $item) {
                      
                      unset($data[$item]);
                }
              
              $attribute_set_id = $catalogModel->getResource()->getEntityType()->getDefaultAttributeSetId();    
              $entity_type_id = $catalogModel->getResource()->getEntityType()->getEntityTypeId();  

              $catalogModel->setEntityTypeId($entity_type_id);  
              $catalogModel->setAttributeSetId($attribute_set_id);  
              $catalogModel->setTypeId('simple'); 
              $catalogModel->addData($data);


              if(!$catalogModel->save())
              {
                  throw new Exception('Product Not Add..', 1);    
              } 
              
              $productModel->setCatalogProductId($catalogModel->getEntityId());   

             if(!$productModel->save())
              {
                  throw new Exception('Product Not Add..', 1);    
              }             

            Mage::getModel('adminhtml/session')->addSuccess("Product Added Successfully");


              $this->_redirect("*/*/"); 


    	 	}

    	 	if($productModel->getVendorStatus()=='update') 
    	 	{

                 $data = $productModel->getData();
                 $list=['entity_id','entity_type_id','attribute_set_id','increment_id','store_id','created_at','updated_at','is_active','vendor_status','vendor_log','request_status','request','admin_log','admin_status'];
              
                foreach ($list as $item) {
                      
                      unset($data[$item]);
                }

                $catalogModel->load($productModel->getCatalogProductId());
                $catalogModel->addData($data);

                if(!$catalogModel->save() || !$productModel->save())
                  {
                      throw new Exception('Product Not Updated..', 1);    
                  }  

            Mage::getModel('adminhtml/session')->addSuccess("Product updated Successfully");

                $this->_redirect('*/*/');  
    	 	}

    	 	if($productModel->getVendorStatus()=='delete')
    	 	{

                $catalogModel->load($productModel->getCatalogProductId());
                
              if(!$catalogModel->delete() || !$productModel->delete())
              {
                  throw new Exception('Product Not Deleted..', 1);    
              }  

              Mage::getModel('adminhtml/session')->addSuccess("Product Deleted Successfully..");     
    	 		
    	 	}    	 	    	 	
    	 	
    	 } catch (Exception $e) {
    	 	Mage::getModel('adminhtml/session')->addError($e->getMessage());
    	 	$this->_redirect("*/*/");
    	 }
    	$this->_redirect("*/*/");
    }

    public function unapproveAction()
    {
   
      try {

       $productId = $this->getRequest()->getParam('id');

        if(!$productId)
            {
                throw new Exception("Not any product specifed", 1);
            }

            $productModel = Mage::getModel('vendor/product')->load($productId);
            $productModel->setRequest('1');
            $productModel->setRequestStatus('0');
            $productModel->setAdminStatus('Rejected');
             date_default_timezone_set('Asia/Kolkata');
            $productModel->setAdminLog(date('j/m/Y'));
 
             if(!$productModel->save())
             {
                throw new Exception("Error In Process..", 1);
             }

        Mage::getModel('adminhtml/session')->addSuccess('Rejected Successfully');

      } catch (Exception $e) {
        Mage::getModel('adminhtml/session')->addError($e->getMessage());
            $this->_redirect("*/*/");
          
      }
            $this->_redirect("*/*/");	
    }
}
  