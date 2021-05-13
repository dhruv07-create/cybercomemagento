<?php

class Ccc_Vendor_ProductController extends Mage_Core_Controller_Front_Action

{

	public function indexAction()
    {

    	if(!Mage::getModel('vendor/session')->isLoggedIn())
 	  	{
 	  		$this->_redirect('*/account/login');
 	  	}
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _initProduct()
    {
       
        $productId = (int) $this->getRequest()->getParam('id');
        $product   = Mage::getModel('vendor/product')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($productId);

        Mage::register('current_vendorproduct', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }

   public function newAction()
    { 
    	if(!Mage::getModel('vendor/session')->isLoggedIn())
 	  	{
 	  		$this->_redirect('*/account/login');
 	  	}
        $this->_forward('edit');
    }

   public function editAction()
    { 
    	if(!Mage::getModel('vendor/session')->isLoggedIn())
 	  	{
 	  		$this->_redirect('*/account/login');
 	  	}
        $productId = (int) $this->getRequest()->getParam('id');
        $product   = $this->_initProduct();

        if ($productId && !$product->getId()) {
            $this->_getSession()->addError(Mage::helper('vendor')->__('This product no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();

    }
	public function saveAction()
	{   
          
		if(!Mage::getModel('vendor/session')->isLoggedIn())
 	  	{
 	  		$this->_redirect('*/account/login');
 	  	}

           try {

            $productData = $this->getRequest()->getPost();

            $sku = $productData['sku'];

             $connection = Mage::getModel('core/resource')->getConnection('core_read');

             $entity_type_id = Mage::getModel('eav/entity')->setType('vendor_product')->getTypeId();

             $q = "SELECT attribute_id FROM eav_attribute where entity_type_id ={$entity_type_id} AND attribute_code = 'sku';";

             $attributeId = $connection->fetchOne($q);

             $q = "SELECT value FROM vendor_product_varchar WHERE attribute_id ={$attributeId} AND value = '{$sku}' ";
         
             $check = $connection->fetchAll($q);

             if($check && !$this->getRequest()->getParam('id'))
             { 
                throw new Exception("Sku all ready Available", 1);     
             }
             
            unset($productData['submit']);

            $productVendor = Mage::getSingleton('vendor/product');
            date_default_timezone_set('Asia/Kolkata');

            if ($productId = $this->getRequest()->getParam('id')) {

                if (!$productVendor->load($productId)) {
                    throw new Exception("No Row Found");
                }
                 
                 $productVendor->setVendorStatus('update');
                 if(!$productVendor->getCatalogProductId())
                 {
                 $productVendor->setVendorStatus('add');
                 }
                 $productVendor->setVendorLog(date('j/m/Y  h:i:s A'));
                 $productVendor->setRequestStatus('0');
                 $productVendor->setRequest('0');
                 $productVendor->setAdminLog('0');
                 $productVendor->setAdminStatus('pending');     

                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            }else{

                 $productVendor->setVendorStatus('add');
                 $productVendor->setVendorLog(date('j/m/Y  h:i:s A'));
                 $productVendor->setRequestStatus('0');
                 $productVendor->setRequest('0');
                 $productVendor->setAdminLog('0');
                 $productVendor->setAdminStatus('pending');

            }

            $productVendor->addData($productData);
            $productVendor->setVendorId(Mage::getModel('vendor/session')->getId());
            $productVendor->save();

            Mage::getSingleton('core/session')->addSuccess("VendorProduct added successfully .");
            $this->_redirect('*/*/');

        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }


	}

    public function deleteAction()
    {
        if(!Mage::getModel('vendor/session')->isLoggedIn())
        {
            $this->_redirect('*/account/login');
        }
        try
        {
           $id = $this->getRequest()->getParam('id'); 
          if(!$id)
          {
            throw new Exception('Delete Not Done', 1);
          }    date_default_timezone_set('Asia/Kolkata');

           
           $model = Mage::getModel('vendor/product')->load($id);

           $model->setVendorStatus('delete');
           $model->setVendorLog(date('j/m/Y  h:i:s A'));
           $model->setRequestStatus('0');
           $model->setRequest('0');
           $model->setAdminLog('0');
           $model->setAdminStatus('pending');

          

           if($model->save())
           {
                Mage::getModel('core/session')->addSuccess("Delete request sended..");
           }


          /* if($model->delete())
           {
           Mage::getModel('core/session')->addSuccess("Delete is Done.");
           }*/



        }catch(Exception $e)
        {
           Mage::getModel('core/session')->addError($e->getMessage());
        }
           $this->_redirect('*/*/');
    }

}
?>