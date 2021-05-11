<?php  
class Ccc_Vendor_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('vendorGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('vendor_filter');

    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
 

   public function _prepareCollection()
   {
   	    $store = $this->_getStore();

        $collection = Mage::getModel('vendor/product')->getCollection()
        ;
    
         $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
         
        $collection->joinAttribute(
            'name',
            'vendor_product/name',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'sku',
            'vendor_product/sku',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'vendor_log',
            'vendor_product/vendor_log',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'vendor_status',
            'vendor_product/vendor_status',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

         $collection->joinAttribute(
            'id',
            'vendor_product/entity_id',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

         $collection->joinAttribute(
            'request',
            'vendor_product/request',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'request_status',
            'vendor_product/request_status',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

         $collection->joinAttribute(
            'firstname',
            'vendor/firstname',
            'vendor_id',
            null,
            'left',
            $adminStore
        );
      
        $collection->addFieldToFilter('request',['eq'=>'0']); 

  
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;

   }

    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header' => Mage::helper('vendor')->__('Id'),
                'width'  => '50px',
                'index'  => 'id',
            ));
        $this->addColumn('name',
            array(
                'header' => Mage::helper('vendor')->__('Name'),
                'width'  => '50px',
                'index'  => 'name',
            ));

        $this->addColumn('sku',
            array(
                'header' => Mage::helper('vendor')->__('Sku'),
                'width'  => '50px',
                'index'  => 'sku',
            ));

        $this->addColumn('Vendor Name',
            array(
                'header' => Mage::helper('vendor')->__('Vendor Name'),
                'width'  => '50px',
                'index'  => 'firstname',
            ));

        $this->addColumn('Vendor Request',
            array(
                'header' => Mage::helper('vendor')->__('vendor_request'),
                'width'  => '50px',
                'index'  => 'vendor_status',
            ));

      
        $this->addColumn('action1',
            array(
                'header'   => Mage::helper('vendor')->__('Approve'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('vendor')->__('approve'),
                        'url'     => array(
                            'base' => '*/*/approve',
                        ),
                        'field'   => 'id',
                    ),
                ),
                'filter'   => false,
                'sortable' => false,
            ));

          $this->addColumn('action2',
            array(
                'header'   => Mage::helper('vendor')->__('Reject'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('vendor')->__('reject'),
                        'url'     => array(
                            'base' => '*/*/unapprove',
                        ),
                        'field'   => 'id',
                    ),
                ),
                'filter'   => false,
                'sortable' => false,
            ));

        parent::_prepareColumns();
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/', array(
            'store' => $this->getRequest()->getParam('store'),
            'id'    => $row->getId())
        );
    }
 
}