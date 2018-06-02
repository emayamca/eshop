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

class Emipro_Marketplace_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Template {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('marketplace/dashboard/index.phtml');
    }

    protected function _prepareLayout() {
        $this->setChild('sales', $this->getLayout()->createBlock('marketplace/adminhtml_dashboard_sales')
        );

        $this->setChild('paymentreport', $this->getLayout()->createBlock('marketplace/adminhtml_dashboard_paymentreport')
        );

        $this->setChild('lastOrders', $this->getLayout()->createBlock('marketplace/adminhtml_dashboard_orders_grid')
        );
        parent::_prepareLayout();
    }

    public function getSwitchUrl() {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, 'period' => null));
    }

}
