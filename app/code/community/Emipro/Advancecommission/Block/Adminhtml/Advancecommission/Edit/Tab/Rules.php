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

class Emipro_Advancecommission_Block_Adminhtml_Advancecommission_Edit_Tab_Rules extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_rules');
        $form->setFieldNameSuffix('rules');
        $this->setForm($form);
        $fieldset = $form->addFieldset('rules_form', array('legend' => Mage::helper('emipro_advancecommission')->__('Seller Commission')));
        $model = Mage::registry('advancecommission');

        if ($model->getAdvancecommId()) {
            $fieldset->addField('advancecomm_id', 'hidden', array(
                'name' => 'advancecomm_id',
            ));

            $fieldset->addField('seller_id', 'select', array(
                'label' => Mage::helper('emipro_advancecommission')->__('Seller'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'seller_id',
                'disabled' => true,
                'values' => Mage::helper('marketplace')->getsellerDropdown(),
            ));
            $fieldset->addField('attributeset_id', 'select', array(
                'label' => Mage::helper('emipro_advancecommission')->__('Category'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'attributeset_id',
                'disabled' => true,
                'values' => Mage::helper('marketplace')->toCategoriesArray(),
            ));
        } else {
            $fieldset->addField('seller_id', 'select', array(
                'label' => Mage::helper('emipro_advancecommission')->__('Seller'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'seller_id',
                'values' => Mage::helper('marketplace')->getsellerDropdown(),
            ));
            $fieldset->addField('attributeset_id', 'select', array(
                'label' => Mage::helper('emipro_advancecommission')->__('Category'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'attributeset_id',
                'values' => Mage::helper('marketplace')->toCategoriesArray(),
            ));
        }

        $fieldset->addField('commission_type', 'select', array(
            'label' => Mage::helper('emipro_advancecommission')->__('Commission Type'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'commission_type',
            'values' => array('0' => 'Percent', '1' => 'Fixed'),
        ));
        $fieldset->addField('commission', 'text', array(
            'label' => Mage::helper('emipro_advancecommission')->__('Commission'),
            'class' => 'required-entry validate-zero-or-greater',
            'required' => true,
            'name' => 'commission',
        ));
        if (Mage::registry('advancecommission')) {
            $form->setValues(Mage::registry('advancecommission'));
        }
        return parent::_prepareForm();
    }

}
