<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_FlexiShipping
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class Emipro_FlexiShipping_Model_Carrier_FlexiShipping extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'flexishipping';
 
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
		if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
            return false;
        }
		$handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
        $result = Mage::getModel('shipping/rate_result');
        $show = true;
        $arr=Mage::helper('emipro_flexishipping')->getFinal_Cost();
		
		if($arr["status"]==1){ // This if condition is just to demonstrate how to return success and error in shipping methods
			$method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($this->_code);
         $method->setMethodTitle($this->getConfigData('name'));
           	$method->setPrice($arr["fees"]);
			$method->setCost($arr["fees"]);
            $result->append($method);
		}else{
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('name'));
            $error->setErrorMessage("test");
            $result->append($error);
        }
      return $result;
    }
    public function getAllowedMethods()
    {
        return array('flexishipping'=>$this->getConfigData('name'));
    }
}

