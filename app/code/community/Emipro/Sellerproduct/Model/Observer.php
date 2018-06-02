<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Sellerproduct 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Sellerproduct_Model_Observer {

	public function onBeforeSave($observer)
	{ 
        if(Mage::app()->getRequest()->getModuleName()=='marketplace' && Mage::app()->getRequest()->getControllerName()=='adminhtml_index' && Mage::app()->getRequest()->getActionName()=='save')
        {
            if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'marketplace_adminhtml_index_save')
            {
                $post = Mage::app()->getRequest()->getPost();
                $link = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post['links']['sellerproducts']);
                $postdata = array_keys($link); 
                $object = $observer->getEvent()->getObject()->getorigData();
                //$sellerId = $object['seller_id'];
                $sellerId = Mage::app()->getRequest()->getParam('id');
                $model = Mage::getModel('sellerproduct/sellerproductmodel');
                $collections = $model->getCollection()->addFieldToFilter('seller_id',$sellerId);
                $data = $collections->getData();
                if(!empty($data))
                {
                    foreach ($data as $key => $val) 
                    {
                        if(isset($val['id']))
                        {
                            $models = Mage::getModel('sellerproduct/sellerproductmodel');
                            $modelId = intval($val['id']);
                            $models->load($modelId)->delete();
                        }
                    }
                }
                foreach ($postdata as $key => $value) 
                {
                    $position = $link[$value]["position"];
                    $position1 = $position!=null ? $position : 0;
                    if($sellerId != null)
                    {
                        $query = "insert into " . Mage::getConfig()->getTablePrefix() . "emipro_sellerproduct(seller_id, product_id, position) VALUES ($sellerId,$value,$position1)";
                        $result = Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
                    }
                }
            }
        }
    }

    public function adminhtml_block_html_before($observer) {
        $block = $observer->getBlock();
        if (!isset($block))
            return $this;

        if ($observer->getBlock() instanceof Mage_Adminhtml_Block_Page_Menu) {
            $config = Mage::getSingleton('admin/config')->getAdminhtmlConfig();
            $menu = $config->getNode('menu');
            if(!Mage::helper('marketplace')->getSellerIdfromLoginUser())
            {
                if ($menu->marketplace->children->sellerproduct) {
                    unset($menu->marketplace->children->sellerproduct);
                }
            }
        }
    }
}
