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

class Emipro_Marketplace_Block_SellerBankdetails extends Mage_Core_Block_Template {

    /*
    *   Seller profile page data 
    */
    public function _construct() {
        //parent::__construct();
        $id = $this->getRequest()->getParam("profile_id");
        $sellerProfile = Mage::getModel("marketplace/seller")->load($id, "seller_id");
        if ($sellerProfile->getSellerId()) {
            if (isset($sellerProfile['meta_description']) && isset($sellerProfile['meta_keywords'])) {
                $head = Mage::app()->getLayout()->getBlock('head');
                if ($head) {
                    $head->setTitle($sellerProfile['store_name'])->setKeywords($sellerProfile['meta_keywords'])->setDescription($sellerProfile['meta_description']);
                }
            }
            $sellerreviews = Mage::getModel('marketplace/sellerreview')->getCollection();
            $sellerreviews->getSelect()->joinLeft(array('ce1' => Mage::getConfig()->getTablePrefix() . 'customer_entity_varchar'), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'value'));
            $sellerreviews->addFieldToFilter('ce1.attribute_id', 5);
            $sellerreviews->addFieldToFilter('main_table.seller_id', $sellerProfile->getSellerId());
            $collection = $sellerreviews;
            $this->_collection = $collection;
        }
    }

    /*
    *   Get Seller in profile page
    */
    
    public function getSeller() {
        $id = $this->getRequest()->getParam("profile_id");
        $sellerProfile = Mage::getModel("marketplace/seller")->load($id, "seller_id");
        if ($sellerProfile->getSellerId()) {
            return $sellerProfile;
        }
        return false;
    }

    /*
    *   Pagination add to Seller Review 
    */
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $limitvalues = Mage::getStoreConfig('catalog/frontend/list_per_page_values');
        $temparray = explode(',', $limitvalues);
        $array = array();
        foreach ($temparray as $item) {
            $array[$item] = $item;
        }
        $array['all'] = 'all';
        $pager->setAvailableLimit($array);
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    protected function _getCollection() {
        return $this->_collection;
    }

    public function getCollection() {
        return $this->_getCollection();
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

}
