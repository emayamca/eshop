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

class Emipro_Marketplace_Block_Bundle_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid {

    public function setCollection($collection) {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid && $sellerid != '') {
            $seller = Mage::getModel('marketplace/list')->load($sellerid);
            if ($seller->getId()) {
                $collection->addAttributeToFilter('vendor_id', $seller->getProductVendorId());
            }
        }
        parent::setCollection($collection);
    }

}
