<?php


 class Ccc_Practice_Model_Attribute extends Mage_Eav_Model_Attribute

 {
 	const MODULE_NAME= 'Ccc_Practice';
 	protected $_eventObject='attribute';
 	   public function _construct()
 	   {
            $this->_init('practice/attribute');
 	   }
 } 