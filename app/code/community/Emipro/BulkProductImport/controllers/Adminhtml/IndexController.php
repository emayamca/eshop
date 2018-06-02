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

class Emipro_BulkProductImport_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Bulk Product Import') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('catalog/bulkproductimport');
        $this->_addContent($this->getLayout()->createBlock('emipro_bulkproductimport/adminhtml_bulkproductimport_edit'));
        $this->renderLayout();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/bulkproductimport');
    }

    public function saveAction() {
        $data = Mage::app()->getRequest()->getPost();
        if (isset($_FILES['csv']['name']) and ( file_exists($_FILES['csv']['tmp_name']))) {
            try {
                $uploader = new Varien_File_Uploader('csv');
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $file_name = "import_" . date(time()) . '.csv';
                $path = Mage::getBaseDir() . DS . 'var' . DS . 'import';
                $uploader->save($path, $file_name);

                $data['csv'] = $file_name;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/index');
            }
        }

        $session = Mage::getSingleton("admin/session");
        $session->setFileName($path . "/" . $data['csv']);
        $this->_redirect('*/*/importdata');
    }

    public function importdataAction() {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('emipro_bulkproductimport/adminhtml_importdata'));
        $this->renderLayout();
    }

    public function importrowdataAction() {
        $keys = json_decode($this->getRequest()->getParam('keys'));
        $row = json_decode($this->getRequest()->getParam('row'));

        $c = array_combine($keys, $row);
        //Mage::log($c,null,"before.log");
        if (array_key_exists('vendor_id', $c)) {
            unset($c['vendor_id']);
        }
        if (array_key_exists('marketplace_product_approve', $c)) {
            unset($c['marketplace_product_approve']);
        }
        if (array_key_exists('tax_class_id', $c)) {
            if ($c['tax_class_id'] == "")
                $c['tax_class_id'] = "None";
        }
        $c['store'] = 'admin';
        //Mage::log($c,null,"after.log");
        $response = Mage::getModel('bulkproductimport/import')->bulkImport($c);

        $this->getResponse()->setBody($response);
    }

    public function responseAction() {
        $count = $this->getRequest()->getParam('response');
        $this->getResponse()->setBody($count);
    }

    public function optionAction() {

        $data = $this->getRequest()->getParams();
        if ($data["set"] == "") {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__("Plese Select the attribute set"));
            $this->_redirect('*/*/index');
            return;
        } else {

            $field = "attribute_set,type,category_ids,sku,name,url_key,meta_title,meta_description,image,small_image,thumbnail,image_label,small_image_label,thumbnail_label,weight,price,special_price,status,visibility,tax_class_id,description,short_description,meta_keyword,news_from_date,news_to_date,special_from_date,special_to_date";
            $field_arr = explode(",", $field);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="downloaded.docx"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');

            echo '<table border=1>';
            $groups = Mage::getModel('eav/entity_attribute_group')
                    ->getResourceCollection()
                    ->setAttributeSetFilter($data["set"])
                    ->setSortOrder()
                    ->load();

            $attributeCodes = array();
            foreach ($groups as $group) {
                $groupName = $group->getAttributeGroupName();
                $groupId = $group->getAttributeGroupId();

                $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                        ->setAttributeGroupFilter($group->getId())
                        ->addVisibleFilter()
                        ->checkConfigurableProducts()
                        ->load();
                if ($attributes->getSize() > 0) {

                    foreach ($attributes->getItems() as $attribute) {
                        if ($attribute->getFrontendInput() == 'select') {
                            if ($attribute->getAttributeCode() != "vendor_id") {
                                echo '<tr><td>' . $attributeCodes[] = $attribute->getAttributeCode() . '</td>';

                                $values = $attribute->getSource()->getAllOptions(false);
                                echo '<td>';
                                foreach ($values as $value) {
                                    if (!empty($value['value']) || $value['label'] == 'No' || $value['label'] == 'None'):
                                        print_r($value['label']);
                                        echo "<br>";
                                    endif;
                                }
                                echo "</td>";

                                echo "</tr>";
                            }
                        }
                    }
                }
            }
        }
    }

    public function csvAction() {
        $data = $this->getRequest()->getParams();
        if ($data["set"] == "") {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__("Plese Select the attribute set"));
            $this->_redirect('*/*/index');
            return;
        }
        $set_id = $data["set"];
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
            if ($count == 2) {

                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=sample.csv");
                header("Pragma: no-cache");
                header("Expires: 0");

                $str.="," . $stock;
                $str.="\n";

                echo $str;
                echo $str1;
                exit;
            }
        }
        if ($count == 0) {
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('marketplace')->__("There are no products in that attribute set"));
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('marketplace')->__("Go to Marketplace -> Category - Attribute Set Mapping"));
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('marketplace')->__("View category for that selected attribute set"));
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('marketplace')->__("Add one or more product to this category manually"));
            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('marketplace')->__("And try again to download sample CSV of that attribute set"));
            $this->_redirect('*/*/index');
            return;
        } else {
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=sample.csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $str.="," . $stock;
            $str.="\n";

            echo $str;
            echo $str1;
            exit;
        }
    }

    public function indexdataAction() {
        $indexCollection = Mage::getModel('index/process')->getCollection();
        foreach ($indexCollection as $index) {
            $index->reindexAll();
        }
        echo $this->__("Finished profile execution.");
    }

}
