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

class Emipro_BulkProductImport_Block_Adminhtml_Exportproduct_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    protected function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function __construct() {
        parent::__construct();
        $this->_objectId = "entity_id";
        $this->_blockGroup = "emipro_bulkproductimport";
        $this->_controller = "adminhtml_exportproduct";

        $this->removeButton("delete");
        $this->removeButton("back");
        $this->removeButton("save");
        $this->removeButton("reset");
    }

    public function getHeaderText() {
        return Mage::helper("marketplace")->__("Export Products");
    }

}
