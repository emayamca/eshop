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

class Emipro_BulkProductImport_Model_Import extends Mage_Catalog_Model_Convert_Adapter_Product {

    public function bulkImport(array $c) {
        try {
            $this->saveRow($c);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}

?>
