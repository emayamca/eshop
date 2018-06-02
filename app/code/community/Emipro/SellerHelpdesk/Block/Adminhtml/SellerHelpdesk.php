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

class Emipro_SellerHelpdesk_Block_Adminhtml_SellerHelpdesk extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_blockGroup = 'emipro_sellerhelpdesk';
        $this->_controller = 'adminhtml_sellerHelpdesk';
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->_headerText = Mage::helper('emipro_sellerhelpdesk')->__('Seller Support Tickets');
        } else {
            $this->_headerText = Mage::helper('emipro_sellerhelpdesk')->__('My Support Tickets');
        }
        parent::__construct();
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        $this->_updateButton('add', 'label', Mage::helper('emipro_sellerhelpdesk')->__('Create Support Ticket'));
        if (empty($sellerid)) {
            $this->_removeButton('add');
        }
    }

}
