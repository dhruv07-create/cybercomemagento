<?php
class Ccc_Order_Model_Resource_Order_Item_Collection extends Mage_core_Model_Resource_Db_Collection_Abstract
{
   protected function _construct()
    {
        $this->_init('order1/order_item');
    }

}