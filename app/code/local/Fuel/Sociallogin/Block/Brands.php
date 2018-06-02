<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fuel
 * @package     Fuel_Brands
 */
/**
 * This Block for helps to display Brands products
 */
class Fuel_Sociallogin_Block_Brands extends Mage_Core_Block_Template {
	const SUPDEALS_BLK_IDX_LABEL = 'label';
    const SUPDEALS_BLK_IDX_FROM = 'special_from_date';
    const SUPDEALS_BLK_IDX_TOOLBAR = 'toolbar';
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
    
    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _prepareLayout() {
        /**
         * SEO Meta Keywords, title, descriptions for deal page
         */
        $STORE_CONFIG_SEO_META_TITLE = Mage::getStoreConfig ( 'superdeals/seosettings/seotitle' );
        /**
         * getting seo keyword
         */
        $STORE_CONFIG_SEO_META_KEYWORDS = Mage::getStoreConfig ( 'superdeals/seosettings/seokeyword' );
        /**
         * getting seo description
         */
        $STORE_CONFIG_SEO_META_DESCRIPTION = Mage::getStoreConfig ( 'superdeals/seosettings/seodescription' );
        /**
         * getting head block
         */
        if ($headBlock = $this->getLayout ()->getBlock ( 'head' )) {
            /**
             * set title
             */
            $headBlock->setTitle ( $STORE_CONFIG_SEO_META_TITLE );
            /**
             * set keywords
             */
            $headBlock->setKeywords ( $STORE_CONFIG_SEO_META_KEYWORDS );
            /**
             * set description
             */
            $headBlock->setDescription ( $STORE_CONFIG_SEO_META_DESCRIPTION );
        }
        /**
         * breadcrumbs
         */
        $breadcrumbs = $this->getLayout ()->getBlock ( 'breadcrumbs' );
        /**
         * add crumbs
         */
        $breadcrumbs->addCrumb ( 'home', array (
            static::SUPDEALS_BLK_IDX_LABEL => Mage::helper ( 'cms' )->__ ( 'Home' ),
            'title' => Mage::helper ( 'cms' )->__ ( 'Home Page' ),
            'link' => Mage::getBaseUrl () 
        ) );
        /**
         * addcrumbs deals
         */
        $breadcrumbs->addCrumb ( 'deals', array (
            static::SUPDEALS_BLK_IDX_LABEL => 'Deals',
            'title' => 'Deals' 
        ) );
        return parent::_prepareLayout ();
    }

    
    /**
     * Function to get product collection
     *
     * This Function will return the product collection
     *
     * @return array
     */
    protected function _getProductCollection() {
        /**
         * category id
         */
        $cat = $this->getRequest ()->getParam ( 'id' );
        /**
         * get value
         */
        $value = $this->getRequest ()->getParam ( 'value' );
        /**
         * get order
         */
        $order = $this->getRequest ()->getParam ( 'order' );
        /**
         * checking whether null or not
         */
        if (is_null ( $this->_productCollection )) {
            $layer = $this->getLayer ();
            if ($this->getShowRootCategory ()) {
                /**
                 * set category id
                 */
                $this->setCategoryId ( Mage::app ()->getStore ()->getRootCategoryId () );
            }
            if (Mage::registry ( 'product' )) {
                /**
                 * registry product
                 */
                $categories = Mage::registry ( 'product' )->getCategoryCollection ()->setPage ( 1, 1 )->load ();
                if ($categories->count ()) {
                    $this->setCategoryId ( current ( $categories->getIterator () ) );
                }
            }
            $origCategory = null;
            if ($this->getCategoryId ()) {
                $category = Mage::getModel ( 'catalog/category' )->load ( $this->getCategoryId () );
                /**
                 * getting category id
                 */
                if ($category->getId ()) {
                    /**
                     * get current category
                     */
                    $origCategory = $layer->getCurrentCategory ();
                    /**
                     * set current category
                     */
                    $layer->setCurrentCategory ( $category );
                }
            }
            $this->_productCollection = $layer->getProductCollection ();
            /**
             * get store id
             */
            $storeId = Mage::app ()->getStore ()->getId ();
            /**
             * get todays date
             */
            $todayDate = Mage::getModel ( 'core/date' )->date ( 'm/d/Y' );
            /**
             * weakdeal
             */
            $weekDeal = date ( 'Y-m-d', strtotime ( $todayDate . ' - 7 days' ) );
            /**
             * monthdeal
             */
            $monthDeal = date ( 'Y-m-d', strtotime ( $todayDate . ' - 30 days' ) );
            
            /**
             * get product collection
             */
            $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect('*');
            /**
             * Set product visibility status
             */
            $collection->setVisibility ( Mage::getSingleton ( 'catalog/product_visibility' )->getVisibleInCatalogIds () );
            if (! empty ( $cat )) {
                /**
                 * check the catagory id is not empty
                 */
                $category = Mage::getModel ( 'catalog/category' )->load ( $cat );
                $collection->addCategoryFilter ( $category );
            }
            /**
             * set store id
             */
            $optionId = Mage::getSingleton('core/session')->getBrands();
            $collection->setStoreId ( $storeId )->addStoreFilter ( $storeId )->addAttributeToFilter('brands', array('eq' => $optionId));
            
            $this->setCollection ( $collection );
        }
        return $this->_productCollection;
    }
    
    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer() {
        $layer = Mage::registry ( 'current_layer' );
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton ( 'catalog/layer' );
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection() {
        /**
         * product collection
         */
        return $this->_getProductCollection ();
    }
    
    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode() {
        /**
         * get current mode
         */
        return $this->getChild ( static::SUPDEALS_BLK_IDX_TOOLBAR )->getCurrentMode ();
    }
    
    /**
     * Need use as _prepareLayout
     */
    protected function _beforeToHtml() {
        /**
         * get toolbar block
         */
        $toolbar = $this->getToolbarBlock ();
        
        /**
         * called prepare sortable parameters
         */
        $collection = $this->_getProductCollection ();
        
        /**
         * use sortable parameters
         */
        if ($orders = $this->getAvailableOrders ()) {
            $toolbar->setAvailableOrders ( $orders );
        }
        if ($sort = $this->getSortBy ()) {
            $toolbar->setDefaultOrder ( $sort );
        }
        if ($dir = $this->getDefaultDirection ()) {
            $toolbar->setDefaultDirection ( $dir );
        }
        if ($modes = $this->getModes ()) {
            $toolbar->setModes ( $modes );
        }
        /**
         * set collection to toolbar and apply sort
         */
        $toolbar->setCollection ( $collection );
        
        $this->setChild ( static::SUPDEALS_BLK_IDX_TOOLBAR, $toolbar );
        Mage::dispatchEvent ( 'catalog_block_product_list_collection', array (
            'collection' => $this->_getProductCollection () 
        ) );
        
        $this->_getProductCollection ()->load ();
        
        return parent::_beforeToHtml ();
    }
    
    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock() {
        /**
         * blockname
         */
        if (($blockName = $this->getToolbarBlockName ()) && ($block = $this->getLayout ()->getBlock ( $blockName ))) {
            return $block;
        }
        return $this->getLayout ()->createBlock ( $this->_defaultToolbarBlock, microtime () );
    }
    
    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml() {
        /**
         * get childhtml
         */
        return $this->getChildHtml ( 'additional' );
    }
    
    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml() {
        /**
         * gettoolbar html
         */
        return $this->getChildHtml ( static::SUPDEALS_BLK_IDX_TOOLBAR );
    }
    
    /**
     * Retrieve list toolbar HTML
     *
     * Passed the collection array
     *
     * @param int $sellerId            
     *
     * @return array
     */
    public function setCollection($collection) {
        $this->_productCollection = $collection;
        return $this;
    }
    /**
     * Retrieve Attribute using the attribute id
     *
     * Passed the attribute id as $code
     *
     * @param varchar $code            
     *
     * @return array
     */
    public function addAttribute($code) {
        /**
         * add attributetoselect code
         */
        $this->_getProductCollection ()->addAttributeToSelect ( $code );
        return $this;
    }
    /**
     * Display deal price information in product detail page
     *
     * Return the layout block in view page
     *
     * @return void
     */
    public function getPriceBlockTemplate() {
        /**
         * getdata priceblock
         */
        return $this->_getData ( 'price_block_template' );
    }
    
    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig() {
        /**
         * get model of catalog config
         */
        return Mage::getSingleton ( 'catalog/config' );
    }
    
    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category            
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (! $this->getAvailableOrders ()) {
            $this->setAvailableOrders ( $category->getAvailableSortByOptions () );
        }
        $availableOrders = $this->getAvailableOrders ();
        if ((! $this->getSortBy ()) && ($categorySortBy = $category->getDefaultSortBy ())) {
            if (! $availableOrders) {
                /**
                 * getting available orders
                 */
                $availableOrders = $this->_getConfig ()->getAttributeUsedForSortByArray ();
            }
            if (isset ( $availableOrders [$categorySortBy] )) {
                $this->setSortBy ( $categorySortBy );
            }
        }
        return $this;
    }
}