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

class Emipro_Marketplace_Block_Adminhtml_Sales_Order_View_Tab_Shipments extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments {

    public function setCollection($collection) {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid && $sellerid != '') {
            $seller = Mage::getModel('marketplace/list')->load($sellerid);
            if ($seller->getId()) {
                $productids = array();
                $optionid = Mage::helper("marketplace")->getProuctVendorid($seller->getSellerAdminId());
                $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
                $productIDs = $productCollection->getAllIds();
                foreach ($collection as $shipmentCollection) {
                    foreach ($shipmentCollection->getAllItems() as $item) {
                        $itemId = $item->getData("product_id");
                        if (in_array($itemId, $productIDs)) {
                            $parentIds[] = $item["parent_id"];
                        }
                    }
                }
                $collection = Mage::getResourceModel($this->_getCollectionClass());
                if (isset($parentIds) && !empty($parentIds)) {
                    $collection->addFieldtoFilter("entity_id", array("in", $parentIds));
                } else {
                    $collection->addFieldtoFilter("entity_id", '-1');
                }
            }
        }
        parent::setCollection($collection);
    }

}
