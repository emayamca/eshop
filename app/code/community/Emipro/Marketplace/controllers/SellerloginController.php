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

class Emipro_Marketplace_SellerloginController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*
    *   Seller forgotpassword action 
    */
    public function forgotpasswordAction() {
        $email = (string) $this->getRequest()->getParam('email');
        $params = $this->getRequest()->getParams();
        if (!empty($email) && !empty($params)) {
            if(isset($params['captcha']['backend_forgotpassword']))
            {
                $_captcha = Mage::getModel('customer/session')->getData('backend_forgotpassword_word'); 
                if($_captcha['data']!=$params['captcha']['backend_forgotpassword'])
                {
                    $this->_redirect('*/*/forgotpassword',array('captcha'=>1));   
                    return;   
                }
            }
            if (Zend_Validate::is($email, 'EmailAddress')) {
                $collection = Mage::getResourceModel('admin/user_collection');
                /** @var $collection Mage_Admin_Model_Resource_User_Collection */
                $collection->addFieldToFilter('email', $email);
                $collection->load(false);

                if ($collection->getSize() > 0) {
                    foreach ($collection as $item) {
                        $user = Mage::getModel('admin/user')->load($item->getId());
                        if ($user->getId()) {
                            $newResetPasswordLinkToken = Mage::helper('admin')->generateResetPasswordLinkToken();
                            $user->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                            $user->save();
                            $user->sendPasswordResetConfirmationEmail();
                        }
                        break; 
                    }
                }
                Mage::getModel('core/session')->addSuccess(Mage::helper('core')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('adminhtml')->escapeHtml($email)));
                $this->_redirect('*/*/index',array('reset'=>1));  
                return;
            } else {
                Mage::getModel('core/session')->addError(Mage::helper('core')->__('Invalid email address.'));
            }
        } elseif (!empty($params)) {
            Mage::getModel('core/session')->addError(Mage::helper('core')->__('The email address is empty.'));
        }
        $this->loadLayout(); 
        $this->renderLayout();
    }
    public function resetpasswordAction() {
    	$this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('marketplace/admin/resetforgottenpassword.phtml');  
        $this->renderLayout();   
    }
}
