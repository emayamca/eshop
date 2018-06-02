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

class Emipro_Marketplace_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = "id";
        $this->_blockGroup = "marketplace";
        $this->_controller = "adminhtml_category";
        $this->_updateButton("save", "label", Mage::helper("marketplace")->__("Save"));
        // $this->removeButton("delete");
        //  $this->removeButton("save");	
    }

    public function getHeaderText() {
        if ($this->getRequest()->getParam('id')) {
            return Mage::helper("marketplace")->__("Edit Category - Attribute Set Mapping");
        } else {
            return Mage::helper("marketplace")->__("Add Category - Attribute Set Mapping");
        }
    }

}
