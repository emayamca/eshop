<?php
class Fuel_Sociallogin_Block_Paypallogin extends Mage_Core_Block_Template {
    /**
     * Constructor for set template file for javacript.
     */
    protected function _construct() {
        $this->setTemplate ( "sociallogin/loginwithpaypal.phtml" );
    }
    /**
     * Get redirect url after authendicate from paypal.
     *
     * @return string|unknown
     */
    public function getRedirectUrl() {
        if ($this->getError ()) {
            return $this->getRedirectError ();
        } else {
            return $this->getRedirectSuccess ();
        }
    }
    /**
     * Redirect to error page after getting authendicate from paypal.
     *
     * @return string
     */
    public function getRedirectError() {
        return $this->getRedirectConfig ( 'error' );
    }
    /**
     * Redirect to success page after getting authendicate from paypal.
     *
     * @return string
     */
    public function getRedirectSuccess() {
        return $this->getRedirectConfig ( 'success' );
    }
    /**
     * Function is used to redirect Auth Url
     *
     * @param string $code
     * @return string $returnUrl
     */
    public function getRedirectConfig($code) {
        $returnUrl = "";
        $getCustomerLink = Mage::getSingleton ( "customer/session" )->getLink ();
        $getPath = trim ( $getCustomerLink, '/' );
        if (strstr ( $getPath, 'onepage' )) {
            $returnUrl = $getPath;
        } else {
            $getRedirectUrl = $this->redirectionurl ();
            $authRefererUrl = Mage::getSingleton ( 'customer/session' )->getBeforeAuthUrl ();
            $getRedirectUrl = ($getRedirectUrl) ? $getRedirectUrl : $authRefererUrl;
            $returnUrl = $getRedirectUrl;
        }
        return $returnUrl;
    }
    /**
     * Get the redirct url from session
     *
     * @return string $redirectUrl
     */
    public function redirectionurl() {
        $redirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
        if ($redirectStatus) {
            $redirectUrl = Mage::helper ( 'customer' )->getAccountUrl ();
        } else {
            $redirectUrl = Mage::getSingleton ( 'customer/session' )->getLink ();
        }
        return $redirectUrl;
    }
    /**
     * Get Login form name (login or checkout page?)
     *
     * @return string
     */
    public function getLoginFormName() {
        if (Mage::getSingleton ( 'customer/session' )->hasLoginFormName ()) {
            return Mage::getSingleton ( 'customer/session' )->getLoginFormName ();
        } else {
            return 'login';
        }
    }
}