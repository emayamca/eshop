<?php

class Emipro_Advancecommission_Model_Observer {
    protected function _canAddTab($product){

        if ($product->getId()){
            return true;
        }
        if (!$product->getAttributeSetId()){
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable'){
            if ($request->getParam('attributes')){
                return true;
            }
        }
        return false;
    }
    
    public function addProductTabBlock($observer){
        $id = Mage::app()->getRequest()->getParam('id','');
        $type = Mage::app()->getRequest()->getParam('type','');
        $comm_basedon = Mage::getStoreConfig('marketplace_setting/categorycommission/commission_based_on');
        if($comm_basedon && ($type|| $id)){
            $block = $observer->getEvent()->getBlock();
            $product = Mage::registry('product');
            if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs){
                $block->addTab('advance_product_commission', array(
                    'label'     => Mage::helper('catalog')->__('Advance Product Commision'),
                    'content'   => Mage::app()->getLayout()->createBlock('emipro_advancecommission/adminhtml_advancecommission_edit_tab_productcomm')->toHtml(),
                )); 
            }
            return $this;
        }
    }
    
    public function detectProductChanges($observer)
    {
        $model = Mage::getModel('emipro_advancecommission/advanceproductcommission');
        $productdata = $observer->getProduct();
        $data = Mage::app()->getRequest()->getPost();
        
        $productId = $productdata->getId();
        $model->load($productId,'product_id');
        $modelproductId = $model->getData('product_id');
        $store = $productdata->getStoreId();
        if(isset($data['productcomm']['commission']))
        {
            try {
                unset($data['form_key']);
                $id = $productdata->getEntityId();
                $store = $productdata->getStoreId();
                $model->load($id,'product_id');
                if($model->getId())
                {
                    //echo $productdata->getEntityId();
                    if(!empty($data['productcomm']['commission'])){
                        $oldid = $model->getId();

                        $model->setData($data['productcomm']);
                        $model->setProductId($id);
                        $model->setStoreId($store);
                        $model->setAdvanceproductcommId($oldid)->save();
                    }else{
                        $model->delete();
                    }
                }
                else
                {
                    if(!empty($data['productcomm']['commission'])){
                    $model->setData($data['productcomm']);
                    $model->setProductId($id);
                    $model->setStoreId($store);
                    $model->save();
                    }
                }
               // Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('marketplace')->__('Data saved successfully.'));
                //$this->_redirect('*/*/index');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());       
            }
        }

        return $this;
    }
}