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

class Emipro_Marketplace_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function getAttributeset() {
        $attribute_set = array(array('value' => '', 'label' => 'Plese select'));
        $attribute_set_id = Mage::getModel('catalog/product')->getResource()->getTypeId();
        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection');
        $attributeSetCollection->addFieldToFilter('entity_type_id', $attribute_set_id);
        $attributeSetCollection->load();

        foreach ($attributeSetCollection as $attributeset) {
            array_push($attribute_set, array('value' => $attributeset->getAttributeSetId(), 'label' => $attributeset->getAttributeSetName()));
        }
        return $attribute_set;
    }

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

        $fieldset = $form->addFieldSet('sellercategory_form', array('legend' => Mage::helper('marketplace')->__('Category - Attributeset Mapping')));

        $fieldset->addField('attributeset_id', 'select', array(
            'label' => Mage::helper('marketplace')->__('Attributeset'),
            'class' => 'required-entry',
            'values' => $this->getAttributeset(),
            'required' => true,
            'name' => 'attributeset_id',
        ));
        $fieldset->addField('category_id', 'select', array(
            'label' => Mage::helper('marketplace')->__('Category'),
            'name' => 'category_id',
            'values' => Mage::helper('marketplace')->toCategoriesArray(),
            'required' => true,
            'class' => 'required-entry',
        ));
        if (Mage::registry('seller_category')) {
            $form->setValues(Mage::registry('seller_category'));
        }

        return parent::_prepareForm();
    }

}
