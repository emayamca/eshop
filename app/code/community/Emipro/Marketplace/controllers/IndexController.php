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

class Emipro_Marketplace_IndexController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function marketplaceAction() {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array) $modules;
        if (isset($modulesArray['Emipro_Marketplace']) && $modulesArray['Emipro_Marketplace']->active == 'true') {
            $this->getResponse()->setBody("true");
        } else {
            $this->getResponse()->setBody("false");
        }
    }

}
