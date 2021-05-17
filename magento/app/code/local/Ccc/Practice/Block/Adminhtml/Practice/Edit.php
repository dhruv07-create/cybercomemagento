<?php

 class Ccc_Practice_Block_Adminhtml_Practice_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
 {
 	  public function __construct()
 	  {

 	  		$this->_blockGroup='practice';
 	  		$this->_controller='adminhtml_practice';
 	  		parent::__construct();
 	  		if(!$this->getRequest()->getParam('set'))
 	  		{
     	  		$this->removeButton('save');
 	  		}
 	  }
 }

?>  
    