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

class Emipro_Marketplace_Block_Adminhtml_Commission_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("commissionGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $prefix = Mage::getConfig()->getTablePrefix();
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        $collection = Mage::getModel('sales/order_item')->getCollection();
        $collection->getSelect()
                ->joinLeft(array('order' => $prefix . 'sales_flat_order'), 'order.entity_id = main_table.order_id', array('increment_id' => 'order.increment_id'))
                ->joinLeft(array('comm' => $prefix . 'seller_commission'), 'comm.order_increment_id = order.increment_id AND comm.product_id = main_table.product_id', array('uniqid' => 'comm.id'))
                ->joinRight(array('commission' => $prefix . 'seller_commission'), 'commission.id = comm.id', array('id' => 'commission.id'));

        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('order_id', 'sku', 'name', 'base_price','base_price_incl_tax'))->columns(array('commission.*'));
        if (Mage::helper('core')->isModuleEnabled('Emipro_FlexiShipping')) {
           // $collection->getSelect()->columns(array('base_shipping_charge'));
        }
        if ($seller_id) {
            $collection->getSelect()->where("comm.seller_id=" . $seller_id);
        }
        $collection->getSelect()->group('commission.id');
        $sort = $this->getRequest()->getParam('sort');
        if (trim($sort) == '') {
            $collection->getSelect()->order('id DESC');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('customer')->__('Transaction Id'),
            'index' => 'id',
            'type' => 'number',
            'filter_index' => 'commission.id',
        ));

        $this->addColumn('date', array(
            'header' => Mage::helper('customer')->__('Date'),
            'width' => '80px',
            'type' => 'datetime',
            'index' => 'date',
            'filter_index' => 'commission.date',
        ));
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->addColumn('seller_id', array(
                'header' => Mage::helper('marketplace')->__('Store Name'),
                'index' => 'seller_id',
                'type' => 'options',
                'width' => '50',
                'options' => $this->_getStorename(),
                'filter_index' => 'commission.seller_id',
            ));
        }

        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('marketplace')->__('Order No.'),
            'index' => 'order_increment_id',
            'filter_index' => 'commission.order_increment_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('marketplace')->__('Product Name'),
            'width' => '100',
            'index' => 'name',
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('marketplace')->__('Sku'),
            'width' => '100',
            'index' => 'sku',
        ));
       
        $checkinclude = Mage::getStoreConfig('marketplace_setting/marketplace/catalogprice_includes_tax');
        if($checkinclude){
            $this->addColumn('base_price_incl_tax', array(
                'header' => Mage::helper('marketplace')->__('Price Incl Tax'),
                'width' => '50',
                'type' => 'number',
                'index' => 'base_price_incl_tax',
            ));
        }else{
            $this->addColumn('base_price', array(
            'header' => Mage::helper('marketplace')->__('Price'),
            'width' => '50',
            'type' => 'number',
            'index' => 'base_price',
             ));
        }
        
        if (Mage::helper('core')->isModuleEnabled('Emipro_FlexiShipping')) {
            $this->addColumn('base_shipping_charge', array(
                'header' => Mage::helper('marketplace')->__('Shipping<br/>Charge'),
                'width' => '50',
                'type' => 'number',
                'index' => 'base_shipping_charge',
                'filter_index' => 'commission.base_shipping_charge',
            ));
        }
        $this->addColumn('qty', array(
            'header' => Mage::helper('marketplace')->__('Qty'),
            'width' => '100',
            'type' => 'number',
            'index' => 'qty',
            'filter_index' => 'commission.qty',
        ));

        $this->addColumn('commission_amt', array(
            'header' => Mage::helper('marketplace')->__('Commission'),
            'width' => '90',
            'index' => 'commission_amt',
            'filter_index' => 'commission.commission_amt',
        ));
        $this->addColumn('seller_credit', array(
            'header' => Mage::helper('marketplace')->__('Seller Credit'),
            'width' => '90',
            'type' => 'number',
            'index' => 'seller_credit',
            'filter_index' => 'commission.seller_credit',
        ));
        $this->addColumn('seller_debit', array(
            'header' => Mage::helper('marketplace')->__('Seller Debit'),
            'width' => '90',
            'type' => 'number',
            'index' => 'seller_debit',
            'filter_index' => 'commission.seller_debit',
        ));
        $this->addColumn('summary', array(
            'header' => Mage::helper('customer')->__('Summary'),
            'width' => '150',
            'type' => 'textarea',
            'index' => 'summary',
            'filter_index' => 'commission.summary',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('marketplace')->__('CSV'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        //return $this->getUrl('*/*/edit', array('id'=>isset($row['id']) ? $row['id'] : ''));
    }

    protected function _getStorename() {
        return Mage::helper('marketplace')->getsellerDropdown();
    }

}
