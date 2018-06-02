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

class Emipro_Marketplace_Adminhtml_RulesController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->LoadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Default Commission') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/rules');
        $this->renderLayout();
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        if ($id != '') {
            $data = Mage::getModel('marketplace/sellerrule')->load($id);
            if ($data->getId()) {
                Mage::register('seller_rule_data', $data->getData());
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Data not found.'));
                $this->_redirect('*/*/index');
            }
        }
        $this->LoadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Default Commission') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/rules');
        $this->renderLayout();
    }

    public function saveAction() {
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('marketplace/sellerrule');
        try {
            if (isset($data) && !empty($data)) {
                if (isset($id) && !empty($id)) {
                    $modelcheck = Mage::getModel('marketplace/sellerrule')->getCollection()->addFieldToFilter('seller_id', $data['rules']['seller_id'])->addFieldToFilter('id', array('neq' => $id));
                    if ($modelcheck->count() > 0) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('This Seller already exist in list.'));
                        $this->_redirect('*/*/index');
                        return;
                    }
                    $model->load($id);
                    $data['rules']['id'] = $id;
                    $model->setData($data['rules']);
                    $model->save();
                } else {
                    $modelcheck = Mage::getModel('marketplace/sellerrule')->getCollection()->addFieldToFilter('seller_id', $data['rules']['seller_id']);
                    if ($modelcheck->count() > 0) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('This Seller already exist in list.'));
                        $this->_redirect('*/*/index');
                        return;
                    }
                    unset($data['form_key']);
                    $model->setData($data['rules']);
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

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_rule');
    }

}
