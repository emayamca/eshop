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

class Emipro_Marketplace_Block_Adminhtml_Dashboard_Paymentreport extends Mage_Adminhtml_Block_Dashboard_Bar {

    protected function _construct() {
        parent::_construct();
        $data['remainamount'] = $this->getremainAmount();
        $data['seller_review'] = $this->getSellerreview();

        $this->setData('data', $data)->setTemplate('marketplace/dashboard/paymentreport.phtml');
    }

    protected function getremainAmount() {
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            $seller_balance = Mage::helper('marketplace')->getSellerBalance($seller_id);
            if (!$seller_balance) {
                $seller_balance = 0;
            }
            $str = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
            $str.= number_format($seller_balance, 2);
            return $str;
        }
        return;
    }

    protected function getSellerreview() {
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            return round(Mage::helper('marketplace')->getSellerRating($seller_id));
        }
    }

}
