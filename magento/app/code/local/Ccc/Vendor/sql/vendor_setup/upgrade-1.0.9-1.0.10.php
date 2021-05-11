<?php 

 $installer=$this;

 $installer->startSetup();

 $this->installEntities($installer->getVedorProductEntities());

 $installer->endSetup();


