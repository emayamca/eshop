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

class Emipro_SellerHelpdesk_Block_Adminhtml_SellerHelpdesk_Edit extends
Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_sellerHelpdesk';
        $this->_blockGroup = 'emipro_sellerhelpdesk';
        parent::__construct();

        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_updateButton('delete', 'label', Mage::helper('emipro_sellerhelpdesk')->__('Delete '));
    }

    public function getHeaderText() {
        $rule = Mage::registry('sellerhelpdesk');
        if ($rule->getId()) {
            return Mage::helper('emipro_sellerhelpdesk')->__("Support for '%s'", $this->escapeHtml($rule->getSubject()));
        } else {
            return Mage::helper('emipro_sellerhelpdesk')->__('Seller Help Desk');
        }
    }

}
