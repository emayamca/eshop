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

class Emipro_Marketplace_Block_Adminhtml_Questionanswer_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('questionanswergrid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('marketplace/questionanswer')->getCollection();
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            $collection->addFieldToFilter('seller_id', $sellerid);
        }
        $sort = $this->getRequest()->getParam('sort');
        if (trim($sort) == '') {
            $collection->getSelect()->order('id DESC');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('date', array(
            'header' => Mage::helper('marketplace')->__('Date'),
            'type' => 'datetime',
            'index' => 'date',
        ));
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if (!$sellerid) {
            $this->addColumn('seller_id', array(
                'header' => Mage::helper('marketplace')->__('Store Name'),
                'index' => 'seller_id',
                'type' => 'options',
                'options' => Mage::helper('marketplace')->getsellerDropdown(),
            ));
            $this->addColumn('customer_email', array(
                'header' => Mage::helper('marketplace')->__('Customer Email'),
                'index' => 'customer_email',
            ));
        }
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('marketplace')->__('Product SKU'),
            'index' => 'product_sku',
        ));
        $this->addColumn('subject', array(
            'header' => Mage::helper('marketplace')->__('Subject'),
            'index' => 'subject',
        ));
        $this->addColumn('question', array(
            'header' => Mage::helper('marketplace')->__('Question'),
            'width' => '200px',
            'index' => 'question',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('marketplace')->__('Status'),
            'width' => '100px',
            'index' => 'status',
            'type' => 'options',
            'options' => array('pending' => 'Pending', 'answered' => 'Answered'),
        ));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction() {
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->setMassactionIdField('id');
            $this->getMassactionBlock()->setFormFieldName('id');

            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('marketplace')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete', array('' => '')),
                'confirm' => Mage::helper('marketplace')->__('Are you sure?')
            ));
            return $this;
        }
    }

}
