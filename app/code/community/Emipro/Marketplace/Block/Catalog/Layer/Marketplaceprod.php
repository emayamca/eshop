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

class Emipro_Marketplace_Block_Catalog_layer_Marketplaceprod extends Mage_Catalog_Block_Layer_View {

    protected function _construct() {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer(), true);
    }

    public function getLayer() {
        return Mage::getSingleton('marketplace/catalog_layer');
    }

}
