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

class Emipro_Marketplace_Block_Adminhtml_Sellerbankdetails_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $countries = Mage::getModel('directory/country')->getCollection();
        foreach ($countries as $country) {
            $countryname[$country->getId()] = $country->getName();
        }
        $store = Mage::registry('store_info');
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setHtmlIdPrefix("_storeinfo");
        $form->setFieldNameSuffix('storeinfo');

        $fieldset = $form->addFieldset('storeinfo_form', array('legend' => Mage::helper('marketplace')->__('My Bank Details')));
        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'entity_id',
            'value' => 'entity_id',
        ));
        

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
             'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('bank_city', 'text', array(
            'label' => Mage::helper('marketplace')->__('Bank City'),
            'name' => 'bank_city',
            'class' => 'required-entry',
            'required' => true,
        ));
        
        $sessiondata = Mage::getSingleton('adminhtml/session')->getData('form_data');
        Mage::getSingleton('adminhtml/session')->unsFormData();    
        if (isset($sessiondata['storeinfo'])) {      
            $form->setValues($sessiondata['storeinfo']);        
            if(isset($store['banner']) && !empty($store['banner'])){
                $form->addValues(array('banner'=>$store['banner']));    
            }
            if(isset($store['company_logo']) && !empty($store['company_logo'])){
                $form->addValues(array('company_logo'=>$store['company_logo']));    
            }
            if(isset($store['store_url']) && !empty($store['store_url'])){
                $form->addValues(array('store_url'=>$store['store_url']));    
            }    
        } else if (Mage::registry('store_info')) { 
            if ($store->getRegion() == '' && $store->getRegionId()) { 
                $regionModel = Mage::getModel('directory/region')->load($store->getRegionId());
                $region = $regionModel->getName(); 
                $store['region'] = $region;
            }
            $form->setValues($store); 
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();
    }

}
