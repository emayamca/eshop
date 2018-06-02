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

class Emipro_SellerHelpdesk_Model_Helpdeskstatus extends Mage_Core_Model_Abstract {

    public function _construct() {

        parent::_construct();
        $this->_init('emipro_sellerhelpdesk/helpdeskstatus');
    }

}
