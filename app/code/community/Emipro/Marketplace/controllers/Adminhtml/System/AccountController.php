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

require_once "Mage/Adminhtml/controllers/System/AccountController.php";

class Emipro_Marketplace_Adminhtml_System_AccountController extends Mage_Adminhtml_System_AccountController {

    public function indexAction() {
        $this->loadLayout();
        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $this->getLayout()->getBlock('head')->setTitle($this->__('My Account') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
            $this->_setActiveMenu('marketplace/seller_account');
        } else {
            $this->_title($this->__('System'))->_title($this->__('My Account'));
            $this->_setActiveMenu('system/account');
        }
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_account_edit'));
        $this->renderLayout();
    }

    public function saveAction() {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $pwd = null;

        $user = Mage::getModel("admin/user")->load($userId);

        $user->setId($userId)
                ->setUsername($this->getRequest()->getParam('username', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->setLastname($this->getRequest()->getParam('lastname', false));
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $user->setEmail(strtolower($this->getRequest()->getParam('email', false)));
        }
        if ($this->getRequest()->getParam('new_password', false)) {
            $user->setNewPassword($this->getRequest()->getParam('new_password', false));
        }

        if ($this->getRequest()->getParam('password_confirmation', false)) {
            $user->setPasswordConfirmation($this->getRequest()->getParam('password_confirmation', false));
        }
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            //Validate current admin password
            $magentoVersion = Mage::getVersion();
            if (version_compare($magentoVersion, '1.9.1.0', '>=')) {
                $currentPassword = $this->getRequest()->getParam('current_password', null);
                $this->getRequest()->setParam('current_password', null);
                $result = $this->_validateCurrentPassword($currentPassword);
            }
        }
        if (!is_array($result)) {
            $result = $user->validate();
        }
        if (is_array($result)) {
            foreach ($result as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
            $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            return;
        }

        try {
            $user->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The account has been saved.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while saving account.'));
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_account');
    }

}
