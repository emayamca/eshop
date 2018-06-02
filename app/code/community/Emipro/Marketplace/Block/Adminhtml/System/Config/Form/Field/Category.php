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

class Emipro_Marketplace_Block_Adminhtml_System_Config_Form_Field_Category extends Mage_Core_Block_Html_Select {

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function _toHtml() {
        if (!$this->getOptions()) {
            $category = Mage::helper('marketplace')->toCategoriesArray();
            foreach ($category as $key => $value) {
                $this->addOption($key, addslashes($value));
            }
        }
        return parent::_toHtml();
    }

}
