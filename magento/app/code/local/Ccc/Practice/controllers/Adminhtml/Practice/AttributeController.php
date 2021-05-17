<?php


class Ccc_Practice_Adminhtml_Practice_AttributeController extends Mage_Adminhtml_Controller_Action

{ 

	protected $_entityTypeId;

	const XML_PATH_ALLOWED_TAGS = 'system/catalog/frontend/allowed_html_tags_list';

	public function preDispatch()
    {
       parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType(Ccc_practice_Model_Resource_practice::ENTITY)->getTypeId();
    }  


   protected function _initAction()
    {
        $this->_title($this->__('practice'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Attributes'));

        if($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $this->loadLayout()
                ->_setActiveMenu('attribute')
                ->_addBreadcrumb($this->__('practice'), $this->__('practice'))
                ->_addBreadcrumb(
                    $this->__('Manage Attributes'),
                    $this->__('Manage Attributes'))
            ;
        }
        return $this;
    }

    public function indexAction()
	{ 
		$this->loadLayout();
        $this->_setActiveMenu('practice');

		$this->_addContent($this->getLayout()->createBlock('practice/adminhtml_practice_attribute'));

		$this->renderLayout();
	}

	public function newAction()
	{	
		$this->_forward('edit');
	}

	public function editAction()
    {	
        $id = $this->getRequest()->getParam('attribute_id');

        $model = Mage::getModel('practice/resource_eav_attribute')
            ->setEntityTypeId($this->_entityTypeId);

        if ($id) {
            $model->load($id);

            if (! $model->getId()) {
                Mage::getSingleton('practice/session')->addError(
                    $this->__('This attribute no longer exists'));
                $this->_redirect('*/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('practice/session')->addError(
                    $this->__('This attribute cannot be edited.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        $data = Mage::getSingleton('practice/session')->getAttributeData(true);

        if (! empty($data)) {
            $model->addData($data);
        }

        Mage::register('entity_attribute', $model);

        $this->_initAction();

        $this->_title($id ? $model->getName() : $this->__('New Attribute'));

        $item = $id ? $this->__('Edit practice Attribute')
                    : $this->__('New practice Attribute');

        $this->_addBreadcrumb($item, $item);

        $this->_setActiveMenu('practice');
        
        $this->renderLayout();

    }

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->getRequest()->getParam('attribute_code');
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        $attribute = Mage::getModel('practice/resource_eav_attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('practice/session')->addError(
                $this->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('practice/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

 protected function _filterPostData($data)
    {
        if ($data) {
         
              $helperCatalog = Mage::helper('practice');

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

    public function saveAction()
    {   
        $data = $this->getRequest()->getPost();
            
        if ($data) {
           
            $session = Mage::getSingleton('practice/session');

            $redirectBack   = $this->getRequest()->getParam('back', false);
      
            $model = Mage::getModel('practice/resource_eav_attribute');
     

            $id = $this->getRequest()->getParam('attribute_id');

            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^(?!event$)[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    $session->addError(
                        $this->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter. Do not use "event" for an attribute code.')
                    );
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        $session->addError($message);
                    }
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                        $this->__('This Attribute no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                        $this->__('This attribute cannot be updated.'));
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

               
            } else {
                /**
                * @todo add to helper and specify all relations for properties
                */
                $data['source_model'] = Mage::helper('practice/practice')->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = Mage::helper('practice/practice')->getAttributeBackendModelByInputType($data['frontend_input']);
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

            //filter
            $data = $this->_filterPostData($data);
            $model->addData($data);

            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }

            if ($this->getRequest()->getParam('set') && $this->getRequest()->getParam('group')) {
                // For creating product attribute on product page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                echo $this->getRequest()->getParam('set');
                $model->setAttributeGroupId($this->getRequest()->getParam('group'));
            }

            try {
                $model->save();
                $session->addSuccess(
                    $this->__('The practice attribute has been saved.'));

                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setAttributeData(false);
              
                $this->_redirect('*/*/', array());
               
                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('attribute_id')) {
            $model = Mage::getModel('practice/resource_eav_attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId || !$model->getIsUserDefined()) {
                Mage::getSingleton('practice/session')->addError(
                    $this->__('This attribute cannot be deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            try {
                $model->delete();
                Mage::getSingleton('practice/session')->addSuccess(
                    $this->__('The practice attribute has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('practice/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('practice/session')->addError(
            $this->__('Unable to find an attribute to delete.'));
        $this->_redirect('*/*/');
    }
}

?>
