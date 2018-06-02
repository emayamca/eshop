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

require_once "Mage/Adminhtml/controllers/Sales/Order/CreditmemoController.php";

class Emipro_Marketplace_Adminhtml_Sales_Order_CreditmemoController extends Mage_Adminhtml_Sales_Order_CreditmemoController {

	protected function _initCreditmemo($update = false)
    { 
		
        $this->_title($this->__('Sales'))->_title($this->__('Credit Memos'));

        $creditmemo = false;
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        $orderId = $this->getRequest()->getParam('order_id');
        $orderItemId = $this->getRequest()->getParam('item_id');
       
        if ($creditmemoId) {
		
			$creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
        } elseif ($orderId) {
            $data   = $this->getRequest()->getParam('creditmemo');
            $order  = Mage::getModel('sales/order')->load($orderId);
            $invoice = $this->_initInvoice($order);
			
            if (!$this->_canCreditmemo($order)) {
                return false;
            }
	
			$savedData = $this->_getItemData();
			
			$qtys = array();
            $backToStock = array();
            foreach ($savedData as $orderItemId =>$itemData) {
              
                if (isset($itemData['qty'])) {
                    $qtys[$orderItemId] = $itemData['qty'];
                }
                if (isset($itemData['back_to_stock'])) {
                    $backToStock[$orderItemId] = true;
                }
            }
            $data['qtys'] = $qtys;
      
            $service = Mage::getModel('sales/service_order', $order);
            if ($invoice) {
				
			    $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
           
            } else {
				
				$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
				 if($sellerid){
                    $sellerids = Mage::helper("marketplace")->sellerProductIds();
					$productids = array_keys($sellerids,$sellerid);
					$orderitems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id',$orderId);
					if($orderItemId!="")
					{
						$orderitems->addFieldToFilter("item_id",$orderItemId);
					}
					$tempitems = array();
                    $items = array();
					foreach($orderitems as $itemk){
                        
                        if(in_array($itemk->getProductId(),$productids))
                        {
                            $tempitems[$itemk->getItemId()] = round($itemk->getQtyInvoiced()-$itemk->getQtyRefunded()); 
                            $items[] = array($itemk->getItemId()=>array('qty'=>round($itemk->getQtyInvoiced()-$itemk->getQtyRefunded())));    
                        }
                        else{  
                            //$tempitems[$itemk->getItemId()] = 0;
                        }   
                    } 
                   
					$shipFees= Mage::helper('marketplace')->getShippingChargeForCreditmemo($orderId,$orderItemId);

                    if(isset($data['shipping_amount']) && isset($shipFees["shipfees"]))
                    {
                        if($data['shipping_amount'] > $shipFees["shipfees"])
                        {
                            $data['shipping_amount'] = $shipFees["shipfees"];  
                            $data['base_shipping_amount'] = $shipFees["baseshipfees"];         
                        }
                    }else
                    {
                        $data['shipping_amount'] = $shipFees["shipfees"];
                        $data['base_shipping_amount'] = $shipFees["baseshipfees"];      
                    }

                    //new creditmemo for seller set seller items and subtotal 
                    if(!isset($data['items']))
                    {
                        $data['qtys'] = $tempitems;
                        $data['items'] = $items;
                    } 
                   $creditmemo = $service->prepareCreditmemo($data);
				}else if(Mage::app()->getRequest()->getActionName()=='save'){
					 
					Mage::unregister('current_creditmemo');    
					$itemsarr = Mage::getSingleton('adminhtml/session')->getEmCreditmemoarray(true); 
					$itemsarray = Mage::getSingleton('adminhtml/session')->getEmCreditmemoItems(true);
					$data['qtys'] = $itemsarr;
					$data['items'] = $itemsarray; 
					$creditmemo = $service->prepareCreditmemo($data);  
				}else{ 
					
					$shipFees= Mage::helper('marketplace')->getShippingChargeForCreditmemo($orderId,$orderItemId);
					$data['shipping_amount'] = $shipFees["shipfees"];  
					$data['base_shipping_amount'] = $shipFees["baseshipfees"]; 
				
					
                    $orderitems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id',$orderId);
					if($orderItemId!="")
					{
						$orderitems->addFieldToFilter("item_id",$orderItemId);
					}
					$tempitems = array();
                    $items = array();
					foreach($orderitems as $itemk)
					{
							$tempitems[$itemk->getItemId()] = round($itemk->getQtyInvoiced()-$itemk->getQtyRefunded()); 
                            $items[] = array($itemk->getItemId()=>array('qty'=>round($itemk->getQtyInvoiced()-$itemk->getQtyRefunded())));    
                         
                    } 
					 if(!isset($data['items']))
                    {
                        $data['qtys'] = $tempitems;
                        $data['items'] = $items;
                    } 
                    $creditmemo = $service->prepareCreditmemo($data);
				} 
            }

            /**
             * Process back to stock flags
             */
             
            foreach ($creditmemo->getAllItems() as $creditmemoItem) {
               $orderItem = $creditmemoItem->getOrderItem();
               
               $parentId = $orderItem->getParentItemId();
                
                if (isset($backToStock[$orderItem->getId()])) {
                    $creditmemoItem->setBackToStock(true);
                } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                    $creditmemoItem->setBackToStock(true);
                } elseif (empty($savedData)) {
                    $creditmemoItem->setBackToStock(Mage::helper('cataloginventory')->isAutoReturnEnabled());
                } else {
                    $creditmemoItem->setBackToStock(false);
                }
            }
        }
		
		$args = array('creditmemo' => $creditmemo, 'request' => $this->getRequest());
        Mage::dispatchEvent('adminhtml_sales_order_creditmemo_register_before', $args);

        Mage::register('current_creditmemo', $creditmemo);
		return $creditmemo;
    }

    /**
     * Save creditmemo
     * We can save only new creditmemo. Existing creditmemos are not editable
     */
    public function saveAction() {
		
		
        $data = $this->getRequest()->getPost('creditmemo');
		
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }
        try {
			
			$sellerids = Mage::helper("marketplace")->sellerProductIds();
			$itemsarray = array();
			$itemsarr = array(); 
				foreach($data['items'] as $itemid=>$qty){
					$temp = array(); 
					$orderitem = Mage::getModel('sales/order_item')->load($itemid);
					if(isset($sellerids[$orderitem->getProductId()]) && !empty($sellerids[$orderitem->getProductId()])){
						if($qty['qty']!=0)
						{
							$itemsarray[$sellerids[$orderitem->getProductId()]][$itemid] = $qty;
							$itemsarr[$sellerids[$orderitem->getProductId()]][$itemid] = $qty['qty'];
						}
					}else{
                        $itemsarray['admin'][$itemid] = $qty;
                        $itemsarr['admin'][$itemid] = $qty['qty'];
					} 
				}
			foreach($itemsarray as  $sellerid=>$items)
			{    
				
				Mage::getSingleton('adminhtml/session')->setEmCreditmemoarray($itemsarr[$sellerid]);
				Mage::getSingleton('adminhtml/session')->setEmCreditmemoItems($items);
			
				$creditmemo = $this->_initCreditmemo();
				
                $data["base_shipping_amount"] = isset($data["base_shipping_amount"])? $data["base_shipping_amount"]:$data["shipping_amount"];

				$creditmemo->setShippingAmount($data["shipping_amount"]);
				$creditmemo->setBaseShippingAmount($data["base_shipping_amount"]);
                if($sellerid!='admin' && $sellerid > 0){
                    $creditmemo->setSellerId($sellerid);
                    $sellerdetail = Mage::getModel('marketplace/seller')->load($sellerid, "seller_id");
                    if ($sellerdetail->getId()) {
                        $days = $sellerdetail->getSellerCreditDays();
                        if(!$days){
                            $creditmemo->setCreditDaysFlag(1);
                        }
                    }
                } 
				
				if($creditmemo->getSubtotalInclTax()!="")
				{
					if($creditmemo->getDiscountAmount()!="")
					{
						$creditmemo->setGrandTotal($creditmemo->getSubtotalInclTax()+$data["shipping_amount"]+$creditmemo->getDiscountAmount());
						$creditmemo->setBaseGrandTotal($creditmemo->getBaseSubtotalInclTax()+$data["base_shipping_amount"]+$creditmemo->getBaseDiscountAmount());
					}
					else
					{
						$creditmemo->setGrandTotal($creditmemo->getSubtotalInclTax()+$data["shipping_amount"]);
						$creditmemo->setBaseGrandTotal($creditmemo->getBaseSubtotalInclTax()+$data["base_shipping_amount"]);
					}
				}
				else
				{
					if($creditmemo->getDiscountAmount()!="")
					{
						$creditmemo->setGrandTotal($creditmemo->getSubtotal()+$data["shipping_amount"]+$creditmemo->getDiscountAmount());
						$creditmemo->setBaseGrandTotal($creditmemo->getBaseSubtotal()+$data["base_shipping_amount"]+$creditmemo->getBaseDiscountAmount());
					}
					else
					{
						$creditmemo->setGrandTotal($creditmemo->getSubtotal()+$data["shipping_amount"]);
						$creditmemo->setBaseGrandTotal($creditmemo->getBaseSubtotal()+$data["base_shipping_amount"]);
					}
				}
            
            if ($creditmemo) {
                if (($creditmemo->getGrandTotal() <= 0) && (!$creditmemo->getAllowZeroGrandTotal())) {
                    Mage::throwException(
                            $this->__('Credit memo\'s total must be positive.')
                    );
                } 
				
                $comment = '';
                if (!empty($data['comment_text'])) {
                    $creditmemo->addComment(
                            $data['comment_text'], isset($data['comment_customer_notify']), isset($data['is_visible_on_front'])
                    );
                    if (isset($data['comment_customer_notify'])) {
                        $comment = $data['comment_text'];
                    }
                }

                if (isset($data['do_refund'])) {
                    $creditmemo->setRefundRequested(true);
                }
                if (isset($data['do_offline'])) {
                    $creditmemo->setOfflineRequested((bool) (int) $data['do_offline']);
                }

                $creditmemo->register();
                if (!empty($data['send_email'])) {
                    $creditmemo->setEmailSent(true);
                }

                $creditmemo->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                
                $item_ids = '';
                $itemqty = array();
                if(isset($items)){
					foreach($items as $key=>$item){
						$item_ids[] = $key;
						$itemqty[$key]=isset($item['qty'])?$item['qty']:'0';
					}
				}
                $refunded_qty = array();
                if (isset($item_ids) && !empty($item_ids)) {
                    $itemcollection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('item_id', array('in' => $item_ids))->addAttributeToFilter('order_id', $creditmemo->getOrderId());
                    foreach ($itemcollection as $_item) {
                        $refunded_qty[$_item->getItemId()] = $_item->getQtyRefunded();
                    }
                }

                $this->_saveCreditmemo($creditmemo);
				  
				if (isset($item_ids) && !empty($item_ids)) {
					$orderload = Mage::getModel('sales/order')->load($creditmemo->getOrderId());
                    $order_status = $orderload->getState(); 
                    $collection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('item_id', array('in' => $item_ids))->addAttributeToFilter('order_id', $creditmemo->getOrderId());
                    $checkincludetax = Mage::getStoreConfig('marketplace_setting/marketplace/catalogprice_includes_tax');
                
                    foreach ($collection as $item) { 
						if(isset($item_ids))
						{    
						$qty = $itemqty[$item->getItemId()];    
                        if ($item->getProductType() != 'downloadable' && $item->getProductType() != 'virtual') {
							$originalrefundqty = $refunded_qty[$item->getItemId()];
                            $remainingqty = $item->getQtyShipped() - $originalrefundqty;
                            if ($remainingqty < $qty && $remainingqty > 0) {
								$qty = $remainingqty;
							}
                        } else {
						    if ($item->getQtyInvoiced() < $qty) {
                                continue;
                            }
                        }
                        if ($order_status == 'closed' || $order_status == 'complete' || $order_status == 'processing') {
						    $prod = Mage::getModel('catalog/product')->load($item->getProductId());
                            if ($prod->getTypeId() != 'bundle') {
								if ($prod->getVendorId() != '') {
						            $seller = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('product_vendor_id', $prod->getVendorId())->getFirstItem()->getData();
                                    if (isset($seller['id']) && $seller['id'] != '') {
                                        $sellerdetail = Mage::getModel('marketplace/seller')->load($seller['id'], "seller_id");
                                        $days = '';
                                        if ($sellerdetail->getId()) {
                                            $days = $sellerdetail->getSellerCreditDays();
                                        }
                                        if ($qty > 0) {
                                            $totalprice='';
                                            $baseprice  = $item->getBasePrice();
                                            $totalprice = ($baseprice * $qty);
                                            if($checkincludetax){
                                                $basepriceincludetax = $item->getBasePriceInclTax();
                                                if($basepriceincludetax!=''){
                                                    $totalprice = ($basepriceincludetax * $qty);
                                                    $baseprice = $basepriceincludetax;
                                                }
                                            }
                                            $shippingcharge=0;
                                            /*if (isset($item['base_shipping_charge']) && !empty($item['base_shipping_charge'])) {
                                                $totalprice += $item['base_shipping_charge']; 
                                                $shippingcharge = $item['base_shipping_charge']; 
                                            }*/
                                            
                                            $shippingcharge = $data["base_shipping_amount"]; 
                                            $totalprice += $shippingcharge;
                                            
                                            $comm_amt = Mage::helper("marketplace")->getSellerCommissionAmount($seller['id'], $item->getProductId(), $baseprice, $qty,$shippingcharge);
                                            $seller_debit = 0;
                                            if (!$days) {
                                                $seller_debit = $totalprice - $comm_amt;
                                                $commission = Mage::getModel('marketplace/commission');
                                                $array = array(
                                                    'date' => now(),
                                                    'order_increment_id' => $orderload->getIncrementId(),
                                                    'order_item_id' => $item->getItemId(),  
                                                    'seller_id' => $seller['id'],
                                                    'product_id' => $item->getProductId(),
                                                    'base_shipping_charge' => $shippingcharge,
                                                    'qty' => $qty,
                                                    'seller_debit' => $seller_debit,
                                                    'summary' => 'Credit Memo',
                                                    'type' => 1,
                                                );
                                                $commission->setData($array);
                                                $commission->save();
                                                if ($commission->getId()) {
                                                    $balance_statement = array();
                                                    $balance_statement["transaction_id"] = $commission->getId();
                                                    $balance_statement["transaction_type"] = "order";
                                                    $balance_statement["debit"] = $commission->getSellerDebit();
                                                    $balance_statement["date"] = $commission->getDate();
                                                    $balance_statement["seller_id"] = $commission->getSellerId();
                                                    $balance_statement["order_id"] = $commission->getOrderIncrementId();
                                                    Mage::helper("marketplace")->saveTransactionReport($balance_statement);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                      }  
                    }
                }
             }
			}  
			$creditmemo->sendEmail(!empty($data['send_email']), $comment);
			$this->_getSession()->addSuccess($this->__('The credit memo has been created.'));
			Mage::getSingleton('adminhtml/session')->getCommentText(true);
			$this->_redirect('*/sales_order/view', array('order_id' => $creditmemo->getOrderId()));
			return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('Cannot save the credit memo.'));
        }
        $this->_redirect('*/*/new', array('_current' => true));
    }
	
	public function refundItemAction()
	{
		
		$request=Mage::app()->getRequest()->getParams();
		$orderId=$request["order_id"];
		$ItemId=$request["order_item_id"];
		echo Mage::helper("adminhtml")->getUrl("/sales_order_creditmemo/new",array("order_id"=>$orderId,"item_id"=>$ItemId));
		
	}

}
