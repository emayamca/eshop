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

class Emipro_Marketplace_Block_Adminhtml_Withdraw extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = "adminhtml_withdraw";
        $this->_blockGroup = "marketplace";
        $this->_headerText = Mage::helper("marketplace")->__("Seller Withdrawals");
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('marketplace')->__('Request new withdrawal'));
    }

}
