<?php

$installer=$this;

$installer->startSetup();

 $columns = ['phoneNo','firstname','email','lastname'];

 $entity_type_id = Mage::getModel('eav/entity')->setType('practice')->getTypeId();

 foreach ($columns as  $col) {
      
      $installer->run('UPDATE `eav_attribute` SET is_user_defined=0 WHERE attribute_code ="'.$col.'" AND entity_type_id ='.$entity_type_id);
 }

$installer->endSetup();