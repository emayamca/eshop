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

class Emipro_Marketplace_Block_Adminhtml_Sellerlist_Edit_Tab_Sellerorders extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('seller_orders');
        $this->setUseAjax(true);
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel("sales/order_grid_collection");
        $user = Mage::getModel("marketplace/list")->load($this->getRequest()->getParam("id"));
        $optionid = Mage::helper("marketplace")->getProuctVendorid($user->getSellerAdminId());
        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
        $productIDs = $productCollection->getAllIds();
        $orderIds = array();
        foreach ($collection as $orderCollection) {
            foreach ($orderCollection->getAllItems() as $item) {
                $itemId = $item->getData("product_id");

                if (in_array($itemId, $productIDs)) {
                    $orderIds[] = $item->getOrderId();
                }
            }
        }
		if(empty($orderIds)){ $orderIds = ''; }
        $collection = Mage::getResourceModel("sales/order_grid_collection");
        $collection->addFieldtoFilter("entity_id", array("in", $orderIds));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('real_order_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('sales')->__('Purchased From (Store)'),
                'index' => 'store_id',
                'type' => 'store',
                'store_view' => true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/ordergrid', array('_current' => true));
    }

}
