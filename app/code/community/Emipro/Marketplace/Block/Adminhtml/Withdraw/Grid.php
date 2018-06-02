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

class Emipro_Marketplace_Block_Adminhtml_Withdraw_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("sellerwithdrawGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
            $collection = Mage::getModel('marketplace/withdraw')->getCollection()->addFieldToFilter('seller_id', $seller_id);
        } else {
            $collection = Mage::getModel('marketplace/withdraw')->getCollection();
        }
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
            'width' => '40px'
        ));
        $this->addColumn('date', array(
            'header' => Mage::helper('customer')->__('Date'),
            'width' => '100',
            'type' => 'datetime',
            'index' => 'date',
        ));
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->addColumn('seller_id', array(
                'header' => Mage::helper('customer')->__('Store Name'),
                'index' => 'seller_id',
                'width' => '200px',
                'type' => 'options',
                'options' => Mage::helper('marketplace')->getsellerDropdown(),
            ));
        }
        $this->addColumn('amount', array(
            'header' => Mage::helper('customer')->__('Amount'),
            'width' => '100',
            'index' => 'amount',
            'type' => 'number',
        ));
        $this->addColumn('request_status', array(
            'header' => Mage::helper('customer')->__('Request Status'),
            'width' => '100',
            'type' => 'options',
            'options' => array("approved" => "Approved", "pending" => "Pending"),
            'index' => 'request_status',
        ));
        $this->addColumn('summary', array(
            'header' => Mage::helper('customer')->__('Summary'),
            'width' => '150px',
            'type' => 'textarea',
            'index' => 'summary',
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
    }

}
