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

class Emipro_Marketplace_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid {

    public function setCollection($collection) {
        $sellrid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellrid && $sellrid != '') {
            $seller = Mage::getModel('marketplace/list')->load($sellrid);
            if ($seller->getId()) {
                $collection->addAttributeToFilter('vendor_id', array('in' => $seller->getProductVendorId()));
            }
        }
        parent::setCollection($collection);
    }

}
