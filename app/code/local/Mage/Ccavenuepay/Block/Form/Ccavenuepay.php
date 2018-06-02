<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Ccavenuepay
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
****
****
****
*/

class Mage_Ccavenuepay_Block_Form_Ccavenuepay extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
		$this->setTemplate('ccavenuepay/form/ccavenuepay.phtml');
    }

    /**
     * Retrieve direcpay configuration object
     *
     * @return Mage_Ccavenuepay_Model_Config
     */
    protected function _getCcavenuepayConfig()
    {
        return Mage::getSingleton('ccavenuepay/config');
		
    }
	

   
	/**
     * Retrieve availables credit card types
     *
     * @return array
     */
    public function getCcavenuepayServiceTypes()
    {
		 
		
         $types = $this->_getCcavenuepayConfig()->getCcavenuepayServiceTypes();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigData('Ccavenuepaytypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
		
        return $types;
    }
	
    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcavenuepayMonths()
    {
        $months = $this->getData('Ccavenuepay_months');
        if (is_null($months)) {
            $months[0] =  $this->__('Month');
            $months = array_merge($months, $this->_getCcavenuepayConfig()->getMonths());
            $this->setData('Ccavenuepay_months', $months);
        }
        return $months;
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcavenuepayYears()
    {
        $years = $this->getData('Ccavenuepay_years');
        if (is_null($years)) {
            $years = $this->_getCcavenuepayConfig()->getYears();
            $years = array(0=>$this->__('Year'))+$years;
            $this->setData('Ccavenuepay_years', $years);
        }
        return $years;
    }

    /**
     * Retrive has verification configuration
     *
     * @return boolean
     */
    public function hasVerification()
    {
        if ($this->getMethod()) {
            $configData = $this->getMethod()->getConfigData('useccv');
            if(is_null($configData)){
                return true;
            }
            return (bool) $configData;
        }
        return true;
    }
	public function getQuoteData()
    {
		return $this->getMethod()->getQuoteData();
	}
	public function getBillingAddress()
	{
		if ($this->getMethod())
		{
			$this->getMethod()->getQuote();
			$aa= $this->getMethod()->getQuote()->getBillingAddress()->getCountry();
		}
	}
}
