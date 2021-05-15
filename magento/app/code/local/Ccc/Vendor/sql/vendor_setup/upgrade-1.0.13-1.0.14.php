<?php

 $installer = $this;
 $installer->startSetup();

 $list = ['admin_log','admin_status','vendor_status','vendor_log','vendor_id','catalog_product_id','request','request_status'];

  $connection = Mage::getModel('core/resource')->getConnection('core_read');

  foreach ($list as $column) {

  $sq = "SELECT attribute_id FROM eav_attribute WHERE attribute_code = '{$column}';";

  $attribute_id = $connection->fetchOne($sq); 

  $installer->run("UPDATE `vendor_product_eav_attribute` SET `is_visible`='0' WHERE attribute_id = '{$attribute_id}' ");
  
  }

 $installer->endSetup();