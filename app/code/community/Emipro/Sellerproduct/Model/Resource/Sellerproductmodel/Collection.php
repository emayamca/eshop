<?php
class Emipro_Sellerproduct_Model_Resource_Sellerproductmodel_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('sellerproduct/sellerproductmodel');
    }
}