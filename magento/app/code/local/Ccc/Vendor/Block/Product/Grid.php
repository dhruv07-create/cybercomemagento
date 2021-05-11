<?php


 class Ccc_Vendor_Block_Product_Grid extends Mage_Core_Block_Template
 {
 	  public function getCollection()
 	  {
 	  	  $model = Mage::getModel('vendor/product')->getCollection();
 	  	   
            $model->joinAttribute(
                            'name',
                            'vendor_product/name',
                            'entity_id',
                             null,
                            'inner',
                            0
                        );   
            $model->joinAttribute(
                            'sku',
                            'vendor_product/sku',
                            'entity_id',
                             null,
                            'inner',
                            0
                        );   
            $model->joinAttribute(
                            'price',
                            'vendor_product/price',
                            'entity_id',
                             null,
                            'inner',
                            0
                        ); 
             $model->joinAttribute(
                            'vendor_status',
                            'vendor_product/vendor_status',
                            'entity_id',
                             null,
                            'inner',
                            0
                        );   
             $model->joinAttribute(
                            'vendor_log',
                            'vendor_product/vendor_log',
                            'entity_id',
                             null,
                            'inner',
                            0
                        );  
              $model->joinAttribute(
                            'admin_log',
                            'vendor_product/admin_log',
                            'entity_id',
                             null,
                            'inner',
                            0
                        );                                         
 	
                 $model->joinAttribute(
                            'admin_status',
                            'vendor_product/admin_status',
                            'entity_id',
                             null,
                            'inner',
                            0
                        ); 

                 $model->joinAttribute(
                            'vendor_id',
                            'vendor_product/vendor_id',
                            'entity_id',
                             null,
                            'inner',
                            0
                        );
    
               $model->addFieldToFilter('vendor_id',['eq'=>Mage::getModel('vendor/session')->getId()]);


 	  	 return $model;
 	  }
 }