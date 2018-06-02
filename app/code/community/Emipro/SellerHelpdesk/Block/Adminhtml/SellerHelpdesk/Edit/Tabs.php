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

class Emipro_SellerHelpdesk_Block_Adminhtml_SellerHelpdesk_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('helpdesk_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('emipro_sellerhelpdesk')->__('Seller Ticket'));
    }

    protected function _beforeToHtml() {
        $this->addTab('ticket_information', array(
            'label' => 'Ticket Information',
            'title' => 'Ticket Information',
            'content' => $this->getLayout()
                    ->createBlock('emipro_sellerhelpdesk/adminhtml_sellerHelpdesk_edit_tab_main')
                    ->toHtml()
        ));


        return parent::_beforeToHtml();
    }

}
