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

class Emipro_Marketplace_Block_Adminhtml_Commission_Edit_Tab_commission extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('sellercommission');
        $form->setFieldNameSuffix('sellercommission');
        $this->setForm($form);
        $fieldset = $form->addFieldset('sellercommission_form', array('legend' => Mage::helper('marketplace')->__('Order Transaction')));

        $fieldset->addField('order_increment_id', 'text', array(
            'label' => Mage::helper('marketplace')->__('Order No.'),
            'name' => 'order_increment_id',
            'class' => 'required-entry',
            'required' => true,
        ));
        $fieldset->addField('product_id', 'select', array(
            'label' => Mage::helper('marketplace')->__('Product SKU'),
            'class' => 'required-entry',
            'name' => 'product_id',
            'required' => true,
            'values' => $this->getProductids(),
        ));
        $fieldset->addField('shipping_charge', 'text', array(
            'label' => Mage::helper('marketplace')->__('Shipping Charge'),
            'name' => 'shipping_charge',
            'class' => 'validate-number validate-zero-or-greater',
        ));

        $fieldset->addField('qty', 'text', array(
            'label' => Mage::helper('marketplace')->__('Qty'),
            'class' => 'validate-number validate-zero-or-greater',
            'name' => 'qty',
        ));

        $fieldset->addField('commission_amt', 'text', array(
            'label' => Mage::helper('marketplace')->__('Commission Amount'),
            'class' => 'validate-number validate-zero-or-greater',
            'name' => 'commission_amt',
        ));
        $fieldset->addField('seller_credit', 'text', array(
            'label' => Mage::helper('marketplace')->__('Seller Credit'),
            'class' => 'validate-number validate-zero-or-greater',
            'name' => 'seller_credit',
        ));
        $fieldset->addField('seller_debit', 'text', array(
            'label' => Mage::helper('marketplace')->__('Seller Debit'),
            'class' => 'validate-number validate-zero-or-greater',
            'name' => 'seller_debit',
        ));
        $fieldset->addField('summary', 'textarea', array(
            'label' => Mage::helper('marketplace')->__('Summary Text'),
            'name' => 'summary',
        ));
        $sessiondata = Mage::getSingleton('adminhtml/session')->getData('current_sellercommission');
        Mage::getSingleton('adminhtml/session')->unsCurrentSellercommission();
        if (isset($sessiondata['sellercommission'])) {
            $form->setValues($sessiondata['sellercommission']);
        } else if (Mage::registry('current_sellercommission')) {
            $form->setValues(Mage::registry('current_sellercommission'));
        }
        return parent::_prepareForm();
    }

    public function getProductids() {
        $array = array();
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', array('neq' => ''));
        $array[''] = 'Select SKU';
        foreach ($products as $item) {
            $array[$item->getId()] = $item->getSku();
        }
        return $array;
    }

}
