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

class Emipro_Marketplace_Block_Adminhtml_Sellerprofile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = "entity_id";
        $this->_blockGroup = "marketplace";
        $this->_controller = "adminhtml_sellerprofile";
        $this->_updateButton("save", "label", Mage::helper("marketplace")->__("Save"));
        $this->removeButton("delete");
        $this->removeButton("back");
    }

    public function getHeaderText() {
        return Mage::helper("marketplace")->__("My Profile Page");
    }

}
