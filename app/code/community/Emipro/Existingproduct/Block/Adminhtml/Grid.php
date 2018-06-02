<?php
class Emipro_Existingproduct_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
        $this->setFilterVisibility(false);
        //$this->setPagerVisibility(false);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	protected function _prepareLayout()
	{
		//$this->unsetChild('reset_filter_button');
		//$this->unsetChild('search_button');
	}
    protected function _prepareCollection()
    {
		$search=$this->getRequest()->getPost("search_product");
		
		if($search){
		Mage::getSingleton('core/session')->setSearchProductSession($search);
		}else{
		$search = Mage::getSingleton('core/session')->getSearchProductSession();
		}
        if($search && strlen($search) >= 4 ){
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToFilter('status', array('eq' => 1))
            ->addFieldToFilter(array(
                              array('attribute' => 'name', 'like' => "%".$search."%"),
                              array('attribute' => 'upc', 'like' => "%".$search."%"),
                              array('attribute' => 'esin', 'like' => "%".$search."%")
                              ))
             /*->groupByAttribute('esin')
             ->groupByAttribute('upc')*/
            ;

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
		}
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        /*$this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));*/
        $this->addColumn('thumbnail', array(
            'header' => Mage::helper('catalog')->__('Image'),
            'align' => 'left',
            'index' => 'thumbnail',
            'width'     => '97',
            'renderer' => 'Emipro_Existingproduct_Block_Adminhtml_Catalog_Product_Renderer_Image'
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
                'sortable'  => false
        ));
        /*$this->addColumn('esin',
            array(
                'header'=> Mage::helper('catalog')->__('ESIN'),
                'index' => 'esin',
                'sortable'  => false
        ));*/
        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
                    'sortable'  => false
            ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '150px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
                'sortable'  => false
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
                'sortable'  => false
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
                'sortable'  => false
        ));
        $this->addColumn('upc',
            array(
                'header'=> Mage::helper('catalog')->__('UPC'),
                'index' => 'upc',
                'width' => '100px',
                'sortable'  => false
        ));
        $link = $this->getUrl('adminhtml/catalog_product/duplicate', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>'$entity_id'));
        $this->addColumn('action_edit', array(
            'header'   => $this->helper('catalog')->__('Action'),
            'width'    => 100,
            'sortable' => false,
            'filter'   => false,
            'type'     => 'action',
            'actions'  => array(
                array(
                    'url'     => $link,
                    'caption' => $this->helper('catalog')->__('Sell Existing'),
                ),
            )
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        /*return $this->getUrl('adminhtml/catalog_product/duplicate', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );*/
    }
    
}
