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

class Emipro_Marketplace_Adminhtml_WithdrawController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->LoadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Withdrawals') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/widthdraw');
        $this->renderLayout();
    }

    public function newAction() {
        $this->LoadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Withdrawals') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/widthdraw');
        $this->renderLayout();
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        if ($id != '') {
            $data = Mage::getModel('marketplace/withdraw')->load($id);
            if ($data->getId()) {
                Mage::register('seller_withdraw_data', $data->getData());
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Data not found.'));
                $this->_redirect('*/*/index');
            }
        }
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Seller can not Edit.'));
            $this->_redirect('*/*/index');
            return;
        }
        $this->LoadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Withdrawals') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/widthdraw');
        $this->renderLayout();
    }

    public function saveAction() {
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('marketplace/withdraw');
        try {
            if (isset($data) && !empty($data)) {
                $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                if ($seller_id) {
                    if (isset($data['withdraw']['request_status']) && $data['withdraw']['request_status'] == 'approved') {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Seller can not select with approved status.'));
                        $this->_redirect('*/*/index');
                        return;
                    }
                }
                if ($seller_id == '' && isset($data['withdraw']['seller_id'])) {
                    $seller_id = $data['withdraw']['seller_id'];
                }
                if ($seller_id) {
                    $seller_balance = Mage::helper('marketplace')->getSellerBalance($seller_id);
                    if ($data['withdraw']['amount'] > $seller_balance || $seller_balance == 0) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Not Have Enough Credit.'));
                        Mage::getSingleton('adminhtml/session')->setData('seller_withdraw_data', $data);
                        $this->_redirect('*/*/edit', array('id' => $id));
                        return;
                    }
                }
                if (isset($id) && !empty($id)) {
                    $model->load($id);
                    $data['withdraw']['id'] = $id;
                    $data['withdraw']['date'] = now();

                    $sellercomm = '';
                    $sellercomm_id = '';
                    $model->setData($data['withdraw']);
                    $model->save();
                    if ($model->getId()) {
                        if ($data['withdraw']['request_status'] == 'approved') {
                            $reqstatus = 'approved';
                            $seller_id = $data['withdraw']['seller_id'];
                            $amount = $data['withdraw']['amount'];
                            $notifyseller = false;
                            Mage::helper('marketplace')->sendTransactionStatus($seller_id, $reqstatus, $amount, $notifyseller);
                            /* code for balance statement start	 */
                            $balance_statement = array();
                            $balance_statement["transaction_id"] = $model->getId();
                            $balance_statement["transaction_type"] = "withdraw";
                            $balance_statement["debit"] = $model->getAmount();
                            $balance_statement["date"] = $data['withdraw']['date'];
                            $balance_statement["summary"] = $model->getSummary();
                            $balance_statement["seller_id"] = $seller_id;

                            $balancedata = Mage::getModel("marketplace/sellerbalancesheet")->getCollection()->addFieldToFilter('transaction_id', $model->getId())->addFieldToFilter('transaction_type', 'withdraw')->getFirstItem();
                            $balid = $balancedata->getId();
                            if ($balid) {
                                Mage::helper("marketplace")->updateTransactionReport($balance_statement);
                            } else {
                                Mage::helper("marketplace")->saveTransactionReport($balance_statement);
                            }
                            /* code for balance statement end	 */
                        }
                        if ($data['withdraw']['request_status'] == 'pending') {
                            $reqstatus = 'pending';
                            $seller_id = $data['withdraw']['seller_id'];
                            $amount = $data['withdraw']['amount'];
                            $notifyseller = true;
                            Mage::helper('marketplace')->sendTransactionStatus($seller_id, $reqstatus, $amount, $notifyseller);
                        }
                    }
                } else {
                    $seller_id = '';
                    if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
                        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                        $data['withdraw']['seller_id'] = $seller_id;
                        $data['withdraw']['request_status'] = 'pending';
                    }
                    unset($data['form_key']);
                    $data['withdraw']['date'] = now();

                    $model->setData($data['withdraw']);
                    $model->save();
                    if ($model->getId()) {
                        if ($seller_id) {
                            if ($data['withdraw']['request_status'] == 'pending') {
                                $reqstatus = 'pending';
                                $amount = $data['withdraw']['amount'];
                                $notifyseller = false;
                                Mage::helper('marketplace')->sendTransactionStatus($seller_id, $reqstatus, $amount, $notifyseller);
                            }
                        }
                        if ($data['withdraw']['request_status'] == 'approved') {
                            $reqstatus = 'approved';
                            $seller_id = $data['withdraw']['seller_id'];
                            $amount = $data['withdraw']['amount'];
                            $notifyseller = false;
                            Mage::helper('marketplace')->sendTransactionStatus($seller_id, $reqstatus, $amount, $notifyseller);
                            /* code for balance statement start	 */
                            $balance_statement = array();
                            $balance_statement["transaction_id"] = $model->getId();
                            $balance_statement["transaction_type"] = "withdraw";
                            $balance_statement["debit"] = $model->getAmount();
                            $balance_statement["date"] = $data['withdraw']['date'];
                            $balance_statement["summary"] = $model->getSummary();
                            $balance_statement["seller_id"] = $seller_id;
                            Mage::helper("marketplace")->saveTransactionReport($balance_statement);
                            /* code for balance statement end	 */
                        }
                    }
                }
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('marketplace')->__('Error saving withdraw data'));
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
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_withdraw');
    }

}
