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

class Emipro_Marketplace_Adminhtml_CommissionController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->LoadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Order Transactions') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/commission');
        $this->renderLayout();
    }

    public function newAction() {
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->LoadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Order Transactions') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
            $this->_setActiveMenu('marketplace/commission');
            $this->renderLayout();
        } else {
            $this->_redirect('*/*/index');
        }
    }

    public function editAction() {

        $this->_redirect('*/*/index');

        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $commisstionId = (int) $this->getRequest()->getParam("id");
            $data = '';
            if ($commisstionId != '') {
                $data = Mage::getModel('marketplace/commission')->load($commisstionId);
                if ($data->getId()) {
                    Mage::register('current_sellercommission', $data->getData());
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Data not found.'));
                    $this->_redirect('*/*/index');
                }
            }
            $this->LoadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Order Transactions') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
            $this->_setActiveMenu('marketplace/commission');
            $this->renderLayout();
        } else {
            $this->_redirect('*/*/index');
        }
    }

    public function saveAction() {
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('marketplace/commission');
        try {
            if (isset($data) && !empty($data)) {
                if (isset($data['sellercommission']['order_increment_id']) && !empty($data['sellercommission']['order_increment_id'])) {
                    $ordercheck = Mage::getModel('sales/order')->load($data['sellercommission']['order_increment_id']);
                    if (!$ordercheck->getId()) {
                        Mage::getSingleton('adminhtml/session')->setData('current_sellercommission', $data);
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Order No. is invalid.'));
                        $this->_redirect('*/*/new');
                        return;
                    }
                }
                if (isset($data['sellercommission']['commission_amt']) && round($data['sellercommission']['commission_amt']) == 0) {
                    $data['sellercommission']['commission_amt'] = null;
                }
                if (isset($data['sellercommission']['product_id']) && $data['sellercommission']['product_id'] == 0) {
                    $data['sellercommission']['product_id'] = null;
                }
                if (isset($data['sellercommission']['qty']) && $data['sellercommission']['qty'] == 0) {
                    $data['sellercommission']['qty'] = null;
                }
                if (isset($data['sellercommission']['product_id']) && !empty($data['sellercommission']['product_id'])) {
                    $product = Mage::getModel('catalog/product')->load($data['sellercommission']['product_id']);
                    $sellerid = Mage::helper("marketplace")->getSelleridfromProductvendorid($product->getVendorId());
                    if ($sellerid) {
                        $data['sellercommission']['seller_id'] = $sellerid;
                    }
                }
                if (isset($id) && !empty($id)) {
                    $model->load($id);
                    unset($data['form_key']);
                    $data['sellercommission']['id'] = $id;
                    $model->setData($data['sellercommission']);
                    $model->save();
                } else {
                    unset($data['form_key']);
                    $data['sellercommission']['date'] = now();
                    $model->setData($data['sellercommission']);
                    $model->save();
                }
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('marketplace')->__('Error saving data'));
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Data saved successfully.'));
                $this->_redirect('*/*/index');
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('No data found to save'));
                $this->_redirect('*/*/index');
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            if ($model && $model->getId()) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
            } else {
                $this->_redirect('*/*/index');
            }
        }
        $this->_redirect('*/*/index');
        return;
    }

    public function exportCsvAction() {
        $fileName = 'sellerCommission.csv';
        $content = $this->getLayout()->createBlock('marketplace/adminhtml_commission_grid')
                ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/sellercommission');
    }

}
