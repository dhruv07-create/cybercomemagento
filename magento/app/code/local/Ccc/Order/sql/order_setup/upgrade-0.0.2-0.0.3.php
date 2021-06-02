<?php
$installer = $this;
$installer->startSetup();
//order table
$table = $installer->getConnection()
    ->newTable($installer->getTable('order1/order_status'))
    ->addColumn('status_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Order Id')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Status')
        ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Comment')
        ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Customer Notified')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
        ->addForeignKey($installer->getFkName('order1/order_status', 'order_id', 'order1/order', 'order_id'),
        'order_id', $installer->getTable('order1/order'), 'order_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Ccc Flat Order Status');
$installer->getConnection()->createTable($table);

$installer->endSetup();
