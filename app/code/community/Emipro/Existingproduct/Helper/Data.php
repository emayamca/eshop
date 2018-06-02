<?php
class Emipro_Existingproduct_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getSkuPrefix($product){
		if($product->getVendorId()){
			$seller = Mage::getModel('marketplace/list')->load($product->getVendorId(),'product_vendor_id');
			if($seller->getId()){
				return $seller->getId().'-';
			}
		}
	} 
}