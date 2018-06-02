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

class Emipro_SellerHelpdesk_Block_Adminhtml_SellerHelpdesk_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $SellerId = Mage::helper("marketplace")->getSellerIdfromLoginUser();
        $roleId = Mage::getSingleton('admin/session')->getUser()->getRoles();
        $roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
        if ($roleName != "Administrators") {
            $collection = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk')->getCollection()->addFieldToFilter("seller_id", array('eq' => $SellerId));
            $this->setCollection($collection);
        } else {
            $collection = Mage::getResourceModel('emipro_sellerhelpdesk/sellerhelpdesk_collection');
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $roleId = Mage::getSingleton('admin/session')->getUser()->getRoles();
        $roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();

        $this->addColumn('id', array(
            'header' => Mage::helper('emipro_sellerhelpdesk')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
            'display' => 'hidden',
        ));

        if ($roleName == "Administrators") {
            $this->addColumn('seller_id', array(
                'header' => Mage::helper('emipro_sellerhelpdesk')->__('Seller'),
                'index' => 'seller_id',
                'width' => '100',
                'type' => 'options',
                'options' => Mage::helper('marketplace')->getsellerDropdown(),
            ));
        }
        $this->addColumn('subject', array(
            'header' => Mage::helper('emipro_sellerhelpdesk')->__('Subject'),
            'align' => 'left',
            'index' => 'subject',
        ));
        $this->addColumn('status_id', array(
            'header' => Mage::helper('emipro_sellerhelpdesk')->__('Status'),
            'align' => 'left',
            'index' => 'status_id',
            'type' => 'options',
            'options' => Mage::helper('emipro_sellerhelpdesk')->getTicketstatus(),
        ));
        $this->addColumn('date', array(
            'header' => Mage::helper('emipro_sellerhelpdesk')->__('Last Updated Date'),
            'align' => 'left',
            'index' => 'date',
            'filter_condition_callback' => array($this, '_DateFilter'),
            'renderer' => 'Emipro_SellerHelpdesk_Block_Adminhtml_Renderer_LastUpdatedDate',
        ));

        parent::_prepareColumns();
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');


        $this->getMassactionBlock()->addItem('Delete', array(
            'label' => Mage::helper('emipro_sellerhelpdesk')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete')
        ));
        return $this;
    }

    protected function _DateFilter($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where(
                "date like ?", "%$value%");
        return $this;
    }

}
