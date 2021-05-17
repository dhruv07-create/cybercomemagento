<?php 

class Ccc_Vendor_Block_Adminhtml_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{  
        parent::__construct();
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('vendor')->__('Product Information'));
	}

    public function _beforeToHtml(){
        $product = Mage::registry('product');
        $vendorId = $product->getVendorId();
       
       $attributeSetId = $product->getResource()->getEntityType()->getDefaultAttributeSetId();
        $entityTypeId = $product->getResource()->getEntityType()->getId();

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

        if (!$product->getId()) {
            foreach ($vendorProductAttributes as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $product->setData($attribute->getAttributeCode(), $default);
                }
            }
        }

        $attributeSetId = $product->getResource()->getEntityType()->getDefaultAttributeSetId();
        $vendorProductAttributeDefaultGroup = Mage::getResourceModel('eav/entity_attribute_group_collection');
        $vendorProductAttributeDefaultGroup->setAttributeSetFilter($attributeSetId)
            ->getSelect()
            ->where("attribute_group_name REGEXP '^[A-z]' ");


        $groupCollection = array_merge($vendorProductAttributeGroup->getItems(), $vendorProductAttributeDefaultGroup->getItems());

       $groupCollection = array_reverse($groupCollection);

        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            if ($defaultGroupId == 0 or $group['is_default'] or $group['attribute_group_name']=='General') {
                $defaultGroupId = $group['attribute_group_id'];
            }

        }	
        
        foreach ($groupCollection as $group) {
            $attributes = array();
             foreach ($vendorProductAttributes as $attribute) {
                if ($product->checkInGroup($attribute->getId(), $attributeSetId, $group->getAttributeGroupId())) {
                    $attributes[] = $attribute;
                }
            }


            if (!$attributes) {
                continue;
            }

            $active = $defaultGroupId == $group->getAttributeGroupId();
            $block = $this->getLayout()->createBlock('vendor/adminhtml_product_edit_tabs_attribute')
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
        return parent::_beforeToHtml();
    }
}