<?php

  class Ccc_Vendor_Block_Group_Create extends Mage_Core_Block_Template
  {
  	public function getSaveUrl()
  	{
  		return $this->getUrl('*/*/save');
  	}

      public function getUnAssignedAttribute()
      {

        $items = array();
        $setId = Mage::getModel('eav/config')
        ->getEntityType('vendor_product')
        ->getDefaultAttributeSetId()
        ;

        $collection = Mage::getResourceModel('vendor/product_productattribute_collection')
            ->setAttributeSetFilter($setId)
            ->load();

        $attributesIds = array('0');
        
        foreach ($collection->getItems() as $item) {
            $attributesIds[] = $item->getAttributeId();
        }

        $attributes = Mage::getResourceModel('vendor/product_productattribute_collection')
            ->setAttributesExcludeFilter($attributesIds)
            ->addFieldToFilter('attribute_code',['like'=>'%_'.Mage::getModel('vendor/session')->getId()])
            
            ;
          

          return $attributes;    
      }      
  }