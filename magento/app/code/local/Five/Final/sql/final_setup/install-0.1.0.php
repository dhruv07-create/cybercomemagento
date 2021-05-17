<?php

$installer = $this;

$installer->startSetup();

  $table = $installer->getConnection()->newTable($installer->getTable('final/myTable'))
  							->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER,11,['primary'=>true,'auto_increment'=>true])
  							->addColumn('email',Varien_Db_Ddl_Table::TYPE_VARCHAR,16,['default'=> ''])
  							->addColumn('full_name',Varien_Db_Ddl_Table::TYPE_VARCHAR,16,['default'=>''])
  							->addColumn('address',Varien_Db_Ddl_Table::TYPE_VARCHAR,16,['default'=> '']);
  
  $installer->getConnection()->createTable($table);

     $installer->endSetup();


?>