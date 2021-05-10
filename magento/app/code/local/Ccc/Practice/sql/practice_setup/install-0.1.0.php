<?php

$this->startSetup();

$this->addEntityType(Ccc_Practice_Model_Resource_Practice::ENTITY, [
    'entity_model'                => 'practice/practice',
    'attribute_model'             => 'practice/attribute',
    'table'                       => 'practice/practice',
    'increment_per_store'         => '0',
    'additional_attribute_table'  => 'practice/eav_attribute',
    'entity_attribute_collection' => 'practice/practice_attribute_collection',
]);

$this->createEntityTables('practice');
$this->installEntities();
$default_attribute_set_id = Mage::getModel('eav/entity_setup', 'core_setup')
    						->getAttributeSetId('practice', 'Default');

$this->run("UPDATE `eav_entity_type` SET `default_attribute_set_id` = {$default_attribute_set_id} WHERE `entity_type_code` = 'practice'");

$this->endSetup();
