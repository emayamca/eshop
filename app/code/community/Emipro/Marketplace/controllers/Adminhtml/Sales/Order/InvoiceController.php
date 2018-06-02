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

require_once "Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php";  
class Emipro_Marketplace_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Sales_Order_InvoiceController
{
	protected function _initInvoice($update = false)
    {
		$this->_title($this->__('Sales'))->_title($this->__('Invoices'));

        $invoice = false;
        $itemsToInvoice = 0;
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        //$invoiceId = '';  
        $orderId = $this->getRequest()->getParam('order_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            if (!$invoice->getId()) {
                $this->_getSession()->addError($this->__('The invoice no longer exists.'));
                return false;
            }
        } elseif ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            /**
             * Check order existing
             */
            if (!$order->getId()) {
                $this->_getSession()->addError($this->__('The order no longer exists.'));
                return false;
            }
            /**
             * Check invoice create availability
             */
            if (!$order->canInvoice()) {
                $this->_getSession()->addError($this->__('The order does not allow creating an invoice.'));
                return false;
            }
            $savedQtys = $this->_getItemQtys(); 
            $sellerinvoiceflag = Mage::getStoreConfig('marketplace_setting/marketplace/sellerinvoice',Mage::app()->getStore()->getId());
            if($sellerinvoiceflag )
			{
				$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
				 if($sellerid){
					$sellerids = Mage::helper("marketplace")->sellerProductIds();
					$productids = array_keys($sellerids,$sellerid);
					$orderitems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id',$orderId);
					$tempitems = array();
					foreach($orderitems as $itemk){
						if(in_array($itemk->getProductId(),$productids)){
							$tempitems[$itemk->getItemId()] = round($itemk->getQtyOrdered()-$itemk->getQtyInvoiced());
						}else{
							$tempitems[$itemk->getItemId()] = 0;
						}
					}
					if(!empty($savedQtys)){ 
						foreach($savedQtys as $key=>$itemqty){
							if(isset($tempitems[$key]) && !empty($tempitems[$key])){
								$tempitems[$key] = $itemqty; 
							}
						}
						$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($tempitems); 
					}else{
						$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($tempitems);
					}   
				}else if(Mage::app()->getRequest()->getActionName()=='save'){
					Mage::unregister('current_invoice');   
					$itemsarray = Mage::getSingleton('adminhtml/session')->getEmInvoiceItems(true);
					$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($itemsarray);    
				}else{
					$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
				}
			} 
			else{   
				$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
			}  
            if (!$invoice->getTotalQty()) { 
                Mage::throwException($this->__('Cannot create an invoice without products.'));
            }
        }

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
			$sellerids = Mage::helper("marketplace")->sellerProductIds();
			$keys = array_keys($data['items']);
			$itemsarray = array();
			$adminproducts = array();
			$sellerinvoiceflag = Mage::getStoreConfig('marketplace_setting/marketplace/sellerinvoice',Mage::app()->getStore()->getId());
            if($sellerinvoiceflag)
			{
				foreach($data['items'] as $itemid=>$qty){
					$temp = array(); 
					if($qty>0){
						$orderitem = Mage::getModel('sales/order_item')->load($itemid);
						if(isset($sellerids[$orderitem->getProductId()]) && !empty($sellerids[$orderitem->getProductId()])){
							$itemsarray[$sellerids[$orderitem->getProductId()]][$itemid] = $qty;
						}else{
							$adminproducts[$itemid] = $qty;
						}	
					}
				}
				foreach($itemsarray as $key=>$item){
					foreach($data['items'] as $dataitemid=>$dataqty)
					{	
						if(!array_key_exists($dataitemid,$item)){
							$itemsarray[$key][$dataitemid] = 0;
						}
					}
				}
				foreach($data['items'] as $dataitemid=>$dataqty){
					if(!array_key_exists($dataitemid,$adminproducts)){
						$adminproducts[$dataitemid] = 0;
					}
				} 
				if(!empty($adminproducts) && array_sum($adminproducts) > 0)
				{
					array_push($itemsarray['admin'],$adminproducts); 
				}
			}else{
				$itemsarray = array($data['items']);
			}
			foreach($itemsarray as  $sellerid=>$items)
			{   
				Mage::getSingleton('adminhtml/session')->setEmInvoiceItems($items);
				$invoice = $this->_initInvoice();
				if ($invoice) {

					if (!empty($data['capture_case'])) {
						$invoice->setRequestedCaptureCase($data['capture_case']);
					}

					if (!empty($data['comment_text'])) {
						$invoice->addComment(
							$data['comment_text'],
							isset($data['comment_customer_notify']),
							isset($data['is_visible_on_front'])
						);
					}
					if($sellerid && $sellerid!='admin')
					{
						$invoice->setSellerId($sellerid);
						if($invoice->getOrder()->getShippingMethod()=="flexishipping_flexishipping" )
						{
							
							$shipFees= Mage::helper('marketplace')->getSellerShippingFees($orderId,$sellerid);
							$invoice->setShippingAmount($shipFees["shipfees"]);
							$invoice->setBaseShippingAmount($shipFees["baseshipfees"]);
							$invoice->setGrandTotal($invoice->getSubtotal()+$shipFees["shipfees"]);
							$invoice->setBaseGrandTotal($invoice->getBaseSubtotal()+$shipFees["baseshipfees"]);
						}
						else
						{
							$ShippingAmount=0;
							$invoice->setShippingAmount($ShippingAmount);
							$invoice->setBaseShippingAmount($ShippingAmount);
							$invoice->setGrandTotal($invoice->getSubtotal()+$ShippingAmount); 
							$invoice->setBaseGrandTotal($invoice->getBaseSubtotal()+$ShippingAmount);
						}
					}     
					
					else
					{	
						//$invoice->setSellerId($sellerid);
						if($invoice->getOrder()->getShippingMethod()=="flexishipping_flexishipping" )
						{ 
							$shipFees= Mage::helper('marketplace')->getAdminShippingFees($orderId);
							$invoice->setShippingAmount($shipFees["shipfees"]);
							$invoice->setBaseShippingAmount($shipFees["baseshipfees"]);
							$invoice->setGrandTotal($invoice->getSubtotal()+$shipFees["shipfees"]); 
							$invoice->setBaseGrandTotal($invoice->getBaseSubtotal()+$shipFees["baseshipfees"]);
						}
						elseif($sellerid=='admin')
						{
							
							$ShippingAmount=$invoice->getOrder()->getShippingAmount();
							$invoice->setShippingAmount($ShippingAmount);
							$invoice->setBaseShippingAmount($ShippingAmount);
							$invoice->setGrandTotal($invoice->getSubtotal()+$ShippingAmount); 
							$invoice->setBaseGrandTotal($invoice->getBaseSubtotal()+$ShippingAmount);
						}
					}
					if($invoice->getBaseTaxAmount()!='')
			        {
			            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+($invoice->getBaseTaxAmount()));  
			            $invoice->setGrandTotal($invoice->getGrandTotal()+($invoice->getTaxAmount()));  
			        }
					if($invoice->getBaseDiscountAmount()!='')
					{
						$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+($invoice->getBaseDiscountAmount()));	
						$invoice->setGrandTotal($invoice->getGrandTotal()+($invoice->getDiscountAmount())); 	
					}

					$invoice->register();

					if (!empty($data['send_email'])) {
						$invoice->setEmailSent(true);
					}

					$invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
					$invoice->getOrder()->setIsInProcess(true);

					$transactionSave = Mage::getModel('core/resource_transaction')
						->addObject($invoice)
						->addObject($invoice->getOrder());
					$shipment = false;
					if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
						$shipment = $this->_prepareShipment($invoice);
						if ($shipment) {
							$shipment->setEmailSent($invoice->getEmailSent());
							$transactionSave->addObject($shipment);
						}
					}
					$transactionSave->save();

					if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
						$this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
					} elseif (!empty($data['do_shipment'])) {
						$this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
					} else {
						//$this->_getSession()->addSuccess($this->__('The invoice has been created.'));
					}

					// send invoice/shipment emails
					$comment = '';
					if (isset($data['comment_customer_notify'])) {
						$comment = $data['comment_text'];
					}
					try { 
						$invoice->sendEmail(!empty($data['send_email']), $comment);
					} catch (Exception $e) {
						Mage::logException($e);
						$this->_getSession()->addError($this->__('Unable to send the invoice email.'));
					}
					if ($shipment) {
						try {
							$shipment->sendEmail(!empty($data['send_email']));
						} catch (Exception $e) {
							Mage::logException($e);
							$this->_getSession()->addError($this->__('Unable to send the shipment email.'));
						}
					}
					Mage::getSingleton('adminhtml/session')->getCommentText(true);
				} 
			} 
			$this->_getSession()->addSuccess($this->__('The invoice has been created.'));  
			$this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            return;
            
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));
    }

}
