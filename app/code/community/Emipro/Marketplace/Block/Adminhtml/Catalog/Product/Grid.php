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

class Emipro_Marketplace_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid {

    public function setCollection($collection) {
        $collection->addAttributeToSelect('vendor_id');
        $collection->addAttributeToSelect('upc');
        $store = $this->_getStore();
        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $user = Mage::getSingleton('admin/session')->getUser();
            $optionid = Mage::helper("marketplace")->getProuctVendorid($user->getUserId());
            if ($optionid) {
                $collection->addAttributeToFilter('vendor_id', $optionid);
            } else {
                $collection = '';
            }
        }
        $collection->joinAttribute('vendor_id', 'catalog_product/vendor_id', 'entity_id', null, 'left', $store->getId());
        $collection->joinAttribute('marketplace_product_approve', 'catalog_product/marketplace_product_approve', 'entity_id', null, 'left', $store->getId());
        $collection->getSelect()->joinLeft(array('s' => Mage::getConfig()->getTablePrefix() . 'seller'), 'at_vendor_id.value = s.product_vendor_id', array('sellerid' => 's.id'));
        //$collection->getSelect()->group('e.entity_id');
        parent::setCollection($collection);
    }

    public function _prepareColumns() {
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->addColumnAfter('sellerid', array(
                'header' => Mage::helper('catalog')->__('Store Name'),
                'index' => 'sellerid',
                'type' => 'options',
                'options' => Mage::helper('marketplace')->getSellerDropdown(),
                'filter_condition_callback' => array($this, '_sellerFilter'),
                    ), 'status');
        }
        $aftercol = 'status';
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $aftercol = 'sellerid';
        }
        $this->addColumnAfter('marketplace_product_approve', array(
            'header' => Mage::helper('catalog')->__('Marketplace<br/>Approved'),
            'index' => 'marketplace_product_approve',
            'type' => 'options',
            'width' => '20px',
            'options' => array('No', 'Yes'),
                ), $aftercol);

        $this->addColumnAfter('upc', array(
            'header' => Mage::helper('catalog')->__('UPC'),
            'index' => 'upc',
            'width' => '80',
                ), 'marketplace_product_approve');

        return parent::_prepareColumns();
    }

    public function _sellerFilter($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where("s.id = $value");
        return $this;
    }

}
