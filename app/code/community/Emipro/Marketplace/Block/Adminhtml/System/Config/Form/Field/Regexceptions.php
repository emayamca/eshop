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

class Emipro_Marketplace_Block_Adminhtml_System_Config_Form_Field_Regexceptions extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    protected $_categoryRenderer;
    protected $_commissiontypeRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getCategory() {
        if (!$this->_categoryRenderer) {
            $this->_categoryRenderer = $this->getLayout()->createBlock(
                    'marketplace/adminhtml_system_config_form_field_category', '', array('is_render_to_js_template' => true)
            );
            $this->_categoryRenderer->setClass('category');

            $this->_categoryRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_categoryRenderer;
    }

    protected function _getCommissionTypeRenderer() {
        if (!$this->_chargeTypeRenderer) {
            $this->_chargeTypeRenderer = $this->getLayout()->createBlock(
                    'marketplace/adminhtml_system_config_form_field_commissiontype', '', array('is_render_to_js_template' => true)
            );
            $this->_chargeTypeRenderer->setClass('commissiontype');

            $this->_chargeTypeRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_chargeTypeRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender() {
        $this->addColumn('category', array(
            'label' => Mage::helper('marketplace')->__('Category'),
            'style' => 'width:120px',
            'renderer' => $this->_getCategory(),
        ));

        $this->addColumn('commissiontype', array(
            'label' => Mage::helper('marketplace')->__('Commission Type'),
            'style' => 'width:100px',
            'renderer' => $this->_getCommissionTypeRenderer(),
            'required' => true,
        ));

        $this->addColumn('commission', array(
            'label' => Mage::helper('marketplace')->__('Commission'),
            'style' => 'width:100px',
            'required' => true,
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('marketplace')->__('Add New');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row) {
        $row->setData(
                'option_extra_attr_' . $this->_getCategory()->calcOptionHash($row->getData('category')), 'selected="selected"'
        );
        $row->setData(
                'option_extra_attr_' . $this->_getCommissionTypeRenderer()->calcOptionHash($row->getData('commissiontype')), 'selected="selected"'
        );
    }

}
