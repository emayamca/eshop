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

class Emipro_Marketplace_Block_Adminhtml_Commission_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('seller_commission_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('marketplace')->__('Order Transaction'));
    }

    protected function _beforeToHtml() {
        $this->addTab('seller_commission_edit', array(
            'label' => Mage::helper('marketplace')->__('Order Transaction'),
            'title' => Mage::helper('marketplace')->__('Order Transaction'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_commission_edit_tab_commission')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
