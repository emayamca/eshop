<?php
class Emipro_Existingproduct_Adminhtml_ExistingproductController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Sell Existing Product'));
    	$this->renderLayout();   
	}
	public function editAction() {
        $id=$this->getRequest()->getParam('id');
        if($id)
        {   
            $order=Mage::getModel('catalog/product')->load($id);
            Mage::register('current_order', $order);
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    public function gridAction(){
         $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('existingproduct/adminhtml_grid')->toHtml()
        );
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/existingproduct');
    }
}
