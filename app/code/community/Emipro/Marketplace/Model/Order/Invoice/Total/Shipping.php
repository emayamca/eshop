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
 
class Emipro_Marketplace_Model_Order_Invoice_Total_Shipping extends Mage_Sales_Model_Order_Invoice_Total_Shipping
{ 
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $invoice->setShippingAmount(0);
        $invoice->setBaseShippingAmount(0);
        $orderShippingAmount        = $invoice->getOrder()->getShippingAmount();
        $baseOrderShippingAmount    = $invoice->getOrder()->getBaseShippingAmount();
        $shippingInclTax            = $invoice->getOrder()->getShippingInclTax();
        $baseShippingInclTax        = $invoice->getOrder()->getBaseShippingInclTax();
		  $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
		  
        if ($orderShippingAmount) {
            /**
             * Check shipping amount in previus invoices
             */
            foreach ($invoice->getOrder()->getInvoiceCollection() as $previusInvoice) {
                if($sellerid){
            		if($previusInvoice->getSellerId()==$sellerid){
	                	if ($previusInvoice->getShippingAmount() && !$previusInvoice->isCanceled()) {
	                    	return $this;
	                	}	
	                }
            	}else{
            		if($previusInvoice->getShippingAmount() && !$previusInvoice->isCanceled()){
                    	return $this;
                	}
            	}
            }
          
			if(!$sellerid)
			{ 
				$invoice->setShippingAmount($orderShippingAmount);
				$invoice->setBaseShippingAmount($baseOrderShippingAmount);
				$invoice->setShippingInclTax($shippingInclTax);
				$invoice->setBaseShippingInclTax($baseShippingInclTax);
				$invoice->setGrandTotal($invoice->getGrandTotal()+$orderShippingAmount);
				$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$baseOrderShippingAmount);
			}
			else
			{
				if($invoice->getOrder()->getShippingMethod()=="flexishipping_flexishipping")
				{
					
					$orderId=$invoice->getOrderId();
					$shipFees= Mage::helper('marketplace')->getSellerShippingFees($orderId,$sellerid);
					$invoice->setShippingAmount($shipFees["shipfees"]);
					$invoice->setBaseShippingAmount($shipFees["baseshipfees"]);
					$invoice->setGrandTotal($invoice->getGrandTotal()+$shipFees["shipfees"]);
					$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$shipFees["baseshipfees"]);
				}
				
			}
		 }
        return $this;
    }
}
