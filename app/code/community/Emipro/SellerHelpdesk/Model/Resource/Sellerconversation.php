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

class Emipro_SellerHelpdesk_Model_Resource_Sellerconversation extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        $this->_init('emipro_sellerhelpdesk/sellerconversation', 'conversation_id');
    }

}
