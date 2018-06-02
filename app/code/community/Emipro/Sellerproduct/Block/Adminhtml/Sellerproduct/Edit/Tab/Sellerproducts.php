<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Sellerproduct 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Sellerproduct_Block_Adminhtml_Sellerproduct_Edit_Tab_Sellerproducts extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('sellerproduct');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultFilter(array('in_products' => 1));
        $this->setSaveParametersInSession(false);
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('attribute_set_id')
                ->addAttributeToSelect('type_id');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
        }
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                    'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore
            );
            $collection->joinAttribute(
                    'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId()
            );
            $collection->joinAttribute(
                    'status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId()
            );
            $collection->joinAttribute(
                    'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId()
            );
            $collection->joinAttribute(
                    'price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId()
            );
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        $param = $this->getRequest()->getParam("id");
        if(isset($param))
        {
            $user = Mage::getModel("marketplace/list")->load($param);
            $optionid = Mage::helper("marketplace")->getProuctVendorid($user->getSellerAdminId());
            $collection->addAttributeToFilter('vendor_id', $optionid);
        }else
        {
            if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
                $user = Mage::getSingleton('admin/session')->getUser();
                $optionid = Mage::helper("marketplace")->getProuctVendorid($user->getUserId());
                if ($optionid) {
                    $collection->addAttributeToFilter('vendor_id', $optionid);
                } 
            }
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _addColumnFilterToCollection($column) {

        if ($column->getId() == 'in_products') {
            $ids = $this->_getSelectedProduct();
            if (empty($ids)) {
                $ids = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $ids));
            } else {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $ids));               
            }   
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns() {

        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'products',
            'values' => $this->_getSelectedProduct(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'width' => '50px',
            'type' => 'number',
            'index' => 'entity_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name', array(
                'header' => Mage::helper('catalog')->__('Name in %s', $store->getName()),
                'index' => 'custom_name',
            ));
        }

        $this->addColumn('type', array(
            'header' => Mage::helper('catalog')->__('Type'),
            'width' => '60px',
            'index' => 'type_id',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();

        $this->addColumn('set_name', array(
            'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width' => '100px',
            'index' => 'attribute_set_id',
            'type' => 'options',
            'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'index' => 'price',
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty', array(
                'header' => Mage::helper('catalog')->__('Qty'),
                'width' => '100px',
                'type' => 'number',
                'index' => 'qty',
            ));
        }

        $this->addColumn('visibility', array(
            'header' => Mage::helper('catalog')->__('Visibility'),
            'width' => '70px',
            'index' => 'visibility',
            'type' => 'options',
            'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '70px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header' => Mage::helper('catalog')->__('Websites'),
                'width' => '100px',
                'sortable' => false,
                'index' => 'websites',
                'type' => 'options',
                'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
        $this->addColumn('position', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'position',
            'width' => 60,
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'position',
            'editable' => true,
            'edit_only' => true
        ));
        return parent::_prepareColumns();
    }


    public function getGridUrl() {
        return $this->getUrl('adminhtml/sellerproduct/productsgrid', array('_current' => true));
    }

    protected function _getSelectedProduct() {
        $product_Ids = array_keys($this->getSelectedProducts());
        return $product_Ids;
    }
    
    public function getSelectedProducts() {
        $prodId=array();
        $id = $this->getRequest()->getParam('id');
        if(isset($id)){
            $option = $id;
        }
        else{
            if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
                $user = Mage::getSingleton('admin/session')->getUser();
                $seller = Mage::getModel('marketplace/list')->load($user->getUserId(), 'seller_admin_id');
                $option = $seller->getId();
            }
        }
        if (isset($option) && !empty($option)) {
            $sql = "select * from " . Mage::getConfig()->getTablePrefix() . "emipro_sellerproduct where seller_id='$option'";
            $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
        }
        foreach ($result as $obj) {
            $prodId[$obj['product_id']] = array('position' => $obj['position']);
        }
        return $prodId;
    }
}
