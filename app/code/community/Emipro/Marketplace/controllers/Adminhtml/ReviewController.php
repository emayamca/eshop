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

class Emipro_Marketplace_Adminhtml_ReviewController extends Mage_Adminhtml_Controller_Action {

    public function IndexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Review') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/review');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_review'));
        $this->renderLayout();
    }

    public function massDeleteAction() {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select Review.'));
        } else {
            try {
                $Model = Mage::getModel('marketplace/sellerreview');
                foreach ($ids as $id) {
                    $Model->load($id)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Total of %d record(s) were deleted.', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massApproveAction() {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select review.'));
        } else {
            try {
                $model = Mage::getModel('marketplace/sellerreview');
                foreach ($ids as $id) {
                    $model->load($id);
                    $model->setStatus(1);
                    $model->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('marketplace')->__('Total of %d record(s) were updated.', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDisapproveAction() {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select review.'));
        } else {
            try {
                $model = Mage::getModel('marketplace/sellerreview');
                foreach ($ids as $id) {
                    $model->load($id);
                    $model->setStatus(0);
                    $model->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('marketplace')->__('Total of %d record(s) were updated.', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_review');
    }

}
