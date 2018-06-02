<?php
class Emipro_Sellerproduct_Adminhtml_SellerproductController extends Mage_Adminhtml_Controller_Action 
{
    public function indexAction(){
        $this->loadLayout();
        $this->_setActiveMenu('marketplace/emipro_sellerproduct');
        $this->_title($this->__("Display Product on Seller Profile"));   
        $this->_addContent($this->getLayout()->createBlock('sellerproduct/adminhtml_sellerproduct_edit'));
        $this->_addLeft($this->getLayout()->createBlock('sellerproduct/adminhtml_sellerproduct_edit_tabs'));
        $this->renderLayout();
    }
    public function productsAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('sellerproduct.grid')->setSelectedProduct($this->getRequest()->getPost('product_ids', null));
        $this->renderLayout();
    }

    public function productsgridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('sellerproduct.grid')->setSelectedProduct($this->getRequest()->getPost('product_ids', null));
        $this->renderLayout();
    }
    public function saveAction()
    {
        $post = $this->getRequest()->getPost();
        $link = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post['links']['sellerproducts']);
        $postdata = array_keys($link);
        
        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()){
            $user = Mage::getSingleton('admin/session')->getUser();
            $seller = Mage::getModel('marketplace/list')->load($user->getUserId(), 'seller_admin_id');
            $sellerId = $seller->getId();
            if ($sellerId) {
                $model = Mage::getModel('sellerproduct/sellerproductmodel');
                $collections = $model->getCollection()->addFieldToFilter('seller_id',$sellerId);
                $data = $collections->getData();
                $models = Mage::getModel('sellerproduct/sellerproductmodel');
                foreach ($data as $key => $value) 
                {
                    $modelId = intval($value['id']);
                    $models->load($modelId)->delete();
                }
                foreach ($postdata as $key => $value) 
                {
                    $modelsave = Mage::getModel('sellerproduct/sellerproductmodel');
                    $modelsave->setData('seller_id',$sellerId);
                    $modelsave->setData('product_id',$value);
                    $modelsave->setData('position',$link[$value]["position"]);
                    $modelsave->save();    
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sellerproduct')->__('Data saved successfully.'));
            }
            else{
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sellerproduct')->__('Data Not saved.'));
            }
        }
        else{
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sellerproduct')->__('Please login in Marketplace.'));
        }
        $this->_redirect('*/*/index');
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/sellerproduct');
    }
}