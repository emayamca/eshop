<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Marketplace 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Marketplace_Block_Adminhtml_Sellerlist_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('seller_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('marketplace')->__('Seller Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('seller_information', array(
            'label' => Mage::helper('marketplace')->__('Seller Details'),
            'title' => Mage::helper('marketplace')->__('Seller Details'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_sellerlist_edit_tab_seller')->toHtml(),
        ));
        $this->addTab('store_information', array(
            'label' => Mage::helper('marketplace')->__('Store Information'),
            'title' => Mage::helper('marketplace')->__('Store Information'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_sellerlist_edit_tab_storeInformation')->toHtml(),
        ));
        $this->addTab('seller_product', array(
            'label' => Mage::helper('marketplace')->__('Products'),
            'title' => Mage::helper('marketplace')->__('Products'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_sellerlist_edit_tab_sellerproducts')->toHtml(),
        ));
        $this->addTab('seller_order', array(
            'label' => Mage::helper('marketplace')->__('Orders'),
            'title' => Mage::helper('marketplace')->__('Orders'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_sellerlist_edit_tab_sellerorders')->toHtml(),
        ));
        $this->addTab('bank_details', array(
            'label' => Mage::helper('marketplace')->__('Bank Details'),
            'title' => Mage::helper('marketplace')->__('Bank Details'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_sellerlist_edit_tab_bankdetails')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
