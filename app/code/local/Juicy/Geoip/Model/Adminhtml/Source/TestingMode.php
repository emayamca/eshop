<?php

class Juicy_Geoip_Model_Adminhtml_Source_TestingMode
{
    public function toOptionArray()
    {        
        $array = array(
            array('value' => '0', 'label' => Mage::helper('geoip')->__('No one (Disabled)')),
            array('value' => '1', 'label' => Mage::helper('geoip')->__('Current Administrators')),
            array('value' => '2', 'label' => Mage::helper('geoip')->__('Specific IP Addresses')),
            array('value' => '3', 'label' => Mage::helper('geoip')->__('Everyone')),           
        );       
    	return $array;
    }
}