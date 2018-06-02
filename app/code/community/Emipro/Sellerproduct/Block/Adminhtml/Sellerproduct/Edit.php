<?php

class Emipro_Sellerproduct_Block_Adminhtml_Sellerproduct_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_blockGroup = 'sellerproduct';
		$this->_controller = 'adminhtml_sellerproduct';
		$this->_mode='edit';
		$this->_updateButton("save", "label", Mage::helper("sellerproduct")->__("save"));
        $this->_removeButton('back');
	}
 
	public function getHeaderText()
	{
		return Mage::helper('sellerproduct')->__('Display Product on Seller Profile');
	}
}
