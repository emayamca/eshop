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

class Emipro_Marketplace_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    /*
    *   Seller grid in admin 
    */

    public function indexAction() {
        Mage::helper('marketplace')->validmarketplacedata();
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Sellers') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/adminhtml_index');
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->_setActiveMenu('marketplace/adminhtml_index');
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('marketplace/adminhtml_sellerlist_edit_tab_sellerproducts')->toHtml()
        );
    }

    public function orderGridAction() {
        $this->loadLayout();
        $this->_setActiveMenu('marketplace/adminhtml_index');
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('marketplace/adminhtml_sellerlist_edit_tab_sellerorders')->toHtml()
        );
    }

    public function stateAction() {
        $arrRes = array();

        $countryId = $this->getRequest()->getParam('country');
        $arrRegions = Mage::getResourceModel('directory/region_collection')
                ->addCountryFilter($countryId)
                ->load()
                ->toOptionArray();

        if (!empty($arrRegions)) {
            foreach ($arrRegions as $region) {
                $arrRes[] = $region;
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($arrRes));
    }

    /*
    *   Edit Seller in admin 
    */
    
    public function editAction() {
        $id = (int) $this->getRequest()->getParam("id");
        $this->getRequest()->setParam('active_tab','seller_information');
        if ($id) {
            $seller = Mage::getModel("marketplace/seller")->load($id, "seller_id");
            Mage::register('store_info', $seller);
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Sellers') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
            $this->_setActiveMenu('marketplace/adminhtml_index');
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Seller id not found.');
            $this->_redirect('*/*/index');
            return;
        }
    }

    /*
    *   Seller status change in admin 
    */
    
    public function sellerStatusAction() {
        $seller = Mage::getModel("marketplace/list");
        $id = $this->getRequest()->getParam("id");
        $status = $this->getRequest()->getParam("status");
        $seller->load($id);
        if ($seller->getId()) {
            try {
                $oldstatus = $seller->getStatus();
                $seller->setData("status", $status);
                $seller->setId($seller->getId());
                $this->storeUrl($id, $status);
                $sellerdetail = Mage::getModel("marketplace/seller")->load($id, 'seller_id');
                $storeurl = '';
                $storename = '';
                if (isset($sellerdetail) && !empty($sellerdetail)) {
                    $storeurl = $sellerdetail['store_url'];
                    $storename = $sellerdetail['store_name'];
                    if ($oldstatus == 'pending' && $seller->getStatus() == 'approved') {
                        $vendorattrid = Mage::helper("marketplace")->createProductattrVendor($storename);
                        $seller->setProductVendorId($vendorattrid);
                        $passw = Mage::helper("marketplace")->getrandomPassword("abcdefghijklmnopqrstuwxyz", 7);
                        $passw .= Mage::helper("marketplace")->getrandomPassword("0123456789", 1);
                        $role = Mage::getModel("admin/role")->getCollection()->addFieldToFilter('role_name', 'Seller/Vendor');
                        $roledata = $role->getFirstItem()->getData();
                        try {
                            $checkusers = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('email', $sellerdetail['email']);
                            if (count($checkusers) > 0) {
                                Mage::getSingleton('adminhtml/session')->addError("Admin user is already exist");
                                $this->_redirect('*/*/index');
                                return;
                            }
                            $commission_rate = Mage::getModel('marketplace/sellerrule');
                            $commission_rate->setSellerId($seller->getId());
                            $commission_rate->setCommissionType(0);
                            $websiteid = $seller->getWebsiteId();
                            $defaultrate = Mage::getStoreConfig('marketplace_setting/categorycommission/commission_rate', Mage::app()->getStore());
                            $defaultType = Mage::getStoreConfig('marketplace_setting/categorycommission/commission_type', Mage::app()->getStore());
                            $commission_rate->setCommissionType($defaultType);
                            $commission_rate->setCommission($defaultrate);
                            $commission_rate->save();
                            $user = Mage::getModel('admin/user')->setData(array(
                                        'username' => $storeurl,
                                        'firstname' => $sellerdetail['firstname'],
                                        'lastname' => $sellerdetail['lastname'],
                                        'email' => $sellerdetail['email'],
                                        'password' => $passw,
                                        'is_active' => 1
                                    ))->save();
                            if (isset($roledata) && !empty($roledata['role_id'])) {
                                $user->setRoleIds(array($roledata['role_id']))->setRoleUserId($user->getUserId())->saveRelations();
                            } else {
                                Mage::getSingleton('adminhtml/session')->addError("Seller role is not created.");
                                $this->_redirect('*/*/index');
                                return;
                            }
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                        $userid = Mage::helper("marketplace")->getAdminUserId($sellerdetail['email']);
                        $seller->setSellerAdminId($userid);
                        $seller->save();
                        Mage::helper("marketplace")->sendSuccessMail($sellerdetail['firstname'], $sellerdetail['email'], $storeurl, $passw);
                    } else if ($oldstatus == 'block' && $seller->getStatus() == 'approved') {
                        $user = Mage::getModel('admin/user')->load($seller->getSellerAdminId(), 'user_id');
                        $user->setIsActive(1);
                        $user->save();
                        $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
                        $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
                        $emailtemplate = Mage::getModel('core/email_template')->loadByCode('seller_status_change');
                        $mailname = Mage::app()->getStore()->getFrontendName();
                        $emailTemplateVariable['status'] = 'Approved';
                        $emailTemplateVariable['sellername'] = $sellerdetail['firstname'];
                        $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
                        $emailtemplate->setSenderName($_sendername);
                        $emailtemplate->setSenderEmail($senderemail);
                        $emailtemplate->send($sellerdetail['email'], $sellerdetail['firstname'], $emailTemplateVariable);
                    } else if ($oldstatus == 'approved' && $seller->getStatus() == 'block') {
                        $user = Mage::getModel('admin/user')->load($seller->getSellerAdminId(), 'user_id');
                        $user->setIsActive(0);
                        $user->save();
                        $sellerdetail = Mage::getModel("marketplace/seller")->load($id, 'seller_id');
                        if (isset($sellerdetail) && !empty($sellerdetail)) {
                            $urlModel = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('request_path', array('like' => '%' . $sellerdetail->getStoreUrl() . '%'));
                            if ($urlModel->count() > 0) {
                                foreach ($urlModel as $item) {
                                    $item->delete();
                                }
                            }
                        }
                        $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
                        $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
                        $emailtemplate = Mage::getModel('core/email_template')->loadByCode('seller_status_change');
                        $mailname = Mage::app()->getStore()->getFrontendName();
                        $emailTemplateVariable['status'] = 'Block';
                        $emailTemplateVariable['sellername'] = $sellerdetail['firstname'];
                        $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
                        $emailtemplate->setSenderName($_sendername);
                        $emailtemplate->setSenderEmail($senderemail);
                        $emailtemplate->send($sellerdetail['email'], $sellerdetail['firstname'], $emailTemplateVariable);
                    }
                }
                $seller->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Seller Status changed successfully.'));
                $this->_redirect('*/*/index');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/index');
                return;
            }
        }
    }

    /*
    *   Url rewrite add seller page
    */

    public function storeUrl($sellerId, $status) {
        $sellerData = Mage::getModel("marketplace/seller")->load($sellerId, "seller_id");
        $request = $sellerData->getStoreUrl();
        $storeid = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();  
        $IdPath = "seller/".$storeid."/" . $sellerId;
        $target = "marketplace/seller/viewprofile/profile_id/" . $sellerId;
        $urlModel = Mage::getModel('core/url_rewrite');
        $urlModel->load($IdPath, "id_path");
        if ($status == "block") {
            $urlModel->delete();
            return;
        }

        if (!$urlModel->getId()) {
            $urlModel->setIsSystem(0)
                    ->setStoreId($storeid)
                    //->setOptions('RP')  
                    ->setIdPath($IdPath)
                    ->setTargetPath($target)
                    ->setRequestPath($request)
                    ->save();
        }
    }

    /*
    *   Seller data save with image and banner image
    */

    public function saveAction() {

        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('marketplace/seller');
        try {
            if (isset($data) && !empty($data)) {
                if (isset($id) && !empty($id)) {
                    $model->load($id, 'seller_id');
                    $oldid = $model->getId();
                    if (isset($data['storeinfo']['banner']['delete']) && $data['storeinfo']['banner']['delete'] == 1) {
                        if ($model->getBanner() != '') {
                            unlink('media/' . $model->getBanner());
                            $data['storeinfo']['banner'] = '';
                        }
                    } else if (isset($_FILES['banner']['name']) && $_FILES['banner']['name'] != '') {
                        $image_info = getimagesize($_FILES['banner']['tmp_name']);
                        $image_width = $image_info[0];
                        $image_height = $image_info[1];
                        if ($image_width > 900 || $image_height > 300) { 
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Store banner image not more than height 300px and width 900px.'));
                            Mage::getSingleton('adminhtml/session')->setData('form_data',$data);  
                            $this->_redirect('*/*/edit', array('id' => $id));   
                            return;
                        }
                        $fileName = $_FILES['banner']['name']; 
                        $fileExt = strtolower(substr(strrchr($fileName, "."), 1));
                        $fileName = 'banner_' . time() . '.' . $fileExt;
                        $uploader = new Varien_File_Uploader('banner');
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'seller' . DS . 'profile';
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $uploader->save($path . DS, $fileName);
                        $data['storeinfo']['banner'] = DS . 'seller' . DS . 'profile' . DS . $fileName;
                    } else {
                        unset($data['storeinfo']['banner']);
                    }
                    if (isset($data['storeinfo']['company_logo']['delete']) && $data['storeinfo']['company_logo']['delete'] == 1) {
                        if ($model->getCompanyLogo() != '') {
                            unlink('media/' . $model->getCompanyLogo());
                            $data['storeinfo']['company_logo'] = '';
                        }
                    } else if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
                        $image_info = getimagesize($_FILES['company_logo']['tmp_name']);
                        $image_width = $image_info[0];
                        $image_height = $image_info[1];
                        if ($image_width > 300 || $image_height > 300) {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Store logo image height and width not more than 300px.'));
                            Mage::getSingleton('adminhtml/session')->setData('form_data',$data);
                            $this->_redirect('*/*/edit', array('id' => $id));
                            return;
                        } 
                        $fileName = $_FILES['company_logo']['name'];
                        $fileExt = strtolower(substr(strrchr($fileName, "."), 1));
                        $fileName = 'logo_' . time() . '.' . $fileExt;
                        $uploader = new Varien_File_Uploader('company_logo');
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'seller' . DS . 'profile';
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $uploader->save($path . DS, $fileName);
                        $data['storeinfo']['company_logo'] = DS . 'seller' . DS . 'profile' . DS . $fileName;
                    } else {
                        unset($data['storeinfo']['company_logo']);
                    }
                    if (isset($data['storeinfo']['store_name']) && !empty($data['storeinfo']['store_name'])) {
                        if ($model->getStoreName() != $data['storeinfo']['store_name']) {
                            $checkseller = Mage::getModel('marketplace/seller')->getCollection()->addFieldToFilter('store_name', $data['storeinfo']['store_name'])->addFieldToFilter('seller_id', array('neq' => $id));
                            if ($checkseller->count() > 0) {
                                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Store name already exist. Please try with different store name.'));
                                Mage::getSingleton('adminhtml/session')->setData('form_data',$data); 
                                $this->_redirect('*/*/edit', array('id' => $id));     
                                return; 
                            }
                            $sellerobj = Mage::getModel('marketplace/list')->load($id);
                            if ($sellerobj->getId()) {
                                $attributeFrontend = Mage::getModel('eav/config')->getAttribute('catalog_product', 'vendor_id');
                                $optionvalue = array();
                                if ($attributeFrontend->usesSource()) {
                                    $optionvalue = array(
                                        $sellerobj->getProductVendorId() => array(
                                            0 => $data['storeinfo']['store_name']
                                        )
                                    ); 
                                    $optionsdata['option']['value'] = $optionvalue;
                                    $attributeFrontend->addData($optionsdata);
                                    $attributeFrontend->save();
                                }
                            }
                        }
                    }
                    $model->setData($data['storeinfo']);
                    $model->setId($oldid);
                    $model->save();
                    if (isset($data['sellerinfo']) && isset($data['sellerinfo']['firstname'])) {
                        $model->setData($data['sellerinfo']);
                        $model->setId($oldid);
                        $model->save();
                    }
                    if(isset($data['bankdetails']))
                    {
                        $model->setData($data['bankdetails']);
                        $model->setId($oldid);
                        $model->save();
                    }
                }
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('marketplace')->__('Error saving store data.'));
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Data saved successfully.'));
                $this->_redirect('*/*/edit', array('id' => $id,'active_tab'=>'seller_information'));
                return;
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('No data found to save'));
                $this->_redirect('*/*/index');
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/index');
        }
        $this->_redirect('*/*/index');
        return;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/manageseller');
    }

}
