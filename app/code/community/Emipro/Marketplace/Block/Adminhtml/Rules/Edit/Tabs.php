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

class Emipro_Marketplace_Block_Adminhtml_Rules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('seller_Rules_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('marketplace')->__('Seller Default Commission'));
    }

    protected function _beforeToHtml() {
        $this->addTab('seller_rules', array(
            'label' => Mage::helper('marketplace')->__('Seller Default Commission'),
            'title' => Mage::helper('marketplace')->__('Seller Default Commission'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_rules_edit_tab_rules')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
