<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_FlexiShipping
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class Emipro_FlexiShipping_Block_Adminhtml_SellerRules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
    {
        parent::__construct();
        $this->setId('ship_id');
        $this->setDefaultSort('ship_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

	protected function _prepareCollection()
    {
		
		$SellerId = Mage::getSingleton('admin/session')->getUser()->getUserId();
		$roleId=Mage::getSingleton('admin/session')->getUser()->getRoles();
		$roleName = Mage::getModel('admin/roles')->load($roleId)->getRoleName();
		if(Mage::helper('marketplace')->getSellerIdfromLoginUser())
		{
			$collection = Mage::getModel('emipro_flexishipping/flexiShipping')->getCollection()->addFieldToFilter("seller_admin_id",array('eq'=>$SellerId));
			$this->setCollection($collection);
		}
		else
		{
			$collection = Mage::getModel('emipro_flexishipping/flexiShipping')->getCollection();
		$collection->getSelect()->join(array("seller"=>Mage::getConfig()->getTablePrefix().'seller'),'main_table.seller_admin_id = seller.seller_admin_id',array("seller.id"))
		->join(array("seller_detail"=>Mage::getConfig()->getTablePrefix()."seller_detail"),"seller.id=seller_detail.seller_id",array("seller_detail.firstname","seller_detail.lastname","seller_detail.store_url"));
		$this->setCollection($collection);
		
		$this->setCollection($collection);
		}
		return parent::_prepareCollection();
    }
	protected function _prepareColumns()
    {
		$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
		
		if(!$sellerid)
		{
			$this->addColumn('seller_name', array(
            'header'    => Mage::helper('emipro_flexishipping')->__('Seller Name'),
            'align'     =>'left',
            'width'     => '150px',
            'index'		=> 'firstname',
          'filter_condition_callback' => array($this, '_SellerNameFilter'),
           'renderer'     =>'Emipro_FlexiShipping_Block_Adminhtml_Renderer_Sellerinfo',
			));
			
			$this->addColumn('store_url', array(
            'header'    => Mage::helper('emipro_flexishipping')->__('Store Url'),
            'align'     =>'left',
            'width'     => '150px',
           'index'		=> 'store_url',
           'filter_condition_callback' => array($this, '_storeurlFilter'),
           'renderer'     =>'Emipro_FlexiShipping_Block_Adminhtml_Renderer_Sellerurl',
			));
		}	
		$this->addColumn('name', array(
            'header'    => Mage::helper('emipro_flexishipping')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));
		$this->addColumn('from_date', array(
            'header'    => Mage::helper('emipro_flexishipping')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));
        $this->addColumn('to_date', array(
            'header'    => Mage::helper('emipro_flexishipping')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('emipro_flexishipping')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'filter_index'=>'main_table.is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('emipro_flexishipping')->__('Active'),
                0 => Mage::helper('emipro_flexishipping')->__('Inactive')
            ),
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('emipro_flexishipping')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_id',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }
        parent::_prepareColumns();
        return $this;
    }
	public function getRowUrl($row)
    {
		return $this->getUrl('*/*/edit', array('id' => $row->getShipId()));
    }
	public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    protected function _storeurlFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
		$this->getCollection()->getSelect()
		->where(
            "seller_detail.store_url like ?", "%$value%");
		return $this;
    }
     protected function _SellerNameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
		$this->getCollection()->getSelect()->where(
            "admin_user.firstname like ?", "%$value%");
		return $this;
    }
 }
