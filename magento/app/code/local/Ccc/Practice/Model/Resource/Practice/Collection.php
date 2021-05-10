<?php
class Ccc_Practice_Model_Resource_Practice_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
	public function __construct()
	{
		$this->setEntity('practice');
		parent::__construct();
		
	}
}