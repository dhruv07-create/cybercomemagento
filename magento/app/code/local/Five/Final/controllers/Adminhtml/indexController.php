<?php

class Five_Final_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();

		$this->_addContent($this->getLayout()->createBlock('final/adminhtml_grid'));

		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');

		$model = Mage::getModel('final/final')->load($id);

		Mage::register('finalDd',$model);

		$this->loadLayout();

		$this->_addContent($this->getLayout()->createBlock('final/adminhtml_grid_edit'));

		$this->renderLayout();
	}

	public function saveAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('final/final');
		$model->load($id);
		$data = $this->getRequest()->getPost('final');
		$model->setData($data);
		$model->setId($id);
		$model->save();
		$this->_redirect('*/*/');
	}

	public function deleteAction()
	{
		$model = Mage::getModel('final/final')->load($this->getRequest()->getParam('id'))->delete();

		$this->_redirect("*/*/");
	}
}

?>