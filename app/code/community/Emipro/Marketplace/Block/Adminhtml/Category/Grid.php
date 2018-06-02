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

class Emipro_Marketplace_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('categorygrid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $prefix = Mage::getConfig()->getTablePrefix();
        $collection = Mage::getModel('marketplace/sellercategory')->getCollection();
        $collection->getSelect()->joinLeft(array('eas' => $prefix . 'eav_attribute_set'), 'eas.attribute_set_id=main_table.attributeset_id', array('eti' => 'eas.entity_type_id', 'attribute_set_name' => 'eas.attribute_set_name'));
        $collection->getSelect()->joinLeft(array('cce' => $prefix . 'catalog_category_entity'), 'cce.entity_id=main_table.category_id', array('cceentity_id' => 'cce.entity_id'));
        $collection->getSelect()->joinLeft(array('ccev' => $prefix . 'catalog_category_entity_varchar'), 'ccev.entity_id=cce.entity_id', array('category_name' => 'ccev.value'));
        //$collection->addFieldToFilter('eas.entity_type_id', Mage::getModel('catalog/product')->getResource()->getTypeId());
        $categorynameid = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'name')->getId();
        if($categorynameid){
            $collection->addFieldToFilter('ccev.attribute_id', $categorynameid); //category name attribute filter    
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        //$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        //if(!$sellerid)
        //{
        $this->addColumn('attribute_set_name', array(
            'header' => Mage::helper('marketplace')->__('Attributeset Name'),
            'index' => 'attribute_set_name',
            'filter_condition_callback' => array($this, '_attrsetFilter'),
        ));
        //} 
        $this->addColumn('category_id', array(
            'header' => Mage::helper('marketplace')->__('Category Id'),
            'index' => 'category_id',
        ));
        $this->addColumn('category_name', array(
            'header' => Mage::helper('marketplace')->__('Category Name'),
            'index' => 'category_name',
            'filter_condition_callback' => array($this, '_cateFilter'),
        ));
        $this->addColumn('category_tree', array(
            'header' => Mage::helper('marketplace')->__('Category Tree Node'),
            'index' => 'category_tree',
            'renderer' => 'Emipro_Marketplace_Block_Adminhtml_Category_Renderer_Categoryname',
            'filter' => false,
        ));
    }

    public function getRowUrl($row) {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if (!$sellerid) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
    }

    public function _attrsetFilter($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where("eas.attribute_set_name like  '%" . $value . "%' ");
        return $this;
    }

    public function _cateFilter($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where("ccev.value like  '%" . $value . "%'");
        return $this;
    }

    protected function _prepareMassaction() {
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->setMassactionIdField('id');
            $this->getMassactionBlock()->setFormFieldName('id');

            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('marketplace')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete', array('' => '')),
                'confirm' => Mage::helper('marketplace')->__('Are you sure?')
            ));
            return $this;
        }
    }

}
