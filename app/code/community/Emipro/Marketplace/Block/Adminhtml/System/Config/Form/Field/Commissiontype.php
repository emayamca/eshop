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

class Emipro_Marketplace_Block_Adminhtml_System_Config_Form_Field_Commissiontype extends Mage_Core_Block_Html_Select {

    private $_chargetype;
    protected $_addChargeAllOption = true;

    public function getCommissiontype() {
        $methods = array();

        return array(
            '0' => Mage::helper('marketplace')->__('Percent'),
            '1' => Mage::helper('marketplace')->__('Fixed')
        );
        //return $methods;
    }

    public function setInputName($value) {
        return $this->setName($value);
    }

    public function _toHtml() {

        if (!$this->getOptions()) {
            if ($this->_addChargeAllOption) {
                $this->addOption(Mage_Customer_Model_Group::CUST_GROUP_ALL, Mage::helper('marketplace')->__('--Select--'));
            }
            foreach ($this->getCommissiontype() as $key => $Titles) {

                $this->addOption($key, addslashes($Titles));
            }
        }
        return parent::_toHtml();
    }

}
