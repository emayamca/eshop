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
 
class Emipro_Sellerreport_Model_Reports_Resource_Product_Sold_Collection extends Mage_Reports_Model_Resource_Product_Sold_Collection
{
	public function getsellerproducts($productids){  
        $this->getSelect()->where('order_items.product_id  IN (?)',(array) $productids);  
        return $this;   
    }  
}
		
