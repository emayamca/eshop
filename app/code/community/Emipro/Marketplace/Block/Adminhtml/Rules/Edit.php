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

class Emipro_Marketplace_Block_Adminhtml_Rules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = "id";
        $this->_blockGroup = "marketplace";
        $this->_controller = "adminhtml_rules";

        $this->_removeButton('delete');
    }

    public function getHeaderText() {
        if ($this->getRequest()->getParam('id')) {
            return Mage::helper("marketplace")->__("Edit Seller Default Commission");
        } else {
            return Mage::helper("marketplace")->__("Add Seller Default Commission");
        }
    }

}
