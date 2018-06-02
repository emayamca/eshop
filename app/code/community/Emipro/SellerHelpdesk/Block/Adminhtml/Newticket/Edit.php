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

class Emipro_SellerHelpdesk_Block_Adminhtml_Newticket_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {

        $this->_objectId = "id";
        $this->_blockGroup = "emipro_sellerhelpdesk";
        $this->_controller = "adminhtml_newticket";
        $this->removeButton("back");
        parent::__construct();
        $this->_updateButton("save", "label", Mage::helper("emipro_sellerhelpdesk")->__("Submit"));


        $this->removeButton("delete");
        $this->removeButton("reset");
    }

    public function getHeaderText() {
        return Mage::helper("emipro_sellerhelpdesk")->__("Create New Ticket");
    }

}
