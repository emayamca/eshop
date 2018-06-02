<?php
class Emipro_Marketplace_Block_Adminhtml_Catalog_Product_Edit_Tab_Related extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
{
	public function setCollection($collection)
	{
		$current_user = Mage::getSingleton('admin/session')->getUser();
		$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
        	$optionid = Mage::helper("marketplace")->getProuctVendorid($current_user->getUserId());
            $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
            $productIds = $productCollection->getAllIds();
            $collection->addFieldtoFilter("entity_id", array("in", $productIds));
        } 
        parent::setCollection($collection);  
	}
}
			