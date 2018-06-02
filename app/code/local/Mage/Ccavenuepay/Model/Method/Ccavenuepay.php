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
 * @package    Mage_ccavenuepay
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



class Mage_Ccavenuepay_Model_Method_Ccavenuepay extends Mage_Payment_Model_Method_Abstract
{
    protected $_formBlockType = 'ccavenuepay/form_ccavenuepay';
    protected $_infoBlockType = 'ccavenuepay/info_ccavenuepay';
    protected $_canSaveCcavenuepay     = false;
	protected $_code  = 'ccavenuepay';

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_ccavenuepay_Model_Info
     */
    public function assignData($data)
    {
		if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setCcavenuepayType($this->getCcavenuepayAccountId1())	
			->setMerchant_Id($data->getMerchant_Id())
			->setOrder_Id($data->getOrder_Id())
			->setAmount($data->getAmount())			 
			->setCurrency($data->getCurrency())
			->setLanguage($data->getLanguage())
			->setCancel_Url($data->getCancel_Url())
			->setMerchant_Param($data->getMerchantParam())
			->setBilling_name($data->getBilling_name())			
			->setBilling_address($data->getBilling_address())
			->setBilling_city($data->getBilling_city())
			->setBilling_state($data->getBilling_state())
			->setBilling_zip($data->getBilling_zip())
			->setBilling_country($data->getBilling_country())
			->setBilling_tel($data->getBilling_tel())
			->setbilling_email($data->getbilling_email())
			->setDelivery_name($data->getDelivery_name())
			->setDelivery_address($data->getDelivery_address())
			->setDelivery_city($data->getDelivery_city())
			->setDelivery_state($data->getDelivery_state())
			->setDelivery_zip($data->getDelivery_zip())
			->setDelivery_country($data->getDelivery_country())
			->setDelivery_tel($data->getDelivery_tel())
			->setBilling_notes($data->getBilling_notes())
			->setDelivery_notes($data->getDelivery_notes())
			->setPayType($data->getPayType())
			 
			->setRedirect_Url($data->getRedirect_Url());
		
        return $this;
    }

    /**
     * Prepare info instance for save
     *
     * @return Mage_ccavenuepay_Model_Abstract
     */
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        if ($this->_canSaveCcavenuepay) {
            $info->setCcavenuepayNumberEnc($info->encrypt($info->getCcavenuepayNumber()));
        }
        $info->setCcavenuepayNumber(null)
            ->setCcavenuepayCid(null);
        return $this;
    }
	public function getProtocolVersion()
    {
        return '1.0';//$this->getConfigData('protocolversion');
    }
	
	/**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('ccavenuepay/session');
    }
    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
	/**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
	public function getStandardCheckoutFormFields($option = '')
    {
       if ($this->getQuote()->getIsVirtual()) 
	   {
            $a = $this->getQuote()->getBillingAddress();
            $b = $this->getQuote()->getShippingAddress();
        } 
		else 
		{
            $a = $this->getQuote()->getShippingAddress();
            $b = $this->getQuote()->getBillingAddress();
        }
		$data	= $this->getQuoteData($option);
		
 	/*
		$sArr = array
		(	
			'Merchant_Id' 			=> $data['Merchant_Id'],
			'Order_Id' 				=> $data['Order_Id'],
			'currency' 				=> $data['currency'],
			'Amount'				=> $data['Amount'],			
			'Redirect_Url'          => $data['Redirect_Url'],
			'cancel_url'            => $data['cancel_url'],			 
			'language'				=> $data['language'],
			'access_code'			=> $data['access_code'],	
			'billing_name' 			=> $data['billing_name'],
			'billing_address'  		=> $data['billing_address'],
			'billing_city' 			=> $data['billing_city'],
			'billing_state'    		=> $data['billing_state'],
			'billing_zip'      		=> $data['billing_zip'],
			'billing_country'  		=> $data['billing_country'],
			'billing_tel'      		=> $data['billing_tel'],
			'billing_email'    		=> $data['billing_email'],
			'billing_notes'    		=> $data['billing_notes'],
			'delivery_name'    		=> $data['delivery_name'],
			'delivery_address'     	=> $data['delivery_address'],
			'delivery_tel'       	=> $data['delivery_tel'],
			'delivery_notes'    	=> $data['delivery_notes'],
			'delivery_city' 		=> $data['delivery_city'],
			'delivery_country'     	=> $data['delivery_country'],
			'delivery_state'   		=> $data['delivery_state'],
			'delivery_zip'    		=> $data['delivery_zip'],
			'Merchant_Param1'		=> $data['Merchant_Param1'],						 			
		);  */
					
			$merchant_data	= 	'merchant_id='. $data['Merchant_Id'].
								'&order_id='.$data['Order_Id'].
								'&amount='.$data['Amount'].
								'&currency='.$data['currency'].
								'&redirect_url='.$data['Redirect_Url'].
								'&cancel_url='.$data['cancel_url'].
								'&language='.$data['language'].
								'&billing_name='.$data['billing_name'].
								'&billing_address='.$data['billing_address'].
								'&billing_city='.$data['billing_city'].
								'&billing_state='.$data['billing_state'].
								'&billing_zip='.$data['billing_zip'].
								'&billing_country='.$data['billing_country'].	
								'&billing_tel='.$data['billing_tel'].
								'&billing_email='.$data['billing_email'].
								'&delivery_name='.$data['delivery_name'].
								'&delivery_address='.$data['delivery_address'].
								'&delivery_city='.$data['delivery_city'].
								'&delivery_state='.$data['delivery_state'].
								'&delivery_country='.$data['delivery_country'].		
								'&delivery_zip='.$data['delivery_zip'].
								'&delivery_tel='.$data['delivery_tel']. 
								'&merchant_param1='.$data['Merchant_Param1'];
								 
		Mage::log("Mage_Ccavenuepay_Model_Method_Ccavenuepay start");			
		Mage::log($merchant_data);	
		Mage::log("Mage_Ccavenuepay_Model_Method_Ccavenuepay end");			
					
		$encryptionkey 	=  Mage::getStoreConfig('payment/ccavenuepay/encryptionkey');

		$encrypted_data	= $this->encrypt($merchant_data,$encryptionkey); // Method for encrypting the data.
		
		$form_input_array = array();
		$form_input_array['encRequest']	 = $encrypted_data;
		//$form_input_array['Merchant_Id'] = $data['Merchant_Id'];
		$form_input_array['access_code'] = $data['access_code'];
		
		 
        $sReq = '';
        $form_value_array = array();
	 
        //foreach ($sArr as $k=>$v) 
        foreach($form_input_array as $k=>$v) 
		{
           // $value =  str_replace("&","and",$v);
			$value =  $v;
            $form_value_array[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }  
		 
		
        return $form_value_array;
    }
	public function getCcavenuepayUrl()
    {
		 $url=$this->_getCcavenuepayConfig()->getCcavenuepayServerUrl();
         return $url;
    }
	public function getOrderPlaceRedirectUrl()
    {
	         return Mage::getUrl('ccavenuepay/ccavenuepay/redirect');
    }
	public function getQuoteData($option = '')
    {					
		if ($option == 'redirect') 
		{
    		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
    		$quote = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		} 
		else 
		{
			$quote = $this->getQuote();
		}
		$data=array();
		
		if ($quote)
		{
			if($quote->getShippingAddress())
			{
				if ($quote->getIsVirtual()) 
				{
					$a = $quote->getBillingAddress();
					$b = $quote->getShippingAddress();
				} 
				else 
				{
					$a = $quote->getShippingAddress();
					$b = $quote->getBillingAddress();
				}
			}
			else
			{
				$a = $quote->getBillingAddress();
				$b = $quote->getBillingAddress();
			}
			 
			
			
			$MerchantId 		= Mage::getStoreConfig('payment/ccavenuepay/merchantid');
			$OrderId 			= $this->getCheckout()->getLastRealOrderId();
			
			$Amount  			= Mage::app()->getStore()->roundPrice($quote->getGrandTotal());
			$encryptionkey 		= Mage::getStoreConfig('payment/ccavenuepay/encryptionkey');
			$Url 				= $this->_getCcavenuepayConfig()->getCcavenuepayRedirecturl();
			$cancel_url			= $this->_getCcavenuepayConfig()->getCcavenuepayCancelurl();
 			 
			$data['Merchant_Id'] 	= Mage::getStoreConfig('payment/ccavenuepay/merchantid');
			$data['Order_Id'] 		= $OrderId;
			$storeID				= Mage::app()->getStore()->getStoreId();
			//$data['currency']		= Mage::app()->getStore($storeID)->getCurrentCurrencyCode();
			$data['currency']		= 'INR';
			$data['Amount']  		= $Amount;
			$data['Redirect_Url']   = $Url;
			$data['cancel_url']   	= $cancel_url;
			$data['Merchant_Param1'] = $OrderId;
			 
			
			
			//$data['language']		= Mage::getStoreConfig('general/country/default');
			$data['language']		= "EN";			
		 	$data['access_code']	= Mage::getStoreConfig('payment/ccavenuepay/accesscode');
			
			if($this->getQuote()->getCustomer())
			{
				$email_id =$this->getQuote()->getCustomer()->getEmail();
			}
			$data['billing_name'] 		= $b->getFirstname()." ".$b->getLastname();
			$data['billing_address']	= $b->getStreet(1)."   ".$b->getStreet(2);
			$data['billing_city'] 		= $b->getCity();
			$data['billing_state'] 		= $b->getRegionCode();
			$data['billing_zip']   		= $b->getPostcode();
			$data['billing_country']	= $b->getCountryModel()->getName();
			$data['billing_tel'] 	    = $b->getTelephone();
			$data['billing_email'] 		= $quote->getCustomerEmail();
			$data['billing_notes'] 		= '';
		}
		
		 
		
		if(!$quote->getShippingAddress())
		{
			
			$data['delivery_name'] 		= '';
			$data['delivery_address']  	= '';
			$data['delivery_city']      = '';
			$data['delivery_state'] 	= '';
			$data['delivery_zip']  		= '';
			$data['delivery_country']  	= '';
			$data['delivery_tel']   	= '';
			$data['delivery_notes']   	= '';
		}	
		else
		{
			
			$data['delivery_name'] 		= $a->getFirstname()." ".$a->getLastname();
			$data['delivery_address']  	= $a->getStreet(1)."   ".$a->getStreet(2);
			$data['delivery_city']      = $a->getCity();
			$data['delivery_state'] 	= $a->getRegionCode();
			$data['delivery_zip']  		= $a->getPostcode();
			$data['delivery_country']  	= $a->getCountryModel()->getName();
			$data['delivery_tel']   	= $a->getTelephone();
			$data['billing_notes'] 		= '';
			$data['delivery_notes'] 	= '';
		}
		 
		
		return $data;
	}
		 
	protected function _getCcavenuepayConfig()
    {
         return Mage::getSingleton('ccavenuepay/config');
    }
	public function isAvailable($quote=null)
    {
        if (is_null($quote)) {
           return false;
        }
		$return = parent::isAvailable($quote);
		if($return==false)return false;
		
		return true;
	 }	
	 
	 
	function encrypt($plainText,$key)
	{
		$secretKey = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
	  	$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
	  	$blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
		$plainPad = $this->pkcs5_pad($plainText, $blockSize);
	  	if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) 
		{
		      $encryptedText = mcrypt_generic($openMode, $plainPad);
	      	      mcrypt_generic_deinit($openMode);
		      			
		} 
		return bin2hex($encryptedText);
	}

	function decrypt($encryptedText,$key)
	{
		$secretKey = $this->hextobin(md5($key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
		$encryptedText=$this->hextobin($encryptedText);
	  	$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
		mcrypt_generic_init($openMode, $secretKey, $initVector);
		$decryptedText = mdecrypt_generic($openMode, $encryptedText);
		$decryptedText = rtrim($decryptedText, "\0");
	 	mcrypt_generic_deinit($openMode);
		return $decryptedText;
		
	}
	//*********** Padding Function *********************

	 function pkcs5_pad ($plainText, $blockSize)
	{
	    $pad = $blockSize - (strlen($plainText) % $blockSize);
	    return $plainText . str_repeat(chr($pad), $pad);
	}

	//********** Hexadecimal to Binary function for php 4.0 version ********

	function hextobin($hexString) 
   	 { 
		$length = strlen($hexString); 
		$binString="";   
		$count=0; 
		while($count<$length) 
		{       
			$subString =substr($hexString,$count,2);           
			$packedString = pack("H*",$subString); 
			if ($count==0)
		{
			$binString=$packedString;
		} 
			
		else 
		{
			$binString.=$packedString;
		} 
			
		$count+=2; 
		} 
		return $binString; 
	} 
	
	
}
 