<?php
 
class Ccc_Payment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
 
protected $_code = 'paymentc';
 
protected $_isInitializeNeeded      = true;
protected $_canUseInternal          = true;
protected $_canUseForMultishipping  = true;
 
/**
* Return Order place redirect url
*
* @return string
*/
public function getOrderPlaceRedirectUrl()
{
//when you click on place order you will be redirected on this url, if you don't want this action remove this method
return Mage::getUrl('customcard/standard/redirect', array('_secure' => true));
}

 public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }
 
}