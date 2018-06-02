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

class Emipro_Stocknotification_Helper_Data extends Mage_Core_Helper_Abstract {

	public function sendStocknotificationmail($selleremail,$sellername,$productname,$productsku,$productqty){            $senderemail = Mage::getStoreConfig('trans_email/ident_general/email');  
            $_sendername = Mage::getStoreConfig('trans_email/ident_general/name');  
            $email = Mage::getModel('core/email_template')->loadByCode('low_stock_notification');
            $emailTemplateVariable['name'] = $_sendername;
            $emailTemplateVariable['frontname'] = Mage::app()->getStore()->getFrontendName();
            $emailTemplateVariable['sellername'] = $sellername; 
            $emailTemplateVariable['product_name'] = $productname;
            $emailTemplateVariable['product_sku'] = $productsku;   
            $emailTemplateVariable['product_quantity'] = $productqty;
            $email->setTemplateSubject('Low Stock Notification');
            $email->setSenderName($_sendername);
            $email->setSenderEmail($senderemail); 
            $email->send($selleremail, $sellername, $emailTemplateVariable);  
    }
}

