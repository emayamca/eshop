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

class Emipro_SellerHelpdesk_Block_Adminhtml_Newticket_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {

        $seller_id = Mage::helper("marketplace")->getSellerIdfromLoginUser();
        $UserName = Mage::getSingleton('admin/session')->getUser()->getUsername();

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/newticket'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('create_form', array('legend' => Mage::helper('emipro_sellerhelpdesk')->__('Create New Ticket')));

        $fieldset->addField('subject', 'text', array(
            'label' => Mage::helper('emipro_sellerhelpdesk')->__('Subject'),
            'name' => "subject",
            'required' => true,
        ));

        $fieldset->addField('message', 'textarea', array(
            'label' => Mage::helper('emipro_sellerhelpdesk')->__('Message'),
            'name' => "message",
            'required' => true,
        ));

        $fieldset->addField('file', 'file', array(
            'label' => Mage::helper('emipro_sellerhelpdesk')->__('Attachment'),
            'name' => "file",
        ));

        $fieldset->addField('seller_id', 'hidden', array(
            'name' => 'seller_id',
            'value' => $seller_id,
        ));
        $fieldset->addField('name', 'hidden', array(
            'name' => 'name',
            'value' => $UserName,
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();
    }

}
