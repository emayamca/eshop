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
 * Abstract class for get the paypal endpont url
 */
abstract class Fuel_Sociallogin_Model_AbstractRequest extends Mage_Core_Model_Abstract {
    /**
     * Get service base url.
     *
     * @return string url
     */
    protected function getServiceBaseUrl($req = '') {
        $url = 'https://';
        $url .= Mage::helper ( 'sociallogin' )->getPaypalEndpoint ();
        return $url . $req;
    }
}