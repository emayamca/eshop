<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_FlexiShipping
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
  class Emipro_FlexiShipping_Block_Adminhtml_FlexiShipping_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
  {
     public function __construct()
     {
          parent::__construct();  
        $this->setId('shipping_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('emipro_flexishipping')->__('Flexi Shipping Rule'));
      }
      
    protected function _beforeToHtml()
      {
		   $this->addTab('rule_information', array(
                   'label' => 'Rule Information',
                   'title' => 'Rule Information',
                   'content' => $this->getLayout()
    ->createBlock('emipro_flexishipping/adminhtml_flexiShipping_edit_tab_main')
     ->toHtml()
         ));
		  
         $this->addTab('conditions', array(
                   'label' => 'Conditions',
                   'title' => 'Conditions',
                   'content' => $this->getLayout()
     ->createBlock('emipro_flexishipping/adminhtml_flexiShipping_edit_tab_conditions')
     ->toHtml()
      ));
         
         $this->addTab('actions', array(
                   'label' => 'Shipping Fees',
                   'title' => 'Actions',
                   'content' => $this->getLayout()
     ->createBlock('emipro_flexishipping/adminhtml_flexiShipping_edit_tab_actions')
     ->toHtml()
      ));
       return parent::_beforeToHtml();
    }
}
