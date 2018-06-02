<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fuel
 * @package     Fuel_Trackyourorder
 */

/**
 * Event Observer
 */
class Fuel_Trackyourorder_Model_Observer {
    /**
     * Order information will be get from the $observer parameter
     *
     * @param array $observer
     * @return void
     */
    public function sendOrderMessage(Varien_Event_Observer $observer){
		$order = $observer->getOrder();
		$incrementId = $order->getIncrementId();
		$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
		$status = $order->getStatus();
		$total = Mage::helper('core')->currency($order->getGrandTotal(), true, false);
		$customer_mobile = $customer->getMobile();
		$billingAddress = $order->getBillingAddress();
		$billingAddressTelephone = $billingAddress->getTelephone();
		if($customer_mobile != $billingAddressTelephone){
			$customer_mobile = $billingAddressTelephone;
		}
		$collection = Mage::getModel('sales/order_item')->getCollection()->addAttributeToFilter('order_id', $order->getId());
		$tracLink = Mage::getUrl('trackyourorder/index/');

		if($status == 'pending'){
			$message = urlencode("We have received your order no.$incrementId. Please proceed the payment of $total. You can always call our support number 8081881122");
			//Mage::log($billingAddressTelephone, null, 'send.log');
			$this->CURLsendordersms($message, $customer_mobile);
		} elseif($status == 'processing') {
			$message = urlencode("We have received your order no.$incrementId amounting to $total and its being prepared. You can always call our support number 8081881122");
			//Mage::log($message, null, 'send.log');
			$this->CURLsendordersms($message, $customer_mobile);
		} elseif($status == 'pending_payment') {
			$message = urlencode("You've placed an order (with Pending-Status) but your payment has failed. Request you to retry payment with a different payment option.");
			//Mage::log($message, null, 'send.log');
			$this->CURLsendordersms($message, $customer_mobile);
		} elseif($status == 'complete') {
			$message = urlencode("Shipment from Metacrust (Business Fuel) vide FedEx has been delivered to you successfully. Call our support 8081881122 for help.");
			//Mage::log($message, null, 'send.log');
			$this->CURLsendordersms($message, $customer_mobile);
		}

	}
	
	/*
	 * Sent Order Notification Message
	 *
	 * @param $customer_mobile integer
	 * @param $message varchar
	 *
	 * @return integer
	 */
	public function CURLsendordersms($message, $customer_mobile){
		// SMS API
		$url = "http://login.smsadda.com/API/pushsms.aspx?loginID=businessfuel&password=business123&mobile=$customer_mobile&text=$message&senderid=BIZFUL&route_id=1&Unicode=1";

		/*CURL Starts Here*/
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$ee       = curl_getinfo($ch);
		curl_close($ch);
        /*CURL Ends Here*/

		return $status;
    }
}