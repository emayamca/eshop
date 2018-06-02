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

class Emipro_Marketplace_Adminhtml_SellerprofileController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $user = Mage::getModel('admin/session');
        if (isset($user)) {
            $userid = $user->getUser()->getId();
            if ($userid) {
                $seller = Mage::getModel("marketplace/list")->load($userid, "seller_admin_id");
                if ($seller->getId()) {
                    $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), 'seller_id');
                    Mage::register('store_info', $sellerdetail);
                }
            }
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Profile Page') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/sellerprofile');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_sellerprofile_edit'));
        $this->renderLayout();
    }

    public function saveAction() {
        $user = Mage::getModel('admin/session');
        if (isset($user)) {
            $userid = $user->getUser()->getId();
            if ($userid) {
                $seller = Mage::getModel("marketplace/list")->load($userid, "seller_admin_id");
                if ($seller->getId()) {
                    $data = $this->getRequest()->getPost();
                    $model = Mage::getModel('marketplace/seller');
                    try {
                        if (isset($data['storeinfo']) && !empty($data['storeinfo'])) {
                            $model->load($seller->getId(), 'seller_id');
                            if ($model->getSellerId()) {
                                if (isset($data['storeinfo']['banner']['delete']) && $data['storeinfo']['banner']['delete'] == 1) {
                                    if ($model->getBanner() != '') {
                                        unlink('media/' . $model->getBanner());
                                        $data["storeinfo"]['banner'] = '';
                                    }
                                } else if (isset($_FILES['banner']['name']) && $_FILES['banner']['name'] != '') {
                                    $image_info = getimagesize($_FILES['banner']['tmp_name']);
                                    $image_width = $image_info[0];
                                    $image_height = $image_info[1];
                                    if ($image_width > 900 || $image_height > 300) {
                                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Store banner image not more than height 300px and width 900px.'));
                                        Mage::getSingleton('adminhtml/session')->setData('form_data',$data); 
                                        $this->_redirect('*/*/index');
                                        return;
                                    }
                                    $fileName = $_FILES['banner']['name'];
                                    $fileExt = strtolower(substr(strrchr($fileName, "."), 1));

                                    $fileNamewoe = rtrim($fileName, $fileExt);
                                    $fileName = preg_replace('/\s+', '', $fileNamewoe) . 'banner_' . uniqid() . '.' . $fileExt;
                                    $uploader = new Varien_File_Uploader('banner');
                                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                                    $uploader->setAllowRenameFiles(false);
                                    $uploader->setFilesDispersion(false);
                                    $path = Mage::getBaseDir('media') . DS . 'seller' . DS . 'profile';
                                    if (!is_dir($path)) {
                                        mkdir($path, 0777, true);
                                    }
                                    $uploader->save($path . DS, $fileName);
                                    $data["storeinfo"]['banner'] = DS . 'seller' . DS . 'profile' . DS . $fileName;
                                } else {
                                    unset($data['storeinfo']['banner']);
                                }

                                if (isset($data['storeinfo']['company_logo']['delete']) && $data['storeinfo']['company_logo']['delete'] == 1) {
                                    if ($model->getCompanyLogo() != '') {
                                        unlink('media/' . $model->getCompanyLogo());
                                        $data["storeinfo"]['company_logo'] = '';
                                    }
                                } else if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
                                    $image_info = getimagesize($_FILES['company_logo']['tmp_name']);
                                    $image_width = $image_info[0];
                                    $image_height = $image_info[1];
                                    if ($image_width > 300 || $image_height > 300) {
                                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Store logo image height and width not more than 300px.'));
                                        Mage::getSingleton('adminhtml/session')->setData('form_data',$data); 
                                        $this->_redirect('*/*/index');
                                        return;
                                    }
                                    $fileName = $_FILES['company_logo']['name'];
                                    $fileExt = strtolower(substr(strrchr($fileName, "."), 1));

                                    $fileNamewoe = rtrim($fileName, $fileExt);
                                    $fileName = preg_replace('/\s+', '', $fileNamewoe) . 'logo_' . uniqid() . '.' . $fileExt;
                                    $uploader = new Varien_File_Uploader('company_logo');
                                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                                    $uploader->setAllowRenameFiles(false);
                                    $uploader->setFilesDispersion(false);
                                    $path = Mage::getBaseDir('media') . DS . 'seller' . DS . 'profile';
                                    if (!is_dir($path)) {
                                        mkdir($path, 0777, true);
                                    }
                                    $uploader->save($path . DS, $fileName);
                                    $data["storeinfo"]['company_logo'] = DS . 'seller' . DS . 'profile' . DS . $fileName;
                                } else {
                                    unset($data['storeinfo']['company_logo']);
                                }

                                if ($model->getStoreName() != $data['storeinfo']['store_name']) {
                                    $checkseller = Mage::getModel('marketplace/seller')->getCollection()->addFieldToFilter('store_name', $data['storeinfo']['store_name'])->addFieldToFilter('seller_id', array('neq' => $seller->getId()));
                                    if ($checkseller->count() > 0) {
                                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Store name already exist. Please try with different store name.'));
                                        Mage::getSingleton('adminhtml/session')->setData('form_data',$data); 
                                        $this->_redirect('*/*/index');  
                                        return;
                                    }
                                    $attributeFrontend = Mage::getModel('eav/config')->getAttribute('catalog_product', 'vendor_id');
                                    $optionvalue = array();
                                    if ($attributeFrontend->usesSource()) {
                                        $optionvalue = array(
                                            $seller->getProductVendorId() => array(
                                                0 => $data['storeinfo']['store_name']
                                            )
                                        );
                                        $optionsdata['option']['value'] = $optionvalue;
                                        $attributeFrontend->addData($optionsdata);
                                        $attributeFrontend->save();
                                    }
                                }
                                $id = $model->getId();
                                $model->setData($data['storeinfo']);
                                $model->setId($id);
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
                        $this->_redirect('*/*/index');
                    }
                }
            }
        }
        $this->_redirect('*/*/index');
        return;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_profile');
    }

}
