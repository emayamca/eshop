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

class Emipro_Marketplace_Block_Adminhtml_Dashboard_Orders_Grid extends Mage_Adminhtml_Block_Dashboard_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('lastOrdersGrid');
    }

    protected function _prepareCollection() {
        $sellerid = Mage::helper("marketplace")->getSellerIdfromLoginUser();
        $orders = Mage::getModel('marketplace/commission')->getCollection();
        if ($sellerid) {
            $orders->addFieldToFilter('seller_id', $sellerid);
        }
        $orders->addFieldToFilter('order_increment_id', array('neq' => ''));
        $orders->addFieldToFilter('summary', array('null' => true));
        $orders->addFieldToSelect(array('oid' => 'order_increment_id', 'orderid' => 'order_increment_id'));
        $orders->getSelect()->columns('SUM(qty) as items, SUM(seller_credit) as total');
        $orders->getSelect()->group('order_increment_id');
        $orders->getSelect()->order('id DESC')->limit(5);
        $this->setCollection($orders);
        return parent::_prepareCollection();
    }

    protected function _preparePage() {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
    }

    protected function _prepareColumns() {
        $this->addColumn('oid', array(
            'header' => $this->__('Customer'),
            'sortable' => false,
            'index' => 'oid',
            'renderer' => 'Emipro_Marketplace_Block_Adminhtml_Dashboard_Orders_Renderer_Customer'
        ));
        $this->addColumn('orderid', array(
            'header' => $this->__('Order Id'),
            'index' => 'orderid',
            'width' => '130px',
            'sortable' => false,
        ));

        $this->addColumn('items', array(
            'header' => $this->__('Items'),
            'sortable' => false,
            'index' => 'items',
        ));

        $this->addColumn('total', array(
            'header' => $this->__('Total'),
            'sortable' => false,
            'index' => 'total',
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return '';
    }

}
