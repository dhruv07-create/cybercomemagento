<?php

 $installer = $this;
 $installer->startSetup();

  $attributes = array('image_label','small_image_label','thumbnail_label');
  $a=Mage::getModel('vendor/product')->getResource()->getEntityType()->getEntityTypeId();


  foreach ($attributes as $attribute) {

  	  $installer->run("DELETE FROM `eav_attribute` 
  	  	WHERE `entity_type_id`={$a}
  			AND `attribute_code` = '{$attribute}'  
  			");
  }

 $installer->endSetup(); 