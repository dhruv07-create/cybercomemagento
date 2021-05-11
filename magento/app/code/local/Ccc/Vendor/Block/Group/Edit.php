<?php

 
  class Ccc_Vendor_Block_Group_Edit extends Mage_Core_Block_Template
  {
      public function getGroupName()
      {
        $attribute_group_id =$this->getRequest()->getParam('attribute_group_id');
      	  $group = Mage::getModel('vendor/product_attribute_group')
      	   ->getCollection()
      	   ->addFieldToFilter('attribute_group_id',$attribute_group_id)
      	   ;
          
      	  return $group->getData()[0]['attribute_group_name'];

      }
     
      public function getSaveUrl()
      {
        $attribute_group_id =$this->getRequest()->getParam('attribute_group_id');

      	  $group = Mage::getModel('vendor/product_attribute_group')
      	     ->getCollection()
      	     ->addFieldToFilter('attribute_group_id',$attribute_group_id);
      	     ;

      	  return $this->getUrl('*/*/save',['attribute_group_id'=>$attribute_group_id,'group_id'=>
      	  	$group->getData()[0]['group_id']]);
      }

      public function getDeleteUrl()
      {
        $attribute_group_id =$this->getRequest()->getParam('attribute_group_id');
          
           $group = Mage::getModel('vendor/product_attribute_group')
              ->getCollection()
              ->addFieldToFilter('attribute_group_id',$attribute_group_id);
              ;

      	  return $this->getUrl('*/*/delete',['attribute_group_id'=>$attribute_group_id,'group_id'=>
            $group->getData()[0]['group_id']]);
      }

      public function getAssignedAttribute()
      {

        $attribute_group_id = $this->getRequest()->getParam('attribute_group_id');
          $model = MAge::getModel("eav/entity_attribute")
          ->getCollection()
          ->setAttributeGroupFilter($attribute_group_id)
          ;

          return $model;
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