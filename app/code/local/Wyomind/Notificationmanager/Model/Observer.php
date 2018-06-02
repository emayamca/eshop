<?php 

class Wyomind_Notificationmanager_Model_Observer
{
    public function observe($user) 
    {
        $model = Mage::getModel('notificationmanager/feed');

        $date = $model->getLastUpdate();
        $time = Mage::getSingleton('core/date')->gmtTimestamp();
        $actionRequired = Mage::getStoreConfig('notificationmanager/notificationmanager/action_required');
        
        if ((int)$time - $date > 24 * 60 * 60 || $actionRequired == 1) {
            Mage::getConfig()->saveConfig(
                'notificationmanager/notificationmanager/action_required', '0', 'default', '0'
            );
            $extensions = Mage::getStoreConfig('notificationmanager/notificationmanager/extensions');
            $exts = $extensions != null ? explode(',', $extensions) : array();
            
            if ($date != "") {
                $rss = $model->getFeedData();

                if ($rss != null) {
                    $items = $rss->xpath('/rss/channel/item');
                    if ($items) {
                        foreach ($items as $item) {
                            $infos = $item->children();
                            $isActive = Mage::getConfig()->getModuleConfig('Wyomind_' . $infos->identifier)
                                                        ->is('active', 'true');
                            if ($infos->identifier == 'Global' || (in_array($infos->identifier, $exts) && $isActive)) {
                                $this->saveNotification($infos);
                            }
                        }
                    }
                }
            }
            $model->setLastUpdate();
        }
    }
    
    private function saveNotification($infos)
    {
        $notification = Mage::getModel('adminnotification/inbox');
        $notification->setTitle($infos->title);
        $notification->setUrl($infos->link);
        $notification->setSeverity($infos->severity);
        $notification->setDescription($infos->description);
        $notification->setDateAdded(date('Y-m-d H:i:s', (int) $infos->pubDate));
        $notification->save();
    }
}