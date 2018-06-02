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
 * The observer file validate the captcha when captcha is entered
 *
 */
class Fuel_Sociallogin_Model_Observer extends Mage_Core_Model_Abstract {

    /**
     * Captcha validation for create account form
     * 
     * @return string $message for validation failed if any
     */
    public function checkCaptcha($observer) {       
        $formId = 'Fuel_Sociallogin';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $request = $controller->getRequest();
        /**
         * Condition to check the captcha model is required.
         */
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);            
            if (!$captchaModel->isCorrect($this->_getCaptchaString($request, $formId)) && (isset($this->getRequest()->isXmlHttpRequest()) && strtolower($this->getRequest()->isXmlHttpRequest()) == 'xmlhttprequest')) {                
                /**
                 * If the form using AJAX returns $message
                 */
                $action = $request->getActionName();
                Mage::app()->getFrontController()->getAction()->setFlag($action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setHttpResponseCode(200);
                $controller->getResponse()->setHeader('Content-type', 'application/json');
                $controller->getResponse()->setBody(json_encode(
                array("msg" => Mage::helper('sociallogin')->__('Incorrect CAPTCHA.'))));
            } else {                  
                Mage::getSingleton('customer/session')->addError(Mage::helper('sociallogin')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*'));
            }            
        }
        return $this;
    }
}