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

class Emipro_Marketplace_Block_Adminhtml_Sellerlist_Edit_Tab_Seller extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $countries = Mage::getModel('directory/country')->getCollection();
        foreach ($countries as $country) {
            $countryname[$country->getId()] = $country->getName();
        }
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_sellerinfo');
        $form->setFieldNameSuffix('sellerinfo');
        $this->setForm($form);
        $fieldset = $form->addFieldset('sellerinfo_form', array('legend' => Mage::helper('marketplace')->__('Seller information')));

        $fieldset->addField('firstname', 'text', array(
            'label' => Mage::helper('marketplace')->__('First Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'firstname',
        ));
        $fieldset->addField('lastname', 'text', array(
            'label' => Mage::helper('marketplace')->__('Last Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'lastname',
        ));
        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('marketplace')->__('Email'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'email',
            'disabled' => 'disabled',
        ));

        $fieldset->addField('street1', 'text', array(
            'label' => Mage::helper('marketplace')->__('Street Address'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'street1',
        ));
        $fieldset->addField('street2', 'text', array(
            'name' => 'street2',
        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('marketplace')->__('City'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'city',
        ));
        $fieldset->addField('country_id', 'select', array(
            'label' => Mage::helper('marketplace')->__('Country'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'country_id',
            'values' => $countryname,
        ));
        $fieldset->addField('region', 'text', array(
            'label' => Mage::helper('marketplace')->__('State/Province'),
            'name' => 'region',
        ));
        $fieldset->addField('postcode', 'text', array(
            'label' => Mage::helper('marketplace')->__('Zip/Postal Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'postcode',
        ));
        $fieldset->addField('telephone', 'text', array(
            'label' => Mage::helper('marketplace')->__('Telephone'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'telephone',
        ));
		$fieldset->addField('mobile', 'text', array(
            'label' => Mage::helper('marketplace')->__('Mobile'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'mobile',
        ));
        $sessiondata = Mage::getSingleton('adminhtml/session')->getData('form_data');
        //Mage::getSingleton('adminhtml/session')->unsFormData();    
        if (isset($sessiondata['sellerinfo'])) {      
            $form->setValues($sessiondata['sellerinfo']);  
        }else
        if (Mage::registry('store_info')) {
            $storeinfo = Mage::registry('store_info');
            if ($storeinfo->getRegion() == '' && $storeinfo->getRegionId()) {
                $regionModel = Mage::getModel('directory/region')->load($storeinfo->getRegionId());
                $region = $regionModel->getName();
                $storeinfo['region'] = $region;
            }
            $form->setValues($storeinfo);
        }
        return parent::_prepareForm();
    }

}
