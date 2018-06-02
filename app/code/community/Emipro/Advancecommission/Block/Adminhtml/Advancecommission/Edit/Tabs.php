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

class Emipro_Advancecommission_Block_Adminhtml_Advancecommission_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('advancecommission_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('emipro_advancecommission')->__('Advance commission'));
    }

    protected function _beforeToHtml() {
        $this->addTab('advancecommission', array(
            'label' => 'Advance Commission',
            'title' => 'Advance Commission',
            'content' => $this->getLayout()
                    ->createBlock('emipro_advancecommission/adminhtml_advancecommission_edit_tab_rules')
                    ->toHtml()
        ));


        return parent::_beforeToHtml();
    }

}
