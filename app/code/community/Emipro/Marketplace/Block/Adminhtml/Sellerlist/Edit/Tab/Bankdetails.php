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

class Emipro_Marketplace_Block_Adminhtml_Sellerlist_Edit_Tab_Bankdetails extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_bankdetails');
        $form->setFieldNameSuffix('bankdetails');
        $this->setForm($form);
        $fieldset = $form->addFieldset('bankdetails_form', array('legend' => Mage::helper('marketplace')->__('Bank Details')));

        $fieldset->addField('bank_account_name', 'text', array(
            'label' => Mage::helper('marketplace')->__('Bank Account Name'),
            'name' => 'bank_account_name',
            'class' => 'required-entry',
            'required' => true,
        ));
        
        $fieldset->addField('bank_account_number', 'text', array(
            'label' => Mage::helper('marketplace')->__('Bank Account Number'),
            'name' => 'bank_account_number',
            'class' => 'required-entry',
            'required' => true,
        ));
           
        $fieldset->addField('bank_name', 'text', array(
            'label' => Mage::helper('marketplace')->__('Bank Name'),
            'name' => 'bank_name',
            'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('bank_branch_name', 'text', array(
            'label' => Mage::helper('marketplace')->__('Bank Branch Name'),
            'name' => 'bank_branch_name',
            'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('bank_ifsc_code', 'text', array(
            'label' => Mage::helper('marketplace')->__('Bank IFSC/NEFT Code'),
            'name' => 'bank_ifsc_code',
        ));

        $fieldset->addField('bank_city', 'text', array(
            'label' => Mage::helper('marketplace')->__('Bank City'),
            'name' => 'bank_city',
            'class' => 'required-entry',
            'required' => true,
        ));

        $sessiondata = Mage::getSingleton('adminhtml/session')->getData('form_data');    
        if (isset($sessiondata['bankdetails'])) {      
            $form->setValues($sessiondata['bankdetails']);  
        }else
        if (Mage::registry('store_info')) {
            $storeinfo = Mage::registry('store_info');
            $form->setValues($storeinfo);
        }
        return parent::_prepareForm();
    }

}
