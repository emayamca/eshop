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

class Emipro_Advancecommission_Model_Resource_Advanceproductcommission extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        $this->_init('emipro_advancecommission/advanceproductcommission', 'advanceproductcomm_id');
    }

}
