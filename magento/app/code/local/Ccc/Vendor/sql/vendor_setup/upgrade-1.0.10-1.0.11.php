<?php

 $installer = $this;

 $installer->startSetup();
  
   $code = [
				'firstname',
				'lastname',
				'email',
				'phoneNo'
			];

	$entity_type_id = Mage::getModel('eav/entity')->setType('vendor_product')->getTypeId();		
 
 foreach ($code as $value) {
 	 $installer->run("DELETE FROM eav_attribute WHERE entity_type_id = {$entity_type_id}   AND attribute_code = '{$value}'"); 
 }

 $installer->endSetup();
 
