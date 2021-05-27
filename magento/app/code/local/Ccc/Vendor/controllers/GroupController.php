<?php

 class Ccc_Vendor_GroupController extends Mage_Core_Controller_Front_Action
 {
 	  public function indexAction()
 	  { 
 	  	if(!Mage::getModel('vendor/session')->isLoggedIn())
 	  	{
 	  		$this->_redirect('*/account/login');
 	  	}
 	  	 $this->loadLayout();
 	  	 $this->renderLayout();
 	  }

 	  public function createAction()
 	  {
 	  	if(!Mage::getModel('vendor/session')->isLoggedIn())
 	  	{
 	  		$this->_redirect('*/account/login');
 	  	}
 	  	$this->loadLayout();
 	  	 $this->renderLayout();
 	  }

 	  public function saveAction()
 	  {
      
 	  	if(!Mage::getModel('vendor/session')->isLoggedIn())
 	  	{
 	  		$this->_redirect('*/account/login');
 	  	}
 	  	try{
 	  	  if($this->getRequest()->isPost())
 	  	  {
              $vendorid = Mage::getModel('vendor/session')->getId(); 
               

 	  	  	  $data = $this->getRequest()->getPost();

             if(!$data['attribute_group_name'])
             {
                
                Mage::getModel('core/session')->addError('Group Name is required ');

                if($this->getRequest()->getParam('attribute_group_id'))
                {
                $this->_redirect('*/*/edit',['_current'=>true]); 
                }else{
                $this->_redirect('*/*/create'); 
                }
               
                 return;

             }
              
              $default_set_id = Mage::getModel('eav/entity_setup','core_setup')
                                      ->getAttributeSetId('vendor_product','Default');         

 	  	  	  $model =  Mage::getModel('eav/entity_attribute_group');

 	  	  	  $groupModel = Mage::getModel('vendor/product_attribute_group');

 	  	  	  $attribute_group_id = $this->getRequest()->getParam('attribute_group_id');
 	  	  	  $group_id = $this->getRequest()->getParam('group_id');

              $group_name = $this->getRequest()->getParam('attribute_group_name');
 
              $model->load($attribute_group_id);
              $groupModel->load($group_id);

              $model
              ->setAttributeSetId($default_set_id)
              ->setAttributeGroupName($vendorid.''.$group_name)             
              ;

              if(!$model->save())
			         {
              	throw new Exception('Group Not Created1', 1);
               }

               $groupModel 
              ->setAttributeSetId($default_set_id)
              ->setAttributeGroupName($group_name);        
              ;

              if(!$group_id)
              {
              	$groupModel
              	->setEntityId($vendorid)
              	->setAttributeGroupId($model->getAttributeGroupId());
              }

              if(!$groupModel->save())
              {
              	throw new Exception('Group Not Created2', 1);
              	
              }else{
              	Mage::getModel('core/session')->addSuccess("Group Operation Sucessfull.");
              }

             $assignAttributes = $this->getRequest()->getPost('old');

             if(!$assignAttributes)
             {
                $assignAttributes=[];
             } 
             $unAssignAttributes = $this->getRequest()->getPost('new');
             if(!$unAssignAttributes)
             {
                $unAssignAttributes=[];
             } 

             
              $attribute_group_id = $this->getRequest()->getParam('attribute_group_id');
              
              $attmodel = Mage::getModel('eav/entity_attribute')
             ->getCollection()
             ->setAttributeGroupFilter($attribute_group_id)
              ;

              $dataAttribute = [];

              foreach ($attmodel->getData() as $attribute) 
              {   
                   $dataAttribute[]=$attribute['attribute_id']; 

              }

              $connection = Mage::getModel('core/resource')->getConnection('core_write');
              $different = array_diff($dataAttribute,$assignAttributes);

              $model1 = Mage::getModel('eav/entity_attribute');
              if($different)
              {
                 foreach ($different as $value) {
    
                    $sql = "DELETE FROM 
                    eav_entity_attribute 
                    WHERE 
                     attribute_id = {$value}
                     ";

                    $connection->query($sql); 
                 }
              } 
             
             if($unAssignAttributes)
             {
               $default_set_id = Mage::getModel('eav/entity_setup','core/setup')
                ->getAttributeSetId('vendor_product','Default');
 
                  if(!$attribute_group_id)
                  {
                     $attribute_group_id = $model->getAttributeGroupId();  
                  }
                    
                    foreach ($unAssignAttributes as $value) {
                    
                       $model_eav = Mage::getModel('eav/entity_attribute')
                        ->setEntityTypeId(Mage::getModel('eav/entity')->setType('vendor_product')->getTypeId())
                        ->setAttributeSetId($default_set_id) 
                        ->setAttributeGroupId($attribute_group_id) 
                        ->setAttributeId($value) 
                   ;
                   $model_eav->save();
                      
                      }  
             } 

          }

          }catch(Exception $e)
          {
          	Mage::getModel('core/session')->addError($e->getMessage());
          	$this->_redirect('*/*/');
          }
            
 			$this->_redirect('*/*/');
 	  }

 	  public function editAction()
 	  {
 	  	  try
 	  	  {
            $this->loadLayout();

            $this->renderLayout(); 

 	  	  }catch(Exception $e)
 	  	  {
              Mage::getModel('core/session')->addError($e->getMessage());
              $this->_redirect('*/group/');
 	  	  }
 	  }

 	  public function deleteAction()
 	  {
 	  	  try
 	  	  {
 	  	  	  $attribute_group_id = $this->getRequest()->getParam('attribute_group_id');
 	  	  	  $group_id = $this->getRequest()->getParam('group_id');

 	  	  	  $model =  Mage::getModel('eav/entity_attribute_group')->load($attribute_group_id);

 	  	  	  $groupModel = Mage::getModel('vendor/product_attribute_group')->load($group_id);

 	  	  	  if(!$model->delete())
 	  	  	  {
 	  	  	  	throw new  Exception("Delete Not Successfully", 1);
 	  	  	  	
 	  	  	  }

 	  	  	  if(!$groupModel->delete())
 	  	  	  {
 	  	  	  	throw new  Exception("Delete Not Successfully", 1);
 	  	  	  	
 	  	  	  }else{
 	  	  	  	 Mage::getModel('core/session')->addSuccess("Delete Successfull..");
 	  	  	  }

 	  	  }catch(Exception $e)
 	  	  {
              Mage::getModel('core/session')->addError($e->getMessage());
 	  	  }
          
           $this->_redirect('*/group/');
 	  }
 }