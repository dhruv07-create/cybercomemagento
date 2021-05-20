<?php

 class Ccc_Vendor_AttributeController extends Mage_Core_Controller_Front_Action
 {
   public $_entityTypeId=null;

 	public function preDispatch()
    {
       // $this->_setForcedFormKeyActions('delete');
        parent::preDispatch();
         $this->_entityTypeId = Mage::getModel('eav/entity')->setType(Ccc_Vendor_Model_Resource_Product::ENTITY)->
         getTypeId();
    }
 	public function indexAction()
 	{

 	  if(!Mage::getModel('vendor/session')->isLoggedIn())
   	  {  
           $this->_redirect('*/account/login');
   	  }
 		$this->loadLayout();

 		$this->renderLayout();
 	}

 	public function newAction()
 	{
     $this->_forward('edit');
 	}

 public function editAction()
  {
      if(!Mage::getModel('vendor/session')->isLoggedIn())
      {  
           $this->_redirect('*/account/login');
      }   
        $id = $this->getRequest()->getParam('attribute_id');

        $model = Mage::getModel('vendor/resource_eav_productattribute')
            ->setEntityTypeId($this->_entityTypeId);
        if ($id) {
            $model->load($id);

            if (! $model->getId()) {
                Mage::getSingleton('vendor/session')->addError(
                    Mage::helper('vendor')->__('This attribute no longer exists'));
                $this->_redirect('*/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('vendor/session')->addError(
                    Mage::helper('vendor')->__('This attribute cannot be edited.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        $data = Mage::getSingleton('vendor/session')->getAttributeData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

        Mage::register('entity_attribute', $model);
     
        $this->loadLayout();  


        $item = $id ? Mage::helper('vendor')->__('Edit Vendor Attribute')
                    : Mage::helper('vendor')->__('New Vendor Attribute');
        
        $this->renderLayout();
  }


   public function saveAction()
   {
   	  if(!Mage::getModel('vendor/session')->isLoggedIn())
   	  {  
           $this->_redirect('*/account/login');
   	  }
    	try {

           if($this->getRequest()->isPost())
           {
           	$session = Mage::getSingleton('vendor/session');
              $helper = Mage::helper('vendor/vendor');

                $id = $this->getRequest()->getParam('attribute_id');
 
              $data = $this->getRequest()->getPost('attribute');
              
              $vendorid = Mage::getModel('vendor/session')->getId();

              $model=Mage::getModel('vendor/resource_eav_productattribute');

              $data['attribute_code']=strtolower($data['frontend_label']).'_'.$vendorid;

              $data['frontend_label']=['0'=>$data['frontend_label'],'1'=>''];

               if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^(?!event$)[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    Mage::getModel('core/session')->addError(
                        Mage::helper('vendor')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter. Do not use "event" for an attribute code.')
                    );
                    $this->_redirect('*/*/new', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            if (isset($data['frontend_input'])) {

                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        Mage::getModel('core/session')->addError($message);
                    }
                    $this->_redirect('*/*/new', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            if($id)
            {
            	 $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                        Mage::helper('vendor')->__('This Attribute no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                        Mage::helper('vendor')->__('This attribute cannot be updated.'));
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['backend_model'] = $model->getBackendModel();
                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();

            }else{

            	  $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
            }


            if (!isset($data['is_configurable'])) {
                $data['is_configurable'] = 0;
            }
            if (!isset($data['is_filterable'])) {
                $data['is_filterable'] = 0;
            }
            if (!isset($data['is_filterable_in_search'])) {
                $data['is_filterable_in_search'] = 0;
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if(!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }

            $data = $this->_filterPostData($data);
            $model->addData($data);

             if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }

             if($model->save())
             {
             	Mage::getModel('core/session')->addSuccess("Attribute Saved...");
             }else{
             	Mage::getModel('core/session')->addSuccess("Attribute Not Saved...");
             }

             if($this->getRequest()->getPost()['group'])
             {
                $default_set_id = Mage::getModel('eav/entity_setup','core/setup')
                ->getAttributeSetId('vendor_product','Default');

                $attribute_id = $model->getId();

                $model_eav = Mage::getModel('eav/entity_attribute')
                ->setEntityTypeId(Mage::getModel('eav/entity')->setType('vendor_product')->getTypeId())
                ->setAttributeSetId($default_set_id) 
                ->setAttributeGroupId($this->getRequest()->getPost()['group']) 
                ->setAttributeId($attribute_id) 
                ;            

                if(!$model_eav->save())
                {
                	throw new Exception("Attribute Not Saved", 1);
                	
                }else{
    		      Mage::getModel('core/session')->addSuccess("Attribute Save Successfull..");
                }
             } 
           }

    	} catch (Exception $e) {
    		
    		Mage::getModel('core/session')->addError($e->getMessage());

    		$this->_redirect('*/*/');
    	}

    	$this->_redirect('*/*/');
   }


    protected function _filterPostData($data)
    {
        if ($data) {
         
            $helperCatalog = Mage::helper('vendor');

            $data['frontend_label'] = (array) $data['frontend_label'];
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = $helperCatalog->stripTags($value);
                }
            }

            if (!empty($data['option']) && !empty($data['option']['value']) && is_array($data['option']['value'])) {
                $allowableTags = isset($data['is_html_allowed_on_front']) && $data['is_html_allowed_on_front']
                    ? sprintf('<%s>', implode('><', $this->_getAllowedTags())) : null;
                foreach ($data['option']['value'] as $key => $values) {
                    foreach ($values as $storeId => $storeLabel) {
                        $data['option']['value'][$key][$storeId]
                            = $helperCatalog->stripTags($storeLabel, $allowableTags);
                    }
                }
            }
        }
        return $data;
    }

    public function deleteAction()
    {
    	try
    	{

    	$id = $this->getRequest()->getParam('attribute_id');

    	$model = Mage::getModel('vendor/resource_eav_productattribute')->load($id);

    	if($model->delete())
    	{
    		Mage::getModel('core/session')->addSuccess("Delete Successfully");
    	}else{
    		Mage::getModel('core/session')->addError("Error In Delete");
    	}
         
    	}catch(Exception $e)
    	{
    		Mage::getModel('core/session')->addError($e->getMessage());

    		$this->_redirect('*/*/');
            
    	}
     
     $this->_redirect('*/*/');

    }

    public function inputTypeAction()
    {
        $data = $this->getRequest()->getPost('attribute')['frontend_input'];
        
        $this->_redirect('*/*/new',['inputType'=>$data]);

    }

 }