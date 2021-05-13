<?php
class Ccc_Vendor_Block_Product_Edit_Tabs extends Mage_Core_Block_Template
{
    public $tabs=[];

    public function getTabs()
    {
        return $this->tabs;
    }

    public function addTab($key,$tab=[])
    {
       $this->tabs[$key]=$tab;
       return $this;
    }

    public function getVendorproduct()
    {
        return Mage::registry('current_vendorproduct');
    }

    public function getVendor()
    {
    	return Mage::getModel('vendor/session')->getVendor();
    }

    public function prepareTab()
    {
        $vendorId = Mage::getModel('vendor/session')->getId();
        $vendorProduct = Mage::getModel('vendor/product');
        $attributeSetId = $vendorProduct->getResource()->getEntityType()->getDefaultAttributeSetId();
        $entityTypeId = $vendorProduct->getResource()->getEntityType()->getId();

        $vendorProductAttributeGroup = Mage::getResourceModel('vendor/product_attribute_group_collection');
        $vendorProductAttributeGroup->getSelect()->where('main_table.entity_id = ' . $vendorId);
        $vendorProductAttributeGroup = $vendorProductAttributeGroup->load();

        $vendorProductAttributes = Mage::getResourceModel('vendor/product_productattribute_collection')
            ->addFieldToFilter('attribute_code', array('like' => '%_' . $vendorId))
            ->addFieldToFilter('is_visible',['eq'=>'1']);
            ;
     
      

        $vendorProductAttributes->getSelect()->joinLeft(
            array('product_attribute' => 'eav_entity_attribute'),
            'product_attribute.attribute_id = main_table.attribute_id',
            array('attribute_group_Id')
        );

        $vendorProductAttributes = $vendorProductAttributes->load();
 /*       echo '<pre>';
        echo($vendorProductAttributes->getSelect());
        die();*/
                         
        $vendorProductDefaultAttributes = Mage::getResourceModel('eav/entity_attribute_collection');
        $vendorProductDefaultAttributes->getSelect()
            ->join(
                array('attribute' => 'eav_entity_attribute'),
                'attribute.attribute_id = main_table.attribute_id',
                array('*')   
            )    
            ->join(
               ['vendor_attribute'=>'vendor_product_eav_attribute'],
               'main_table.attribute_id = vendor_attribute.attribute_id',
               ['*']
              )
            ->where("main_table.entity_type_id = {$entityTypeId} AND main_table.is_user_defined = 0 AND attribute.attribute_set_id = {$attributeSetId} and vendor_attribute.is_visible='1'");

        $vendorProductDefaultAttributes = $vendorProductDefaultAttributes->load();
         //echo $vendorProductDefaultAttributes->getSelect(); die();

        $vendorProductAttributes = array_merge($vendorProductAttributes->getItems(), $vendorProductDefaultAttributes->getItems());
       
        if (!$this->getVendorproduct()->getId()) {
            foreach ($vendorProductAttributes as $attribute) {
                if ($attribute->getDefaultValue() != '') {
                    $this->getVendorproduct()->setData($attribute->getAttributeCode(), $attribute->getDefaultValue());
                }
            }
        }

        $vendorProductAttributeDefaultGroup = Mage::getResourceModel('eav/entity_attribute_group_collection');
        $vendorProductAttributeDefaultGroup->setAttributeSetFilter($attributeSetId)
            ->getSelect()
            ->where("attribute_group_name REGEXP '^[A-z]' ");


        $groupCollection = array_merge($vendorProductAttributeGroup->getItems(), $vendorProductAttributeDefaultGroup->getItems());

        
        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            if ($defaultGroupId == 0 or $group->getIsDefault()) {
                $defaultGroupId = $group->getId();
            }
        }

      $groupCollection = array_reverse($groupCollection);
    
        foreach ($groupCollection as $group) {
             
             $attributes = [];

            foreach ($vendorProductAttributes as $attribute) {
                if ($this->getVendor()->checkInGroup($attribute->getId(), $attributeSetId, $group->getAttributeGroupId())) {
                    $attributes[] = $attribute;
                }
            }

        
            if (!$attributes) {
                continue;
            }
         
             $active = $defaultGroupId == $group->getAttributeGroupId();
            $block = $this->getLayout()->createBlock('vendor/product_edit_tabs_attribute')
                ->setGroup($group)
                ->setAttributes($attributes)
                //>setAddHiddenFields($active)
                ->toHtml();

            $this->addTab('group_' . $group->getId(), array(
                'label' => Mage::helper('vendor')->__($group->getAttributeGroupName()),
                'content' => $block,
                'active' => $active,
            ));
        }
    }
    
}