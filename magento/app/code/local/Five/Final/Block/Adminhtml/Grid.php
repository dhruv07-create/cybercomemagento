<?php

 class Five_Final_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
 {
 	public function __construct()
 	{
 		parent::__construct();
 		$this->_blockGroup = 'final';
 		$this->_controller = 'adminhtml_grid';
 		$this->_headerText = 'Final..';

 	}
 }

?>