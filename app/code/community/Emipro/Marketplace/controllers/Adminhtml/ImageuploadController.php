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

class Emipro_Marketplace_Adminhtml_ImageuploadController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
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
        $this->getLayout()->getBlock('head')->setTitle($this->__('Multiple Image Upload') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('catalog/multiple_image_upload');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_imageupload_edit'));
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_imageupload_imagetable')->setTemplate('marketplace/imagetable.phtml'));
        $this->renderLayout();
    }

    public function saveAction() {
        $user = Mage::getModel('admin/session');
        if (isset($user)) {
            $userid = $user->getUser()->getId();
            if ($userid) {
                $seller = Mage::getModel("marketplace/list")->load($userid, "seller_admin_id");
                if ($seller->getId()) {
                    try {
                        $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), "seller_id");

                        if ($sellerdetail->getId() != '') {
                            $path = Mage::getBaseDir('media') . DS . 'import' . DS . $sellerdetail->getStoreUrl();
                            $configValue = Mage::getStoreConfig('marketplace_setting/marketplace/maxupload');
                            if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {

                                if (!empty($configValue)) {
                                    foreach ($_FILES['image']['name'] as $key => $item) {
                                        $file_totalsize += $_FILES['image']['size'][$key];
                                    }
                                    $file_totalsize = round($file_totalsize / 1024, 2);
                                    $total_allowed_size = $configValue * 1024;
                                    $directory_size = $this->foldersize($path);
                                    $directory_size = round($directory_size / 1024, 2);
                                    if ($total_allowed_size < ($file_totalsize + $directory_size)) {
                                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__(' Image folder size exceeds the limit ' . $configValue . "MB .... "));
                                        $this->_redirect('*/*/index');
                                        return;
                                    }
                                }
                                if (is_array($_FILES['image']['name'])) {
                                    foreach ($_FILES['image']['name'] as $key => $item) {
                                        $uploader = new Varien_File_Uploader(array(
                                            'name' => $_FILES['image']['name'][$key],
                                            'type' => $_FILES['image']['type'][$key],
                                            'tmp_name' => $_FILES['image']['tmp_name'][$key],
                                            'error' => $_FILES['image']['error'][$key],
                                            'size' => $_FILES['image']['size'][$key]
                                        ));
                                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                                        $uploader->setAllowRenameFiles(true);
                                        $uploader->setFilesDispersion(false);
                                        if (!is_dir($path)) {
                                            mkdir($path, 0777, true);
                                        }

                                        $uploader->save($path . DS, $_FILES['image']['name'][$key]);
                                    }
                                }
                            }
                        }
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Data saved successfully.'));
                        $this->_redirect('*/*/index');
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

    public function deleteimageAction() {
        $userid = Mage::helper('marketplace')->getSellerIdfromLoginUser();

        if ($userid) {
            $seller = Mage::getModel("marketplace/list")->load($userid, "id");
            if ($seller->getId()) {
                try {
                    $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), "seller_id");
                    if ($sellerdetail->getId() != '') {

                        $post = $this->getRequest()->getParam("image");
                        $image_name = explode(",", $post);
                        $path = Mage::getBaseDir('media') . DS . 'import' . DS . $sellerdetail->getStoreUrl();
                        foreach ($image_name as $image) {
                            unlink($path . "/" . $image);
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Image(s) are deleted successfully.'));
                    $this->_redirect('*/*/index');
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->_redirect('*/*/index');
                }
            }
        }
    }

    function foldersize($path) {
        $total_size = 0;
        $files = scandir($path);

        foreach ($files as $t) {
            if (is_dir(rtrim($path, '/') . '/' . $t)) {
                if ($t <> "." && $t <> "..") {
                    $size = foldersize(rtrim($path, '/') . '/' . $t);
                    $total_size += $size;
                }
            } else {
                $size = filesize(rtrim($path, '/') . '/' . $t);
                $total_size += $size;
            }
        }
        return $total_size;
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/multiple_image_upload');
    }

}
