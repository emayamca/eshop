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

class Emipro_Marketplace_Block_Adminhtml_Rules_Edit_Tab_Rules extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_rules');
        $form->setFieldNameSuffix('rules');
        $this->setForm($form);
        $fieldset = $form->addFieldset('rules_form', array('legend' => Mage::helper('marketplace')->__('Seller Default Commission')));

        $fieldset->addField('seller_id', 'select', array(
            'label' => Mage::helper('marketplace')->__('Seller'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'seller_id',
            'values' => Mage::helper('marketplace')->getsellerDropdown(),
        ));

        $fieldset->addField('commission_type', 'select', array(
            'label' => Mage::helper('marketplace')->__('Commission Type'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'commission_type',
            'values' => array('0' => 'Percent', '1' => 'Fixed'),
        ));
        $fieldset->addField('commission', 'text', array(
            'label' => Mage::helper('marketplace')->__('Commission'),
            'class' => 'required-entry validate-zero-or-greater',
            'required' => true,
            'name' => 'commission',
        ));
        if (Mage::registry('seller_rule_data')) {
            $form->setValues(Mage::registry('seller_rule_data'));
        }
        return parent::_prepareForm();
    }

}
