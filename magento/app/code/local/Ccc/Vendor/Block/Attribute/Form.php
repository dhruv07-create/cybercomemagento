<?php
class Ccc_Vendor_Block_Attribute_Form extends Mage_Core_Block_Template
{
      public function getAttribute()
      {
            return Mage::registry('entity_attribute');
      }

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

      public function getSaveUrl()
      {
      	  return $this->getUrl('*/*/save',['attribute_id'=>$this->getAttribute()->getAttributeId()]);
      }

      public function getAttributeOptions($idi)
      {
          $options = Mage::getModel('eav/entity_attribute')->getCollection();

          $options
          ->join(
            ['main'=>'attribute_option'],
            'main_table.attribute_id = main.attribute_id',
            ['option_id'=>'main.option_id','sort_order'=>'main.sort_order']
          )
          ->addFieldToFilter('main_table.attribute_id',['eq'=>$idi])
          ->setOrder(
            'main.sort_order',
            'asc'
             )
          ;

          $options
          ->join(

              ['option_value'=>'eav/attribute_option_value'],
               'main.option_id = option_value.option_id',
               ['value'=>'option_value.value','store_id'=>'option_value.store_id']
          );

          return $options->getData();

      }
      public function getAttributeGroup($id)
      {
          if($id)
            { 

              $model = Mage::getModel('core/resource')->getConnection('core_read');
          
                     $sql = "
                     SELECT attribute_group_id
                     FROM eav_entity_attribute eea 
                       WHERE attribute_id = {$id}                   
                     ";
          
                     $groupId = $model->fetchOne($sql);
          
          
          
                     return $groupId;

            }
      }

}