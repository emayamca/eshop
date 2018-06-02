<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @Author Emipro Technologies Private Limited
 * @Category Emipro
 * @Package  Emipro_SellerpriceRules
 * @License http://shop.emiprotechnologies.com/license-agreement/
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */

class Emipro_SellerpriceRules_Adminhtml_PricerulesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Dirty rules notice message
     *
     * @var string
     */
    protected $_dirtyRulesNoticeMessage;

    public function indexAction()
    {
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Seller Catalog Price Rules').' - '.Mage::getStoreConfig('general/store_information/name',Mage::app()->getStore()->getId())); 
		$this->_setActiveMenu('marketplace/pricerules');
		$this->_addContent($this->getLayout()->createBlock('emipro_sellerpricerules/adminhtml_priceRules'));
        $this->renderLayout();
    }
     public function gridAction()
    {
     $this->loadLayout();
        
       $this->getResponse()->setBody(
            $this->getLayout()->createBlock('emipro_sellerpricerules/adminhtml_priceRules_grid')->toHtml()
        );
    }

	protected function _isAllowed()
	{
			return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller_pricerules');
	}

}
