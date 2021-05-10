<?php

class Five_Final_Model_Resource_Final extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
	{
		$this->_init('final/myTable','id');
	}
}

?>