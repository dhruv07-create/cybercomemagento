<?php
class Ccc_Order_Model_Resource_Order_Status extends Mage_Core_Model_Resource_Db_Abstract
{

   protected function _construct()
    {
        $this->_init('order1/order_status','status_id');
    }

}