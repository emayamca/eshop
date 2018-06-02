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

class Emipro_Marketplace_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Dashboard') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            $this->_setActiveMenu('marketplacedashboard');
        } else {
            $conrtollername = $this->getRequest()->getControllerName();
            if ('adminhtml_dashboard' == $conrtollername) {
                $this->_redirect('adminhtml/dashboard/index');
                return;
            }
        }
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Dashboard'), Mage::helper('adminhtml')->__('Dashboard'));
        $this->renderLayout();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplacedashboard');
    }

}
