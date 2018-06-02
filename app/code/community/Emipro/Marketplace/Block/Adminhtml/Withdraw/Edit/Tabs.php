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

class Emipro_Marketplace_Block_Adminhtml_Withdraw_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {

        parent::__construct();
        $this->setId('seller_withdraw_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('marketplace')->__('Seller Withdrawals'));
    }

    protected function _beforeToHtml() {
        $this->addTab('seller_withdraw', array(
            'label' => Mage::helper('marketplace')->__('Seller Withdrawals'),
            'title' => Mage::helper('marketplace')->__('Seller Withdrawals'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_withdraw_edit_tab_withdraw')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
