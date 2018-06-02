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

class Emipro_Marketplace_Block_Adminhtml_Questionanswer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
                )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldSet('questionanswer_form', array('legend' => Mage::helper('marketplace')->__('Customer Question')));
        $quesitondata = Mage::registry('questionanswer');
        $fieldset->addField('product_sku', 'note', array(
            'label' => Mage::helper('marketplace')->__('Product SKU'),
            'text' => isset($quesitondata['product_sku']) ? $quesitondata['product_sku'] : '',
        ));
		$fieldset->addField('customer_email', 'note', array(
                'label' => Mage::helper('marketplace')->__('Customer Email'),
                'text' => isset($quesitondata['customer_email']) ? $quesitondata['customer_email'] : '',
            ));
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if (!$sellerid) {
            $fieldset->addField('seller_id', 'note', array(
                'label' => Mage::helper('marketplace')->__('Store Name'),
                'text' => isset($quesitondata['seller_id']) ? Mage::helper('marketplace')->getsellernamefromid($quesitondata['seller_id']) : '',
            ));
        }
        $fieldset->addField('subject', 'note', array(
            'label' => Mage::helper('marketplace')->__('Subject'),
            'text' => isset($quesitondata['subject']) ? $quesitondata['subject'] : '',
        ));
        $fieldset->addField('question', 'note', array(
            'label' => Mage::helper('marketplace')->__('Question'),
            'text' => isset($quesitondata['question']) ? $quesitondata['question'] : '',
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('marketplace')->__('Status'),
            'name' => 'status',
            'required' => true,
            'style' => 'width:100px',
            'class' => 'required-entry',
            'values' => array('pending' => 'Pending', 'answered' => 'Answered'),
        ));
        $fieldset->addField('answer', 'textarea', array(
            'label' => Mage::helper('marketplace')->__('Answer'),
            'name' => 'answer',
            'required' => true,
            'class' => 'required-entry',
        ));
        if (Mage::registry('questionanswer')) {
            $form->setValues(Mage::registry('questionanswer'));
        }
        return parent::_prepareForm();
    }

}
