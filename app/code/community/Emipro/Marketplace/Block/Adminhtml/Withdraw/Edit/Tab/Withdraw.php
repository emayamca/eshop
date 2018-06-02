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

class Emipro_Marketplace_Block_Adminhtml_Withdraw_Edit_Tab_Withdraw extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_withdraw');
        $form->setFieldNameSuffix('withdraw');
        $this->setForm($form);
        $fieldset = $form->addFieldset('withdraw_form', array('legend' => Mage::helper('marketplace')->__('Seller withdrawal')));

        if ($sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $fieldset->addField('Balance', 'note', array(
                'label' => Mage::helper('marketplace')->__('Balance'),
                'text' => Mage::helper('marketplace')->getSellerBalance($sellerid),
            ));
        }
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $fieldset->addField('seller_id', 'select', array(
                'label' => Mage::helper('marketplace')->__('Seller'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'seller_id',
                'values' => Mage::helper('marketplace')->getsellerDropdown(),
            ));
        }
        $fieldset->addField('amount', 'text', array(
            'label' => Mage::helper('marketplace')->__('Amount'),
            'name' => 'amount',
            'class' => 'required-entry validate-number',
            'after_element_html' => '<span style="font-size:10px;">' . Mage::helper('marketplace')->__('Enter amount lower than your balance.') . '</span>',
        ));
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {

            $fieldset->addField('request_status', 'select', array(
                'label' => Mage::helper('marketplace')->__('Request Status'),
                'name' => 'request_status',
                'class' => 'required-entry',
                'required' => true,
                'values' => array('pending' => 'Pending', 'approved' => 'Approved'),
            ));
        }
        $fieldset->addField('summary', 'textarea', array(
            'label' => Mage::helper('marketplace')->__('Summary'),
            'name' => 'summary',
        ));
        $sessiondata = Mage::getSingleton('adminhtml/session')->getData('seller_withdraw_data');
        Mage::getSingleton('adminhtml/session')->unsSellerWithdrawData();
        if (isset($sessiondata['withdraw'])) {
            $form->setValues($sessiondata['withdraw']);
        } else if (Mage::registry('seller_withdraw_data')) {
            $form->setValues(Mage::registry('seller_withdraw_data'));
        }
        return parent::_prepareForm();
    }

}
