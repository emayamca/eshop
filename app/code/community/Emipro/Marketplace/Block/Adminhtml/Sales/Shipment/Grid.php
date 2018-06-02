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

class Emipro_Marketplace_Block_Adminhtml_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Sales_Shipment_Grid {

    public function setCollection($collection) {
        $parentIds = array();
        $current_user = Mage::getSingleton('admin/session')->getUser();
        $_roleId = $current_user->getRole()->getRoleId();
        $roleId = Mage::helper("marketplace")->getVendorRoleId();
        if ($_roleId == $roleId) {
            $optionid = Mage::helper("marketplace")->getProuctVendorid($current_user->getUserId());
            $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
            $productIDs = $productCollection->getAllIds();
            $collection = Mage::getResourceModel($this->_getCollectionClass());
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
        parent::setCollection($collection);
    }

}
