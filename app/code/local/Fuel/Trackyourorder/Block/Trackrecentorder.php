<?php
class Fuel_Trackyourorder_Block_Trackrecentorder extends Mage_Sales_Block_Order_Recent{
    /**
     *  Rewrites the My Orders Page 
     *  @return  void
     * */
    public function __construct(){
        parent::__construct();
        $this->setTemplate('trackyourorder/sales/order/recent.phtml');
    }
}