<?php

class Ccc_Practice_Block_Adminhtml_Practice_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
    	$this->_blockGroup = 'practice';
        $this->_controller = 'adminhtml_practice_attribute';
        $this->_headerText = $this->__('Manage Attributes');
        $this->_addButtonLabel = $this->__('Add New Attribute');
        parent::__construct();
    }

}
