<?php

 class Five_Final_Block_Adminhtml_Grid_Grid extends Mage_Adminhtml_Block_Widget_Grid
 {
 	public function __construct()
 	{ 
 		parent::__construct();
 		$this->setDefaultSort('id');
 		$this->setDefaultDir('desc');
 		$this->setId('idd');
 	}

 	public function _prepareColumns()
 	{
 		$this->addColumn('id',[

 			 'header'=>'Id',
 			 'index'=>'id'
 		]);

 		$this->addColumn('name',[

 			 'header'=>'Full-Name',
 			 'index'=>'full_name'
 		]);

 		$this->addColumn('email',[

 			 'header'=>'Email',
 			 'index'=>'email'
 		]);

 		$this->addColumn('address',[

 			 'header'=>'Address',
 			 'index'=>'address'
 		]);

 		return parent::_prepareColumns();
 		
 	}

 	public function getRowUrl($row)
 	{
 		return $this->getUrl('*/*/edit',['id'=>$row->getId()]);
 	}

 	public function _prepareCollection()
 	{
 		 $collection = Mage::getModel('final/final')->getCollection();
 		 $this->setCollection($collection);
 		 return parent::_prepareCollection();
 	}
 }

?>