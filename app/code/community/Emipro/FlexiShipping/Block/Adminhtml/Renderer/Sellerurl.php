<?php
class Emipro_FlexiShipping_Block_Adminhtml_Renderer_Sellerurl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$sellerId =  $row->getSellerAdminId();
		
		$seller=Mage::getModel("marketplace/list")->load($sellerId,"seller_admin_id");  
		$Seller_info = Mage::getModel('marketplace/seller')->load($seller->getId(),'seller_id');
		$seller_id=$Seller_info->getSellerId();
		$storeUrl=$Seller_info->getStoreUrl();
		
		$baseUrl=Mage::getBaseUrl();
       return "<a href='".$baseUrl.$storeUrl."' target='_blank'>".$storeUrl."</a>";
		
	} 
}

