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

class Emipro_Stocknotification_Model_Observer {

	public function onBeforeSave($observer)
	{  
        if(Mage::app()->getRequest()->getModuleName()=='marketplace' && Mage::app()->getRequest()->getControllerName()=='adminhtml_index' && Mage::app()->getRequest()->getActionName()=='save'){
            if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'marketplace_adminhtml_index_save')
            {
                $object = $observer->getEvent()->getObject();
                $data= Mage::app()->getRequest()->getPost();
                $object->setLowStockEnable($data['stocknotification']['low_stock_enable']);
                $object->setLowStockQuantity($data['stocknotification']['low_stock_quantity']);
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
                if ($menu->marketplace->children->stocknotification) {
                    unset($menu->marketplace->children->stocknotification);
                }
            }
        }
        
    }
    
    
    public function sellerapprovecheck($observer){
        if (Mage::getDesign()->getArea() == 'adminhtml') {
            if($observer->getEvent()->getControllerAction()->getFullActionName()=='marketplace_adminhtml_index_sellerstatus')
            {
                $_id = Mage::app()->getRequest()->getParam('id');
                $_status = Mage::app()->getRequest()->getParam('status');
                if($_id!='' && $_status=='approved'){
                    $sellerobj = Mage::getModel('marketplace/list')->load($_id);
                    if($sellerobj->getId()&&  $sellerobj->getStatus()=='pending'){
                        $sellerdetail = Mage::getModel("marketplace/seller")->load($_id, "seller_id");
                        if($sellerdetail->getId()){
                            $qty=Mage::getStoreConfig('marketplace_setting/lowstocknotification/stockquantity');
                            $sellerdetail->setLowStockEnable(0);
                            $sellerdetail->setLowStockQuantity($qty);
                            try{
                                $sellerdetail->save();
                            }catch(Exception $e){
                                Mage::log($e->getMessage());
                            }
                        }
                    }
                }
            }
        }
    }
    public function checkoutSubmitAllAfter($observer)
    {  
        $order = $observer->getOrder();
        $tableheader = '<table border=0 cellspacing=10>
                                            <thead>
                                                <tr> 
                                                    <th >Items In Order</th>
                                                    <th>Qty</th>
                                                </tr>
                                            </thead> 
                                            <tbody>';
        $tablefooter.='</tbody></table>';
        $selleritems = array();
        $sellers = array();
        $rowhtml = array();
        foreach ($order->getAllVisibleItems() as $key => $_item) {
            if($_item->getSellerId())
            {
                $product_ids=array();
                $product_ids=$_item->getProductId();
                $seller = Mage::getModel("marketplace/seller")->load($_item->getSellerId(), "seller_id");
                $product = Mage::getModel('catalog/product')->load($_item->getProductId()); 
                $stocklevel = (int)Mage::getModel('cataloginventory/stock_item')
                                         ->loadByProduct($product)->getQty();
                $stockenable=$seller->getLowStockEnable();
                $stockqty=$seller->getLowStockQuantity();
                if($stockenable == 1 && $stocklevel<=$stockqty)
                {
                    $selleritems[$_item->getSellerId()][]=$product_ids;
                    $sellers[] = $_item->getSellerId();

                    $rowhtml[$_item->getProductId()] ='<tr><td>' . $_item->getName() . '</td>
                    <td>' . $stocklevel . '</td>
                    </tr>';
                }
            }
        }
        $configValue = Mage::getStoreConfig('marketplace_setting/lowstocknotification/stockenable');
        if($configValue)
        {
            if(count($sellers)){
                foreach ($selleritems as $sellerid => $prodid) {
                    $html='';
                    foreach($prodid as $productid)
                    {
                        $html.=$rowhtml[$productid];   
                    }
                    $sellerdetail = Mage::getModel('marketplace/seller')->load($sellerid, 'seller_id');
                    $selleremail=$sellerdetail->getEmail();
                    $Sellername=$sellerdetail->getFirstname();
                    $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
                    $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');  
                    $email = Mage::getModel('core/email_template')->loadByCode('low_stock_notification');
                    $emailTemplateVariable['name'] = $_sendername;
                    $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
                    $emailTemplateVariable['sellername'] = $sellername; 
                    //$emailTemplateVariable['order_id'] = $orderId; 
                    $emailTemplateVariable['product_html'] = $tableheader.$html.$tablefooter;
                    $email->setTemplateSubject('Low Stock Notification');
                    $email->setSenderName($_sendername);
                    $email->setSenderEmail($senderemail); 
                    if($sellerdetail->getLowStockEnable()==1)
                        $email->send($selleremail, $sellername, $emailTemplateVariable);
                }
            }
        }
    }
}
