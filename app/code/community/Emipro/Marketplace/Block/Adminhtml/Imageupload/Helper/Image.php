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

class Emipro_Marketplace_Block_Adminhtml_Imageupload_Helper_Image extends Varien_Data_Form_Element_Image {

    public function getHtmlAttributes() {
        return array_merge(parent::getHtmlAttributes(), array('multiple'));
    }

}
