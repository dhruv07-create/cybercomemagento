<?php

$this->startSetup();

$this->addEntityType(Ccc_Vendor_Model_Resource_Product::ENTITY, [
    'entity_model'                => 'vendor/product',
    'attribute_model'             => 'vendor/productattribute',
    'table'                       => 'vendor/product',
    'increment_per_store'         => '0',
    'additional_attribute_table'  => 'vendor/product_eav_productattribute',
    'entity_attribute_collection' => 'vendor/product_productattribute_collection',
]);

$this->createEntityTables('vendor_product');
$this->installEntities();

$default_attribute_set_id = Mage::getModel('eav/entity_setup', 'core_setup')
    						->getAttributeSetId('vendor_product', 'Default');

$this->run("UPDATE `eav_entity_type` SET `default_attribute_set_id` = {$default_attribute_set_id} WHERE `entity_type_code` = 'vendor_product'");

$types = ['datetime', 'decimal', 'int', 'text', 'varchar', 'char'];
foreach ($types as $type) {
    $query = "ALTER TABLE `vendor_product_{$type}` ADD UNIQUE( `attribute_id`, `store_id`, `entity_id`)";
    $installer->run($query);
}


$this->endSetup();
