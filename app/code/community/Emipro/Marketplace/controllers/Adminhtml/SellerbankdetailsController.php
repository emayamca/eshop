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

class Emipro_Marketplace_Adminhtml_SellerbankdetailsController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $user = Mage::getModel('admin/session');
        if (isset($user)) {
            $userid = $user->getUser()->getId();
            if ($userid) {
                $seller = Mage::getModel("marketplace/list")->load($userid, "seller_admin_id");
                if ($seller->getId()) {
                    $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), 'seller_id');
                    Mage::register('store_info', $sellerdetail);
                }
            }
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Bank Details') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/sellerbankdetails');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_sellerbankdetails_edit'));
        $this->renderLayout();
    }

    public function saveAction() {
        $user = Mage::getModel('admin/session');
        if (isset($user)) {
            $userid = $user->getUser()->getId();
            if ($userid) {
                $seller = Mage::getModel("marketplace/list")->load($userid, "seller_admin_id");
                if ($seller->getId()) {
                    $data = $this->getRequest()->getPost();
                    $model = Mage::getModel('marketplace/seller');
                    try {
                        if (isset($data['storeinfo']) && !empty($data['storeinfo'])) {
                            $model->load($seller->getId(), 'seller_id');
                            if ($model->getSellerId()) {
                                $id = $model->getId();
                                $model->setData($data['storeinfo']);
                                $model->setId($id);
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
                        $this->_redirect('*/*/index');
                    }
                }
            }
        }
        $this->_redirect('*/*/index');
        return;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_bankdetails');
    }

}
