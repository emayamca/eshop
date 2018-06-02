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

class Emipro_Marketplace_Block_Sellerreview extends Mage_Core_Block_Template {

    /*
    *   Check available seller for which customer can review in frontend, customer can review once per seller
    */
    
    public function getSellerforreview() {
        //frontend customer seller review form 
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $collection = Mage::getModel('sales/order')->getCollection();
            $collection->join('order_item', 'order_item.order_id=main_table.entity_id', array('product_id' => new Zend_Db_Expr('group_concat(order_item.product_id SEPARATOR ",")'),
            ));
            $collection->getSelect()->group('main_table.entity_id');
            $collection->addFieldToFilter('main_table.customer_id', $customer_id);
            $collection->addFieldToFilter('main_table.status', 'complete');
            $date = date('Y-m-d', strtotime("now -30 days"));
            $collection->addFieldToFilter('main_table.created_at', array('gteq' => $date));
            $ids = array();
            if ($collection->count() > 0) {
                foreach ($collection as $order) {
                    $temp = explode(',', $order->getProductId());
                    foreach ($temp as $val) {
                        $ids[] = $val;
                    }
                }
                $productids = array_unique($ids);
                if (isset($productids) && !empty($productids)) {
                    $product_obj = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id', array('in' => $productids))->addAttributeToSelect('vendor_id')->addAttributeToFilter('vendor_id', array('neq' => ''));
                    $array = array();
                    foreach ($product_obj as $proditem) {
                        $vendor = $proditem->getVendorId();
                        $seller = Mage::getModel('marketplace/list')->load($vendor, 'product_vendor_id');
                        $checkalreadyrate = Mage::getModel('marketplace/sellerreview')->getCollection()->addFieldToFilter('seller_id', $seller->getId())->addFieldToFilter('customer_id', $customer_id);
                        if ($checkalreadyrate->count() < 1) {
                            $array[$seller->getId()] = Mage::helper('marketplace')->getsellernamefromid($seller->getId());
                        }
                    }
                    return $array;
                }
            }
        }
        return false;
    }

    /*
    *  More sellers for same product data in product page
    */
    
    public function getsellersData() {
        //sellertable product page 
        $current_prod = Mage::registry('current_product');
        if (isset($current_prod) && ($current_prod->getUpc() != '' || $current_prod->getEsin() != '')) {
            $productobj = Mage::getModel('catalog/product')->getCollection();
            if($current_prod->getUpc() && $current_prod->getEsin()){
                $productobj->addAttributeToFilter(
                    array(
                        array('attribute'=> 'upc','eq' => $current_prod->getUpc()),
                        array('attribute'=> 'esin','eq' => $current_prod->getEsin()),
                    )
                );
            }else if($current_prod->getUpc()){
                $productobj->addAttributeToFilter('upc', $current_prod->getUpc());
            }else if($current_prod->getEsin()){
                $productobj->addAttributeToFilter('esin', $current_prod->getEsin());
            }
            $productobj->addAttributeToFilter('vendor_id', array('neq' => ''));
            $productobj->addAttributeToFilter('vendor_id', array('neq' => $current_prod->getVendorId()));
            $productobj->addAttributeToFilter('entity_id', array('neq' => $current_prod->getEntityId()));
            $productobj->addAttributeToSelect('price')->addAttributeToSelect('final_price');
            $data = array();
            if ($productobj->count() > 0) {
                $selleridcheck = array();
                foreach ($productobj as $proitem) {
                    $vendor_id = $proitem->getVendorId();
                    $sellerobj = Mage::getModel('marketplace/list')->load($vendor_id, 'product_vendor_id');
                    if ($sellerobj->getId()) {
                        if (!in_array($sellerobj->getId(), $selleridcheck)) {
                            $selleridcheck = $sellerobj->getId();
                            $seller['rating'] = Mage::helper('marketplace')->getSellerRating($sellerobj->getId());
                            $seller['productid'] = $proitem->getId();
                            $seller['price'] = $proitem->getFinalPrice();
                            $sellerdetail = Mage::getModel('marketplace/seller')->load($sellerobj->getId(), 'seller_id');
                            if ($sellerdetail->getSellerId()) {
                                $seller['name'] = $sellerdetail->getFirstname();
                                $seller['storeurl'] = $sellerdetail->getStoreUrl();
                                $seller['replacement_guarantee'] = $sellerdetail->getReplacementGaurantee();
                                $seller['delivered_in'] = $sellerdetail->getDeliveredIn();
                            }
                            $data[] = $seller;
                        }
                    }
                }
                return $data;
            }
        }
        return false;
    }

    /*
    *   Get current seller data
    */

    public function getcurrentsellerData() {
        $current_prod = Mage::registry('current_product');
        if ($current_prod->getVendorId() != '') {
            $seller = Mage::getModel('marketplace/list')->load($current_prod->getVendorId(), 'product_vendor_id');
            if ($seller->getId()) {
                $data = array();
                $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), 'seller_id');
                if (isset($sellerdetail) && $sellerdetail->getSellerId()) {
                    $data['storeurl'] = $sellerdetail->getStoreUrl();
                    $data['store_name'] = $sellerdetail->getStoreName();
                    $data['rate'] = Mage::helper('marketplace')->getSellerRating($seller->getId());
                    $data['replacement_guarantee'] = $sellerdetail->getReplacementGaurantee();
                    $data['delivered_in'] = $sellerdetail->getDeliveredIn();
                    return $data;
                }
            }
        }
        return false;
    }

}
