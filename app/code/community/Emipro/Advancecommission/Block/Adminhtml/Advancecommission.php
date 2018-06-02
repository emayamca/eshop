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

class Emipro_Advancecommission_Block_Adminhtml_Advancecommission extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_blockGroup = 'emipro_advancecommission';
        $this->_controller = 'adminhtml_advancecommission';
        $this->_headerText = Mage::helper('emipro_advancecommission')->__('Seller Advanced Commission');
        parent::__construct();
    }

}
