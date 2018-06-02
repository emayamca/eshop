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

class Emipro_SellerHelpdesk_Model_System_Config_Source_Adminuser {

    public function toOptionArray() {
        $user = array();
        $admin_user = Mage::getModel('admin/user')->getCollection()->getData();
        foreach ($admin_user as $value) {
            $user[] = array('value' => $value["user_id"], 'label' => Mage::helper('adminhtml')->__($value["firstname"] . " " . $value["lastname"]));
        }
        return $user;
    }

}
