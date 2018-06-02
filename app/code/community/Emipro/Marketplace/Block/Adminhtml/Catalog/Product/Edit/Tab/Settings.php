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

class Emipro_Marketplace_Block_Adminhtml_Catalog_Product_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareLayout() {
        $this->setChild('continue_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Continue'),
                            'onclick' => "setSettings('" . $this->getContinueUrl() . "','attribute_set_id','product_type')",
                            'class' => 'save'
                        ))
        );
        $this->getLayout()->getBlock('head')->addJs('emipro/productedit.js');
        return parent::_prepareLayout();
    }

    protected function _prepareForm() {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend' => Mage::helper('catalog')->__('Create Product Settings')));

        $entityType = Mage::registry('product')->getResource()->getEntityType();
        $attributeField = $fieldset->addField('attribute_set_id', 'select', array(
            'label' => Mage::helper('catalog')->__('Attribute Set'),
            'title' => Mage::helper('catalog')->__('Attribute Set'),
            'name' => 'set',
            'value' => $entityType->getDefaultAttributeSetId(),
            'values' => Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter($entityType->getId())
                    ->load()
                    ->toOptionArray()
        ));

        $fieldset->addField('product_type', 'select', array(
            'label' => Mage::helper('catalog')->__('Product Type'),
            'title' => Mage::helper('catalog')->__('Product Type'),
            'name' => 'type',
            'value' => '',
            'values' => Mage::getModel('catalog/product_type')->getOptionArray()
        ));

        $continuebtn = $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));
        if ($sellerid) {
            $attributeField->setAfterElementHtml('<script>$("attribute_set_id").parentNode.parentNode.style.display="none";</script>');
            $continuebtn->setAfterElementHtml('<script>$("continue_button").style.display="block";</script>');
        }
        $this->setForm($form);
    }

    public function getContinueUrl() {
        return $this->getUrl('*/*/new', array(
                    '_current' => true,
                    'set' => '{{attribute_set}}',
                    'type' => '{{type}}'
        ));
    }

}
