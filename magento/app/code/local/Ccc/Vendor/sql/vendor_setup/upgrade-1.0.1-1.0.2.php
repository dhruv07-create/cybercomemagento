<?php

$installer = $this;

$installer->startSetup();
$installer->run("
CREATE TABLE `{$installer->getTable('vendor/eav_attribute_website')}` (
  `attribute_id` smallint(5) unsigned NOT NULL,
  `website_id` smallint(5) unsigned NOT NULL,
  `is_visible` tinyint(1) unsigned DEFAULT NULL,
  `is_required` tinyint(1) unsigned DEFAULT NULL,
  `default_value` text,
  `multiline_count` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`attribute_id`, `website_id`),
  KEY `IDX_WEBSITE` (`website_id`),
  CONSTRAINT `FK_VENDOR_EAV_ATTRIBUTE_WEBSITE_ATTRIBUTE_EAV_ATTRIBUTE` FOREIGN KEY (`attribute_id`)
    REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_VENDOR_EAV_ATTRIBUTE_WEBSITE_WEBSITE_CORE_WEBSITE` FOREIGN KEY (`website_id`)
    REFERENCES `{$installer->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

?>