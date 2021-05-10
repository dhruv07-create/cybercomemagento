<?php
class Ccc_Practice_Block_Adminhtml_Practice_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

	public function __construct() {
		parent::__construct();
		$this->setId('practice_attribute_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('practice')->__('Attribute Information'));
	}

	protected function _beforeToHtml() {
		$this->addTab('main', array(
			'label' => Mage::helper('practice')->__('Properties'),
			'title' => Mage::helper('practice')->__('Properties'),
			'content' => $this->getLayout()->createBlock('practice/adminhtml_practice_attribute_edit_tab_main')->toHtml(),
			'active' => true
			
		));

		$model = Mage::registry('entity_attribute');

		$this->addTab('labels', array(
			'label' => Mage::helper('practice')->__('Manage Label / Options'),
			'title' => Mage::helper('practice')->__('Manage Label / Options'),
			'content' => $this->getLayout()->createBlock('practice/adminhtml_practice_attribute_edit_tab_options')->toHtml(),
			
		));

		return parent::_beforeToHtml();
	}
}