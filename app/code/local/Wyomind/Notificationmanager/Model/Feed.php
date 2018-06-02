<?php 

class Wyomind_Notificationmanager_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    public function getFeedUrl() 
    {
        Mage::getSingleton('admin/session')->getData();
        $url = Mage::getStoreConfig('web/secure/base_url');
        $version = Mage::getConfig()->getNode('modules/Wyomind_Notificationmanager')->version;
        $lastcheck = $this->getLastUpdate();
        $rssUrl = 'rss.wyomind.com';
        $time = Mage::getSingleton('core/date')->gmtTimestamp();
        
        return "http://$rssUrl/?domain=$url&version=$version&lastcheck=$lastcheck&now=" . $time;
    }

    public function getLastUpdate() 
    {
        return Mage::getStoreConfig('notificationmanager/notificationmanager/lastcheck');
    }

    public function setLastUpdate() 
    {
        $time = Mage::getSingleton('core/date')->gmtTimestamp();
        Mage::getConfig()->saveConfig('notificationmanager/notificationmanager/lastcheck', $time, 'default', '0');
        Mage::getConfig()->cleanCache();
        return $this;
    }
}