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

class Emipro_Marketplace_Block_Adminhtml_Report_Renderer_Balancetotal extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $data = $row->getData();
        if (isset($data['seller_id']) && !empty($data['seller_id'])) {
            $commissiondata = Mage::getModel('marketplace/sellerbalancesheet')->getCollection()->addFieldToFilter('seller_id', $data['seller_id'])->addFieldToFilter("id", array('lteq' => $data['id']));
            // $commissiondata->getSelect()->columns("SUM(credit)-SUM(debit) as seller_balance");
            $commissiondata->getSelect()->columns("ifnull(SUM(credit),0)-ifnull(SUM(debit),0) as seller_balance");
            if ($commissiondata->count() > 0) {
                return $commissiondata->getFirstItem()->getData('seller_balance');
            }
        }
        return;
    }

}
