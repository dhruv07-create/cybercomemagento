<?php

class Five_Final_Block_Adminhtml_Grid_Edit_Form extends Mage_Adminhtml_Block_Widget_Form

{
   public function __construct()
   {
   	  parent::__construct();
   }

   public function _prepareForm()
   {
   	  $form = new Varien_Data_Form([

   	  		'id'=>'edit_form',
   	  		'action'=>$this->getUrl('*/*/save',['id'=>$this->getRequest()->getParam('id')]),
   	  		'method'=>'post'		

   	  ]);

   	  $form->setUseContainer(true);
   	  $this->setForm($form);

   	  $fildSet = $form->addFieldSet('add',['legend'=>$this->__('INFo...')]);

   	  $fildSet->addField('full_name','text',[

   	  			'name'=>'final[full_name]',
   	  			'required'=>true,
   	  			'label'=>'Full_Name'
   	  ]); 

   	  $fildSet->addField('email','text',[

   	  			'name'=>'final[email]',
   	  			'required'=>true,
   	  			'label'=>'Email'
   	  ]); 

   	  $fildSet->addField('address','text',[

   	  			'name'=>'final[address]',
   	  			'required'=>true,
   	  			'label'=>'Address'
   	  ]); 

      if(Mage::registry('finalDd'))
      {
      	 $form->setValues(Mage::registry('finalDd')->getData());
      }
 
   	  return parent::_prepareForm();
   	     	     	     	  
   }
}

?>