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

class Emipro_Marketplace_Block_Adminhtml_Dashboard_Orders_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $order = Mage::getModel('sales/order')->loadByIncrementId($value);
        $billing = $order->getBillingAddress();
        if (isset($billing) && !empty($billing)) {
            $str = $billing->getFirstname() . '  ' . $billing->getLastname();
            return $str;
        }
        return $value;
    }

}
