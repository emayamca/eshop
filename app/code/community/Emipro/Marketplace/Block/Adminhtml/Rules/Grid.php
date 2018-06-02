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

class Emipro_Marketplace_Block_Adminhtml_Rules_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("sellerruleGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('marketplace/sellerrule')->getCollection();
        $sort = $this->getRequest()->getParam('sort');
        if (trim($sort) == '') {
            $collection->getSelect()->order('id DESC');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('seller_id', array(
            'header' => Mage::helper('customer')->__('Store Name'),
            'index' => 'seller_id',
            'width' => '100',
            'type' => 'options',
            'options' => Mage::helper('marketplace')->getsellerDropdown(),
        ));

        $this->addColumn('commission_type', array(
            'header' => Mage::helper('customer')->__('Commission Type'),
            'width' => '100',
            'index' => 'commission_type',
            'type' => 'options',
            'options' => array('0' => 'Percent', '1' => 'Fixed'),
        ));
        $this->addColumn('commission', array(
            'header' => Mage::helper('customer')->__('Commission'),
            'width' => '100',
            'type' => 'number',
            'index' => 'commission',
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
