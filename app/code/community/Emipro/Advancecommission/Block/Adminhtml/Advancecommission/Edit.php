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

class Emipro_Advancecommission_Block_Adminhtml_Advancecommission_Edit extends
Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = 'advancecomm_id';
        $this->_controller = 'adminhtml_advancecommission';
        $this->_blockGroup = 'emipro_advancecommission';
    }

    public function getHeaderText() {
        $rule = Mage::registry('advancecommission');
        if ($rule->getAdvancecommId()) {

            return Mage::helper('emipro_advancecommission')->__("Edit Rule ");
        } else {
            return Mage::helper('emipro_advancecommission')->__('New Rule');
        }
    }

}
