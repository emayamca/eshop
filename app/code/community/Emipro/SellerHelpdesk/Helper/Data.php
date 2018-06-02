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

class Emipro_SellerHelpdesk_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getTicketstatus() {
        $option = array();
        $ticket_status = Mage::getModel('emipro_sellerhelpdesk/helpdeskstatus')->getCollection();
        $options[-1] = 'Select Category';
        foreach ($ticket_status as $status) {

            $option[$status["status_id"]] = $status["status"];
        }
        return $option;
    }

    public function getStatus($status_id) {
        $model = Mage::getModel('emipro_sellerhelpdesk/helpdeskstatus')->load($status_id);
        return $model->getStatus();
    }

}
