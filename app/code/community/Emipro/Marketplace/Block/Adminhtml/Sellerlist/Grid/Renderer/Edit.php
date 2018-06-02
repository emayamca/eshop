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

class Emipro_Marketplace_Block_Adminhtml_Sellerlist_Grid_Renderer_Edit extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {

    public $link;

    public function render(Varien_Object $row) {
        $seller = Mage::getModel('marketplace/list');
        $seller->load($row->getId());
        $status = $seller->getStatus();
        switch ($status) {
            case "approved":
                $approveUrl = $this->getUrl("*/*/sellerstatus", array("id" => $row->getId(), "status" => "block"));
                $link = '<a href="' . $approveUrl . '">' . $this->__('Block') . '</a>';
                break;
            case "block":
                $approveUrl = $this->getUrl("*/*/sellerstatus", array("id" => $row->getId(), "status" => "approved"));
                $link = '<a href="' . $approveUrl . '">' . $this->__('Approve') . '</a>';
                break;
            default:
                $approveUrl = $this->getUrl("*/*/sellerstatus", array("id" => $row->getId(), "status" => "approved"));
                $link = '<a href="' . $approveUrl . '">' . $this->__('Approve') . '</a>';
                break;
        }
        return $link;
    }

}
