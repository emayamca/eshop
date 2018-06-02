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
 * @package     Fuel_Sociallogin
 */
/**
 * Access token class for get access toket from paypal
 *
 */
class Fuel_Sociallogin_Model_AccessToken extends Fuel_Sociallogin_Model_AbstractRequest {
    /**
     * Retrieve access token from PayPal oauth.
     * 
     * return boolean
     */
    public function retrieve() {
        $username = Mage::helper('sociallogin')->getPaypalClientId();
        $password = Mage::helper('sociallogin')->getPaypalSecretKey();
        $post = array('grant_type' => 'client_credentials');
        $headers = array('Accept: application/json', 'Accept-Language: en_US');
        /**
         * Initialize the curl function
         */
        $ch = curl_init($this->getServiceBaseUrl('/v1/oauth2/token'));
        /**
         * set the curl parameters for access paypal
         */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_POST, count($post));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (($output = curl_exec($ch)) === FALSE) {
            throw new Zend_Exception("Could not obtain PayPal Access Token");
        }
        curl_close($ch);
        $this->setResponse(json_decode($output));
        $this->validateResponse();
        return true;
    }
    /**
     * Validate response.
     * 
     * @throws Zend_Exception
     */
    protected function validateResponse() {
        if (!$this->getResponse()) {
            throw new Zend_Exception("Could not obtain PayPal Access Token");
        }
        if (isset($this->getResponse()->error)) {
            throw new Zend_Exception($this->getResponse()->error_description);
        }
        if (!isset($this->getResponse()->access_token)) {
            throw new Zend_Exception("Could not obtain PayPal Access Token");
        }
    }
}