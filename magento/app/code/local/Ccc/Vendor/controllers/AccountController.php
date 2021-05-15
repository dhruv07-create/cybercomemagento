<?php

 class Ccc_Vendor_AccountController extends Mage_Core_Controller_Front_Action
 {
  
   protected $_cookieCheckActions = array('loginPost', 'createpost');


 const MINIMUM_PASSWORD_LENGTH = 6;

  const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE = 'customer/changed_account/password_or_email_template';
    const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY = 'customer/changed_account/password_or_email_identity';
  
   
  public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();
        
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = strtolower($this->getRequest()->getActionName());

        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'changeforgotten',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'index',
            'logout',
            'confirmation'
        );

        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Action postdispatch
     *
     * Remove No-referer flag from customer session after each action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }

   public function indexAction()
    {   
        if(!$this->_getSession()->isLoggedIn())
        { 
            $this->_redirect("*/*/login");
        }
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }

    public function logoutAction()
    {  
        $session = $this->_getSession();
        $session->logout()->renewSession();

        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }
        $this->_redirect('*/*/logoutSuccess');
    }

    /**
     * Logout success page
     */
    public function logoutSuccessAction()
    {  
     
        $this->loadLayout();
        $this->renderLayout();
    }  

   public function loginAction()
    { 

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

protected function _welcomeVendor(Ccc_Vendor_Model_Vendor $vendor, $isJustConfirmed = false)
    { 
        $this->_getSession()->addSuccess(
            $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );

        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType =  $this->_getHelper('vendor/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation',
                        $this->_getUrl('vendor/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation',
                        $this->_getUrl('vendor/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        

        $vendor->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId(),
            $this->getRequest()->getPost('password')
        );

        $successUrl = $this->_getUrl('*/*/index', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        } 
        
        return $successUrl;
    }

  }  


 public function loginPostAction()
   {  
        if (!$this->_validateFormKey()) {

            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();



        if ($this->getRequest()->isPost()) {


             $login = $this->getRequest()->getPost('login');

            if (!empty($login['username']) && !empty($login['password'])) {
                try {
            
                    $session->login($login['username'], $login['password']);
                   /* echo '<pre>'; print_r($session->getData());
                    die();*/
                    if ($session->getVendor()->getIsJustConfirmed()) {
                        $this->_welcomeVendor($session->getVendor(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Ccc_Vendor_Model_Vendor::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('vendor')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('vendor')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Ccc_Vendor_Model_Vendor::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage(); 
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                     //Mage::logException($e); // PA DSS violation: this exception log can disclose Customer password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }

        $this->_loginPostRedirect(); 
    }    

   protected function _loginPostRedirect()
    {
         $session = $this->_getSession();

        
        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl($this->_getHelper('vendor')->getAccountUrl());
       
            // Redirect customer to the last page visited after logging in

            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Ccc_Vendor_Helper_Data::XML_PATH_Vendor_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Ccc_Vendor_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {

                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = $this->_getModel('core/url')
                            ->getRebuiltUrl( $this->_getHelper('core')->urlDecodeAndEscape($referer));
                        $referer = 'http://127.0.0.1/git/magento/account/';
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl( $this->_getHelper('vendor')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() ==  $this->_getHelper('vendor')->getLogoutUrl()) {
            $session->setBeforeAuthUrl( $this->_getHelper('vendor')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
            
        $this->_redirectUrl($session->getBeforeAuthUrl(true));

    }

  public function forgotPasswordAction()
    {
        $this->loadLayout();

        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    }

 protected function _isVatValidationEnabled($store = null)
    {
        return  $this->_getHelper('vendor/address')->isVatValidationEnabled($store);
    }

  protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

   protected function _getHelper($path)
    {
        return Mage::helper($path);
    }

   protected function editAction()
   {
       $this->loadLayout();
       $this->renderLayout();
   } 

  protected function _addSessionError($errors)
    {
        $session = $this->_getSession();
        $session->setVendorFormData($this->getRequest()->getPost());
        if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
                $session->addError($this->_escapeHtml($errorMessage));
            }
        } else {
            $session->addError($this->__('Invalid customer data'));
        }
    }

  public function createAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
           $this->_redirect('*/*');
           return;
       }

        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    } 

   public function sendChangedPasswordOrEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE,
            self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY,
            array('vendor' => $this), $storeId, $this->getOldEmail());

        return $this;
    }

   
   protected function _getWebsiteStoreId($defaultStoreId = null)
    {
        if ($this->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

   protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }


 public function editPostAction()
    {
       
        
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }

        if ($this->getRequest()->isPost()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $vendor = $this->_getSession()->getVendor();
            $vendor->setOldEmail($vendor->getEmail());
            /** @var $vendorForm Mage_vendor_Model_Form */
            $vendorForm = $this->_getModel('vendor/form');
            $vendorForm->setFormCode('vendor_account_edit')
                ->setEntity($vendor);

            $vendorData = $vendorForm->extractData($this->getRequest());

            $errors = array();
            $vendorErrors = $vendorForm->validateData($vendorData);
            if ($vendorErrors !== true) {
                $errors = array_merge($vendorErrors, $errors);
            } else {
                $vendorForm->compactData($vendorData);
                $errors = array();

                if (!$vendor->validatePassword($this->getRequest()->getPost('current_password'))) {
                    $errors[] = $this->__('Invalid current password');
                }

                // If email change was requested then set flag
                $isChangeEmail = ($vendor->getOldEmail() != $vendor->getEmail()) ? true : false;
                $vendor->setIsChangeEmail($isChangeEmail);

                // If password change was requested then add it to common validation scheme
                $vendor->setIsChangePassword($this->getRequest()->getParam('change_password'));

                if ($vendor->getIsChangePassword()) {
                    $newPass    = $this->getRequest()->getPost('password');
                    $confPass   = $this->getRequest()->getPost('confirmation');

                    if (strlen($newPass)) {
                        /**
                         * Set entered password and its confirmation - they
                         * will be validated later to match each other and be of right length
                         */
                        $vendor->setPassword($newPass);
                        $vendor->setPasswordConfirmation($confPass);
                    } else {
                        $errors[] = $this->__('New password field cannot be empty.');
                    }
                }

                // Validate account and compose list of errors if any
                $vendorErrors = $vendor->validate();
                if (is_array($vendorErrors)) {
                    $errors = array_merge($errors, $vendorErrors);
                }
            }

            if (!empty($errors)) {
                $this->_getSession()->setVendorFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }

                $this->_redirect('*/*/index');
                return $this;
            }

            try {
               // $vendor->cleanPasswordsValidationData();

                // Reset all password reset tokens if all data was sufficient and correct on email change
                if ($vendor->getIsChangeEmail()) {
                    $vendor->setRpToken(null);
                    $vendor->setRpTokenCreatedAt(null);
                }
                $form = $this->getRequest()->getPost();
                $vendor->setFirstname($form['firstname'])
                ->setLastname($form['lastname'])
                ->setMiddlename($form['middlename'])
                ->setEmail($form['email']);
                 
                  if($this->getRequest()->getParam('change_password'))
                  {
                     $new = $this->getRequest()->getPost('password'); 
                     $vendor->setPasswordHash(md5($new));                   
                  }
  
                $vendor->save();
                 
                $this->_getSession()->setVendor($vendor)
                    ->addSuccess($this->__('The account information has been saved.'));

                if ($vendor->getIsChangeEmail() || $vendor->getIsChangePassword()) {
                    //$vendor->sendChangedPasswordOrEmail();
                }

                $this->_redirect('vendor/account');
                return;
            } catch (Mage_Core_Exception $e) {
                echo '<pre>';
                print_r($e);  die();
                $this->_getSession()->setVendorFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                     echo '<pre>';
                print_r($e);
                die();
                $this->_getSession()->setVendorFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__($e->getMessage()));
            }
        }
            
        $this->_redirect('*/*/edit');
    }

   public function createPostAction()
    {
        try{
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));

        if (!$this->_validateFormKey()) {
            $this->_redirectError($errUrl);
            return;
        }

        /** @var $session Mage_Customer_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->_redirectError($errUrl);
            return;
        }

        $vendor = $this->_getVendor();
        $data = $this->getRequest()->getPost();
         $model = Mage::getModel('vendor/vendor')->loadByEmail($data['email']);
         if($model->getData())
         {
              throw new Exception("Email is already Available", 1);
                                      
         }
        $vendor->setFirstname($data['firstname'])
         ->setLastname($data['lastname'])
         ->setMiddlename($data['middlename'])
         ->setPasswordHash(md5($data['password']))
         ->setEmail($data['email']);


         if($vendor->save())
         {
            $this->_redirect('*/*/login');
         }

        // $this->_successProcessRegistration($vendor);
     }catch(Exception $e)
     {
        Mage::getModel('core/session')->addError($e->getMessage());
        $this->_redirect("*/*/create");
     }

       /* try {
            $errors = $this->_getVendorErrors($vendor);
         
            if (empty($errors)) {
                $vendor->cleanPasswordsValidationData();
                $vendor->save();
                $this->_dispatchRegisterSuccess($vendor);
                $this->_successProcessRegistration($vendor);
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setVendorFormData($this->getRequest()->getPost());
            if ($e->getCode() === Ccc_Vendor_Model_Vendor::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('vendor/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
            } else {
                $message = $this->_escapeHtml($e->getMessage());
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            $session->addException($e, $this->__($e->getMessage()));
        }

        $this->_redirectError($errUrl);*/
    }

   protected function _dispatchRegisterSuccess($vendor)
    {
        Mage::dispatchEvent('vendor_register_success',
            array('account_controller' => $this, 'vendor' => $vendor)
        );
    }

   protected function _getVendor()
    {
        $vendor = $this->_getFromRegistry('current_vendor');
        if (!$vendor) {
            $vendor = $this->_getModel('vendor/vendor')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $vendor->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $vendor->getGroupId();

        return $vendor;
    }

  public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
    }

  protected function _getFromRegistry($path)
    {
        return Mage::registry($path);
    }

  public function getGroupId()
    {
        if (!$this->hasData('group_id')) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
            $groupId = Mage::getStoreConfig(Mage_Vendor_Model_Group::XML_PATH_DEFAULT_ID, $storeId);
            $this->setData('group_id', $groupId);
        }
        return $this->getData('group_id');
    } 

   protected function _successProcessRegistration(Ccc_Vendor_Model_Vendor $vendor)
    {
        $session = $this->_getSession();
        if ($vendor->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store*/
            $store = $app->getStore();
            $vendor->sendNewAccountEmail(
                'confirmation',
                $session->getBeforeAuthUrl(),
                $store->getId(),
                $this->getRequest()->getPost('password')
            );
            $vendorHelper = $this->_getHelper('vendor');
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
                $vendorHelper->getEmailConfirmationUrl($vendor->getEmail())));
            $url = $this->_getUrl('*/*/index', array('_secure' => true));
        } else {
            $session->setVendorAsLoggedIn($vendor);
            $url = $this->_welcomeVendor($vendor);
        }

        $this->_redirectSuccess($url);
        return $this;
    }

  protected function _escapeHtml($text)
    {
        return Mage::helper('core')->escapeHtml($text);
    }
   
  
 protected function _getApp()
    {
        return Mage::app();
    } 

   protected function _getVendorErrors($vendor)
    {
        $errors = array();
        $request = $this->getRequest();
       /* if ($request->getPost('create_address')) {
            $errors = $this->_getErrorsOnVendorAddress($vendor);
        }*/
        $vendorForm = $this->_getVendorForm($vendor);
        $vendorData = $vendorForm->extractData($request);
        $vendorErrors = $vendorForm->validateData($vendorData);
        if ($vendorErrors !== true) {
            $errors = array_merge($vendorErrors, $errors);
        } else {
            $vendorForm->compactData($vendorData);
            $vendor->setPassword($request->getPost('password'));
            $vendor->setPasswordConfirmation($request->getPost('confirmation'));
            $vendorErrors = $vendor->validate();
            if (is_array($vendorErrors)) {
                $errors = array_merge($vendorErrors, $errors);
            }
        }
        return $errors;
    }  

 protected function _getVendorForm($vendor)
    {
        /* @var $customerForm Mage_Customer_Model_Form */
        $vendorForm = $this->_getModel('vendor/form');
        $vendorForm->setFormCode('vendor_account_create');
        $vendorForm->setEntity($vendor);
        return $vendorForm;
    } 



   public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('password_confirmation', null);
        return $this;
    }   
    
   
/*
   protected function _getErrorsOnVendorAddress($vendor)
    {
        $errors = array();
        $address = $this->_getModel('vendor/address');
        $addressForm = $this->_getModel('vendor/form');
        $addressForm->setFormCode('customer_register_address')
            ->setEntity($address);

        $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
        $addressErrors = $addressForm->validateData($addressData);
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        $address->setId(null)
            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        $addressForm->compactData($addressData);
        $customer->addAddress($address);

        $addressErrors = $address->validate();
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        return $errors;
    }  
*/    

 }