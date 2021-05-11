<?php
class Ccc_Vendor_Model_Vendor extends Mage_Core_Model_Abstract
{

    const ENTITY = 'vendor';
    const XML_PATH_IS_CONFIRM                   = 'vendor/create_account/confirm';

    const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_EMAIL_EXISTS              = 3;
    const EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN = 4;


    const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'vendor/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'vendor/create_account/email_identity';
    const XML_PATH_REMIND_EMAIL_TEMPLATE = 'vendor/password/remind_email_template';
    const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'vendor/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY = 'vendor/password/forgot_email_identity';
    const XML_PATH_DEFAULT_EMAIL_DOMAIN         = 'vendor/create_account/email_domain';
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'vendor/create_account/email_confirmation_template';
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'vendor/create_account/email_confirmed_template';
    const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID   = 'vendor/create_account/generate_human_friendly_id';
    const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE = 'vendor/changed_account/password_or_email_template';
    const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY = 'vendor/changed_account/password_or_email_identity';

    const MINIMUM_PASSWORD_LENGTH =6;

    private static $_isConfirmationRequired;
    protected function _construct()
    {
        $this->_init('vendor/vendor');
    }

   public function getSharingConfig()
    {
        return Mage::getSingleton('vendor/config_share');
    }  

    protected $_attributes;

    public function getAttributes()
    {

        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()
                ->loadAllAttributes($this)
                ->getSortedAttributes();
        }
        return $this->_attributes;
    }

   public function authenticate($login, $password,$session)
    {      
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('vendor')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            
            throw Mage::exception('Mage_Core', Mage::helper('vendor')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD);             
            return 0;
        
        }
        Mage::dispatchEvent('vendor_vendor_authenticated', array(
           'model'    => $this,
           'password' => $password,
        ));

        $session->setId($this->getEntityId());    
        return true;
    }

    public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('password_confirmation', null);
        return $this;
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
 
  
 public function isConfirmationRequired()
    {
        if ($this->canSkipConfirmation()) {
            return false;
        }
        if (self::$_isConfirmationRequired === null) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : null;
            self::$_isConfirmationRequired = (bool)Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM, $storeId);
        }

        return self::$_isConfirmationRequired;
    }   

  public function validate()
    {  
        $errors = array();
        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is( trim($this->getLastname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('vendor')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(self::MINIMUM_PASSWORD_LENGTH))) {
            $errors[] = Mage::helper('vendor')
                ->__('The minimum password length is %s', self::MINIMUM_PASSWORD_LENGTH);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('vendor')->__('Please make sure your passwords match.');
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('vendor');
        $attribute = Mage::getModel('vendor/attribute')->loadByCode($entityType, 'dob');
        if ($attribute->getIsRequired() && '' == trim($this->getDob())) {
            $errors[] = Mage::helper('vendor')->__('The Date of Birth is required.');
        }
        $attribute = Mage::getModel('vendor/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && '' == trim($this->getTaxvat())) {
            $errors[] = Mage::helper('vendor')->__('The TAX/VAT number is required.');
        }
        $attribute = Mage::getModel('vendor/attribute')->loadByCode($entityType, 'gender');
        if ($attribute->getIsRequired() && '' == trim($this->getGender())) {
            $errors[] = Mage::helper('vendor')->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

 public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0', $password = null)
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('vendor')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        if (!is_null($password)) {
            $this->setPassword($password);
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('vendor' => $this, 'back_url' => $backUrl), $storeId);
        $this->cleanPasswordsValidationData();

        return $this;
    }


   public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        $this->setPasswordConfirmation(null);
        return $this;
    }   


 public function loadByEmail($vendorEmail)
    {

        $this->_getResource()->loadByEmail($this, $vendorEmail);
        return $this;
    }



    public function canSkipConfirmation()
    {
        return $this->getId() && $this->hasSkipConfirmationIfEmail()
            && strtolower($this->getSkipConfirmationIfEmail()) === strtolower($this->getEmail());
    } 

  public function findEmailDuplicates()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('vendor/entity'), array('email', 'cnt' => 'COUNT(*)'))
            ->group('email')
            ->order('cnt DESC')
            ->limit(1);
        $lookup = $adapter->fetchRow($select);
        if (empty($lookup)) {
            return false;
        }
        return $lookup['cnt'] > 1;
    }


  public function validatePassword($password)
    { 

        $hash = $this->getPasswordHash();

        if (!$hash) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
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

    public function checkInGroup($attributeId, $setId, $groupId)
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');
        //$readConnection = $resource->getConnection('core_read');

        $query = '
            SELECT * FROM ' .
        $resource->getTableName('eav/entity_attribute')
            . ' WHERE `attribute_id` =' . $attributeId
            . ' AND `attribute_group_id` =' . $groupId
            . ' AND `attribute_set_id` =' . $setId
        ;

        $results = $readConnection->fetchRow($query);

        if ($results) {
            return true;
        }
        return false;
    }

   protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null, $vendorEmail = null)
    {
        $vendorEmail = ($vendorEmail) ? $vendorEmail : $this->getEmail();
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($vendorEmail, $this->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }  
}