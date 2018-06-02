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

class Emipro_BulkProductImport_Block_Adminhtml_Importdata extends Mage_Adminhtml_Block_Widget_Form {

    function _construct() {
        parent::_construct();
        $this->setTemplate('bulkimport/import.phtml');
    }

}
