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

class Emipro_BulkProductImport_Block_Adminhtml_Bulkproductimport_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

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
        $fieldset = $form->addFieldset('imageupload_form', array('legend' => Mage::helper('marketplace')->__('Bulk Product Import')));

        $entityType = Mage::getModel('catalog/product')->getResource()->getTypeId();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityType);
        $allSet = array();
        $allSet[0] = "Select Attribute Set";
        foreach ($collection as $coll) {
            $attributeSet['label'] = $coll->getAttributeSetName();
            $attributeSet['value'] = $coll->getAttributeSetId();
            $allSet[] = $attributeSet;
        }







        $fieldset->addField('csv', 'file', array(
            'name' => 'csv',
            'label' => Mage::helper('marketplace')->__('Import file'),
            'title' => Mage::helper('marketplace')->__('Import file'),
            'required' => true,
            'mulitple' => false,
                //'after_element_html' => '<button title="Import Now" type="button" class="scalable save" onclick="editForm.submit();" style=""><span><span><span>Import Now</span></span></span></button>',
        ));

        $fieldset->addField('import', 'button', array(
            'value' => Mage::helper('marketplace')->__('Import Now'),
            'class' => 'scalable save',
            'style' => 'background-color: '.$button.';border-color: '.$button.';color: '.$textcolor.';height: 34px;',
            'onclick' => 'mysubmit()',
            'after_element_html' => '<div style="margin-top:22px;margin-left: -210px;"><strong style="
               padding-left: 5px;
               ">You can download sample product CSV and options list file from below section</strong></div>',
        ));

        $fieldset->addField('attribute_set', 'select', array(
            'label' => Mage::helper('marketplace')->__('Attribute Set'),
            'index' => 'attribute_set',
            'name' => 'attribute_set',
            'type' => 'options',
            'onchange' => "getAttr(this.value)",
            'values' => $allSet,
        ));
        $link = $fieldset->addField('register', 'link', array(
            'value' => Mage::helper('marketplace')->__('Download Sample Csv'),
            'style' => 'color:'.$button.';',
            'href' => Mage::helper('adminhtml')->getUrl('*/*/csv'),
        ));


        $fieldset->addField('label', 'label', array(
            'value' => Mage::helper('marketplace')->__('Fill up your product data in sample downloaded csv file.'),
        ));
        $fieldset->addField('label1', 'label', array(
            'value' => Mage::helper('marketplace')->__('For multi select attribute,  you can set option from only specific values.'),
            'after_element_html' => '<div style="margin-top:7px">Click on below link to download options list for multiple value field.</div>',
        ));
        /* $fieldset->addField('label2', 'label', array(
          'value'     => Mage::helper('marketplace')->__(''),
          )); */
        $link=$fieldset->addField('options', 'link', array(
        'value' => Mage::helper('marketplace')->__('Download Option List'),
        'style' => 'color:'.$button.';',
        'href' => Mage::helper('adminhtml')->getUrl('*/*/option')
        ));
        $fieldset->addField('label3', 'label', array(
            'value' => Mage::helper('marketplace')->__('Please upload all images from Manage Products -> Upload Multiple Product Images and verify that image is available there in list.'),
        ));
        $fieldset->addField('label4', 'label', array(
            'value' => Mage::helper('marketplace')->__('For category, please set here category Ids with comma seperated.'),
        ));


        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();
    }

}
?>

