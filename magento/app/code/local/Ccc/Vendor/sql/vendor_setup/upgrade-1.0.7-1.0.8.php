<?php

  $installer =  $this;

  $installer->startSetup();

  $installer->run("ALTER TABLE `vendor_product_eav_attribute` ADD `sort_order` INT(11) NOT NULL AFTER `is_used_for_promo_rules`;");


  $installer->endSetup();
