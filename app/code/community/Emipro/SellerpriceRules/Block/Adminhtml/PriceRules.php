<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @Author Emipro Technologies Private Limited
 * @Category Emipro
 * @Package  Emipro_SellerpriceRules
 * @License http://shop.emiprotechnologies.com/license-agreement/
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class Emipro_SellerpriceRules_Block_Adminhtml_PriceRules extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
		$this->_blockGroup = 'emipro_sellerpricerules';
        $this->_controller = 'adminhtml_priceRules';
		///$this->_controller = 'promo_catalog';
        $this->_headerText = Mage::helper('catalogrule')->__('Seller Catalog Price Rules');
        $this->_addButtonLabel = Mage::helper('catalogrule')->__('Add New Rule');
		parent::__construct();
		$this->_removeButton('add');
    }
}
