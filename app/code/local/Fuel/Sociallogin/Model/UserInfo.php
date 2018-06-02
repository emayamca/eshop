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
 *  User info abstract the AbstractRequest class.
 * 
 */
class Fuel_Sociallogin_Model_UserInfo extends Fuel_Sociallogin_Model_AbstractRequest {
    /**
     * Retrieve access token from PayPal oauth.
     * 
     * @return boolean
     */
    public function retrieve() {
        $headers = array('Accept: application/json', 'Authorization: Bearer '
            . $this->getAccessToken()->getResponse()->access_token);
        $ch = curl_init($this->getServiceBaseUrl('/v1/identity/openidconnect/userinfo/?schema=openid'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (($output = curl_exec($ch)) === FALSE) {
            throw new Zend_Exception("Could not obtain user info");
        }
        curl_close($ch);
        $data = json_decode($output);
        $this->validateData($data);
        $retriveObject = new Varien_Object();
        $retriveObject->addData((array) $data);
        $this->setResponse($retriveObject);
        return true;
    }
    /**
     *  Validate data from response.    
     * 
     *  @throws Zend_Exception
     */
    public function validateData($data) {
        if (!$data) {
            throw new Zend_Exception("Could not obtain user info");
        }
        if (isset($data->error)) {
            throw new Zend_Exception($data->error_description);
        }
    }
}