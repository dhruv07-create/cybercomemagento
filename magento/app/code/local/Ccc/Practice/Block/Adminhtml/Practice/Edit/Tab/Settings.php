<?php

class Ccc_Practice_Block_Adminhtml_Practice_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('practice')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','attribute_set_id')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('practice')->__('Create Vendor Settings')));

        $entityType = Mage::registry('current_practice')->getResource()->getEntityType();

        $fieldset->addField('attribute_set_id', 'select', array(
            'label' => Mage::helper('practice')->__('Attribute Set'),
            'title' => Mage::helper('practice')->__('Attribute Set'),
            'name'  => 'set',
            'value' => $entityType->getDefaultAttributeSetId(),
            'values'=> Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityType->getId())
                ->load()
                ->toOptionArray()
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'set'       => '{{attribute_set}}',
            'type'      => '{{type}}'
        ));
    }
}
?>

<script type="text/javascript">
//<![CDATA[
    var productTemplateSyntax = /(^|.|\r|\n)({{(\w+)}})/;

    function setSettings(urlTemplate, setElement) {
        var template = new Template(urlTemplate, productTemplateSyntax);
        
        setLocation(template.evaluate({attribute_set:$F(setElement),type:$F(setElement)}));
        console.log(template);        
    }

</script>