<?php

 class Ccc_Practice_Block_Adminhtml_Practice_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('practiceGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('practice_filter');

    }

   /* protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }*/

    protected function _prepareCollection()
    {
       // $store = $this->_getStore();

        $collection = Mage::getModel('practice/practice')->getCollection();
           
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $collection->joinAttribute(
            'firstname',
            'practice/firstname',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'lastname',
            'practice/lastname',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'email',
            'practice/email',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'phoneNo',
            'practice/phoneNo',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'id',
            'practice/entity_id',
            'entity_id',
            null,
            'inner',
            $adminStore
        );


        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header' => $this->__('id'),
                'width'  => '50px',
                'index'  => 'id',
            ));
        $this->addColumn('firstname',
            array(
                'header' => $this->__('First Name1'),
                'width'  => '50px',
                'index'  => 'firstname',
            ));

        $this->addColumn('lastname',
            array(
                'header' => $this->__('Last Name'),
                'width'  => '50px',
                'index'  => 'lastname',
            ));

        $this->addColumn('email',
            array(
                'header' => $this->__('Email'),
                'width'  => '50px',
                'index'  => 'email',
            ));

        $this->addColumn('phoneNo',
            array(
                'header' => $this->__('Phone Number'),
                'width'  => '50px',
                'index'  => 'phoneNo',
            ));

        $this->addColumn('action',
            array(
                'header'   => $this->__('Action'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption' => $this->__('Delete'),
                        'url'     => array(
                            'base' => '*/*/delete',
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
       return $this->getUrl('*/*/index', array('_current' => true));
   }

    public function getRowUrl($row)
    { 
        return $this->getUrl('*/*/edit', array(
            'store' => $this->getRequest()->getParam('store'),
            'id'    => $row->getId())
        );
    }
}
