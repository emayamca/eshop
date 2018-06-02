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
 
class Emipro_Marketplace_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{
    public function __construct()
    {   
		parent::__construct();
		if(Mage::helper('marketplace')->getSellerIdfromLoginUser())
		{  
		$this->_removeButton('order_cancel');
        $this->_removeButton('order_hold');
        $this->_removeButton('order_unhold');
        $this->_removeButton('order_reorder');
        $this->_removeButton('order_edit'); 
        $this->_removeButton('send_notification');
        	$order_id = Mage::app()->getRequest()->getParam('order_id');
            if($order_id!=''){
				$order = Mage::getModel('sales/order')->load($order_id);
				if(Mage::helper('marketplace')->shipmentAvailable($order_id)) //|| $order->getStatus()=='pending' 
				{  
					$this->_removeButton('order_ship');
				}
				if($order->getStatus()=='pending' || $this->creditmemoNotAvailable($order_id))
				{
					$this->_removeButton('order_creditmemo');
				}
				if($order->getStatus()=='closed' || $order->getStatus()=='complete' || !Mage::getStoreConfig('marketplace_setting/marketplace/sellerinvoice') || $this->invoiceNotAvailable($order_id)){
					$this->_removeButton('order_invoice');  
				} 
			}
		} 
    } 
    public function creditmemoNotAvailable($order_id){
		$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
		if($sellerid){
			$sellerproductids = Mage::helper("marketplace")->sellerProductIds();
			$productIDs = array_keys($sellerproductids,$sellerid);
			$collection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('order_id',$order_id);
		   foreach($collection as $i):
			if($i->getProductType()!='bundle') 
			{
			   if(in_array($i->getProductId(),$productIDs)){ 
					if($i->getQtyInvoiced()>$i->getQtyRefunded()){
						return false;
						break;
					}	 
			   }
			}
			endforeach;
		}
		return true;
	}
	protected function invoiceNotAvailable($order_id){
		$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
		if($sellerid){
			$sellerproductids = Mage::helper("marketplace")->sellerProductIds();
			$productIDs = array_keys($sellerproductids,$sellerid);
			$collection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('order_id',$order_id);
		   foreach($collection as $i){
				if($i->getProductType()!='bundle') 
				{
				   if(in_array($i->getProductId(),$productIDs)){ 
					   if($i->getQtyOrdered()>$i->getQtyInvoiced()){
							return false;
							break;
						}	 
				   }
				}
			} 
		}
		return true; 
	}
}
