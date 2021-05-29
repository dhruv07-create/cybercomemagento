<?php
class Ccc_Order_Model_Resource_Cart_Collection extends Mage_core_Model_Resource_Db_Collection_Abstract
{
   protected function _construct()
    {
        $this->_init('order1/cart');
    }

}