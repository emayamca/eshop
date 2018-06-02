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

class Emipro_Advancecommission_Block_Adminhtml_Advancecommission_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('advancecomm_id');
        $this->setDefaultSort('advancecomm_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getResourceModel('emipro_advancecommission/advancecommission_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('advancecomm_id', array(
            'header' => Mage::helper('emipro_advancecommission')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'advancecomm_id',
        ));
        $this->addColumn('seller_id', array(
            'header' => Mage::helper('customer')->__('Seller'),
            'index' => 'seller_id',
            'width' => '100',
            'type' => 'options',
            'options' => Mage::helper('marketplace')->getsellerDropdown(),
        ));

        $this->addColumn('attributeset_id', array(
            'header' => Mage::helper('emipro_advancecommission')->__('Category'),
            'align' => 'left',
            'index' => 'attributeset_id',
            'type' => 'options',
            'options' => Mage::helper('marketplace')->toCategoriesArray(),
        ));

        $this->addColumn('commission_type', array(
            'header' => Mage::helper('customer')->__('Commission Type'),
            'width' => '100',
            'index' => 'commission_type',
            'type' => 'options',
            'options' => array('0' => 'Percent', '1' => 'Fixed'),
        ));
        $this->addColumn('commission', array(
            'header' => Mage::helper('emipro_advancecommission')->__('Commission'),
            'align' => 'left',
            'index' => 'commission',
        ));


        parent::_prepareColumns();
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getAdvancecommId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('advancecomm_id');
        $this->getMassactionBlock()->setFormFieldName('advancecomm_id');


        $this->getMassactionBlock()->addItem('Delete', array(
            'label' => Mage::helper('emipro_advancecommission')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete')
        ));
        return $this;
    }

}
