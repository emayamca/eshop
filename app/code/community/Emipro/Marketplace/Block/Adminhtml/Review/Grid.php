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

class Emipro_Marketplace_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultDir('DESC');
        $this->setDefaultSort('date');
        $this->setSaveParametersInSession(true);
    }

    public function _prepareCollection() {
        $collection = Mage::getModel('marketplace/sellerreview')->getCollection();
        $collection->getSelect()->joinLeft(array('ce1' => Mage::getConfig()->getTablePrefix() . 'customer_entity_varchar'), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'value'));
        $collection->addFieldToFilter('ce1.attribute_id', 5);
        $sort = $this->getRequest()->getParam('sort');
        if (trim($sort) == '') {
            $collection->getSelect()->order('id DESC');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function _prepareColumns() {
        $this->addColumn('created_date', array(
            'header' => Mage::helper('marketplace')->__('Created Date'),
            'index' => 'created_date',
            'type' => 'datetime',
        ));
        $this->addColumn('seller_id', array(
            'header' => Mage::helper('marketplace')->__('Store Name'),
            'index' => 'seller_id',
            'type' => 'options',
            'options' => Mage::helper('marketplace')->getsellerDropdown(),
        ));
        $this->addColumn('customer_id', array(
            'header' => Mage::helper('marketplace')->__('Customer Name'),
            'index' => 'firstname',
            'filter_condition_callback' => array($this, '_customerFilter'),
        ));
        $this->addColumn('text', array(
            'header' => Mage::helper('marketplace')->__('Review Text'),
            'index' => 'text',
        ));
        $this->addColumn('rating', array(
            'header' => Mage::helper('marketplace')->__('Ratings'),
            'index' => 'rating',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'Emipro_Marketplace_Block_Adminhtml_Review_Rating',
        ));
        $this->addColumn('approved', array(
            'header' => Mage::helper('marketplace')->__('Approved'),
            'index' => 'approved',
            'type' => 'options',
            'options' => array('0' => 'No', '1' => 'Yes')
        ));
    }

    public function _customerFilter($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where("ce1.value like  '%" . $value . "%' ");
        return $this;
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
            $this->getMassactionBlock()->addItem('status_approve', array(
                'label' => Mage::helper('marketplace')->__('Approve'),
                'url' => $this->getUrl('*/*/massApprove', array('' => '')),
                'confirm' => Mage::helper('marketplace')->__('Are you sure to approve ?')
            ));
            $this->getMassactionBlock()->addItem('status_disapprove', array(
                'label' => Mage::helper('marketplace')->__('Disapprove'),
                'url' => $this->getUrl('*/*/massDisapprove', array('' => '')),
                'confirm' => Mage::helper('marketplace')->__('Are you sure to disapprove ?')
            ));

            return $this;
        }
    }

}
