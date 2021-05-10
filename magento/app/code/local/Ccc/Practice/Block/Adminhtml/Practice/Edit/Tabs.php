<?php

class Ccc_Practice_Block_Adminhtml_Practice_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{


    public function __construct()
    {
      parent::__construct();
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('practice')->__('practice Information'));
    }
    public function getVendor()
    {
        return Mage::registry('current_practice');
    }

    protected function _beforeToHtml()
    {    
        $vendorAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
             ->setEntityTypeFilter(Mage::getModel('eav/entity')->setType('practice')->getTypeId());
       
        if (!$this->getVendor()->getId()) {
            foreach ($vendorAttributes as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $this->getVendor()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }
        
        $attributeSetId = $this->getVendor()->getResource()->getEntityType()->getDefaultAttributeSetId();



        // $attributeSetId = 21;
        
        $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($attributeSetId)
            ->setSortOrder()
            ->load();


        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            if ($defaultGroupId == 0 or $group->getIsDefault()) {
                $defaultGroupId = $group->getId();
            }

        }	


        foreach ($groupCollection as $group) {
            $attributes = array();
            foreach ($vendorAttributes as $attribute) {
                if ($this->getVendor()->checkInGroup($attribute->getId(),$attributeSetId, $group->getId())) {
                    $attributes[] = $attribute;
                }
            }

            if (!$attributes) {
                continue;
            }

            $active = $defaultGroupId == $group->getId();
            $block = $this->getLayout()->createBlock('practice/adminhtml_practice_edit_tab_attributes')
                ->setGroup($group)
                ->setAttributes($attributes)
                ->setAddHiddenFields($active)
                ->toHtml();
            $this->addTab('group_' . $group->getId(), array(
                'label'     => Mage::helper('practice')->__($group->getAttributeGroupName()),
                'content'   => $block,
                'active'    => $active
            ));
        }
      return parent::_beforeToHtml();
    }
}
