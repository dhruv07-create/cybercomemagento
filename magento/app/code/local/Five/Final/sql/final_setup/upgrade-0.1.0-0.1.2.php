<?php
$installer=$this;

$installer->startSetup();

 $installer->run('ALTER TABLE `finaltable` CHANGE `email` `email` VARCHAR(255) ;');

 $installer->endSetup();
