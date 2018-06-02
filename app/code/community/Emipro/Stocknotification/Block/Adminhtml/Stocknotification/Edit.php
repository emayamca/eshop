<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Stocknotification 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Stocknotification_Block_Adminhtml_Stocknotification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        
        parent::__construct();
        $this->_objectId = "entity_id";
        $this->_blockGroup = "stocknotification";
        $this->_controller = "adminhtml_stocknotification";
        $this->_updateButton("save", "label", Mage::helper("stocknotification")->__("Save"));
        $this->removeButton("delete");
        $this->removeButton("back");
    }

    public function getHeaderText() {
        return Mage::helper("stocknotification")->__("Low Stock Notification");
    }

}
