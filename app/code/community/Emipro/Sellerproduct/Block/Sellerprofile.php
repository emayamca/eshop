<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Sellerproduct 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Sellerproduct_Block_Sellerprofile extends Mage_Catalog_Block_Product_List {

    /*
    *   Seller products in seller product page
    */
    protected function _getProductCollection() {
        if (is_null($this->_productCollection)) {

            $id = $this->getRequest()->getParam("profile_id");
            if ($id) {
                $seller = Mage::getModel("marketplace/list")->load($id);
                $vendor_id = $seller->getProductVendorId();
                $sellerid = $seller->getId();
                $productFilter = array(); 
                $product = Mage::getModel("sellerproduct/sellerproductmodel")->getCollection()->addFieldToFilter('seller_id',$sellerid);
                foreach ($product as $key => $value) {
                    $productFilter[] = $value->getProductId()."-".$value->getPosition();
                }
                if(!empty($productFilter)){
                    $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $vendor_id)->addAttributeToFilter('entity_id', array('in' => $productFilter));
                    $collection->getSelect()->join(array('temp' => 'emipro_sellerproduct'),'temp.product_id = e.entity_id','temp.position')->where("temp.seller_id=".$sellerid);
                    $collection->getSelect()->order('temp.position');
                    
                }else{
                    $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $vendor_id);
                }
            }
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
            $collection->addStoreFilter();
            $configValue = Mage::getStoreConfig('marketplace_setting/marketplace/displaysellerproduct');
            $sellerProductCount = $configValue!=null ?  $configValue : 9;
            $numProducts = $this->getNumProducts() ? $this->getNumProducts() : $sellerProductCount;
            $collection->setPage(1, $numProducts)->load();
               
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

}
