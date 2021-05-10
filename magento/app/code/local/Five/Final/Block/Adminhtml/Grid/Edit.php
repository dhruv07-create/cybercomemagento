<?php

 class Five_Final_Block_Adminhtml_Grid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
 {
 	public function __construct()
 	{ 
 		parent::__construct();
 		$this->_blockGroup = 'final';
 		$this->_controller = 'adminhtml_grid';
 		$this->_objectId='id';
 		$this->_headerText='Fill Form';

 	}

 }

?>