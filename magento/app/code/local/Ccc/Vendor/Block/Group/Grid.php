<?php

 
  class Ccc_Vendor_Block_Group_Grid extends Mage_Core_Block_Template
  {
      public function getAttributeGroups()
      {
      	$id = Mage::getModel('vendor/session')->getId();

      	  $collection = Mage::getModel('eav/entity_attribute_group')
      	        ->getCollection()
      	        ->addFieldToFilter('attribute_group_name',['like'=>$id.'%'])
      	        ;
      	        
         return $collection;
      }

      public function getGroupName($model)
      {

      	  $group = Mage::getModel('vendor/product_attribute_group')
      	   ->getCollection()
      	   ->addFieldToFilter('attribute_group_id',$model['attribute_group_id'])
      	   ;
          
      	  return $group->getData()[0]['attribute_group_name'];

      }
     
      public function getEditUrl($model)
      {

      	  $group = Mage::getModel('vendor/product_attribute_group')
      	     ->getCollection()
      	     ->addFieldToFilter('attribute_group_id',$model['attribute_group_id']);
      	     ;

      	  return $this->getUrl('*/*/edit',['attribute_group_id'=>$model['attribute_group_id'],'group_id'=>
      	  	$group->getData()[0]['group_id']]);
      }

      public function getDeleteUrl($model)
      {

      	  $group = Mage::getModel('vendor/product_attribute_group')
              ->getCollection()
              ->addFieldToFilter('attribute_group_id',$model['attribute_group_id']);
              ;
      	  return $this->getUrl('*/*/delete',['attribute_group_id'=>$model['attribute_group_id'],'group_id'=>
            $group->getData()[0]['group_id']]);
      }
  }