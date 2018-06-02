<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fuel
 * @package     Fuel_Bestseller
 */
/**
 * This Block for helps to display Brands products
 */
class Fuel_Sociallogin_Block_Bestseller extends Mage_Core_Block_Template {
    
     /**
     * Best selleing product Collection
     *
     * @return array
     */
    public function bestSellerCollection() {
		
		$visibility = array (
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG 
		);
		
		/*$productIds = array();
		//Get products Ids based on the category id
		if($catIds != null){
			$products = Mage::getModel('catalog/category')->load($catIds)
			->getProductCollection()
			->addAttributeToSelect('entity_id') // add all attributes - optional
			->addAttributeToFilter('status', 1) // enabled
			->addAttributeToFilter ( 'visibility', $visibility ) //visibility in catalog,search
			->setOrder('name', 'ASC');

			foreach($products as $product){
				$productIds[] = $product->getId();
			}
		}*/
 
        $storeId = Mage::app ()->getStore ()->getId ();
		//Checking Flat_catalog is enabled
        if (Mage::getStoreConfigFlag ( 'catalog/frontend/flat_catalog_product' )) {
            $products = Mage::getResourceModel ( 'reports/product_collection' )->addOrderedQty ()->setStoreId ( $storeId )->addStoreFilter ( $storeId )->setOrder ( 'ordered_qty', 'desc' );
            $prefix = Mage::getConfig ()->getTablePrefix ();
            $products->getSelect ()->joinInner ( array (
                'e2' => $prefix . 'catalog_product_flat_' . $storeId 
            ), 'e2.entity_id = e.entity_id' );
            
            Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
            Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
            $products->setPageSize ( 10 );
        } else {
            $products = Mage::getResourceModel ( 'reports/product_collection' )->addOrderedQty ()->addAttributeToSelect ( '*' )->addAttributeToSelect ( array (
                'name',
                'price',
                'small_image' 
            ) )->setStoreId ( $storeId )->addStoreFilter ( $storeId )->addAttributeToFilter ( 'visibility', $visibility )->addAttributeToFilter ( 'status', array (
                'eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED 
            ) );
			
			//Check array is not empty
			/*if(!empty($productIds)){
				$products->addAttributeToFilter('entity_id', array('in' => $productIds));
			}*/
			$products->setOrder ( 'ordered_qty', 'desc' );
            $products->setPageSize ( 10 );
        }
        //Restrict out of stock products in the collection
        $configValueStockStatus = Mage::getStoreConfig ( 'cataloginventory/options/show_out_of_stock', $storeId );
        if ($configValueStockStatus == 0) {
            Mage::getSingleton ( 'cataloginventory/stock' )->addInStockFilterToCollection ( $products );
        }
        
        return $products;
    }
}