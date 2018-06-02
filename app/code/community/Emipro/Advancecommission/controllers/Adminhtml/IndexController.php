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

class Emipro_Advancecommission_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Advanced Commission') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/advancecommission');
        $this->_addContent($this->getLayout()->createBlock('emipro_advancecommission/adminhtml_advancecommission'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('emipro_advancecommission/adminhtml_advancecommission_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $commisionId = $this->getRequest()->getParam('id');

        $commModel = Mage::getModel('emipro_advancecommission/advancecommission')->load($commisionId);

        if ($commModel->getAdvancecommId() || $commisionId == 0) {
            Mage::register('advancecommission', $commModel);
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Advanced Commission') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
            $this->_setActiveMenu('marketplace/advancecommission');
            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                            ->createBlock('emipro_advancecommission/adminhtml_advancecommission_edit'))
                    ->_addLeft($this->getLayout()
                            ->createBlock('emipro_advancecommission/adminhtml_advancecommission_edit_tabs')
            );
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Commission rule does not exist');
            $this->_redirect('*/*/');
        }
    }

    public function saveAction() {


        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('emipro_advancecommission/advancecommission');
        try {
            if (isset($data) && !empty($data)) {
                if(isset($data['rules']['seller_id'])){
                    $modelcheck = Mage::getModel('emipro_advancecommission/advancecommission')->getCollection()->addFieldToFilter('seller_id', $data['rules']['seller_id'])->addFieldToFilter('attributeset_id', array('eq' => $data["rules"]["attributeset_id"]));
                    if ($modelcheck->count() > 0) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('emipro_advancecommission')->__('This Seller already exist with same category in list.'));
                        $this->_redirect('*/*/index');
                        return;
                        exit;
                    }
                }
                unset($data['form_key']);
                $model->setData($data['rules']);
                $model->save();
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Data saved successfully.'));
            $this->_redirect('*/*/index');
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

    public function massDeleteAction() {
        $ids = $this->getRequest()->getParam('advancecomm_id');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('emipro_advancecommission')->__('Please select sellers.'));
        } else {
            try {
                $Model = Mage::getModel('emipro_advancecommission/advancecommission');
                foreach ($ids as $id) {
                    $Model->load($id)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('emipro_advancecommission')->__('Total of %d record(s) were deleted.', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('emipro_advancecommission/advancecommission');
                $model->setId($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('emipro_advancecommission')->__('The rule has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                        Mage::helper('emipro_advancecommission')->__('An error occurred while deleting the rule. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('emipro_advancecommission')->__('Unable to find a rule to delete.')
        );
        $this->_redirect('*/*/');
    }

}
