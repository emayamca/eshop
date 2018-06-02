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

class Emipro_SellerHelpdesk_Block_Adminhtml_Renderer_LastUpdatedDate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $Id = $row->getId();
        $helpdesk = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk')->load($Id, "id");
        $conversation_info = Mage::getModel('emipro_sellerhelpdesk/sellerconversation')->getCollection()->addFieldToFilter("id", $Id)->setOrder("conversation_id", "DESC");
        foreach ($conversation_info as $value) {
            return $value["date"];
        }
    }

}
