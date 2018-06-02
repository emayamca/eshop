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

class Emipro_Marketplace_Block_Adminhtml_Sellerlist_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("sellerlistGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $prefix = Mage::getConfig()->getTablePrefix();
        $collection = Mage::getModel('marketplace/list')->getCollection();
        $collection->getSelect()
                ->join(array('sd' => $prefix . 'seller_detail'), 'main_table.id = sd.seller_id', array("name" => "sd.firstname", "email" => "sd.email", "postcode" => "sd.postcode", "telephone" => "sd.telephone", "country_id" => "sd.country_id", "default_product_approve" => "sd.default_product_approve", "store_name" => "sd.store_name", "store_url" => "sd.store_url"));
        $collection->getSelect()
                ->join(array('w' => $prefix . 'core_website'), 'w.website_id =main_table.website_id', array("website_name" => "w.name"));

        $sort = $this->getRequest()->getParam('sort');
        if (trim($sort) == '') {
            $collection->getSelect()->order('id DESC');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '30',
            'index' => 'id',
            'filter_index' => 'main_table.id',
            'type' => 'number',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('marketplace')->__('Website'),
                'index' => 'website_id',
                'type' => 'options',
                'options' => $this->_getWebsitetoArray(),
                'filter_condition_callback' => array($this, '_websiteFilter'),
            ));
        }
        $this->addColumn('store_name', array(
            'header' => Mage::helper('customer')->__('Store Name'),
            'index' => 'store_name'
        ));
        $this->addColumn('store_url', array(
            'header' => Mage::helper('customer')->__('Store Url'),
            'index' => 'store_url'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('customer')->__('Name'),
            'index' => 'name'
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '80',
            'index' => 'email'
        ));
        $this->addColumn('seller_admin_id', array(
            'header' => Mage::helper('customer')->__('Admin<br/>User ID'),
            'width' => '80',
            'index' => 'seller_admin_id'
        ));
        $this->addColumn('telephone', array(
            'header' => Mage::helper('customer')->__('Telephone'),
            'width' => '100',
            'index' => 'telephone'
        ));
        /*
          $this->addColumn('postcode', array(
          'header'    => Mage::helper('customer')->__('ZIP Code'),
          'width'     => '90',
          'index'     => 'postcode',
          ));
         */
        $this->addColumn('country_id', array(
            'header' => Mage::helper('customer')->__('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'country_id',
        ));

        /* $this->addColumn('region', array(
          'header'    => Mage::helper('customer')->__('State/Province'),
          'width'     => '100',
          'index'     => 'region',
          )); */

        $this->addColumn('email_verify_status', array(
            'header' => Mage::helper('marketplace')->__('Email Verification'),
            'width' => '50',
            'index' => 'email_verify_status',
            'filter_condition_callback' => array($this, '_emailverifyFilter'),
        ));
        $this->addColumn('default_product_approve', array(
            'header' => Mage::helper('marketplace')->__('Default <br/>Product Approve'),
            'index' => 'default_product_approve',
            'width' => '40',
            'type' => "options",
            'options' => array('1' => 'Yes', '0' => 'No'),
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('marketplace')->__('Status'),
            'width' => '100',
            'type' => "options",
            'index' => 'status',
            'filter_index' => 'seller.status',
            'filter_condition_callback' => array($this, '_statusFilter'),
            'options' => Mage::helper("marketplace")->getStatus(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('customer')->__('Action'),
            'width' => '100',
            'renderer' => 'Emipro_Marketplace_Block_Adminhtml_Sellerlist_Grid_Renderer_Edit',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _statusFilter($collection, $column) {
        $this->getCollection()->getSelect()->where(new Zend_Db_Expr("main_table.status like '{$column->getFilter()->getValue()}'"));
        return $this;
    }

    protected function _emailverifyFilter($collection, $column) {
        $this->getCollection()->getSelect()->where(new Zend_Db_Expr("main_table.email_verify_status like '{$column->getFilter()->getValue()}%'"));
        return $this;
    }

    protected function _getWebsitetoArray() {
        $collection = Mage::getModel('core/website')->getCollection();
        if ($collection->count() > 0) {
            $array = array();
            foreach ($collection->getData() as $item) {
                $array[$item['website_id']] = $item['name'];
            }
            return $array;
        }
    }

    protected function _websiteFilter($collection, $column) {
        $this->getCollection()->getSelect()->where(new Zend_Db_Expr("main_table.website_id = '{$column->getFilter()->getValue()}'"));
        return $this;
    }

}
