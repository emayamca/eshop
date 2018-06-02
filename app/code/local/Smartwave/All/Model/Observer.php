<?php
class Smartwave_All_Model_Observer
{
    public function preDispatch(Varien_Event_Observer $observer)
    {

        if (Mage::getSingleton('admin/session')->isLoggedIn()) {

            $feedModel  = Mage::getModel('all/feed');
            $feedModel->checkUpdate();

        }

    }
}
