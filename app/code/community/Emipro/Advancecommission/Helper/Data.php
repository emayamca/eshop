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

Class Emipro_Advancecommission_Helper_Data extends Mage_Core_Helper_Abstract {

    public function sellerCommissionAmount($sellerId, $categoryids, $price, $qty,$shippingcharge=0) 
    {
        $commission = '';
        $commAmount = '';
        $commType='';
        if ($sellerId != "" && !empty($categoryids)) {
            $advanceCommission = Mage::getModel('emipro_advancecommission/advancecommission')->getCollection()->addFieldToFilter('seller_id', $sellerId)->addFieldToFilter('attributeset_id', array('in' => $categoryids));
            foreach ($advanceCommission as $value) {
                $commType = $value["commission_type"];
                $commAmount = $value["commission"];
                if ($commAmount != '') {
                    break;
                }
            }
            if ($commType != "" && $qty != "" && $commType == 1) {
                return $commission = $commAmount * $qty;
            }
            if ($commType != "" && $qty != "" && $commType == 0) {
                $shipping = (($shippingcharge * $commAmount) / 100);
                $amount = $price * $commAmount / 100;
                $commission = $amount * $qty;
                if($shipping>0){
                    $commission += $shipping;
                }
                return $commission;
            }
        }
        return $commission;
    }
    public function sellerProductCommissionAmount($sellerId, $productId, $price, $qty,$shippingcharge=0) 
    {

        $commission = '';
        $commAmount = '';
        $commType='';
        //Mage::log("advancecommision/helper/data:-productId",null,'commi.log');
        if ($productId) {
            $advanceCommission = Mage::getModel('emipro_advancecommission/advanceproductcommission')->getCollection()->addFieldToFilter('product_id',$productId);
            
            //Mage::log("advancecommision/helper/data:-productId".$productId,null,'commi.log');
            
            foreach ($advanceCommission as $value) {
                $commType = $value["commission_type"];
                $commAmount = $value["commission"];
                if ($commAmount != '') {
                    break;
                }
            }
            if ($commType != "" && $qty != "" && $commType == 1) {
                return $commission = $commAmount * $qty;
            }
            if ($commType != "" && $qty != "" && $commType == 0) {
                $shipping = (($shippingcharge * $commAmount) / 100);
                $amount = $price * $commAmount / 100;
                $commission = $amount * $qty;
                if($shipping>0){
                    $commission += $shipping;
                }

                return $commission;
            }
        }
        return $commission;
    }
}
