<?php
class Ccc_Vendor_Model_Session extends Mage_Core_Model_Session_Abstract
{

     protected $_isVendorIdChecked = null;
     protected $_vendor = null;

    public function __construct()
    {
        $this->init('adminhtml');
    }


   public function getVendor()
    {
        if ($this->_vendor instanceof Mage_Vendor_Model_Vendor) {
            return $this->_vendor;
        }

        $vendor = Mage::getModel('vendor/vendor')
             ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        if ($this->getId()) {
            $vendor->load($this->getId());
        }

        $this->setVendor($vendor);
        return $this->_vendor;
    }
   public function authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
        if ($this->isLoggedIn()) {
            return true;
        }

        $this->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
        if (isset($loginUrl)) {
            $action->getResponse()->setRedirect($loginUrl);
        } else {
            $action->setRedirectWithCookieCheck(Ccc_Vendor_Helper_Data::ROUTE_ACCOUNT_LOGIN,
                Mage::helper('vendor')->getLoginUrlParams()
            );
        }

        return false;
    } 
  public function setVendor(Ccc_Vendor_Model_Vendor $vendor)
    {
        // check if Vendor is not confirmed
        if ($vendor->isConfirmationRequired()) {
            if ($vendor->getConfirmation()) {
                return $this->_logout();
            }
        }
        $this->_vendor = $vendor;
        $this->setId($vendor->getId());
        // save Vendor as confirmed, if it is not
        if ((!$vendor->isConfirmationRequired()) && $vendor->getConfirmation()) {
            $vendor->setConfirmation(null)->save();
            $vendor->setIsJustConfirmed(true);
        }
        return $this;
    } 

  public function login($username, $password)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $vendor = Mage::getModel('vendor/vendor')->loadByEmail($username)
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
         if ($vendor->authenticate($username, $password,$this)) {

            $this->setVendorAsLoggedIn($vendor);
            return true;
        }
          
        return false;
    }    

  protected function _logout()
    {
        $this->setId(null);
        $this->setVendorGroupId(0);
        $this->getCookie()->delete($this->getSessionName());
        Mage::getSingleton('core/session')->renewFormKey();
        return $this;
    }    

  public function isLoggedIn()
    {
        return (bool)$this->getId() && (bool)$this->checkVendorId($this->getId());
    }

  public function setVendorAsLoggedIn($customer)
    {
        $this->setVendor($customer);
        $this->renewSession();
        Mage::getSingleton('core/session')->renewFormKey();
        Mage::dispatchEvent('vendor_login', array('vendor'=>$customer));
        return $this;
    }   

  public function checkVendorId($vendorId)
    {
        if ($this->_isVendorIdChecked === null) {
            $this->_isVendorIdChecked = Mage::getResourceSingleton('vendor/vendor')->checkVendorId($vendorId);
        }
        return $this->_isVendorIdChecked;
    }  

  public function setVendorGroupId($id)
    {
        $this->setData('vendor_group_id', $id);
        return $this;
    }

  protected function _setAuthUrl($key, $url)
    {
        $url = Mage::helper('core/url')
            ->removeRequestParam($url, Mage::getSingleton('core/session')->getSessionIdQueryParam());
        // Add correct session ID to URL if needed
        $url = Mage::getModel('core/url')->getRebuiltUrl($url);
        return $this->setData($key, $url);
    }


    public function setBeforeAuthUrl($url)
    {
        return $this->_setAuthUrl('before_auth_url', $url);
    }

    /**
     * Set After auth url
     *
     * @param string $url
     * @return Mage_Customer_Model_Session
     */
  public function setAfterAuthUrl($url)
    {
        return $this->_setAuthUrl('after_auth_url', $url);
 
    }



    public function logout()
    {
        if ($this->isLoggedIn()) {
            Mage::dispatchEvent('vendor_logout', array('vendor' => $this->getVendor()) );
            $this->_logout();
        }
        return $this;
    }  

   public function renewSession()
    {
        parent::renewSession();
        Mage::getSingleton('core/session')->unsSessionHosts();

        return $this;
    }      
    

}