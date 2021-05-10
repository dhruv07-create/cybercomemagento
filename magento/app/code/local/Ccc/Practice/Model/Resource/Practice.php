<?php
class Ccc_Practice_Model_Resource_Practice extends Mage_Eav_Model_Entity_Abstract
{

	const ENTITY = 'practice';
	
	public function __construct()
	{

		$this->setType(self::ENTITY)
			 ->setConnection('core_read', 'core_write');

	   parent::__construct();
    }

}