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

class Emipro_Marketplace_Block_Adminhtml_Imageupload_Imagetable extends Mage_Core_Block_Template {

    protected $_storeurl;

    public function getImages() {
        $user = Mage::getModel('admin/session');
        if (isset($user)) {
            $userid = $user->getUser()->getId();
            if ($userid) {
                $seller = Mage::getModel("marketplace/list")->load($userid, "seller_admin_id");
                if ($seller->getId()) {
                    $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), 'seller_id');
                    if ($sellerdetail->getId()) {
                        $this->_storeurl = $sellerdetail->getStoreUrl();
                        $path = Mage::getBaseDir() . '/media/import/' . $sellerdetail->getStoreUrl() . '/*';
                        $files = glob($path);
                        return $files;
                    }
                }
            }
        }
    }

    public function getStoreurl() {
        return $this->_storeurl;
    }

}
