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

class Emipro_Marketplace_Block_Toplinks extends Mage_Page_Block_Switch {

    /*
    *   Add marketplace link in header in frontend
    */
    public function addMarketplaceLinks() {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            $magentoVersion = Mage::getVersion();
            if (!version_compare($magentoVersion, '1.8', '>=') || Mage::getSingleton('core/design_package')->getPackageName() == 'default') {
                $position = 1;
                $LinkPath = 'marketplace/seller/create';
                $Link = Mage::getUrl('', array('_direct' => $LinkPath));
                $title = $this->__('Seller Sign Up');
                $parentBlock->addLink($title, $Link, $title, false, array(), $position);
                $position = 2;
                $LinkPath = 'marketplace/sellerlogin';
                $Link = Mage::getUrl('', array('_direct' => $LinkPath));
                $title = $this->__('Seller Sign In');
                $parentBlock->addLink($title, $Link, $title, false, array(), $position);
            }
        }
        return $this;
    }

}
