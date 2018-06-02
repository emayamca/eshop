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

class Emipro_Marketplace_Block_Index extends Mage_Core_Block_Template {

    /*
    *   Seller data in frontend page 
    */
    public function getSellerdata() {
        $websiteId = Mage::app()->getWebsite()->getId(); 
        $collection = Mage::getModel('marketplace/seller')->getCollection()
                ->join(array('s' => 'marketplace/list'), 's.id=main_table.seller_id', 'status')
                ->addFieldToFilter('status', 'approved')
                ->addFieldToFilter('website_id',$websiteId);
        return $collection;
    }

    /*
    *   Seller product data in seller page
    */
    
    public function getSellerProducts() {
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', array('neq' => ''))
                ->addAttributeToFilter('marketplace_product_approve', array('eq' => 1))
                 ->addAttributeToFilter('visibility', 4)
                ->setPageSize(15)
                ->setOrder('entity_id', 'DESC');
        return $collection;
    }

}
