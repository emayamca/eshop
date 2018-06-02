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

class Emipro_BulkProductImport_Block_Adminhtml_Exportproduct_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $form = new Varien_Data_Form(array
            (
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $button = Mage::getStoreConfig('marketplace_setting/sellerpanelcolor/maincolor');
        $textcolor = Mage::getStoreConfig('marketplace_setting/sellerpanelcolor/textcolor');
        if($button){$button=$button;}else{$button='#0083d5';}
        if($textcolor){$textcolor=$textcolor;}else{$textcolor='#FFFFFF';}
        $fieldset = $form->addFieldset('imageupload_form', array('legend' => Mage::helper('marketplace')->__('Export Products')));

        $entityType = Mage::getModel('catalog/product')->getResource()->getTypeId();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityType);
        $allSet = array();
        $allSet[''] = "Select Attribute Set";
        foreach ($collection as $coll) {
            $attributeSet['label'] = $coll->getAttributeSetName();
            $attributeSet['value'] = $coll->getAttributeSetId();
            $allSet[] = $attributeSet;
        }

        $fieldset->addField('attribute_set', 'select', array(
            'label' => Mage::helper('marketplace')->__('Attribute Set'),
            'index' => 'attribute_set',
            'name' => 'attribute_set',
            'type' => 'options',
            'onchange' => "getAttr(this.value)",
            'values' => $allSet,
            'required' => true
        ));

        $fieldset->addField('export', 'button', array(
            'value' => Mage::helper('marketplace')->__('Export Now'),
            'class' => 'scalable save',
            'style' => 'background-color: '.$button.';border-color: '.$button.';color: '.$textcolor.';height: 34px;',
            'onclick' => 'editForm.submit()'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();
    }

}
?>

