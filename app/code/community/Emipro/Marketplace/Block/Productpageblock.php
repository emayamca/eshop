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

class Emipro_Marketplace_Block_Productpageblock extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface {

	/*
	* 	Product page seller  block 
	*/

    protected function _toHtml() {
        $block = $this->getLayout()->createBlock('marketplace/sellerreview')->setTemplate('marketplace/seller_product_page.phtml');
        return $block->toHtml();
    }

}
