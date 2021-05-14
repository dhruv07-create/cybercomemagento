    protected function _prepareLayout()
    {
        $product = $this->getProduct();
        $productAttributes = Mage::getResourceModel('vendor/product_attribute_collection');
      
        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        
        if ($setId) {

            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
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
                foreach ($productAttributes as $attribute) {
                    if ($product->checkInGroup($attribute->getId(), $setId, $group->getId())) {
                        $attributes[] = $attribute;
                    }
                }
                if (count($attributes)==0) {
                    continue;
                }

                $active = $defaultGroupId == $group->getId();
                $block = $this->getLayout()->createBlock('vendor/adminhtml_product_edit_tab_attributes')
                    ->setGroup($group)
                    ->setAttributes($attributes)
                    ->setAddHiddenFields($active)
                    ->toHtml();


                $this->addTab('group_' . $group->getId(), array(
                    'label' => Mage::helper('vendor')->__($group->getAttributeGroupName()),
                    'content' => $block,
                    'active' => $active
                ));
                
            }
        } 
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('vendor')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('vendor/adminhtml_product_edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }

        return parent::_prepareLayout();
    }