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

class Emipro_Marketplace_Block_Adminhtml_Withdraw_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = "id";
        $this->_blockGroup = "marketplace";
        $this->_controller = "adminhtml_withdraw";
        $this->_removeButton("delete");
    }

    public function getHeaderText() {
        if ($this->getRequest()->getParam('id')) {
            if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
                return Mage::helper("marketplace")->__("Edit Withdrawal");
            } else {
                return Mage::helper("marketplace")->__("Edit Seller Withdrawal");
            }
        } else {
            if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
                return Mage::helper("marketplace")->__("Request New Withdrawal");
            } else {
                return Mage::helper("marketplace")->__("Add Seller Withdrawal");
            }
        }
    }

}
