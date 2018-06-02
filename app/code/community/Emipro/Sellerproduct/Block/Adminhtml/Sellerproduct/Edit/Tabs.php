<?php
class Emipro_Sellerproduct_Block_Adminhtml_Sellerproduct_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('sellerproduct_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('sellerproduct')->__('Seller Product'));
	}
	public function _beforeToHtml()
	{
		$this->addTab('product_grid_section', array(
					'label' => Mage::helper('sellerproduct')->__('Display Product on Seller Profile'),
					'title' => Mage::helper('sellerproduct')->__('Display Product on Seller Profile'),
					'url'       => $this->getUrl('*/*/products', array('_current' => true)),
					'class'     => 'ajax',
					));
		return parent::_beforeToHtml();
	}
}
