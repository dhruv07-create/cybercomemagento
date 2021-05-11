<?php
class Ccc_Vendor_Block_Product_Edit_Form extends Mage_Core_Block_Template
{
	public function __construct()
	{
		$this->setTemplate('vendor/product/edit/form.phtml');
	}

	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save',['id'=>$this->getRequest()->getParam('id')]);
	}

	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/delete',['id'=>$this->getRequest()->getParam('id')]);
	}
}