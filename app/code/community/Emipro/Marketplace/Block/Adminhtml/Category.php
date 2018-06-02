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

class Emipro_Marketplace_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = "adminhtml_category";
        $this->_blockGroup = "marketplace";
        $this->_headerText = Mage::helper("marketplace")->__("Category -  Attribute Set Mapping");
        parent::__construct();
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            $this->_removeButton('add');
        }
    }

}
