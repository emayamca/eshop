<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Stocknotification 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Stocknotification_Adminhtml_StocknotificationController extends Mage_Adminhtml_Controller_Action 
{

    public function indexAction() 
    {
        $this->_forward('edit');
	}
	public function editAction()
	{  
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        // echo $sellerid;
        // exit;
        if ($sellerid) {
            $seller = Mage::getModel("marketplace/seller")->load($sellerid, "seller_id");
            Mage::register('store_info', $seller);
        }
	 	$this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Stocknotification Page'));
        $this->_setActiveMenu('marketplace/stocknotification');
        $this->_addContent($this->getLayout()->createBlock('stocknotification/adminhtml_stocknotification_edit'));
        $this->renderLayout();
    }

    public function formAction()
    {
        $id=$this->getRequest()->getParam('id');
        if ($id) {
            $seller = Mage::getModel("marketplace/seller")->load($id, "seller_id");
            Mage::register('store_info', $seller);
        }
       
       echo $this->getLayout()->createBlock('stocknotification/adminhtml_stocknotification_edit_form')->toHtml();
    }
    public function saveAction(){
        $id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('marketplace/seller');
        $model->load($id, 'seller_id');
        $oldid = $model->getId();
        if(isset($data['stocknotification']))
        {          
            $model->setData($data['stocknotification']);
            $model->setId($oldid);
            $model->save();
        }
        $this->_forward('edit');
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/stocknotification');
    }
}