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
class Emipro_SellerpriceRules_Block_Adminhtml_Renderer_Sellerinfo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$SellerId =  $row->getSellerAdminId();
		$seller=Mage::getModel("marketplace/list")->load($SellerId,"seller_admin_id");  
		
		$Seller_info = Mage::getModel('marketplace/seller')->load($seller->getId(),'seller_id');
		
		$first_name=$Seller_info->getFirstname();
		$last_name=$Seller_info->getLastname();
	
		return  $first_name." ".$last_name;
	} 
}

