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

class Emipro_Marketplace_Model_Catalog_Category extends Mage_Catalog_Model_Category {

    /*
    *  Check marketplace product approve and remove product from category page 
    */
    public function getProductCollection() {
        $flatcatalog = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
        if($flatcatalog){
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($this->getStoreId())
                ->addCategoryFilter($this);

            $sellers = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('status', 'block')->addFieldToSelect('product_vendor_id');
            $blockedproductids = $sellers->getColumnValues('product_vendor_id');
            $blockprod = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(array('entity_id','vendor_id'))
            ->addAttributeToFilter(
            array(
                array('attribute'=>'vendor_id', 'in' => $blockedproductids),
            )
            );
            $exclude_ids1 = $blockprod->getAllIds();
            
            $prodCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(array('entity_id','vendor_id','marketplace_product_approve'))
            ->addAttributeToFilter(
            array(
                array('attribute'=>'vendor_id', 'neq'=>''), 
            )
            )
            ->addAttributeToFilter(
            array(
                array('attribute'=>'marketplace_product_approve', 'eq' => 0),
            )
            );
            $exclude_ids2 = $prodCollection->getAllIds();

            $exclude_ids = array_merge($exclude_ids1, $exclude_ids2);
            if (isset($exclude_ids) && !empty($exclude_ids)) {
                $collection->addAttributeToSelect(array('entity_id'))
                ->addAttributeToFilter(
                    array( 
                        array('attribute'=>'entity_id', 'nin' => $exclude_ids),
                    )
                );  
            } 
        }else{
             $collection = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($this->getStoreId())
                ->addCategoryFilter($this);

            $sellers = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('status', 'block')->addFieldToSelect('product_vendor_id');
            $blockedproductids = $sellers->getColumnValues('product_vendor_id');
            $blockprod = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('vendor_id', array('in' => $blockedproductids));
            $exclude_ids1 = $blockprod->getAllIds();
            
            $prodCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('vendor_id', array('neq' => ''))
            ->addAttributeToFilter('marketplace_product_approve', 0);
            $exclude_ids2 = $prodCollection->getAllIds();

            $exclude_ids = array_merge($exclude_ids1, $exclude_ids2);
            if (isset($exclude_ids) && !empty($exclude_ids)) {
                 $collection->addAttributeToFilter('entity_id', array('nin' => $exclude_ids));
            } 
        }
           
        return $collection; 
        
    }

}
