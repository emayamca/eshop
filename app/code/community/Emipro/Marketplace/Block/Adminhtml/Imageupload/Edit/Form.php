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

class Emipro_Marketplace_Block_Adminhtml_Imageupload_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setHtmlIdPrefix("_imageupload");
        $form->setFieldNameSuffix('imageupload');

        $fieldset = $form->addFieldset('imageupload_form', array('legend' => Mage::helper('marketplace')->__('Multiple Image Upload')));
        $fieldset->addType('image', 'Emipro_Marketplace_Block_Adminhtml_Imageupload_Helper_Image');

        $fieldset->addField('entity_id', 'image', array(
            'name' => 'image[]',
            'multiple' => 'multiple',
            'label' => Mage::helper('marketplace')->__('Upload Image(s)'),
            'title' => Mage::helper('marketplace')->__('Upload Image'),
            'required' => true,
            'mulitple' => true,
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();
    }

}
