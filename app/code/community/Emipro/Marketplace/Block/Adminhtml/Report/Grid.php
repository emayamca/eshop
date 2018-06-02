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

class Emipro_Marketplace_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("reportGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('marketplace/sellerbalancesheet')->getCollection();
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            $collection->addFieldToFilter('seller_id', $seller_id);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareTotals($columns = null) {
        $columns = explode(',', $columns);
        if (!$columns) {
            return;
        }
        $this->_countTotals = true;
        $totals = new Varien_Object();
        $fields = array();
        foreach ($columns as $column) {
            $fields[$column] = 0;
        }
        foreach ($this->getCollection() as $item) {
            foreach ($fields as $field => $value) {
                $fields[$field]+=$item->getData($field);
            }
        }
        $totals->setData($fields);
        $this->setTotals($totals);
        return;
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('marketplace')->__('Transaction ID'),
            'index' => 'id',
            'type' => 'number',
            'width' => '40',
            'filter_index' => 'id',
        ));
        $this->addColumn('date', array(
            'header' => Mage::helper('marketplace')->__('Date'),
            'width' => '80px',
            'type' => 'datetime',
            'index' => 'date',
            'filter_index' => 'date',
        ));
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->addColumn('seller_id', array(
                'header' => Mage::helper('marketplace')->__('Store Name'),
                'index' => 'seller_id',
                'type' => 'options',
                'width' => '200px',
                'options' => $this->_getSellerName(),
                'filter_index' => 'seller_id',
                'filter_condition_callback' => array($this, '_storenameFilter'),
            ));
        }
        $this->addColumn('order_id', array(
            'header' => Mage::helper('marketplace')->__('Order No.'),
            'index' => 'order_id',
            'filter_index' => 'order_id',
        ));
        $this->addColumn('credit', array(
            'header' => Mage::helper('marketplace')->__('Seller Credit'),
            'width' => '90px',
            'index' => 'credit',
        ));
        $this->addColumn('debit', array(
            'header' => Mage::helper('marketplace')->__('Seller Debit'),
            'index' => 'debit',
        ));

        $this->addColumn('total', array(
            'header' => Mage::helper('marketplace')->__('Seller Balance'),
            'index' => 'total',
            'renderer' => 'Emipro_Marketplace_Block_Adminhtml_Report_Renderer_Balancetotal',
            'width' => '140px',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('summary', array(
            'header' => Mage::helper('customer')->__('Summary'),
            'width' => '150px',
            'index' => 'summary',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('marketplace')->__('CSV'));

        return parent::_prepareColumns();
    }

    protected function _getSellerName() {
        return Mage::helper('marketplace')->getsellerDropdown();
    }

    public function _storenameFilter($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where("main_table.seller_id like ?", "%$value%");

        return $this;
    }

}
