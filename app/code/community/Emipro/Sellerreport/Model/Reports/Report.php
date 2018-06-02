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
 
class Emipro_Sellerreport_Model_Reports_Report extends Mage_Reports_Model_Report
{
	public function initCollection($modelClass)
    { 
		$sellerid = '';
        $productids = array();
        $post = Mage::app()->getRequest()->getParam('filter');
        $filter_data = Mage::helper('adminhtml')->prepareFilterString($post);
        if(isset($filter_data['sellerid']) && !empty($filter_data['sellerid'])){
            $sellerid = $filter_data['sellerid'];
            $sellerproductids = Mage::helper("marketplace")->sellerProductIds();
            $productids = array_keys($sellerproductids,$sellerid);
        }
        if($sellerid) 
        {  
			if(empty($productids)){
				$productids = array(-1);
			} 
            $this->_reportModel = Mage::getResourceModel($modelClass)->getsellerproducts($productids);
            Mage::register('sellerproductids',$productids);
        }else{  
			$this->_reportModel = Mage::getResourceModel($modelClass);
		}  
        return $this;  
    }
    
    public function getReport($from, $to)
    { 
		$sellerproducts = Mage::registry('sellerproductids');
        if(isset($sellerproducts) && !empty($sellerproducts))
        { 
            return $this->_reportModel
                    ->setDateRange($from, $to)  
                    ->setPageSize($this->getPageSize())
                    ->setStoreIds($this->getStoreIds())->getsellerproducts($sellerproducts);  
        }else{
            return $this->_reportModel
        ->setDateRange($from, $to) 
        ->setPageSize($this->getPageSize())
        ->setStoreIds($this->getStoreIds());  
        }
    }
}
		
