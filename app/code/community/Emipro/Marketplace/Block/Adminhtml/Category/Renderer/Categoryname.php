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

class Emipro_Marketplace_Block_Adminhtml_Category_Renderer_Categoryname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected $_array;

    public function __construct() {
        $array = Mage::helper('marketplace')->toCategoriesArray();
        $this->_array = $array;
    }

    public function render(Varien_Object $row) {
        if (isset($this->_array[$row->getData('category_id')])) {
            $value = $this->_array[$row->getData('category_id')];
        }
        return $value;
    }

}
