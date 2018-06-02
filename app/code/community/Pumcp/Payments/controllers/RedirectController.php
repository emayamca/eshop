<?php
class Pumcp_Payments_RedirectController extends Mage_Core_Controller_Front_Action {
	protected $order;
	public function getCheckout() {
		return Mage::getSingleton ( 'checkout/session' );
	}
	protected function _expireAjax() {
		if (! Mage::getSingleton ( 'checkout/session' )->getQuote ()->hasItems ()) {
			$this->getResponse ()->setHeader ( 'HTTP/1.1', '403 Session Expired' );
			exit ();
		}
	}
	public function indexAction() {
		$this->loadLayout ();
		$block = $this->getLayout ()->createBlock ( 'pumcp/redirect' );
		$this->getLayout ()->getBlock ( 'content' )->append ( $block );
		$this->renderLayout ();
	}
	
	
	
	private static function _generateHmacKey($data, $apiKey = null) {
		$hmackey = Zend_Crypt_Hmac::compute ( $apiKey, "sha1", $data );
		return $hmackey;
	}
	
	public function payumpaymentAction() {
		$pumkey = 		Mage::getStoreConfig('payment/pumcp/pumkey');	
		$pumsalt =		Mage::getStoreConfig('payment/pumcp/pumsalt');	

		if ($this->getRequest ()->isPost ()) {
			
			$postdata = $this->getRequest ()->getPost ();			
			
			if (isset($postdata ['key']) && ($postdata['key'] == $pumkey)) {
				$ordid = $postdata['txnid'];
    	    	$order = Mage::getModel('sales/order')->loadByIncrementId($ordid);

				$amount      		= 	$postdata['amount'];
				$productInfo  		= 	$postdata['productinfo'];
				$firstname    		= 	$postdata['firstname'];
				$email        		=	$postdata['email'];
				$udf5 				= 	$postdata['udf5'];
				$keyString 	  		=  	$pumkey.'|'.$ordid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||' . $udf5. '|||||';
				$keyArray 	  		= 	explode("|",$keyString);
				$reverseKeyArray 	= 	array_reverse($keyArray);
				$reverseKeyString	=	implode("|",$reverseKeyArray);
				
				if (isset($postdata['status']) && $postdata['status'] == 'success') {
				 	$saltString     = $pumsalt.'|'.$postdata['status'].'|'.$reverseKeyString;
					$sentHashString = strtolower(hash('sha512', $saltString));
				 	$responseHashString=$postdata['hash'];
				
					$message = '';
					$message .= 'orderId: ' . $ordid . "\n";
					$message .= 'Transaction Id: ' . $postdata['mihpayid'] . "\n";
					foreach($postdata as $k => $val){
						$message .= $k.': ' . $val . "\n";
					}
					if($sentHashString==$responseHashString){
						// success	
						Mage::getSingleton ( 'core/session' )->addSuccess ( "PayU Money payment successful.." );
						$order->setState ( Mage_Sales_Model_Order::STATE_PROCESSING, true );
						$order->setTotalPaid ( $amount );
						$order->addStatusHistoryComment ( $message )->setIsVisibleOnFront ( false )->setIsCustomerNotified ( false );
						$order->save ();
						$order->sendNewOrderEmail ();
						
						foreach ( $order->getInvoiceCollection () as $invoice ) {
							// $invoiceIncrementIds[] = $invoce->getIncrementId();
							// $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID)->save();
							$invoice->capture ()->save ();
							//$payment->pay($invoice);
						}
						//save payment additional info
						$payment = $order->getPayment();
   						$payment->setAdditionalInformation('payumoney');
						$payment->save();
						$this->_redirect ( 'checkout/onepage/success' );
					}
					else {
						//tampered
						$order->setState ( Mage_Sales_Model_Order::STATE_NEW, true );
						$order->addStatusHistoryComment ( "PayU Money Response signature does not match. You might have received tampered data" )->setIsVisibleOnFront ( false )->setIsCustomerNotified ( false );
						$order->cancel ()->save ();
						Mage::getSingleton ( 'checkout/session' )->setErrorMessage ( "<strong>Error:</strong> PayU Money Response signature does not match. You might have received tampered data" );
						Mage::log ( "PayU Money Response signature did not match " );
						$this->_redirect ( 'checkout/onepage/failure' );
					}
				} else {
		    		if(isset($postdata['status']) && $postdata['unmappedstatus'] == 'userCancelled')
					{
						//usercancelled
						$order->setState ( Mage_Sales_Model_Order::STATE_CANCELED, true );
						// Inventory updated
						$this->updateInventory ( $ordid );
						$order->cancel ()->save ();
						Mage::getSingleton ( 'checkout/session' )->setErrorMessage ( "<strong>Error:</strong> Payment cancelled... <br/>" );
						$this->_redirect ( 'checkout/onepage/failure' );
					}
					else {
						//failure
						$order->setState ( Mage_Sales_Model_Order::STATE_CANCELED, true );
						// Inventory updated
						$this->updateInventory ( $ordid );
						$order->cancel ()->save ();
						Mage::getSingleton ( 'checkout/session' )->setErrorMessage ( "<strong>Error:</strong> Failed to process payment... <br/>" );
						$this->_redirect ( 'checkout/onepage/failure' );
					}					
				} 
			}
		}
	}
	
	
	public function citruspaymentAction() {
		$txnid = "";
		$txnrefno = "";
		$TxStatus = "";
		$txnmsg = "";
		$firstName = "";
		$lastName = "";
		$email = "";
		$street1 = "";
		$city = "";
		$state = "";
		$country = "";
		$pincode = "";
		$mobileNo = "";
		$signature = "";
		$reqsignature = "";
		$pgtxnno = "";
		$data = "";
		$authidcode = "";
		$flag = "dataValid";
		$respdata = "";
		$pincode = "";
		$issuerrefno = "";
		
		$orderid = - 1;
		
		$order = Mage::getModel ('sales/order');
		
		$apiKey = Mage::getStoreConfig ( 'payment/pumcp/apikey' );
		$merchantAccess = Mage::getStoreConfig ( 'payment/pumcp/accesskey' );
		$signatureFlag = 'Y';
		
		if ($this->getRequest ()->isPost ()) {
			
			$postdata = $this->getRequest ()->getPost ();			
			
			if (isset($postdata ['TxStatus'])) {
				
				$pdata = $postdata;
				
				// Mage::log("Wallet Response ". http_build_query($pdata));
				$txnrefno = $pdata ['TxRefNo'];
				$respdata .= "<br/><strong>Citrus Transaction Id: </strong>" . $txnrefno;
				
				$txnid = $pdata ['TxId'];
				$orderid = $txnid;
				$order->loadByIncrementId ( $orderid );
				
				$TxStatus = $pdata ['TxStatus'];
				$respdata .= "<br/><strong>Transaction Status: </strong>" . $TxStatus;
				
				$amount = $pdata ['amount'];
				$respdata .= "<br/><strong>Amount: </strong>" . $amount;
				
				if (isset ( $pdata ['firstName'] )) {
					$firstName = $pdata ['firstName'];
					$respdata .= "<br/><strong>First Name: </strong>" . $firstName;
				}
				
				if (isset ( $pdata ['lastName'] )) {
					$lastName = $pdata ['lastName'];
					$respdata .= "<br/><strong>Last Name: </strong>" . $lastName;
				}
				
				if (isset ( $pdata ['pgRespCode'] )) {
					$pgrespcode = $pdata ['pgRespCode'];
					$respdata .= "<br/><strong>PG Response Code: </strong>" . $pgrespcode;
				}
				
				if (isset ( $pdata ['pgTxnNo'] )) {
					$pgtxnno = $pdata ['pgTxnNo'];
				}
				
				if (isset ( $pdata ['issuerRefNo'] )) {
					$issuerrefno = $pdata ['issuerRefNo'];
				}
				
				if (isset ( $pdata ['authIdCode'] )) {
					$authidcode = $pdata ['authIdCode'];
				}
				
				if (isset ( $pdata ['addressZip'] )) {
					$pincode = $pdata ['addressZip'];
				}
				
				$currency = $pdata ['currency'];
				
				$signature = $pdata ['signature'];
							
				$data = $txnid . $TxStatus . $amount . $pgtxnno . $issuerrefno . $authidcode . $firstName . $lastName .  $pgrespcode . $pincode;
				
				$respSignature = self::_generateHmacKey ( $data, $apiKey );
				//$respSignature = $signature;
				
				//Mage::log ("Raw DATA - ".serialize($_POST). " ICP Sig Ret " . $signature . ' Sig Data ' . $data . ' Sig Calc ' . $respSignature );
				
				if($signature != "" && strcmp($signature, $respSignature) != 0)
				{
				 	$flag = "dataTampered";
				}
				
				
				$txMsg = 'CitrusPay: ' . $pdata ['TxMsg'];
				$respdata .= "<br/><strong>Citrus Transaction Message: </strong>" . $txMsg;
				
				$paymentMode = (isset ( $pdata ['paymentMode'] )) ? $pdata ['paymentMode'] : '';
				$cardType = (isset ( $pdata ['cardType'] )) ? $pdata ['cardType'] : '';
				$maskedCardNumber = (isset ( $pdata ['maskedCardNumber'] )) ? $pdata ['maskedCardNumber'] : '';
				
				// Modified on 29/06/2015
				if ($paymentMode == 'CREDIT_CARD' || $paymentMode == 'DEBIT_CARD') {
					$txMsg .= '. Paid by ' . $cardType . ' Card (No.' . $maskedCardNumber . ').';
				} elseif ($paymentMode == 'NET_BANKING') {
					$txMsg .= '. Paid by Net Banking.';
				} elseif ($paymentMode == 'WALLET') {
					$txMsg .= '. Paid by Wallet.';
				}
				
				//Mage::log ( "Citrus Response Message is " . $txMsg . "-paymentmode:" . $paymentMode . "-cardType:" . $cardType . "-maskedCardNumber:" . $maskedCardNumber );
				// end of modification
				
				if (strtoupper ( $TxStatus ) == 'SUCCESS') {
					if ($signatureFlag == 'Y') {
						if ($flag != "dataValid") {
							$order->setState ( Mage_Sales_Model_Order::STATE_NEW, true );
							$order->addStatusHistoryComment ( "Citrus Response signature does not match. You might have received tampered data" )->setIsVisibleOnFront ( false )->setIsCustomerNotified ( false );
							$order->cancel ()->save ();
							Mage::getSingleton ( 'checkout/session' )->setErrorMessage ( "<strong>Error:</strong> Citrus Response signature does not match. You might have received tampered data" );
							Mage::log ( "Citrus Response signature did not match " );
							$this->_redirect ( 'checkout/onepage/failure' );
						} else {
							Mage::getSingleton ( 'core/session' )->addSuccess ( $txMsg );
							$order->setState ( Mage_Sales_Model_Order::STATE_PROCESSING, true );
							$order->setTotalPaid ( $amount );
							$order->addStatusHistoryComment ( $txMsg )->setIsVisibleOnFront ( false )->setIsCustomerNotified ( false );
							$order->save ();
							$order->sendNewOrderEmail ();
							
							foreach ( $order->getInvoiceCollection () as $invoice ) {
								// $invoiceIncrementIds[] = $invoce->getIncrementId();
								// $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID)->save();
								$invoice->capture ()->save ();
								//$payment->pay($invoice);
							}
							//save payment additional info
							$payment = $order->getPayment();
      						$payment->setAdditionalInformation('citruspay');
							$payment->save();
							
							//Mage::log ( "Citrus Response Order success.." . $txMsg );
							$this->_redirect ( 'checkout/onepage/success' );
						}
					} else {
						Mage::log ( "Citrus Response - Must enable signature validation in Admin..." );
					}
				} else {
					$order->setState ( Mage_Sales_Model_Order::STATE_CANCELED, true );
					// Inventory updated
					$this->updateInventory ( $orderid );
					
					$baseurl = Mage::getBaseUrl ();
					$enquiryurl = $baseurl . "pumcp/redirect/citrusenquiry/txnid/" . $txnid;
					$historymessage = $txMsg . '<br/>View Citrus Payment using the following URL: ' . $enquiryurl;
					
					$order->addStatusHistoryComment ( $historymessage )->setIsVisibleOnFront ( false )->setIsCustomerNotified ( false );
					$order->cancel ()->save ();
					Mage::getSingleton ( 'checkout/session' )->setErrorMessage ( "<strong>Error:</strong> $txMsg <br/>" );
					//Mage::log ( "Citrus Wallet Response Order canceled .." );
					
					$this->_redirect ( 'checkout/onepage/failure' );
				}
			}
		}
		//Mage::log ( "Citrus Wallet Transaction END from Citruspay" );
	}
	
	public function updateInventory($order_id) {
	
		$order = Mage::getModel ( 'sales/order' )->loadByIncrementId ( $order_id );
		$items = $order->getAllItems ();
		foreach ( $items as $itemId => $item ) {
			$ordered_quantity = $item->getQtyToInvoice ();
			$sku = $item->getSku ();
			$product = Mage::getModel ( 'catalog/product' )->load ( $item->getProductId () );
			$qtyStock = Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $product->getId () )->getQty ();
			
			$updated_inventory = $qtyStock + $ordered_quantity;
			
			$stockData = $product->getStockItem ();
			$stockData->setData ( 'qty', $updated_inventory );
			$stockData->save ();
		}
	}
	
	
	public function citrusenquiryAction() {
		$params = $this->getRequest ()->getParams ();
		
		$apiKey = Mage::getStoreConfig ( 'payment/pumcp/apikey' );
		$accessKey = Mage::getStoreConfig ( 'payment/pumcp/accesskey' );
		$environment = Mage::getStoreConfig('payment/pumcp/environment');
		
		$apiUrl = "https://admin.citruspay.com/api/v2/txn/";
		if ($environment == "sandbox")
		{
			$apiUrl = "https://sandboxadmin.citruspay.com/api/v2/txn/";
		}
		
		
		$orderid = $params ['txnid'];
		$url = $apiUrl . 'enquiry/' . $params ['txnid'];
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'access_key:' . $accessKey,
				'Accept:application/json' 
		) );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		
		$returnData = curl_exec ( $ch );
		$returnCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		
		curl_close ( $ch );
		
		$error = '';
		$resp = array ();
		try {
			$resp = json_decode ( $returnData );
		} catch ( Exception $e ) {
			$error = "Could not receive details...";
		}
		// echo is_array($resp);
		if (substr ( $returnCode, 0, 1 ) == '2') {
			$enquiry = $resp->enquiryResponse;
			$content = "";
			for($i = 0; $i < count ( $enquiry ); ++ $i) {
				$enqObj = $enquiry [$i];
				
				// $content .= "<li> Order ID: ".$enqObj->merchantTxnId."</li>";
				$content .= "<li> TXN Response Code: " . $enqObj->respCode . "</li>";
				$content .= "<li> TXN Response Message : " . $enqObj->respMsg . "</li>";
				$content .= "<li> TXN ID: " . $enqObj->txnId . "</li>";
				$content .= "<li> PG TXN ID: " . $enqObj->pgTxnId . "</li>";
				$content .= "<li> TXN TYPE: " . $enqObj->txnType . "</li>";
				$content .= "<li> TXN DATE: " . $enqObj->txnDateTime . "</li>";
				$content .= "<li> Amount: " . $enqObj->currency . ' ' . $enqObj->amount . "</li>";
				$content .= "<li> Transaction Gateway: " . $enqObj->txnGateway . "</li>";
				$content .= "<li> Payment Mode: " . $enqObj->paymentMode . "</li>";
				$content .= "<li> Issuer Code: " . $enqObj->issuerCode . "</li>";
				$content .= "<li> Masked Card Number: " . $enqObj->maskedCardNumber . "</li>";
				$content .= "<li> Card Type: " . $enqObj->cardType . "</li>";
			}
		} else {
			$content = "<p>Error: Could not receive details...</p>";
		}
		
		$html = <<<EOF
		<html>
		<head>
			<title>Citrus Payment Gateway</title>
			<style>
				li {
					margin: 5px 0;
					color: #303030;
				}
				body {
					margin: 0;
					padding: 0;
					font-size: 14px;
					width: 100%;
					font-family: “Century Gothic”, CenturyGothic, AppleGothic, sans-serif;
				}
    
				ul {
					padding-left: 0;
					list-style-position: inside;
				}
			</style>
		</head>
		<body>
			<div id="wrapper" style="width:100%">
				<div style="background-color:#F6931D; color:#ffffff; font-size:24px; padding-top:20px; padding-bottom:20px; text-align:center">
					Citrus Payment Gateway
				</div>
				<div style="width: 30%; margin: 20px auto;">
					<div style="font-weight:bold; font-size:18px;">
						Order ID: $orderid
					</div>
					<div>
						<ul>
							$content
						</ul>
					</div>
				</div>
			</div>
		</body>
		</html>
EOF;
		
		echo $html;
	}

	public function citrusnotifyAction()
	{
		$txnid = "";
		$txnrefno = "";
		$TxStatus = "";
		$txnmsg = "";
		$firstName = "";
		$lastName = "";
		$email = "";
		$street1 = "";
		$city = "";
		$state = "";
		$country = "";
		$pincode = "";
		$mobileNo = "";
		$signature = "";
		$reqsignature = "";
		$data = "";
		$flag = "dataValid";
		$respdata = "";
		$orderid = "-1";
		
		$paymentmode = "";
		$sigMatch = false;
		
		if ($this->getRequest()->isPost())
		{
			$apiKey = Mage::getStoreConfig('payment/motowallet/apikey');		
			$signatureFlag = Mage::getStoreConfig('payment/motowallet/matchSignature');
			
			$pdata = $this->getRequest()->getPost();
						
			$txnrefno = $pdata ['TxRefNo'];
			$respdata .= "<br/><strong>Citrus Transaction Id: </strong>" . $txnrefno;
			
			$txnid = $pdata ['TxId'];
			$orderid = $txnid;
			
			$TxStatus = $pdata ['TxStatus'];
			$respdata .= "<br/><strong>Transaction Status: </strong>" . $TxStatus;
			
			$amount = $pdata ['amount'];
			$respdata .= "<br/><strong>Amount: </strong>" . $amount;
			
			if (isset ( $pdata ['firstName'] )) {
				$firstName = $pdata ['firstName'];
				$respdata .= "<br/><strong>First Name: </strong>" . $firstName;
			}
			
			if (isset ( $pdata ['lastName'] )) {
				$lastName = $pdata ['lastName'];
				$respdata .= "<br/><strong>Last Name: </strong>" . $lastName;
			}
			
			if (isset ( $pdata ['pgRespCode'] )) {
				$pgrespcode = $pdata ['pgRespCode'];
				$respdata .= "<br/><strong>PG Response Code: </strong>" . $pgrespcode;
			}
			
			if (isset ( $pdata ['pgTxnNo'] )) {
				$pgtxnno = $pdata ['pgTxnNo'];
			}
			
			if (isset ( $pdata ['issuerRefNo'] )) {
				$issuerrefno = $pdata ['issuerRefNo'];
			}
			
			if (isset ( $pdata ['authIdCode'] )) {
				$authidcode = $pdata ['authIdCode'];
			}
			
			if (isset ( $pdata ['addressZip'] )) {
				$pincode = $pdata ['addressZip'];
			}
			
			$currency = $pdata ['currency'];
			
			$signature = $pdata ['signature'];
			
			$data = $txnid . $TxStatus . $amount . $pgtxnno . $issuerrefno . $authidcode . $firstName . $lastName .  $pgrespcode . $pincode;
			
			$respSignature = self::_generateHmacKey ( $data, $apiKey );
			
			
			if (strcmp($signature, $respSignature) == 0)
			{
				$sigMatch = true;
			}									
			
			$txMsg = 'CitrusPay: '.$pdata['TxMsg'];
			$respdata .= "<br/><strong>Citrus Transaction Message: </strong>".$txMsg;
			
			$paymentMode = (isset($pdata['paymentMode']))? $pdata['paymentMode'] : '';
			$cardType = (isset($pdata['cardType']))? $pdata['cardType']: '';
			$maskedCardNumber = (isset($pdata['maskedCardNumber']))? $pdata['maskedCardNumber'] : '';

			if($paymentMode=='CREDIT_CARD' || $paymentMode=='DEBIT_CARD')
			{
				$txMsg .= '. Paid by '.$cardType.' Card (No.'.$maskedCardNumber.').';
			}
			elseif($paymentMode=='NET_BANKING') {
				$txMsg .= '. Paid by Net Banking.';
			}				
		}
				
		if(strtoupper($TxStatus) == 'SUCCESS' && $sigMatch)
		{					
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
						
			if (strtolower($order->status) == 'canceled' || strtolower($order->status) == 'pending')
			{			
				
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
				$order->setTotalPaid($amount);
				$order->addStatusHistoryComment($txMsg)->setIsVisibleOnFront(false)->setIsCustomerNotified(false);
				$order->save();						
				
				foreach ($order->getInvoiceCollection() as $invoice) {
					//$invoiceIncrementIds[] = $invoce->getIncrementId();
					//$invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID)->save();
					$invoice->capture ()->save ();
					//$payment->pay($invoice);
				}
				//save payment additional info
				$payment = $order->getPayment();
				$payment->setAdditionalInformation('citruspay');
				$payment->save();
				//Mage::log("Citrus ICP Notify success..".$txMsg);
			}
		}
		
		//Mage::log("Citrus ICP Notify END");
		
	}

}
