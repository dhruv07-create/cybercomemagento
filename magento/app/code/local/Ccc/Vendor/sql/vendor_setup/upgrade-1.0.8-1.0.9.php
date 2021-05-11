<?php

 $installer = $this;

 $installer->startSetup();

$types = ['datetime', 'decimal', 'int', 'text', 'varchar', 'char'];
/*foreach ($types as $type) {
    $query = "ALTER TABLE `vendor_product_{$type}` ADD UNIQUE( `attribute_id`, `store_id`, `entity_id`)";
    $installer->run($query);
}*/

foreach ($types as $type) {
    $query = "ALTER TABLE `vendor_{$type}` ADD UNIQUE( `attribute_id`, `store_id`, `entity_id`)";
    $installer->run($query);
}

 $installer->run("ALTER TABLE `vendor_eav_attribute` ADD `sort_order` INT(11) NOT NULL;");


 $installer->endSetup();


