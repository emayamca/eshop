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

class Emipro_Marketplace_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Category - Attribute Set Mapping') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/category');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_category'));
        $this->renderLayout();
    }

    public function newAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Category - Attribute Set Mapping') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/category');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_category_edit'));
        $this->renderLayout();
    }

    public function editAction() {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            Mage::getSingleton('adminhtml/session')->addError('Seller can not Edit category.');
            $this->_redirect('*/*/index');
            return;
        }
        $id = $this->getRequest()->getParam('id');
        if ($id != '') {
            $data = Mage::getModel('marketplace/sellercategory')->load($id)->getData();
            Mage::register('seller_category', $data);
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Category - Attribute Set Mapping') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/category');
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_category_edit'));
        $this->renderLayout();
    }

    public function saveAction() {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            Mage::getSingleton('adminhtml/session')->addError('Seller can not save category.');
            $this->_redirect('*/*/index');
            return;
        }
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('marketplace/sellercategory');
        try {
            if (isset($data) && !empty($data)) {
                if (isset($id) && !empty($id)) {
                    $model->load($id);
                    unset($data['form_key']);
                    $model->setData($data);
                    $model->setId($id);
                    $model->save();
                } else {
                    $categories = Mage::getModel('marketplace/sellercategory')->getCollection();
                    $categories->addFieldToFilter('attributeset_id',$data['attributeset_id']);
                    $categories->addFieldToFilter('category_id',$data['category_id']);
                    if($categories->count() > 0) {
                        Mage::getSingleton('adminhtml/session')->addError('Category Already assigned.');
                        $this->_redirect('*/*/index');
                        return;
                    }
                    $model->setData($data);
                    $model->save();
                }
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('marketplace')->__('Error saving category data'));
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

    public function deleteAction() {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            Mage::getSingleton('adminhtml/session')->addError('Seller can not Delete data.');
            $this->_redirect('*/*/index');
            return;
        }
        $id = $this->getRequest()->getParam('id');
        if ($id != '') {
            try {
                $model = Mage::getModel('marketplace/sellercategory')->load($id);
                if ($model->getId()) {
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess('Data deleted successfully.');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Error while Deleting data.');
            }
        }
        $this->_redirect('*/*/index');
        return;
    }

    public function massDeleteAction() {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select Categories.'));
        } else {
            try {
                $Model = Mage::getModel('marketplace/sellercategory');
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

    public function getAttributesetfromcategoryAction() {
        $cate = $this->getRequest()->getPost('categoryid');
        if($cate!='')
        {
             Mage::getSingleton('adminhtml/session')->setCategoryIdCustom($cate); 
        } 
        $sellercategory = Mage::getModel('marketplace/sellercategory')->load($cate, 'category_id');
        if (isset($sellercategory) && $sellercategory['id']) {
            echo $sellercategory['attributeset_id'];
        }
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_category');
    }

}
