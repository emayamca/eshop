<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Stocknotification 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Stocknotification_Block_Adminhtml_Stocknotification_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $store = Mage::registry('store_info');
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setHtmlIdPrefix('_stocknotification');
        $form->setFieldNameSuffix('stocknotification');
        $fieldset = $form->addFieldset('stocknotification_form', array('legend' => Mage::helper('stocknotification')->__('Low Stock Notification')));

        $fieldset->addField('low_stock_enable', 'select', array(
          'label'     => Mage::helper('stocknotification')->__('Enable Low Notification'),
          'name'      => 'low_stock_enable',
          //'onclick' => "",
          //'onchange' => "",
          //'value'  => '0',
          'values' => array('0' => 'No','1' => 'Yes'),
        ));
        // print_r(Mage::registry('store_info'));
        // exit;
        
        $fieldset->addField('low_stock_quantity', 'text', array(
            'label' => Mage::helper('stocknotification')->__('Low Stock Quantity'),
            //'value' => '5',
            'name'  => 'low_stock_quantity',
        ));
        $sessiondata = Mage::getSingleton('adminhtml/session')->getData('form_data'); 
        if (isset($sessiondata['stocknotification'])) {      
            $form->setValues($sessiondata['stocknotification']);  
        }else
        if (Mage::registry('store_info')) {
            $storeinfo = Mage::registry('store_info');
            //print_r($storeinfo);
            $form->setValues($storeinfo);
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm(); 
    }
}
