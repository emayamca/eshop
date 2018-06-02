<?php
class Emipro_Sellerproduct_Model_Resource_Sellerproductmodel extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('sellerproduct/sellerproductmodel', 'id');
    }
}