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

class Emipro_Marketplace_SellerreviewController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('marketplace/sellerreview', 'sellerreview.new', array(
            'template' => 'marketplace/sellerreview_index.phtml'
                )
        );
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function newAction() {
        $id = $this->getRequest()->getParam('id');
        if (Mage::getSingleton('customer/session')->isLoggedIn() && $id != '') {
            $this->loadLayout();
            $block = $this->getLayout()->createBlock('marketplace/sellerreview', 'sellerreview.new', array(
                        'template' => 'marketplace/sellerreview.phtml'
                            )
                    )->setData('seller_id', $id);
            $this->getLayout()->getBlock('content')->append($block);
            $this->_initLayoutMessages('core/session');

            $this->renderLayout();
        } else {
            $this->_redirect('*/*/index');
            return;
        }
    }

    public function saveAction() {

        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $data = $this->getRequest()->getPost();
        if ($data && $customer_id != '') {
            $data['created_date'] = now();
            if (!isset($data['status'])) {
                $data['approved'] = 1;
            }
            $data['customer_id'] = $customer_id;
            $text = trim($data['text']);
            $data['text'] = $text;
            $model = Mage::getModel('marketplace/sellerreview');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
            Mage::getSingleton('core/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('marketplace')->__('Error saving Data'));
                }
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('marketplace')->__('Review successfully saved.'));
                Mage::getSingleton('core/session')->setFormData(false);
                $this->_redirect('*/*/index');
            } catch (Exception $e) {
                $this->_redirect('*/*/index');
            }
            return;
        }
        Mage::getSingleton('core/session')->addError(Mage::helper('marketplace')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

}
