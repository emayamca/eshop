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

class Emipro_Marketplace_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid {

    public function setCollection($collection) {
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if($sellerid){
            $orderitems = Mage::getModel('sales/order_item')->getCollection()->addFieldtoFilter('seller_id',$sellerid);
            foreach ($orderitems as $key => $item) {
                $orderIds[] = $item->getOrderId();
            }
            $collection = Mage::getResourceModel($this->_getCollectionClass());
            $collection->addFieldtoFilter("entity_id", array("in", $orderIds));
            Mage::getSingleton('core/session')->setCurrentOrderIds($orderIds);
        }
        parent::setCollection($collection);
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if (!$sellerid) {
            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
                $this->getMassactionBlock()->addItem('cancel_order', array(
                    'label' => Mage::helper('sales')->__('Cancel'),
                    'url' => $this->getUrl('*/sales_order/massCancel'),
                ));
            }

            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
                $this->getMassactionBlock()->addItem('hold_order', array(
                    'label' => Mage::helper('sales')->__('Hold'),
                    'url' => $this->getUrl('*/sales_order/massHold'),
                ));
            }

            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
                $this->getMassactionBlock()->addItem('unhold_order', array(
                    'label' => Mage::helper('sales')->__('Unhold'),
                    'url' => $this->getUrl('*/sales_order/massUnhold'),
                ));
            }
        }
        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
            'label' => Mage::helper('sales')->__('Print Invoices'),
            'url' => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
            'label' => Mage::helper('sales')->__('Print Packingslips'),
            'url' => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
            'label' => Mage::helper('sales')->__('Print Credit Memos'),
            'url' => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
            'label' => Mage::helper('sales')->__('Print All'),
            'url' => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
            'label' => Mage::helper('sales')->__('Print Shipping Labels'),
            'url' => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

}
