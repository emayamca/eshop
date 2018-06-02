<?php
class Fuel_Trackyourorder_Block_Trackyourorderajax extends Mage_Core_Block_Template{
    /**
     * Function to get Product object
     * @param productid
     * @return object
     * 
     */
    public function getProductObject($productId){
        return Mage::getModel('catalog/product')->load($productId);
    }
    /**
     * Function to get Order Collection
     * @param orderid
     * @return object
     * 
     */
    public function getOrderCollection($orderId){
       return  Mage::getResourceModel('sales/order_item_collection') ->addFieldToFilter('order_id', array('eq' => $orderId));
    }
}