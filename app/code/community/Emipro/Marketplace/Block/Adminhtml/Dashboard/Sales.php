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

class Emipro_Marketplace_Block_Adminhtml_Dashboard_Sales extends Mage_Adminhtml_Block_Dashboard_Bar {

    protected $_pending_amount;

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('marketplace/dashboard/salebar.phtml');
        $data['remainamount'] = $this->getremainAmount();
        $data['seller_review'] = $this->getSellerreview();
        $data['pending_amount'] = $this->getPendingAmount();
        $this->setData('data', $data);
    }

    protected function _prepareLayout() {
        $isFilter = $this->getRequest()->getParam('store') || $this->getRequest()->getParam('website') || $this->getRequest()->getParam('group');

        $collection = Mage::getResourceModel('reports/order_collection');
        $current_user = Mage::getSingleton('admin/session')->getUser();

        if (Mage::helper("marketplace")->getSellerIdfromLoginUser()) {
            $sellerid = Mage::helper("marketplace")->getSellerIdfromLoginUser();
            $sales = Mage::getModel('marketplace/commission')->getCollection();
            $sales->addFieldToSelect('seller_credit');
            $sales->addFieldToFilter('seller_id', $sellerid);
            $sales->addFieldToFilter('product_id', array('neq' => ''));
            $sales->addFieldToFilter('seller_credit', array('gt' => 0));
            $this->addStoreFilter($sales);
            $totalsales = '';
            foreach ($sales->getData() as $item) {
                $totalsales += $item['seller_credit'];
            }

            $date = date('Y-m-d');
            $yesturday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
            $week = date('Y-m-d', strtotime('-7 day', strtotime($date)));
            $month = date('Y-m-d', strtotime('-1 month', strtotime($date)));
            $tomorrow = date('Y-m-d', strtotime('+1 day', strtotime($date)));
            $today = date('Y-m-d H:i:s');

            $monthsales = Mage::getModel('marketplace/commission')->getCollection();
            $monthsales->addFieldToSelect('seller_credit');
            $monthsales->addFieldToFilter('seller_id', $sellerid);
            $monthsales->addFieldToFilter('product_id', array('neq' => ''));
            $monthsales->addFieldToFilter('date', array('from' => $month, 'to' => $today));
            $monthsales->addFieldToFilter('seller_credit', array('gt' => 0));
            $this->addStoreFilter($monthsales);
            $monthtotalsales = '';
            foreach ($monthsales->getData() as $item) {
                $monthtotalsales += $item['seller_credit'];
            }

            $weeksales = Mage::getModel('marketplace/commission')->getCollection();
            $weeksales->addFieldToSelect('seller_credit');
            $weeksales->addFieldToFilter('seller_id', $sellerid);
            $weeksales->addFieldToFilter('product_id', array('neq' => ''));
            $weeksales->addFieldToFilter('date', array('from' => $week, 'to' => $today));
            $weeksales->addFieldToFilter('seller_credit', array('gt' => 0));
            $this->addStoreFilter($weeksales);
            $weektotalsales = '';
            foreach ($weeksales->getData() as $item) {
                $weektotalsales += $item['seller_credit'];
            }

            $todaysales = Mage::getModel('marketplace/commission')->getCollection();
            $todaysales->addFieldToSelect('seller_credit');
            $todaysales->addFieldToFilter('seller_id', $sellerid);
            $todaysales->addFieldToFilter('product_id', array('neq' => ''));
            $todaysales->addFieldToFilter('date', array('gteq' => date('Y-m-d 00:00:00')));
            $todaysales->addFieldToFilter('seller_credit', array('gt' => 0));
            $this->addStoreFilter($todaysales);
            $todaytotalsales = '';
            foreach ($todaysales->getData() as $item) {
                $todaytotalsales += $item['seller_credit'];
            }

            $yesturdaysales = Mage::getModel('marketplace/commission')->getCollection();
            $yesturdaysales->addFieldToSelect('seller_credit');
            $yesturdaysales->addFieldToFilter('seller_id', $sellerid);
            $yesturdaysales->addFieldToFilter('product_id', array('neq' => ''));
            $yesturdaysales->addFieldToFilter('date', array('from' => $yesturday, 'to' => date('Y-m-d 00:00:00')));
            $yesturdaysales->addFieldToFilter('seller_credit', array('gt' => 0));
            $this->addStoreFilter($yesturdaysales);
            $yesturdaytotalsales = '';
            foreach ($yesturdaysales->getData() as $item) {
                $yesturdaytotalsales += $item['seller_credit'];
            }
            $orders = Mage::getModel('marketplace/commission')->getCollection();
            $orders->addFieldToSelect('seller_credit');
            $orders->addFieldToFilter('product_id', array('neq' => ''));
            $orders->addFieldToFilter('seller_id', $sellerid);
            $orders->addFieldToFilter('seller_credit', array('gt' => 0));
            $orders->getSelect()->group('order_increment_id');
            $this->addStoreFilter($orders);
            $ave = '';
            if (count($orders) > 0) {
                $ave = $totalsales / count($orders);
            }
            $this->addTotal($this->__('Lifetime Sales'), ($totalsales + $this->_pending_amount));
            $this->addTotal($this->__('Average Orders'), number_format($ave, 2));
            $this->addTotal($this->__('Today Sales'), $todaytotalsales);
            $this->addTotal($this->__('Yesterday Sales'), $yesturdaytotalsales);
            $this->addTotal($this->__('Week Sales'), $weektotalsales);
            $this->addTotal($this->__('Month Sales'), $monthtotalsales);
        } else {

            if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
                return $this;
            }

            $_roleId = $current_user->getRole()->getRoleId();
            $roleId = Mage::helper("marketplace")->getVendorRoleId();
            if ($_roleId != $roleId) {
                $collection = Mage::getResourceModel('reports/order_collection');
                $sales = $this->filtercollection_custom($collection, $isFilter);
                $this->addTotal($this->__('Lifetime Sales'), $sales->getLifetime());
                $this->addTotal($this->__('Average Orders'), $sales->getAverage());
            }
        }
    }
    protected function addStoreFilter($collection) {
        $collection->getSelect()->joinLeft(array('sfo'=>'sales_flat_order'),'main_table.order_increment_id=sfo.increment_id',array('store_id'=>'sfo.store_id')); 
        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }
        $collection->load();
        return $collection; 
    }
    protected function filtercollection_custom($collec, $isFilter) {
        $collec->calculateSales($isFilter);
        if ($this->getRequest()->getParam('store')) {
            $collec->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } else if ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collec->addFieldToFilter('store_id', array('in' => $storeIds));
        } else if ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collec->addFieldToFilter('store_id', array('in' => $storeIds));
        }
        $collec->load();
        $salesdata = $collec->getFirstItem();
        return $salesdata;
    }

    protected function getremainAmount() {
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            $seller_balance = Mage::helper('marketplace')->getSellerBalance($seller_id);
            if (!$seller_balance) {
                $seller_balance = 0;
            }
            $str = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
            $str.= number_format($seller_balance, 2);
            return $str;
        }
        return;
    }

    protected function getSellerreview() {
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            return round(Mage::helper('marketplace')->getSellerRating($seller_id));
        }
    }

    protected function sellerProductIds($sellerid) {
        $array = array();
        $seller = Mage::getModel('marketplace/list')->load($sellerid);
        if ($seller->getProductVendorId()) {
            $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect(array('entity_id', 'vendor_id'))->addAttributeToFilter('vendor_id', $seller->getProductVendorId());
            $array = array();
            foreach ($products as $item) {
                $array[] = $item->getId();
            }
        }
        return $array;
    }

    protected function getPendingAmount() {
        $prefix = Mage::getConfig()->getTablePrefix();
        $pending_amount = 0;
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            $orderitem = Mage::getModel('sales/order_item')->getCollection();
            $orderitem->getSelect()->joinLeft(array('order' => $prefix . 'sales_flat_order'), 'order.entity_id = main_table.order_id', array('increment_id' => 'order.increment_id', 'status' => 'order.status'));
            $orderitem->addAttributeToFilter('order.status', array('in' => array('pending', 'processing')));
            if ($this->getRequest()->getParam('store')) {
                $orderitem->addAttributeToFilter('order.store_id', $this->getRequest()->getParam('store')); 
            } else if ($this->getRequest()->getParam('website')) {
                $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
                $orderitem->addAttributeToFilter('order.store_id', array('in' => $storeIds));
            } else if ($this->getRequest()->getParam('group')) { 
                $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
                $orderitem->addAttributeToFilter('order.store_id', array('in' => $storeIds));
            } 

            $orderitem->addAttributeToFilter('main_table.product_id', array('in' => $this->sellerProductIds($seller_id)));
            
            if (($orderitem->count()) > 0) {
                $productids = array();
                foreach ($orderitem as $key1 => $item) {
                    if ($item->getProductId() && in_array($item->getProductType(), array('simple', 'downloadable', 'virtual', 'grouped'))) {
                        $prodid = $item->getProductId();
                        $price = $item->getBasePrice();
                        $shippingcharge = $item->getShippingCharge();
                        $sellerdetail = Mage::getModel('marketplace/seller')->load($seller_id, "seller_id");
                        $days = $sellerdetail->getSellerCreditDays();

                        $orderedqty = $item->getQtyOrdered();
                        $invoicedqty = $item->getQtyInvoiced();
                        $shipqty = $item->getQtyShipped();
                        $refundqty = $item->getQtyRefunded();
                        $actualqty = '';
                        if ($invoicedqty > 0) {
                            $actualqty = $invoicedqty - ($shipqty + $refundqty);
                        } else {
                            $actualqty = $orderedqty;
                        }
                        if ($actualqty > 0 && $actualqty != '') {
                            $totalprice = $price * $actualqty;
                            if ($shippingcharge) {
                                $totalprice += $shippingcharge;
                            }
                            $comm_amt = Mage::helper("marketplace")->getSellerCommissionAmount($seller_id, $item->getProductId(), $totalprice, $actualqty);
                            $seller_credit = $totalprice - $comm_amt;
                            $pending_amount += $seller_credit;

                        }
                    }
                }
            }
        }
        $symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $this->_pending_amount = $pending_amount;
        return $symbol . number_format($pending_amount, 2);
    }

}
