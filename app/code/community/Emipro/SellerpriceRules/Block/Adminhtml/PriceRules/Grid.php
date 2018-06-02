<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @Author Emipro Technologies Private Limited
 * @Category Emipro
 * @Package  Emipro_SellerpriceRules
 * @License http://shop.emiprotechnologies.com/license-agreement/
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class Emipro_SellerpriceRules_Block_Adminhtml_PriceRules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     * Set sort settings
     */
    public function __construct()
    {
		
        parent::__construct();
        $this->setId('promo_catalog_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
		     
    }

    /**
     * Add websites to catalog rules collection
     * Set collection
     *
     * @return Mage_Adminhtml_Block_Promo_Catalog_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_CatalogRule_Model_Mysql4_Rule_Collection */
		
		
		$collection = Mage::getModel('catalogrule/rule')->getResourceCollection();
		if(Mage::helper('marketplace')->getSellerIdfromLoginUser())
		{
			$collection->addWebsitesToResult();
		}	
		
		else
		{
		$collection->getSelect()->join(array("seller"=>Mage::getConfig()->getTablePrefix().'seller'),'main_table.seller_admin_id = seller.seller_admin_id',array("seller.id"))
		->join(array("seller_detail"=>Mage::getConfig()->getTablePrefix()."seller_detail"),"seller.id=seller_detail.seller_id",array("seller_detail.firstname","seller_detail.lastname","seller_detail.store_url"));
	
		$this->setCollection($collection);
		}
		return  parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return Mage_Adminhtml_Block_Promo_Catalog_Grid
     */
    protected function _prepareColumns()
    {
       $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
		
		if(!$sellerid)
		{
			$this->addColumn('seller_name', array(
            'header'    => Mage::helper('catalogrule')->__('Seller Name'),
            'align'     =>'left',
            'width'     => '150px',
            'index'		=> 'firstname',
          'filter_condition_callback' => array($this, '_SellerNameFilter'),
           'renderer'     =>'Emipro_SellerpriceRules_Block_Adminhtml_Renderer_Sellerinfo',
			));
			
			$this->addColumn('store_url', array(
            'header'    => Mage::helper('catalogrule')->__('Store Url'),
            'align'     =>'left',
            'width'     => '150px',
           'index'		=> 'store_url',
           'filter_condition_callback' => array($this, '_storeurlFilter'),
           'renderer'     =>'Emipro_SellerpriceRules_Block_Adminhtml_Renderer_Sellerurl',
			));
		}	

        $this->addColumn('name', array(
            'header'    => Mage::helper('catalogrule')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('catalogrule')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('catalogrule')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('catalogrule')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'filter_index'=>'main_table.is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('catalogrule')->__('Active'),
                0 => Mage::helper('catalogrule')->__('Inactive')
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('rule_website', array(
                'header'    => Mage::helper('catalogrule')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
		return $this->getUrl('adminhtml/promo_catalog/edit', array('id' => $row->getRuleId(),"seller"=>1));
    }

}
