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

class Emipro_Marketplace_Model_Commission extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init("marketplace/commission");
    }

    /*
    *   Calculate seller commission and seller credit when seller credit days set in seller 
    *   Enable set true to enable 
    *   Check $firstorderid from which order you want to calculate commission and seller credit
    */

    public function creditfromOrder() {
        $prefix = Mage::getConfig()->getTablePrefix();
        $_date = now();
        $_date = strtotime($_date);
        $_olddate = date('Y-m-d H:i:s', strtotime("-60 day", $_date));
        $enable = true;
        $firstorderid =  '';

        //commission table
        $collection = $this->getCollection()->addFieldToFilter('date', array('from' => $_olddate));
        $commissionorder = array_unique($collection->getColumnValues('order_increment_id'));

        $productSellerIds = $this->sellerProductIds();
        $itemarray = array();
        $commissionobj = Mage::getModel("marketplace/commission");

        $orderitem = Mage::getModel('sales/order_item')->getCollection();
        $orderitem->getSelect()->joinLeft(array('order' => $prefix . 'sales_flat_order'), 'order.entity_id = main_table.order_id', array('increment_id' => 'order.increment_id', 'state' => 'order.state'));
        $orderitem->addAttributeToFilter('order.created_at', array('from' => $_olddate)); 
        $orderitem->addAttributeToFilter('order.state', array('in' => array('processing', 'complete', 'closed'))); 
        if($firstorderid){
            $orderitem->addAttributeToFilter('main_table.order_id',array('gteq'=> $firstorderid));    
        }
        if($enable){
            $checkincludetax = Mage::getStoreConfig('marketplace_setting/marketplace/catalogprice_includes_tax');
            try { 
				if (($orderitem->count()) > 0) {
                    $productids = array();
                    $configurableitemids = array();
                    foreach ($orderitem as $key1 => $item) {
                        if($item->getProductType()=='configurable'){
                                $configurableitemids[] = $item->getItemId();
                            }
                    }
                    $commissionqty= array();  
					//commission table ordersids 
					foreach($commissionorder as $orderincid){ 
						
						$_order = Mage::getModel('sales/order')->loadByIncrementId($orderincid);
						$_items = $_order->getAllItems(); 
						 
						$commissiondata = $commissionobj->getCollection()->addFieldToFilter('date', array('from' => $_olddate));
						$commissiondata->addFieldToFilter('order_increment_id',$orderincid);
					
						foreach ($commissiondata as $val) {
							if ($val->getType() == 1) {
								$commissionqty['refundqty'][$val->getOrderItemId()][$val->getProductId()] += $val->getQty();//$refundqty += $val->getQty();
							} else {
								$commissionqty['shipqty'][$val->getOrderItemId()][$val->getProductId()] += $val->getQty();//$shipqty += $val->getQty();
							} 
						}
					}
					foreach ($orderitem as $key1 => $item) {
                        if( $item->getProductType() != 'bundle' && !in_array($item->getParentItemId(),$configurableitemids)) 
                        {     
                        	if (in_array($item->getIncrementId(), $commissionorder)) 
                        	{ 
                        		$productid = $item->getProductId();
								if (isset($productSellerIds[$productid]) && !empty($productSellerIds[$productid])) {

									$seller_id = $productSellerIds[$productid]; 
									$remainshipqty='';
									$remainrefundqty='';
									$qtyshipped = $item->getQtyShipped();
									if($item->getProductType()=='virtual' || $item->getProductType()=='downloadable'){
										$qtyshipped = $item->getQtyOrdered();  
									}
									if(isset($commissionqty['shipqty'][$item->getItemId()][$productid]) && !empty($commissionqty['shipqty'][$item->getItemId()][$productid]) ){
										$remainshipqty = $qtyshipped-$commissionqty['shipqty'][$item->getItemId()][$productid];
									}else{ 
										$remainshipqty = $qtyshipped; 
									}  
									if(isset($commissionqty['refundqty'][$item->getItemId()][$productid]) && !empty($commissionqty['refundqty'][$item->getItemId()][$productid])){
										$remainrefundqty = ($item->getQtyRefunded()-$commissionqty['refundqty'][$item->getItemId()][$productid]);
									}else{
										$remainrefundqty = $item->getQtyRefunded();
									}
									
									$shipqtyfinal='';
									$refundqtyfinal='';
									if($remainshipqty > $remainrefundqty && $remainshipqty>0){
										$shipqtyfinal = ($remainshipqty - $remainrefundqty); 
									}else if($remainrefundqty>$remainshipqty && $remainrefundqty>0){
										$refundqtyfinal = ($remainrefundqty - $remainshipqty); 
									}
									$itemshippingcharge = ($item->getBaseShippingCharge()) ? $item->getBaseShippingCharge() : ''; 
                                    //Order exist in order transaction table and add order shipment entry add in table   
									if ($shipqtyfinal > 0) 
									{ 
                                        $qty = $shipqtyfinal;    
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
										if ($itemshippingcharge > 0) {
											$totalprice += $itemshippingcharge;
										}
										$comm_amt = Mage::helper("marketplace")->getSellerCommissionAmount($seller_id, $item->getProductId(), $baseprice, $qty,$itemshippingcharge);
										$seller_credit = '';
										$seller_credit = $totalprice - $comm_amt;
										$data = array(
											'seller_id' => $seller_id,
											'order_increment_id' => $item->getIncrementId(),
											'order_item_id' => $item->getItemId(),
											'product_id' => $productid,
											'commission_amt' => $comm_amt,
                                            'base_shipping_charge' => $itemshippingcharge,
											'seller_credit' => $seller_credit,
											'summary' => '',
											'qty' => $qty,
											'date' => now(),
											'type' => 0,
										);
										$modelcommission = Mage::getModel('marketplace/commission');
										$modelcommission->setData($data);
										$modelcommission->save();    
										if ($modelcommission->getId()) {
											$balance_statement = array();
											$balance_statement["transaction_id"] = $modelcommission->getId();
											$balance_statement["transaction_type"] = "order";
											$balance_statement["credit"] = $modelcommission->getSellerCredit();
											$balance_statement["date"] = $modelcommission->getDate();
											$balance_statement["seller_id"] = $modelcommission->getSellerId();
											$balance_statement["order_id"] = $modelcommission->getOrderIncrementId();
											Mage::helper("marketplace")->saveTransactionReport($balance_statement);
										} 
									}
									//Order exist in order transaction table and add refund entry add in table
									if ($refundqtyfinal > 0)
									{ 
                                        $qty = $refundqtyfinal;     
                                        $itemshippingcharge = 0;    
                                        $refundedBaseFees=0;
                                        $_refund_qty=0;
                                            $creditMemoItem = Mage::getResourceModel('sales/order_creditmemo_item_collection');
                                            $creditMemoItem->addFieldToFilter('order_item_id', $item->getId());
                                            $creditMemoItem->addFieldToFilter('credit_days_flag',0);
                                            if($creditMemoItem->count()>0)
                                            {
                                                foreach($creditMemoItem as $itemval)
                                                {
                                                    $refundedBaseFees=$itemval->getBaseShippingCharge()+$refundedBaseFees;
                                                    $_refund_qty=$itemval->getQty()+$_refund_qty;
                                                    $creditmemoitemids[] = $itemval->getId();
                                                }
                                            }
                                            if($_refund_qty==$qty)
                                            {
                                                $itemshippingcharge = $refundedBaseFees;
                                                foreach ($creditmemoitemids as $key => $id_value) {
                                                    $creditMemoItemobj = Mage::getModel('sales/order_creditmemo_item')->load($id_value);    
                                                    $creditMemoItemobj->setCreditDaysFlag(1);
                                                    $creditMemoItemobj->save();
                                                }
                                            } 
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
										if ($itemshippingcharge > 0) {
											$totalprice += $itemshippingcharge;
										}
										$comm_amt = Mage::helper("marketplace")->getSellerCommissionAmount($seller_id, $item->getProductId(),$baseprice, $qty,$itemshippingcharge);
										$seller_credit = '';
										$seller_debit = $totalprice - $comm_amt;  
										$data = array( 
											'seller_id' => $seller_id,
											'order_increment_id' => $item->getIncrementId(),
											'order_item_id' => $item->getItemId(),
											'product_id' => $productid,
											//'commission_amt' => $comm_amt,
                                            'base_shipping_charge' => $itemshippingcharge,
											'seller_debit' => $seller_debit,
											'summary' => 'Credit Memo',
											'qty' => $qty,  
											'date' => now(),
											'type' => 1,
										);
										$modelcommission = Mage::getModel('marketplace/commission');
										$modelcommission->setData($data);
										$modelcommission->save(); 
										if ($modelcommission->getId()) {
											$balance_statement = array();
											$balance_statement["transaction_id"] = $modelcommission->getId();
											$balance_statement["transaction_type"] = "order"; 
											$balance_statement["debit"] = $modelcommission->getSellerDebit();
											$balance_statement["date"] = $modelcommission->getDate();
											$balance_statement["seller_id"] = $modelcommission->getSellerId();
											$balance_statement["order_id"] = $modelcommission->getOrderIncrementId();
											Mage::helper("marketplace")->saveTransactionReport($balance_statement);
										}
									}
								}
                        	}
                        }
                    }
                    //new entry in order trasaction table and balance statement
                    foreach ($orderitem as $key1 => $item) {
                        if( $item->getProductType() != 'bundle' && !in_array($item->getParentItemId(),$configurableitemids)) 
                        {    
                            if (!in_array($item->getIncrementId(), $commissionorder)) {
                                $prodid = $item->getProductId();
                                $price = $item->getBasePrice();
                                $shippingcharge = $item->getBaseShippingCharge(); 
                                if (isset($productSellerIds[$prodid]) && !empty($productSellerIds[$prodid])) {
                                    $seller_id = $productSellerIds[$prodid];
                                    $sellerdetail = Mage::getModel('marketplace/seller')->load($seller_id, "seller_id");
                                    $days = $sellerdetail->getSellerCreditDays();
                                    if ($days > 0) 
                                    {   
                                        $date = $item->getCreatedAt();
                                        $date = strtotime($date); 
                                        $date2 = strtotime("+" . $days . " day", $date);
                                        $execdate = date('Y-m-d', $date2);
                                        if ($execdate <= date('Y-m-d')) 
                                        {  
                                            $orderedqty = $item->getQtyOrdered();
                                            $invoiceqty = $item->getQtyInvoiced();
                                            $shipqty = $item->getQtyShipped();
                                            $refundedqty = $item->getQtyRefunded();
                                           
                                            $actualqty = '';
                                            if ($item->getIsVirtual()) {
                                                $actualqty = $orderedqty - $refundedqty;
                                            } else if ($shipqty > $refundedqty) { 
                                                $actualqty = $shipqty - $refundedqty;
                                            }
                                            
                                            if ($actualqty > 0) {
                                                $totalprice='';
                                                $baseprice  = $item->getBasePrice();
                                                $totalprice = ($baseprice * $actualqty);
                                                if($checkincludetax){
                                                    $basepriceincludetax = $item->getBasePriceInclTax();
                                                    if($basepriceincludetax!=''){
                                                        $totalprice = ($basepriceincludetax * $actualqty);
                                                        $baseprice = $basepriceincludetax;
                                                    }
                                                }
                                                if ($shippingcharge) {
                                                    $totalprice += $shippingcharge;
                                                }
                                                $comm_amt = Mage::helper("marketplace")->getSellerCommissionAmount($seller_id, $item->getProductId(), $baseprice, $actualqty,$shippingcharge);
                                                $seller_credit = $totalprice - $comm_amt;
                                                $sellercomm = Mage::getModel('marketplace/commission');
                                                $data = array(
                                                    'seller_id' => $seller_id,
                                                    'order_increment_id' => $item->getIncrementId(),
                                                    'order_item_id' => $item->getItemId(), 
                                                    'product_id' => $item->getProductId(),
                                                    'commission_amt' => $comm_amt,
                                                    'base_shipping_charge' => $shippingcharge,
                                                    'seller_credit' => $seller_credit,
                                                    'summary' => '',
                                                    'qty' => $actualqty,
                                                    'date' => now(),
                                                    'type' => 0,
                                                );
                                                $sellercomm->setData($data);
                                                $sellercomm->save();
                                                if ($sellercomm->getId()) {
                                                    $balance_statement = array();
                                                    $balance_statement["transaction_id"] = $sellercomm->getId();
                                                    $balance_statement["transaction_type"] = "order";
                                                    $balance_statement["credit"] = $sellercomm->getSellerCredit();
                                                    $balance_statement["date"] = $sellercomm->getDate();
                                                    $balance_statement["seller_id"] = $sellercomm->getSellerId();
                                                    $balance_statement["order_id"] = $sellercomm->getOrderIncrementId();
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
            } catch (Exception $e) {
                echo 'Error : ' . $e->getMessage();
            }     
        }
    }

    /*
    *  Check seller product ids 
    */
    
    public function sellerProductIds() {
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect(array('entity_id', 'vendor_id'))->addAttributeToFilter('vendor_id', array('neq' => ''));
        $array = array();
        foreach ($products as $itemk) { 
            if ($itemk->getVendorId() != '') {
                $seller = Mage::getModel('marketplace/list')->getCollection();
                $seller->addFieldToFilter('product_vendor_id', $itemk->getVendorId());
                $sellerid = $seller->getFirstItem()->getId();
                if ($sellerid) {
                    $array[$itemk->getId()] = $sellerid;
                }
            }
        }
        return $array;
    }

}
