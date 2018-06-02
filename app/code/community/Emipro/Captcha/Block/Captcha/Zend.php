<?php

/* 
* ////////////////////////////////////////////////////////////////////////////////////// 
* 
* @author   Emipro Technologies 
* @Category Emipro 
* @package  Emipro_Marketplace 
* @license http://shop.emiprotechnologies.com/license-agreement/   
* 
* ////////////////////////////////////////////////////////////////////////////////////// 
*/ 
 
class Emipro_Captcha_Block_Captcha_Zend extends Mage_Captcha_Block_Captcha_Zend
{
	 /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
         $modelobj = $this->getCaptchaModel();
        $modelobj->setDotNoiseLevel(4);     
        $modelobj->setLineNoiseLevel(2);    
        $modelobj->generate();
 
        if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
 
        return $html;
    }
 
    /**
     * Returns URL to controller action which returns new captcha image
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        return Mage::getUrl(
            Mage::app()->getStore()->isAdmin() ? 'adminhtml/refresh/refresh' : 'emcaptcha/captcha/refresh',
            array('_secure' => Mage::app()->getStore()->isCurrentlySecure())
        );
    }
}
