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

class Emipro_Marketplace_Model_Catalog_Layer extends Mage_Catalog_Model_Layer {

	/*
    *  Check marketplace product approve and remove product from category layered navigation in search page
    */

    public function prepareProductCollection($collection) {
    	parent::prepareProductCollection($collection);
    	if (Mage::registry('seller_product_vendor_id')) {
	    	$flatcatalog = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
	    	if($flatcatalog){
			  $collection->addAttributeToSelect(array('vendor_id'))
	                ->addAttributeToFilter(
	                    array( 
	                        array('attribute'=>'vendor_id', 'eq' => Mage::registry('seller_product_vendor_id')), 
	                    )
	            );
	    	}else{
	            	$collection->addAttributeToFilter('vendor_id', Mage::registry('seller_product_vendor_id'));
	        }
	    }
        
        return $this; 
    }

}
