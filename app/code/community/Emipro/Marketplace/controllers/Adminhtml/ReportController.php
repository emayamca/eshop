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

class Emipro_Marketplace_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->LoadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Balance Statement') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/report');
        $this->renderLayout();
    }

    public function exportCsvAction() {
        $fileName = 'sellerReport.csv';
        $content = $this->getLayout()->createBlock('marketplace/adminhtml_report_grid')
                ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_report');
    }

}
