<?php

class Pumcp_Payments_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $pumcp = Mage::getModel('pumcp/checkout');		
		//Mage::log('pumcp - '.$pumcp->getAdnlInfo(), Zend_Log::EMERG);
		if($pumcp->getAdnlInfo()== 'payumoney')
		{
			//Mage::log('pumcp payumoney ok', Zend_Log::EMERG);
			$environment =          Mage::getStoreConfig('payment/pumcp/environment');
			$pumkey = 		Mage::getStoreConfig('payment/pumcp/pumkey');	
			$pumsalt =		Mage::getStoreConfig('payment/pumcp/pumsalt');	
			
			$ordid = Mage::getSingleton('checkout/session')->getLastOrderId();
    	    $order = Mage::getModel('sales/order')->load($ordid);
        
	        $txnid = $order->getIncrementId();
    	    $amount = $order->getGrandTotal();
        	$amount = number_format((float)$amount, 2, '.', '');
        	
			$action = 'https://secure.payu.in/_payment.php';
			
			if($environment == 'sandbox')
				$action = 'https://sandboxsecure.payu.in/_payment.php';
			
			$currency = $order->getOrderCurrencyCode();
        	$billingAddress = $order->getBillingAddress();
			$productInfo  = "Product Information";
	        $baseurl = Mage::getBaseUrl();
			
			$firstname = $billingAddress->getData('firstname');
			$lastname = $billingAddress->getData('lastname');
			$zipcode = $billingAddress->getData('postcode');
			$email = $billingAddress->getData('email');
			$phone = $billingAddress->getData('telephone');
			$address = $billingAddress->getData('street');
        	$state = $billingAddress->getData('region');
        	$city = $billingAddress->getData('city');
        	$country = $billingAddress->getData('country');
			$Pg = 'CC';
			$surl = $baseurl."pumcp/redirect/payumpayment/";
			$furl = $baseurl."pumcp/redirect/payumpayment/";
			$curl = $baseurl."pumcp/redirect/payumpayment/";
			
			$udf5="Magento_v_1.9.2.4";
			
			$hash=hash('sha512', $pumkey.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'||||||'.$pumsalt); 
			$user_credentials = $pumkey.':'.$email;		
			$service_provider = 'payu_paisa';
	
			$html = "<html><body><form action=\"".$action ."\" method=\"post\" id=\"payu_form\" name=\"payu_form\">
						<input type=\"hidden\" name=\"key\" value=\"". $pumkey. "\" />
						<input type=\"hidden\" name=\"txnid\" value=\"".$txnid."\" />
						<input type=\"hidden\" name=\"amount\" value=\"".$amount."\" />
						<input type=\"hidden\" name=\"productinfo\" value=\"".$productInfo."\" />
						<input type=\"hidden\" name=\"firstname\" value=\"". $firstname."\" />
						<input type=\"hidden\" name=\"Lastname\" value=\"". $lastname."\" />
						<input type=\"hidden\" name=\"Zipcode\" value=\"". $zipcode. "\" />
						<input type=\"hidden\" name=\"email\" value=\"". $email."\" />
						<input type=\"hidden\" name=\"phone\" value=\"".$phone."\" />
						<input type=\"hidden\" name=\"surl\" value=\"". $surl. "\" />
						<input type=\"hidden\" name=\"furl\" value=\"". $furl."\" />
						<input type=\"hidden\" name=\"curl\" value=\"".$curl."\" />
						<input type=\"hidden\" name=\"Hash\" value=\"".$hash."\" />
						<input type=\"hidden\" name=\"Pg\" value=\"". $Pg."\" />
						<input type=\"hidden\" name=\"service_provider\" value=\"". $service_provider ."\" />
						<input type=\"hidden\" name=\"address1\" value=\"".$address ."\" />
				        <input type=\"hidden\" name=\"address2\" value=\"\" />
					    <input type=\"hidden\" name=\"city\" value=\"". $city."\" />
				        <input type=\"hidden\" name=\"country\" value=\"".$country."\" />
				        <input type=\"hidden\" name=\"state\" value=\"". $state."\" />
						<input type=\"hidden\" name=\"udf5\" value=\"". $udf5."\" />
				        <button style='display:none' id='submit_payum_payment_form' name='submit_payum_payment_form'>Pay Now</button>
					</form>
					<script type=\"text/javascript\">document.getElementById(\"payu_form\").submit();</script>
					</body></html>";
					Mage::log($html, Zend_Log::EMERG);
			return $html;
		}//payumoney
        
		if($pumcp->getAdnlInfo()== 'citruspay')
		{
			//Mage::log('pumcp citruspay ok', Zend_Log::EMERG);
			$apiKey = Mage::getStoreConfig('payment/pumcp/apikey');
        	$vanityUrl = Mage::getStoreConfig('payment/pumcp/vanityurl');
	        $merchantAccess = Mage::getStoreConfig('payment/pumcp/accesskey');
    	    $environment = Mage::getStoreConfig('payment/pumcp/environment');
        
	        $ordid = Mage::getSingleton('checkout/session')->getLastOrderId();
    	    $order = Mage::getModel('sales/order')->load($ordid);
        
	        $txnid = $order->getIncrementId();
    	    $amount = $order->getGrandTotal();
        	$amount = number_format((float)$amount, 2, '.', '');
        	
	        $baseurl = Mage::getBaseUrl();
    	    $returnUrl = $baseurl."pumcp/redirect/citruspayment/";
        	$notifyUrl = $baseurl."pumcp/redirect/citrusnotify/";
	        $submit_url = Mage::getUrl('pumcp/redirect/citruspayment/', array('_secure' => true));       

    	    $currency = $order->getOrderCurrencyCode();
        	$billingAddress = $order->getBillingAddress();
	        $nameFlag = Mage::getStoreConfig('payment/motowallet/passName');
    	    $emailFlag = Mage::getStoreConfig('payment/motowallet/passEmail');
        	$addFlag = Mage::getStoreConfig('payment/motowallet/passAddress');
	        $phoneFlag = Mage::getStoreConfig('payment/motowallet/passPhone');
    	    $firstName = $billingAddress->getData('firstname');
        	$lastName = $billingAddress->getData('lastname');
	        $email = $billingAddress->getData('email');
    	    $street = $billingAddress->getData('street');
        	$city = $billingAddress->getData('city');
	        $postcode = $billingAddress->getData('postcode');
    	    $region = $billingAddress->getData('region');
        	$country = $billingAddress->getData('country');
	        $telephone = $billingAddress->getData('telephone');
        
    	    $data = $vanityUrl.$amount.$txnid.$currency;
        	$signatureData = self::_generateHmacKey($data, $apiKey);
                        
	        $icpURL = "https://sboxcontext.citruspay.com/kiwi/kiwi-popover";
        
    	    $html = "<form name=\"pumcp-form\" id=\"pumcp-form\" method=\"POST\">
                    <input type=\"hidden\" name=\"pumcp_payment_id\" id=\"pumcp_payment_id\" />
                    <input type=\"hidden\" name=\"merchant_order_id\" id=\"order_id\" value=\"".$txnid."\"/>
                    <input type=\"hidden\" name=\"citrus_ret\" id=\"citrus_ret\" value=\"\"/>
					<button style='display:none' id='submit_citrus_payment_form' name='submit_citrus_payment_form' disabled onclick='launchICP(); return false;'>Pay Now</button>
                </form>";
        
        	$js = "<script>";
        
	        $js .= "
    		function launchICP() {
				
				if(typeof citrusICP == 'undefined') return false;
				
			//enable button
			//document.getElementById('#submit_citrus_payment_form').disabled = false;
        	    
	        var dataObj = {
	            orderAmount:'". $amount."',
	            currency:'". $currency."',
	            phoneNumber:'". $telephone."',
	            email:'". $email."',
	            merchantTxnId:'". $txnid."',
	            secSignature:'". $signatureData."',
	            firstName:'". $firstName."',
	            lastName:'". $lastName."',
	            addressStreet1:'". $street."',
	            addressCity:'". $city."',
	            addressState:'". $region."',
	            addressCountry:'". $country."',
	            addressZip:'". $postcode."',
	            vanityUrl:'". $vanityUrl."',
	            returnUrl:'". $returnUrl."',
				notifyUrl:'". $notifyUrl."',
	            mode:'". "dropAround"."'
	        };
	    ";
		
			$js .="var icpURL ='".$icpURL."';";

			$js .="var configObj = {};";
	        $js .=" configObj = {";
    	    if ($environment == "sandbox")
        	{
				$js .=" icpUrl: '".$icpURL."',";
	        }		
			$js .=" 
				eventHandler: function (cbObj) {				
				if (cbObj.event === 'icpLaunched') {
					console.log('Citrus ICP pop-up is launched');
				} else if (cbObj.event === 'icpClosed') {
					console.log(JSON.stringify(cbObj.message));				
				 	console.log('Citrus ICP pop-up is closed');
				 }
			  } 
			};
		";
        
			$js .="  try {
	            citrusICP.launchIcp(dataObj, configObj);
	        }
	        catch (error) {
	            console.log(error);
	        }
	    }";
     
				
    	    $js .= 'var checkoutOrderBtn = $$("button.btn-checkout");
                if(checkoutOrderBtn.length == 0)  checkoutOrderBtn = $$("button:contains(\'Place Order\')");
                checkoutOrderBtn[0].removeAttribute("onclick");
                checkoutOrderBtn[0].observe("click", launchICP);        
                ';
                
        	$js .="console.log('start timer');";
        
	        $js .= "setTimeout(launchICP, 1800);";
        
    	    $js .= '</script>';
        
        	return $html.$js;
		} //citrusicp		
        
    }
    
    private static function _generateHmacKey($data, $apiKey=null){
    	$hmackey = Zend_Crypt_Hmac::compute($apiKey, "sha1", $data);
    	return $hmackey;
    }
    
}

?>
