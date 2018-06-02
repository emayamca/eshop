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

class Emipro_Marketplace_Block_Adminhtml_Notification_Toolbar extends Mage_Adminhtml_Block_Notification_Toolbar {

    public function isShow() {
        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            return false;
        }
        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            return false;
        }
        if ($this->getRequest()->getControllerName() == 'notification') {
            return false;
        }
        if ($this->getCriticalCount() == 0 && $this->getMajorCount() == 0 && $this->getMinorCount() == 0 && $this->getNoticeCount() == 0
        ) {
            return false;
        }

        return true;
    }

}
