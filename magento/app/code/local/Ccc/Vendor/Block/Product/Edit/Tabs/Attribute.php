<?php


/**
 * 
 */
class Ccc_Vendor_Block_Product_Edit_Tabs_Attribute extends Mage_Core_Block_Template
{
	
	function __construct()
	{
		$this->setTemplate('vendor/product/edit/tabs/attribute.phtml');
	}

	public function getOptions($attributeId)
	{
		if (!$attributeId) {
            
			return false;

		}

		$connection = Mage::getModel('core/resource')->getConnection('core_read');
		$sql = "SELECT 
		option_id
		FROM eav_attribute_option
		WHERE 
		 attribute_id = {$attributeId}
		 ";

		 $optionsCollction = $connection -> fetchAll($sql);
        
        if($optionsCollction)
        {    
		 foreach ($optionsCollction as $key => $option) {
		   
		     $optionId = $option['option_id'];

		    $sql = "SELECT * 
		     FROM
             `eav_attribute_option_value`
             WHERE 
              `option_id` = 
 		    ".$option['option_id'];

 		    $options = $connection -> fetchAll($sql);

 		    $arr[] = $options[0];              	 		
		   }

		 }
		return $arr;
	}     
      
   public function getVendorproductdata()
    {
    	return Mage::registry('current_vendorproduct');
    } 

}