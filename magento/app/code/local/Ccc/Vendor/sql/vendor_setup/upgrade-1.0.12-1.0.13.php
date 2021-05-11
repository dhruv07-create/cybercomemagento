<?php

 $installer = $this;
 $installer->startSetup();

 $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'admin_log', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'admin_log',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'admin_status', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'admin_status',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'catalog_product_id', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'catalog_product_id',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'vendor_id', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'vendor_id',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'vendor_log', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'vendor_log',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'vendor_status', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'vendor_status',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'request', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'request',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

$setup->addAttribute(Ccc_Vendor_Model_Resource_Product::ENTITY, 'request_status', array(
    'input'                      => 'text',
    'type'                       => 'varchar',
    'label'                      => 'request_status',
    'backend'                    => '',
    'visible'                    => 0,
    'required'                   => 0,
    'user_defined'               => 0,
    'searchable'                 => 1,
    'filterable'                 => 0,
    'comparable'                 => 1,
    'visible_on_front'           => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front'   => 1,
    'global'                     => Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE,

));

 $installer->endSetup(); 