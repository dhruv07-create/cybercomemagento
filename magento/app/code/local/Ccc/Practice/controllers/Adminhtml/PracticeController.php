<?php

class Ccc_Practice_Adminhtml_PracticeController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('practice/practice');
    }

    public function indexAction()
    {    
        $this->loadLayout();
        $this->_setActiveMenu('practice');
        $this->_title('practice Grid');

        $this->_addContent($this->getLayout()->createBlock('practice/adminhtml_practice'));

        $this->renderLayout();
    }

    protected function _initPractice()
    {
        $this->_title($this->__('Practice'))
            ->_title($this->__('Manage practice'));

        $practiceId = (int) $this->getRequest()->getParam('id');
        $practice   = Mage::getModel('practice/practice');
        if($practiceId)
        {
        $practice
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($practiceId);            
        }

        Mage::register('current_practice', $practice);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $practice;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $practiceId = (int) $this->getRequest()->getParam('id');

        $practice   = $this->_initPractice();
        

        if ($practiceId && !$practice->getId()) {
            $this->_getSession()->addError($this->__('This vendor no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($practice->getName());

        $this->loadLayout();

        $this->_setActiveMenu('practice/practice');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();

    }

    public function saveAction()
    {
                   
        try {

            $practiceData = $this->getRequest()->getPost('account');

            $practice = Mage::getSingleton('practice/practice');
               
            if ($practiceId = $this->getRequest()->getParam('id')) {

                if (!$practice->load($practiceId)) {
                    throw new Exception("No Row Found");
                }
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            }

            $practice->addData($practiceData);
            $practice->setAttributeSetId($this->getRequest()->getParam('set'));
            $practice->save();

            Mage::getSingleton('core/session')->addSuccess("practice data added.");
            $this->_redirect('*/*/');

        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }

    }

    public function deleteAction()
    {
        try {

            $practiceModel = Mage::getModel('practice/practice');

            if (!($practiceId = (int) $this->getRequest()->getParam('id')))
                throw new Exception('Id not found');

            if (!$practiceModel->load($practiceId)) {
                throw new Exception('practice does not exist');
            }

            if (!$practiceModel->delete()) {
                throw new Exception('Error in delete record', 1);
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('The practice has been deleted.'));

        } catch (Exception $e) {
            Mage::logException($e);
            $Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }
}


?>