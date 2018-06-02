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

class Emipro_BulkProductImport_Adminhtml_ExportController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Export Products') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('catalog/export');
        $this->_addContent($this->getLayout()->createBlock('emipro_bulkproductimport/adminhtml_exportproduct_edit'));
        $this->renderLayout();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/export');
    }

    public function saveAction() {
        $data = $this->getRequest()->getParams();
        if ($data["attribute_set"] == "") {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__("Plese Select the attribute set"));
            $this->_redirect('*/*/index');
            return;
        }
        $set_id = $data["attribute_set"];
        $str = "attribute_set,type,category_ids,sku,name,url_key,meta_title,meta_description,image,small_image,thumbnail,image_label,small_image_label,thumbnail_label,weight,price,special_price,status,visibility,tax_class_id,description,short_description,meta_keyword,news_from_date,news_to_date,special_from_date,special_to_date";
        $stock = "qty,min_qty,min_sale_qty,max_sale_qty,is_in_stock,notify_stock_qty,manage_stock,qty_increments,product_name,product_type_id";
        $stock_array = explode(",", $stock);

        $attribute_text = array('status', 'visibility');
        $marketplace = array('marketplace_product_approve', 'vendor_id');
        $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($set_id)
                ->setSortOrder()
                ->load();

        $custom_attr = "";
        foreach ($groups as $node) {

            $nodeChildren = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->setAttributeGroupFilter($node->getId())
                    ->addVisibleFilter()
                    ->checkConfigurableProducts()
                    ->addFieldToFilter("is_user_defined", 1)
                    ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    if ($child->getFrontendInput() == "select" || $child->getFrontendInput() == "multiselect") {
                        if (in_array($child->getAttributeCode(), $marketplace)) {
                            
                        } else {
                            $custom_attr.="," . $child->getAttributeCode();
                        }
                    }
                    if (in_array($child->getAttributeCode(), $marketplace)) {
                        
                    } else {
                        $str.="," . $child->getAttributeCode();
                    }
                }
            }
        }
        $custom_attr_array = explode(",", $custom_attr);
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        $data = explode(",", $str);
        $seller = Mage::getModel('marketplace/list')->load($seller_id);
        $vendor_id = $seller->getProductVendorId();
        $pro = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('attribute_set_id', $set_id)
                ->addFieldtoFilter("vendor_id", $vendor_id);

        $str1 = "";
        $count = 0;

        foreach ($pro as $productid) {
            $productdata = Mage::getModel('catalog/product')->load($productid['entity_id']);
            $_productdata = $productdata->getData();

            foreach ($data as $d) {
                if ($d == "type") {
                    $d = "type_id";
                    $str1.='"' . $_productdata[$d] . '",';
                } else if ($d == "category_ids") {
                    $cat = implode(",", $productdata->getCategoryIds());
                    $str1.='"' . $cat . '",';
                } else if ($d == "attribute_set") {
                    $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
                    $attributeSetModel->load($productdata->getAttributeSetId());
                    $attributeSetName = $attributeSetModel->getAttributeSetName();

                    $str1.='"' . $attributeSetName . '",';
                } else if ($d == "tax_class_id") {
                    if ($_productdata[$d] == 0) {
                        $str1.='"' . None . '",';
                    } else {
                        $taxClass = Mage::getModel('tax/class')->load($_productdata[$d]);
                        $str1.='"' . $taxClass->getClassName() . '",';
                    }
                } else if (in_array($d, $attribute_text) || in_array($d, $custom_attr_array)) {
                    $str1.='"' . str_replace('"', '""', $productdata->getAttributeText($d)) . '",';
                } else if ($d == "image" || $d == "small_image" || $d == "thumbnail") {
                    $pos = strrpos($_productdata[$d], "/");
                    $str1.='"' . substr($_productdata[$d], $pos + 1) . '",';
                } else {
                    $str1.='"' . str_replace('"', '""', $_productdata[$d]) . '",';
                }
            }
            foreach ($stock_array as $stockdata) {

                $str1.='"' . str_replace('"', '""', $_productdata["stock_item"][$stockdata]) . '",';
            }
            $count++;
            $str1.="\n";
        }
        if ($count > 0) {
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=export.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $str.="," . $stock;
            $str.="\n";

            echo $str;
            echo $str1;
            exit;
        } else {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('marketplace')->__("There are no products in that attribute set"));
            $this->_redirect('*/*/index');
        }
    }

}
