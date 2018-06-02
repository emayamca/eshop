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

class Emipro_Marketplace_Model_Mysql4_Sellercategory extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        $this->_init("marketplace/sellercategory", "id");
    }

}
