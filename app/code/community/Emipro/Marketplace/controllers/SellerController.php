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

class Emipro_Marketplace_SellerController extends Mage_Core_Controller_Front_Action {

    public function viewProfileAction() {
		$this->loadLayout();
		$this->renderLayout();
    }

    public function productSaveAction() {
        $data = $this->getRequest()->getPost();
        $product = Mage::getModel('catalog/product');
        try {
            $product->setData($data);
            $product->save();
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    /*
    *   Seller create form action 
    */

    public function createAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*
    *   Seller create form post action 
    */

    public function createpostAction() { 
        $websiteId = Mage::app()->getWebsite()->getId();
        $data = $this->getRequest()->getPost();
        try {
            if (isset($data['email']) && $data['email'] != '') {
               /* $storeobj = Mage::getModel("marketplace/seller")->load($data['store_url'], 'store_url');
                if ($storeobj->getId()) {
                    Mage::getSingleton('core/session')->addError($this->__("Store Url already exists"));
                    Mage::getSingleton('core/session')->setSellerdata($data);
                    $this->_redirect('marketplace/seller/create');
                    return;
                }*/
                $storecheck = Mage::getModel("marketplace/seller")->getCollection();
                $storecheck->addFieldToFilter('store_name', $data['store_name']);
                if ($storecheck->count() > 0) {
                    Mage::getSingleton('core/session')->addError($this->__("Store Name already exists."));
                    Mage::getSingleton('core/session')->setSellerdata($data);
                    $this->_redirect('marketplace/seller/create');
                    return;
                }

                $adminUserModel = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('email', $data['email']);

                if (($adminUserModel->count()) > 0) {
                    Mage::getSingleton('core/session')->addError($this->__("Seller Email already exists. Enter New email."));
                    Mage::getSingleton('core/session')->setSellerdata($data);
                    $this->_redirect('marketplace/seller/create');
                    return;
                }
                $storeinfo = Mage::getModel("marketplace/seller");
                $sellerload = $storeinfo->load($data['email'], 'email');
                if (!$sellerload->getId()) {
                    $seller = Mage::getModel("marketplace/list");
                    $seller->setStatus("pending");
                    $emailverifykey = Mage::helper("marketplace")->getrandomPassword("abcdefghijklmnopqrstuwxyz", 7);
                    $seller->setEmailVerifyKey($emailverifykey);
                    $seller->setEmailVerifyStatus('Unconfirmed');
                    $seller->setWebsiteId($websiteId);
                    $seller->save();
                    if (!$seller->getId()) {
                        Mage::getSingleton('core/session')->addError($this->__('Error in data.'));
                        return;
                    }
                    $data['created_at'] = now();
                    $storeinfo->setData($data);
                    if (isset($data['street'][0])) {
                        $storeinfo->setStreet1($data['street'][0]);
                    }
                    if (isset($data['street'][1])) {
                        $storeinfo->setStreet2($data['street'][1]);
                    }
                    $storeinfo->setSellerId($seller->getId());
                    $storeinfo->setDefaultProductApprove(1);
					$storeinfo->setmobile($data['mobile']);
                    $storeinfo->setVatNumber($data['vat_number']);
                    $storeinfo->save();

                    Mage::helper("marketplace")->sendVerifymail($seller->getData(), $data['email'], $data['firstname']);
                    //mail to admin
                    Mage::helper("marketplace")->sendAdminmail($data['email'],$data['firstname']." ".$data["lastname"]); 
                    Mage::getSingleton('core/session')->addSuccess($this->__('Seller Information sent to admin. Please wait for confirmation.'));
                    Mage::getSingleton('core/session')->setSellerEmail($data['email']);
                    $this->_redirect('marketplace/seller/success');
                } else {
                    Mage::getSingleton('core/session')->addError($this->__('Seller already exist.'));
                    Mage::getSingleton('core/session')->setSellerdata($data);
                    $this->_redirect('marketplace/seller/create');
                }
            } else {
                Mage::getSingleton('core/session')->addError($this->__('Data not found.'));
                Mage::getSingleton('core/session')->setSellerdata($data);
                $this->_redirect('marketplace/seller/create');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            Mage::getSingleton('core/session')->setSellerdata($data);
            $this->_redirect('marketplace/seller/create');
        }
    }

    public function checkData($data, $sellerid) {
        $seller = Mage::getModel("marketplace/seller")->load($data["store_url"], "store_url");
        if ($seller->getId() && $sellerid != $seller->getEntityId()) {
            return false;
        }
        return true;
    }

    /*
    *   Check seller email is valid or not
    */

    public function emailverifyAction() {

        $data = $this->getRequest()->getParams();
        try {
            if ($data['sellerId'] != '' && $data['key'] != '') {
                $sellerdata = Mage::getModel('marketplace/list')->load($data['sellerId']);
                $checkdata = $sellerdata->getData();
                if (isset($checkdata) && $checkdata['email_verify_key'] == $data['key']) {
                    $sellerdata->setEmailVerifyStatus('Confirmed');
                    $sellerdata->save();

                    Mage::getSingleton('core/session')->addSuccess($this->__("Your seller account email has been confirmed."));
                    $this->loadLayout(); 
                    $root = $this->getLayout()->getBlock('root');
                    $template = "page/2columns-right.phtml";   
                    $root->setTemplate($template);     
                    $block = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('seller_email_confirmed'); 
                    $this->getLayout()->getBlock('content')->append($block);
                    $this->renderLayout();   
                }else{
                    
                    Mage::getSingleton('core/session')->addError($this->__("Invalid Email Verification Data."));
                    $this->loadLayout();
                    $root = $this->getLayout()->getBlock('root');
                    $template = "page/2columns-right.phtml";   
                    $root->setTemplate($template);     
                    /*$block = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('seller_email_confirmed'); 
                    $this->getLayout()->getBlock('content')->append($block);*/
                    $this->renderLayout();     
                }
            }
            else{
                $this->_redirect('/');
            }
        } catch (Exception $e) {
            Mage::log('Exception in seller email verification' . $e->getMessage());
        }
    }

    /*
    *   Seller product page display
    */

    public function productviewAction() {
        Mage::dispatchEvent(
                'catalog_controller_category_init_before', array(
            'controller_action' => $this
                )
        );

        $rootCategoryId = (int) Mage::app()->getStore()->getRootCategoryId();
        if (!$rootCategoryId) {
            $this->_forward('noRoute');
            return;
        }
        $sellerstore = '';
        $id = Mage::app()->getRequest()->getParam('id');
        if ($id && $id != '') {
            $seller = Mage::getModel('marketplace/list')->load($id);
            if ($seller->getId() && $seller->getStatus() == 'approved') {
                $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), 'seller_id');
                if ($sellerdetail->getSellerId()) {
                    $sellerstore = $sellerdetail->getStoreName();
                }
                $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', array('neq' => ''));
                $collection->addAttributeToFilter('vendor_id', $seller->getProductVendorId());
                Mage::register('seller_product_vendor_id', $seller->getProductVendorId());
            }
        } else {
            $this->_redirect('marketplace/');
            return;
        }

        $rootCategory = Mage::getModel('catalog/category')
                ->load($rootCategoryId)
                ->setName($this->__($sellerstore))
                ->setMetaTitle($this->__("Metacrust E-shop"))
                ->setMetaDescription($this->__("Metacrust E-shop"))
                ->setMetaKeywords($this->__("Metacrust E-shop"));


        Mage::register('current_category', $rootCategory);

        Mage::getSingleton('catalog/session')
                ->setLastVisitedCategoryId($rootCategory->getId());

        try {
            Mage::dispatchEvent('catalog_controller_category_init_after', array(
                'category' => $rootCategory,
                'controller_action' => $this
                    )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return;
        }
        if (!$rootCategory->getId()) {
            $this->_forward('noRoute');
            return;
        }

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $seller = Mage::getModel('marketplace/list')->load($id);
            if ($seller->getStatus() == 'approved') {
                $this->loadLayout();
                $this->_initLayoutMessages('catalog/session');
                $this->_initLayoutMessages('checkout/session');
                $this->renderLayout();
                return;
            }
        }
        $this->_redirect('marketplace/');
    }

    /*
    *   Customer product question save and email to seller
    */

    public function emailToSellerAction() { 
        $data = $this->getRequest()->getPost();
        if (isset($data) && !empty($data)) {
            $customeremail = '';
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$customeremail = $customer->getEmail();
            $array = array(
                'seller_id' => $data['seller-id'],
                'product_sku' => $data['product-sku'],
                'customer_email' => $customeremail,
                'subject' => $data['questionsubject'],
                'question' => $data['question'],
                'status' => 'pending',
                'date' => now(),
            );
            try {
                $model = Mage::getModel('marketplace/questionanswer');
                $model->setData($array);
                $model->save();
            } catch (Exception $e) {
                echo 'Error while saving question data.';
                return;
            }
        }
        try {
            $sellerid = $array['seller_id'];
            $selleremail = '';
            if ($sellerid) {
                $sellerobj = Mage::getModel('marketplace/seller')->load($sellerid, 'seller_id');
                if ($sellerobj->getId()) {
                    $selleremail = $sellerobj->getEmail();
                }
            }
            Mage::helper("marketplace")->askquestionsellermail($selleremail, $array['subject'], $array['product_sku'], $array['question'], $array['customer_email'],'');
            echo $this->__('Email is Sent. You will get your answer soon.');
        } catch (Exception $e) {
            echo $this->__('Problem in sending mail.');
            return;
        } 
    }

    /*
    *   seller register success page. 
    */

    public function successAction() { 
        $selleremail = Mage::getSingleton('core/session')->getSellerEmail(true);
        if(isset($selleremail) && $selleremail!='')
        {
            $this->loadLayout();   
            $root = $this->getLayout()->getBlock('root');
            $template = "page/2columns-right.phtml";   
            $root->setTemplate($template);      
            $block = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('seller_signup_success'); 
            $this->getLayout()->getBlock('content')->append($block);
            $this->renderLayout(); 
        }
        else{ 
            $this->_redirect('/');
        }
    }

    /*
    *   check login customer id
    */

    public function currentcustomeridAction(){
        $data = $this->getRequest()->getPost();
        if(isset($data['customerid']))
        { 
            if(Mage::getSingleton('customer/session')->isLoggedIn())
            {
                echo Mage::getSingleton('customer/session')->getId();
            }
            else
            {
                 echo 'false';
            }
        }
    }
}
