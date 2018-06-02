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

class Emipro_Marketplace_Adminhtml_QuestionanswerController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Customer Product Questions') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/questionanswer');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_questionanswer'));
        $this->renderLayout();
    }

    public function newAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Customer Product Questions') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/questionanswer');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_questionanswer_edit'));
        $this->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        if ($id != '') {
            $data = Mage::getModel('marketplace/questionanswer')->load($id)->getData();
            Mage::register('questionanswer', $data);
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Customer Product Questions') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/questionanswer');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_questionanswer_edit'));
        $this->renderLayout();
    }

    public function saveAction() {
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('marketplace/questionanswer');
        try {
            if (isset($data) && !empty($data)) {
                $msg = '';
                if (isset($id) && !empty($id)) {
                    $model->load($id);
                    $product_sku = $model->getProductSku();
                    $question = $model->getQuestion();
                    $answer = nl2br($data['answer']);
                    $customeremail = $model->getCustomerEmail();
                    $subject = $model->getSubject();
                    unset($data['form_key']);
                    $model->setData($data);
                    $model->setId($id);
                    $model->save();
                    if (isset($data['status']) && $data['status'] == 'answered' && $customeremail != '') {
                        $msg = ' Answer sent to customer.';
                        Mage::helper("marketplace")->askquestionsellermail($customeremail, $subject, $product_sku, $question, $customeremail, $answer);
                    }
                } else {
                    $data['date'] = now();
                    if (!isset($data['status'])) {
                        $data['status'] = 'pending';
                    }
                    $model->setData($data);
                    $model->save();
                }
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('marketplace')->__('Error saving question answer data'));
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Data saved successfully.' . $msg));
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

    public function massDeleteAction() {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            Mage::getSingleton('adminhtml/session')->addError('Seller can not delete question.');
            $this->_redirect('*/*/index');
            return;
        }
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select Question.'));
        } else {
            try {
                $Model = Mage::getModel('marketplace/questionanswer');
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

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_withdraw');
    }

}
