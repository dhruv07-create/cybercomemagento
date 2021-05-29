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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ccc_Order_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(false);
        $this->setDefaultSort('order_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'order1/order_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'index' => 'order_id',
        ));

         $this->addColumn('customer_firstname', array(
            'header'=> Mage::helper('sales')->__('Customer Name'),
            'width' => '80px',
            'index' => 'customer_firstname',
        ));

           $this->addColumn('customer_email', array(
            'header'=> Mage::helper('sales')->__('Customer Email'),
            'width' => '80px',
            'index' => 'customer_email',
        ));

        $this->addColumn('customer_email', array(
            'header'=> Mage::helper('sales')->__('Customer Email'),
            'width' => '80px',
            'index' => 'customer_email',
        ));  
        
        $this->addColumn('shipping_amount', array(
            'header'=> Mage::helper('sales')->__('Shipping Amount'),
            'width' => '80px',
            'index' => 'shipping_amount',
        ));     

         $this->addColumn('grand_total', array(
            'header'=> Mage::helper('sales')->__('Total'),
            'width' => '80px',
            'index' => 'grand_total',
        ));      

         $this->addColumn('status', array(
            'header'=> Mage::helper('sales')->__('Status'),
            'width' => '80px',
            'index' => 'status',
        ));         

         $this->addColumn('created_at', array(
            'header'=> Mage::helper('sales')->__('Create Data'),
            'width' => '80px',
            'type' => 'datetime',
            'index' => 'created_at',
        ));        
 
       
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/adminhtml_order/massCancel'),
            ));
        }

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/adminhtml_order/edit', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current'=>true));
    }

}
