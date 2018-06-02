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

class Emipro_Marketplace_Block_Adminhtml_Sellerlist_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = "entity_id";
        $this->_blockGroup = "marketplace";
        $this->_controller = "adminhtml_sellerlist";
//      $this->_updateButton("save", "label", Mage::helper("customoptions")->__("Save Options"));
        $this->removeButton("delete");
        //	$this->removeButton("save");	
    }

    public function getHeaderText() {
        return Mage::helper("marketplace")->__("Edit Seller");
    }

}
