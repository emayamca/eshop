<?php
class Fuel_Trackyourorder_Block_Trackyourorder extends Mage_Sales_Block_Order_History{
    /**
     *  Rewrites the My Orders Page 
     *  @return void
     *  */
    public function __construct() {
        parent::__construct ();
        $this->setTemplate ( 'trackyourorder/sales/order/history.phtml' );
    }
    /**
     * Function to get Order Item Collection
     * @return object
     */
    public function getOrderItemCollection($orderId){
        return Mage::getResourceModel ( 'sales/order_item_collection' )->addFieldToFilter ( 'order_id', array (
            'eq' => $orderId 
    ) );
    }
    
    /**
     * 
     * Function to get order details
     * @param int orderid
     * @return object
     */
    public function getOrderObject($_orderid){
        return Mage::getModel ( 'sales/order' )->loadByIncrementId ( $_orderid );
    }
    
    /**
     * 
     * Function to get Product object
     * @param int productid
     * @return object
     */
    public function getProductObject($productId){
        return Mage::getModel('catalog/product')->load($productId);
    }
}