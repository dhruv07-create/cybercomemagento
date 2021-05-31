<?php
$installer = $this;
$installer->startSetup();


$installer->run("
    ALTER TABLE `order` CHANGE `status` `status` VARCHAR(32) NULL DEFAULT NULL COMMENT 'Status';
    ");


//
/*$table = $installer->getConnection()
    ->newTable($installer->getTable('order1/order'))
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            'identity'  =>true
            ), 'OrderId')

         ->addColumn('cart_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Cart Id')

   
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR,32, array(
          'unsigned'  => true,
           ), 'Status')
        
        ->addColumn('shipping_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Shipping Name')
       
        ->addColumn('payment_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Payment Name')
       
        ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_VARCHAR,255, array(
        ), 'Shipping Amount')
       
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER,11, array(
        ), 'CustomerId')
     
        ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Customer Email')
     
        ->addColumn('customer_prefix', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            ), 'Customer Prefix')
       
        ->addColumn('customer_firstname', Varien_Db_Ddl_Table::TYPE_VARCHAR,255, array(
            ), 'Customer Firstname')
      
        ->addColumn('customer_middlename', Varien_Db_Ddl_Table::TYPE_VARCHAR,100, array(
            ), 'Customer Middlename')
   
    ->addColumn('customer_lastname', Varien_Db_Ddl_Table::TYPE_VARCHAR,100, array(
        ), 'Customer Lastname')
     ->addColumn('item_quantity', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        ), 'Item Quantity')
     ->addColumn('item_count', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        ), 'Item Count') 
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Updated At')

     ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')

      ->addColumn('subtotal', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Subtotal')

      ->addColumn('grand_total', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Grand Total ')
    
    ->addForeignKey($installer->getFkName('order1/order', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Cc')

     ->addForeignKey($installer->getFkName('order1/order', 'cart_id', 'order1/cart', 'cart_id'),
        'cart_id', $installer->getTable('order1/cart'), 'cart_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Ccc Fla');

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order1/order_address'))
    ->addColumn('address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Address Id')

      ->addColumn('customer_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Customer Address Id')

     ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Customer Id')

    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Order Id')

      ->addColumn('address_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Address Type')
 
        ->addColumn('prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Prefix')

    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Firstname')

      ->addColumn('middlename', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Middlename')

       ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Lastname')

        ->addColumn('suffix', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Suffix')

      ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Email')
      ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Telephone')
   

        ->addColumn('fax', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Fax')
        ->addColumn('region', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Region')
        ->addColumn('zipcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Zipcode')
       
        ->addColumn('street', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Street')
        
        ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'City')
     
        ->addColumn('country', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
            ), 'Country Id')
   
         ->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
                ), 'State')

        ->addForeignKey($installer->getFkName('order1/order_address', 'order_id', 'order1/order', 'order_id'),
            'order_id', $installer->getTable('order1/order'), 'order_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Order Address');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order1/order_item'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false, 
        'primary'   => true,
        ), 'Item Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Order Id')
      ->addColumn('parent_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Prent Product Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Product Id')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Sku')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price')
    
    ->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Base Price')

    ->addColumn('quantity', Varien_Db_Ddl_Table::TYPE_INTEGER,null, array(
        'nullable'  => false,
        ), 'Quantity')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => '0.0000',
        ), 'Discount Amount')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated At')
    ->addForeignKey($installer->getFkName('order1/order_item', 'order_id', 'order1/order', 'order_id'),
        'order_id', $installer->getTable('order1/order'), 'order_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Order Item');*/
/*$installer->getConnection()->createTable($table);
*/

$installer->endSetup();