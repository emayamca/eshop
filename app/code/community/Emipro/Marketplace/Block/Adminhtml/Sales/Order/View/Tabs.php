<?php

class Emipro_Marketplace_Block_Adminhtml_Sales_Order_View_Tabs extends Mage_Adminhtml_Block_Sales_Order_View_Tabs
{
    protected function _beforeToHtml()
    {
        if ($activeTab = $this->getRequest()->getParam('active_tab')) {
            $this->setActiveTab($activeTab);
        } elseif ($activeTabId = Mage::getSingleton('admin/session')->getActiveTabId()) {
            $this->_setActiveTab($activeTabId);
        }
        $_new = array();
        $sellerid = Mage::helper("marketplace")->getSellerIdfromLoginUser();
        foreach( $this->_tabs  as $key => $tab ) {
                if($key=='order_history' && $sellerid){ 
					   continue;
                    }  
            foreach( $this->_tabs  as $k => $t ) {
                if( $t->getAfter() == $key ) {
                    $_new[$key] = $tab;
                    $_new[$k] = $t;
                } else {
                    if( !$tab->getAfter() || !in_array($tab->getAfter(), array_keys($this->_tabs)) ) {
                        $_new[$key] = $tab;
                    }
                }
            }
        }

        $this->_tabs = $_new;
        unset($_new);

        $this->assign('tabs', $this->_tabs);
        //return parent::_beforeToHtml();
    }
}
