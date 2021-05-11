<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ccc_Vendor_Helper_Address extends Mage_Core_Helper_Abstract
{
    /**
     * VAT Validation parameters XML paths
     */
    const XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT = 'vendor/create_account/viv_disable_auto_group_assign_default';
    const XML_PATH_VIV_ON_EACH_TRANSACTION         = 'vendor/create_account/viv_on_each_transaction';
    const XML_PATH_VAT_VALIDATION_ENABLED          = 'vendor/create_account/auto_group_assign';
    const XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE = 'vendor/create_account/tax_calculation_address_type';
    const XML_PATH_VAT_FRONTEND_VISIBILITY = 'vendor/create_account/vat_frontend_visibility';

    /**
     * Array of Customer Address Attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Customer address config node per website
     *
     * @var array
     */
    protected $_config          = array();

    /**
     * Customer Number of Lines in a Street Address per website
     *
     * @var array
     */
    protected $_streetLines     = array();
    protected $_formatTemplate  = array();

    /**
     * Addresses url
     */
    public function getBookUrl()
    {

    }

    public function getEditUrl()
    {

    }

    public function getDeleteUrl()
    {

    }

    public function getCreateUrl()
    {

    }
   
    public function isVatValidationEnabled($store = null)
     {
        return (bool)Mage::getStoreConfig(self::XML_PATH_VAT_VALIDATION_ENABLED, $store);
     }
   
    public function getTaxCalculationAddressType($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_VIV_TAX_CALCULATION_ADDRESS_TYPE, $store);
    }


 }