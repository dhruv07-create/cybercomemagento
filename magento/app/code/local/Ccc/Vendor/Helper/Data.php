<?php

class Ccc_Vendor_Helper_Data extends Mage_Core_Helper_Abstract {

    const ROUTE_ACCOUNT_LOGIN = 'vendor/account/login';

    const REFERER_QUERY_PARAM_NAME = 'referer';



    /**
     * Config name for Redirect vendor to Account Dashboard after Logging in setting
     */
    const XML_PATH_Vendor_STARTUP_REDIRECT_TO_DASHBOARD = 'vendor/startup/redirect_dashboard';

    /**
     * Config paths to VAT related vendor groups
     */
    const XML_PATH_Vendor_VIV_INTRA_UNION_GROUP = 'vendor/create_account/viv_intra_union_group';
    const XML_PATH_Vendor_VIV_DOMESTIC_GROUP = 'vendor/create_account/viv_domestic_group';
    const XML_PATH_Vendor_VIV_INVALID_GROUP = 'vendor/create_account/viv_invalid_group';
    const XML_PATH_Vendor_VIV_ERROR_GROUP = 'vendor/create_account/viv_error_group';


	 public function getLoginUrl()
    {
        return $this->_getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }


    public function getLoginUrlParams()
    {
        $params = array();

        $referer = $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME);

        if (!$referer && !Mage::getStoreConfigFlag(self::XML_PATH_Vendor_STARTUP_REDIRECT_TO_DASHBOARD)
            && !Mage::getSingleton('vendor/session')->getNoReferer()
        ) {
            $referer = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
            $referer = Mage::helper('core')->urlEncode($referer);
        }

        if ($referer) {
            $params = array(self::REFERER_QUERY_PARAM_NAME => $referer);
        }

        return $params;
    }

 

    /**
     * Retrieve vendor login POST URL
     *
     * @return string
     */
    public function getLoginPostUrl()
    {
        $params = array();
        if ($this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = array(
                self::REFERER_QUERY_PARAM_NAME => $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)
            );
        }
        return $this->_getUrl('vendor/account/loginPost', $params);
    }

    /**
     * Retrieve vendor logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_getUrl('vendor/account/logout');
    }

    /**
     * Retrieve vendor dashboard url
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->_getUrl('vendor/account');
    }

     public function getRegisterUrl()
    {
        return $this->_getUrl('vendor/account/create');
    }


    /**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_getUrl('vendor/account');
    }

    /**
     * Retrieve vendor register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->_getUrl('vendor/account/createpost');
    }   

    /**
     * Retrieve vendor account edit form url
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->_getUrl('vendor/account/edit');
    }

   public function getForgotPasswordUrl()
    {
        return $this->_getUrl('vendor/account/forgotpassword');
    }  


   public function getEmailConfirmationUrl($email = null)
    {
        return $this->_getUrl('vendor/account/confirmation', array('email' => $email));
    }   


     public function isRegistrationAllowed()
    {
        $result = new Varien_Object(array('is_allowed' => true));
        Mage::dispatchEvent('vendor_registration_is_allowed', array('result' => $result));
        return $result->getIsAllowed();
    }
	
}