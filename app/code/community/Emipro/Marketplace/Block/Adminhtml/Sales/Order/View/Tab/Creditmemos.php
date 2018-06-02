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

class Emipro_Marketplace_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Creditmemos {

    public function setCollection($collection) {
        $parentIds = array();
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid && $sellerid != '') {
            $seller = Mage::getModel('marketplace/list')->load($sellerid);
            if ($seller->getId()) {
                $optionid = Mage::helper("marketplace")->getProuctVendorid($seller->getSellerAdminId());
                $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
                $productIDs = $productCollection->getAllIds();
                $creditmemoitems = Mage::getModel('sales/order_creditmemo_item')->getCollection()->addAttributeToFilter('product_id', array('in' => $productIDs));
                $parentIds = array();
                foreach ($creditmemoitems as $item) {
                    $parentIds[] = $item->getParentId();
                }
                if (isset($parentIds) && !empty($parentIds)) {
                    $collection->addAttributetoFilter("entity_id", array("in", $parentIds));
                } else {
                    $collection->addAttributetoFilter("entity_id", array("in", '-1'));
                }
            }
        }
        parent::setCollection($collection);
    }

}
