<?php

class Wyomind_Licensemanager_Model_Observer
{
    public function saveConfig($observer)
    {
        $namespace = $observer->getEvent()->getObject()->getSection();

        if (Mage::helper('core')->isModuleEnabled('Wyomind_' . ucfirst($namespace))) {
            $domain = Mage::getStoreConfig("web/secure/base_url");
            $beforeActivationCode = Mage::getStoreConfig("$namespace/license/activation_code");
            $beforeActivationKey = Mage::getStoreConfig("$namespace/license/activation_key");
            $post = (Mage::app()->getRequest()->getPost());
            if (isset($post["groups"]["license"])) {
                $afterActivationKey = $post["groups"]["license"]["fields"]["activation_key"]["value"];
                if (isset($post["groups"]["license"]["fields"]["activation_code"]["value"])) {
                    $afterActivationCode = $post["groups"]["license"]["fields"]["activation_code"]["value"];
                } else {
                    $afterActivationCode = "N/A";
                }

                $registeredVersion = Mage::getStoreConfig("$namespace/license/version");
                if ($beforeActivationKey != $afterActivationKey) {
                    Mage::helper("licensemanager")->log(
                        $namespace, $registeredVersion, $domain, null, 
                        'update activation key -> from ' . $beforeActivationKey . ' to ' . $afterActivationKey
                    );
                }
                if ($beforeActivationCode != $afterActivationCode) {
                    Mage::helper("licensemanager")->log(
                        $namespace, $registeredVersion, $domain, null, 
                        'update license code -> from ' . $beforeActivationCode . ' to ' . $afterActivationCode
                    );
                }
            }
        }
    }
}