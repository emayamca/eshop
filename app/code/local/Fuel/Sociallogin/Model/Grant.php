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
class Fuel_Sociallogin_Model_Grant extends Fuel_Sociallogin_Model_AbstractRequest {
    /**
     * Grant token from authorization code.
     * 
     * return boolean
     */
    public function grant() {
        $username = Mage::helper('sociallogin')->getPaypalClientId();
        $password = Mage::helper('sociallogin')->getPaypalSecretKey();
        $post = array('grant_type' => 'authorization_code',
            'code' => $this->getCode(),
            'redirect_uri' => Mage::getBaseUrl().'sociallogin/paypallogin/');        
        $ch = curl_init($this->getServiceBaseUrl('/v1/identity/openidconnect/tokenservice'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        if (($output = curl_exec($ch)) === FALSE) {
            throw new Zend_Exception("Could not grant authorization code");
        }
        curl_close($ch);
        $this->setResponse(json_decode($output));
        $this->validateResponse();
        Mage::getSingleton('customer/session')->setPayPalAccessToken($this->getResponse()->access_token);
        return true;
    }
    /**
     * Validate response.
     * 
     * @throws Zend_Exception
     */
    protected function validateResponse() {
        if (!$this->getResponse()) {
            throw new Zend_Exception("Could not obtain PayPal Grant Access Token");
        }
        if (isset($this->getResponse()->error)) {
            throw new Zend_Exception($this->getResponse()->error_description);
        }
        if (!isset($this->getResponse()->access_token)) {
            throw new Zend_Exception("Could not obtain PayPal Grant Access Token");
        }
    }
}
