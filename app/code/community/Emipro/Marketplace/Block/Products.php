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

class Emipro_Marketplace_Block_Products extends Mage_Catalog_Block_Product_List {

    /*
    *   Seller products in seller product page
    */
    protected function _getProductCollection() {
        if (is_null($this->_productCollection)) {

            $id = $this->getRequest()->getParam("profile_id");
            if ($id) {
                $seller = Mage::getModel("marketplace/list")->load($id);
                $vendor_id = $seller->getProductVendorId();
                $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $vendor_id);
            }
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
            //$collection->getSelect()->order('entity_id desc');
            $collection->addStoreFilter();

            $numProducts = $this->getNumProducts() ? $this->getNumProducts() : 8;

            $collection->setPage(1, $numProducts)->load();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

}
