<?php



  class Ccc_Vendor_Block_Attribute_Grid extends Mage_Core_Block_Template
  {
     public function getAttributes()
     {
     	 $collection = $collection = Mage::getResourceModel('vendor/product_productattribute_collection')
     	 ->addFieldToFilter('attribute_code',['like'=>'%_'.Mage::getModel('vendor/session')->getId()])
         ;
        
     	 return $collection;
     }

     public function getEditUrl($attribute)
     {
         return $this->getUrl('*/*/edit',['attribute_id'=>$attribute['attribute_id']]);
     }

     public function getDeleteUrl($attribute)
     {
         return $this->getUrl('*/*/delete',['attribute_id'=>$attribute['attribute_id']]);
     }
  }