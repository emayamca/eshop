<?php
class Emipro_Stocknotification_Block_Adminhtml_Stocknotification_Edit_Tab extends Mage_Adminhtml_Block_Widget
implements Mage_Adminhtml_Block_Widget_Tab_Interface

{
    public function canShowTab()
    {
        return true;
    }
    public function getTabLabel()
    {
        return $this->__('Low Stock Notification');
    }
    public function getTabTitle()
    {
        return $this->__('Low Stock Notification');
    }
    public function isHidden()
    {
        return false;
    }
    public function getTabUrl()
    {
        return $this->getUrl('stocknotification/adminhtml_stocknotification/form',array('_current'=>true));
    }
    public function getTabClass()
    {
        return 'ajax';
    }
} 