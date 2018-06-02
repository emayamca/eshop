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
class Emipro_Marketplace_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{
	/*
	*  seller folder in media and seller allow to add image in media in his folder
	*/
	public function getStorageRoot()
    {
		
		$sellerid = Mage::helper("marketplace")->getSellerIdfromLoginUser();
		if($sellerid)
		{
			$sellerdetail = Mage::getModel("marketplace/seller")->load($sellerid);
			$_path=Mage::getConfig()->getOptions()->getMediaDir() . DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY. DS .$sellerdetail->getStoreUrl(). DS;
			
			$io = new Varien_Io_File();
			$io->checkAndCreateFolder($_path);
			return $_path;
		}
        
        return Mage::getConfig()->getOptions()->getMediaDir() . DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY. DS;
    }
}

