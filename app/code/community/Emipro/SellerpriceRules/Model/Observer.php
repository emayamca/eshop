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
class Emipro_SellerpriceRules_Model_Observer
{
	public function methodcheck($observer)
	{		
		if($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_promo_catalog_save')
		{
			$UserId = Mage::getSingleton('admin/session')->getUser()->getUserId();
			$Id=Mage::app()->getRequest()->getParam('id');
			$rules = Mage::getModel('catalogrule/rule')->load($Id);
			if (Mage::helper('core')->isModuleEnabled("Emipro_Marketplace"))
			{
				$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
				if($sellerid)
				{
					if($UserId!=$rules->getSellerAdminId() && $Id!="")
					{
							$redirecturl = Mage::helper("adminhtml")->getUrl('adminhtml/promo_catalog/index');
							Mage::getSingleton('core/session')->addError('Other seller rule can not edit.');
							$response = Mage::app()->getResponse();  
							$response->setRedirect($redirecturl);
					}
				}
			}
		}
		
		if($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_promo_catalog_edit')
		{
			$UserId = Mage::getSingleton('admin/session')->getUser()->getUserId();
			$Id=Mage::app()->getRequest()->getParam('id');
			$rules = Mage::getModel('catalogrule/rule')->load($Id);
			if (Mage::helper('core')->isModuleEnabled("Emipro_Marketplace"))
			{
				$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
				if($sellerid)
				{
					if($UserId!=$rules->getSellerAdminId() && $Id!="")
					{
							$redirecturl = Mage::helper("adminhtml")->getUrl('adminhtml/promo_catalog/index');
							Mage::getSingleton('core/session')->addError('Other seller rule can not edit.');
							$response = Mage::app()->getResponse();  
							$response->setRedirect($redirecturl);
					}
				}
			}
		}
	}
	
	
}
