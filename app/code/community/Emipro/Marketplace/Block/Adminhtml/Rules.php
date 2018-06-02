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

class Emipro_Marketplace_Block_Adminhtml_Rules extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = "adminhtml_rules";
        $this->_blockGroup = "marketplace";
        $this->_headerText = Mage::helper("marketplace")->__("Seller Default Commission");
        parent::__construct();
        $this->removeButton("add");
    }

}
