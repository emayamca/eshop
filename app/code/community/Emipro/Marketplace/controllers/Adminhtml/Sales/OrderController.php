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

require_once "Mage/Adminhtml/controllers/Sales/OrderController.php";

class Emipro_Marketplace_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController {

    public function cancelAction() {
        if ($order = $this->_initOrder()) {
            if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
                try {
                    $order->cancel()
                            ->save();
                    $this->_getSession()->addSuccess(
                            $this->__('The order has been cancelled.')
                    );
                } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                    Mage::logException($e);
                }
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                if ($sellerid && $sellerid != '') {
                    $seller = Mage::getModel('marketplace/list')->load($sellerid);
                    if ($seller->getId()) {
                        $productids = array();
                        $optionid = Mage::helper("marketplace")->getProuctVendorid($seller->getSellerAdminId());
                        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
                        $productIDs = $productCollection->getAllIds();
                        foreach ($invoices as $invoiceCollection) {
                            foreach ($invoiceCollection->getAllItems() as $item) {
                                $itemId = $item->getData("product_id");
                                if (in_array($itemId, $productIDs)) {
                                    $parentIds[] = $item["parent_id"];
                                }
                            }
                        }
                        $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                                ->setOrderFilter($orderId);

                        if (isset($parentIds) && !empty($parentIds)) {
                            $invoices->addFieldtoFilter("entity_id", array("in", $parentIds));
                        } else {
                            $invoices->addFieldtoFilter("entity_id", '-1');
                        }
                        $invoices->load();
                    }
                }
                if ($invoices->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                                'invoice' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');

        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                if ($sellerid && $sellerid != '') {
                    $seller = Mage::getModel('marketplace/list')->load($sellerid);
                    if ($seller->getId()) {
                        $productids = array();
                        $optionid = Mage::helper("marketplace")->getProuctVendorid($seller->getSellerAdminId());
                        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
                        $productIDs = $productCollection->getAllIds();
                        foreach ($shipments as $shipmentCollection) {
                            foreach ($shipmentCollection->getAllItems() as $item) {
                                $itemId = $item->getData("product_id");
                                if (in_array($itemId, $productIDs)) {
                                    $parentIds[] = $item["parent_id"];
                                }
                            }
                        }
                        $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                                ->setOrderFilter($orderId);
                        if (isset($parentIds) && !empty($parentIds)) {
                            $shipments->addFieldtoFilter("entity_id", array("in", $parentIds));
                        } else {
                            $shipments->addFieldtoFilter("entity_id", '-1');
                        }
                        $shipments->load();
                    }
                }

                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                                'packingslip' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print creditmemos for selected orders
     */
    public function pdfcreditmemosAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                if ($sellerid && $sellerid != '') {
                    $seller = Mage::getModel('marketplace/list')->load($sellerid);
                    if ($seller->getId()) {
                        $productids = array();
                        $optionid = Mage::helper("marketplace")->getProuctVendorid($seller->getSellerAdminId());
                        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
                        $productIDs = $productCollection->getAllIds();
                        foreach ($creditmemos as $creditmemoCollection) {
                            foreach ($creditmemoCollection->getAllItems() as $item) {
                                $itemId = $item->getData("product_id");
                                if (in_array($itemId, $productIDs)) {
                                    $parentIds[] = $item["parent_id"];
                                }
                            }
                        }
                        $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                                ->setOrderFilter($orderId);

                        if (isset($parentIds) && !empty($parentIds)) {
                            $creditmemos->addFieldtoFilter("entity_id", array("in", $parentIds));
                        } else {
                            $creditmemos->addFieldtoFilter("entity_id", '-1');
                        }
                        $creditmemos->load();
                    }
                }


                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                                'creditmemo' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print all documents for selected orders
     */
    public function pdfdocsAction() {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        $isseller = false;
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid && $sellerid != '') {
            $seller = Mage::getModel('marketplace/list')->load($sellerid);
            if ($seller->getId()) {
                $productids = array();
                $optionid = Mage::helper("marketplace")->getProuctVendorid($seller->getSellerAdminId());
                $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
                $productIDs = $productCollection->getAllIds();
                $isseller = true;
            }
        }
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($isseller) {
                    foreach ($invoices as $invoiceCollection) {
                        foreach ($invoiceCollection->getAllItems() as $item) {
                            $itemId = $item->getData("product_id");
                            if (in_array($itemId, $productIDs)) {
                                $parentIds[] = $item["parent_id"];
                            }
                        }
                    }
                    $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                            ->setOrderFilter($orderId);

                    if (isset($parentIds) && !empty($parentIds)) {
                        $invoices->addFieldtoFilter("entity_id", array("in", $parentIds));
                    } else {
                        $invoices->addFieldtoFilter("entity_id", '-1');
                    }
                    $invoices->load();
                }

                if ($invoices->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_invoice')->getPdf($invoices);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }

                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($isseller) {
                    foreach ($shipments as $shipmentCollection) {
                        foreach ($shipmentCollection->getAllItems() as $item) {
                            $itemId = $item->getData("product_id");
                            if (in_array($itemId, $productIDs)) {
                                $parentIds[] = $item["parent_id"];
                            }
                        }
                    }
                    $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                            ->setOrderFilter($orderId);
                    if (isset($parentIds) && !empty($parentIds)) {
                        $shipments->addFieldtoFilter("entity_id", array("in", $parentIds));
                    } else {
                        $shipments->addFieldtoFilter("entity_id", '-1');
                    }
                    $shipments->load();
                }

                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_shipment')->getPdf($shipments);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                        ->setOrderFilter($orderId)
                        ->load();
                if ($isseller) {
                    foreach ($creditmemos as $creditmemoCollection) {
                        foreach ($creditmemoCollection->getAllItems() as $item) {
                            $itemId = $item->getData("product_id");
                            if (in_array($itemId, $productIDs)) {
                                $parentIds[] = $item["parent_id"];
                            }
                        }
                    }
                    $creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')
                            ->setOrderFilter($orderId);

                    if (isset($parentIds) && !empty($parentIds)) {
                        $creditmemos->addFieldtoFilter("entity_id", array("in", $parentIds));
                    } else {
                        $creditmemos->addFieldtoFilter("entity_id", '-1');
                    }
                    $creditmemos->load();
                }
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = Mage::getModel('sales/order_pdf_creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                                'docs' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
                );
            } else {
                $this->_getSession()->addError($this->__('There are no printable documents related to selected orders.'));
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

}
