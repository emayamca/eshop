<?php
class Fuel_Trackyourorder_Model_Trackyourorder extends Mage_Core_Model_Abstract{
    /**
     * class construct
     * @return void
     * 
     */
    public function _construct() {
        parent::_construct ();
        $this->_init ( 'trackyourorder/trackyourorder' );
    }
}