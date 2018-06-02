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
 
class Emipro_Captcha_Block_Captcha extends Mage_Captcha_Block_Captcha
{
	protected function _toHtml()
    {
        $blockPath = 'emcaptcha/captcha_zend';
        $block = $this->getLayout()->createBlock($blockPath);
        $block->setData($this->getData());
        return $block->toHtml();
    }
}
