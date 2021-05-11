<?php
class Ccc_Vendor_Model_Resource_Vendor extends Mage_Eav_Model_Entity_Abstract
{

	const ENTITY = 'vendor';
	
	public function __construct()
	{

		$this->setType(self::ENTITY)
			 ->setConnection('core_read', 'core_write');

	   parent::__construct();
    }

   public function checkVendorId($vendorId)
    {  
        $adapter = $this->_getReadAdapter();
        $bind    = array('entity_id' => (int)$vendorId);
        $select  = $adapter->select()
            ->from($this->getTable('vendor/vendor'), 'entity_id')
            ->where('entity_id = :entity_id')
            ->limit(1);

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            return true;
        }
        return false;
    }

     public function loadByEmail(Ccc_Vendor_Model_Vendor $vendor, $email, $testOnly = false)
        { 
            $adapter = $this->_getReadAdapter();
            $bind    = array('vendor_email' => $email);

          /*  $select  = $adapter->select()
                ->from($this->getEntityTable(), array($this->getEntityIdField()))
                ->where('email = :vendor_email');*/

            $select = "SELECT vv.entity_id FROM eav_attribute ea join eav_entity_type
                 
                 eet on ea.entity_type_id = eet.entity_type_id
                 join vendor_varchar vv on vv.attribute_id = ea.attribute_id
                 where  eet.entity_type_code = 'vendor' and ea.attribute_code = 'email'
                 and vv.value = '{$email}' 
            ";    
            
           $vendorId = $adapter->fetchOne($select,$bind);            

            if ($vendorId) {
                $this->load($vendor, $vendorId);
            } else {
                $vendor->setData(array());
            }

            return $this;
        }
   

 protected function _getReadAdapter()
    {
        if (is_string($this->_read)) {
            $this->_read = Mage::getSingleton('core/resource')->getConnection($this->_read);
        }
        return $this->_read;
    }    

}