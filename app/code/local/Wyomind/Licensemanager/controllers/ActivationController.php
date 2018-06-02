<?php

class Wyomind_Licensemanager_ActivationController extends Mage_Core_Controller_Front_Action
{
    public function webserviceAction() 
    {
        $post = array();
        
        foreach ($this->getRequest()->getPost() as $key => $value) {
            $post[$key] = $value;
        }
        
        if (!isset($post['namespace'])) {
            return;
        }

        $activationKey = Mage::getStoreConfig($post['namespace'] . "/license/activation_key");
        $baseUrl = Mage::getStoreConfig("web/secure/base_url");
        $registeredVersion = Mage::getStoreConfig($post['namespace'] . "/license/version");

        if (isset($post['wgs_activation_key']) && $post['wgs_activation_key'] == $activationKey) {
            if (isset($post['wgs_status'])) {
                switch ($post['wgs_status']) {
                    case "success" :
                        Mage::getConfig()->saveConfig(
                            $post['namespace'] . "/license/version", $post['wgs_version'], "default", "0"
                        );
                        Mage::getConfig()->saveConfig(
                            $post['namespace'] . "/license/activation_code", $post['wgs_activation'], "default", "0"
                        );
                        Mage::helper("licensemanager")->log(
                            ucfirst($post['namespace']), $registeredVersion, $baseUrl, $activationKey, 
                            'manual activation -> success:' . $post['wgs_message']
                        );
                        Mage::getSingleton("core/session")->setData("update_" . $post['namespace'], "false");
                        Mage::getConfig()->cleanCache();
                        Mage::getSingleton("core/session")->addSuccess($post['wgs_message']);
                        break;
                    case "error" :
                        Mage::getSingleton("core/session")->addError($post['wgs_message']);
                        Mage::getConfig()->saveConfig(
                            $post['namespace'] . "/license/activation_code", "", "default", "0"
                        );
                        Mage::helper("licensemanager")->log(
                            ucfirst($post['namespace']), $registeredVersion, $baseUrl, $activationKey, 
                            'manual activation -> error:' . $post['wgs_message']
                        );
                        Mage::getConfig()->cleanCache();
                        break;
                    case "uninstall" :
                        Mage::getSingleton("core/session")->addError($post['wgs_message']);
                        Mage::getConfig()->saveConfig(
                            $post['namespace'] . "/license/activation_key", "", "default", "0"
                        );
                        Mage::getConfig()->saveConfig(
                            $post['namespace'] . "/license/activation_code", "", "default", "0"
                        );
                        Mage::helper("licensemanager")->log(
                            ucfirst($post['namespace']), $registeredVersion, $baseUrl, $activationKey, 
                            'uninstall -> success:' . $post['wgs_message']
                        );
                        Mage::getConfig()->cleanCache();
                        $this->getResponse()->setBody("<form action='http://www.wyomind.com/license_activation/?method=post' " 
                                . "id='license_uninstall' method='post'>
                                    <input type='hidden' type='action' value='uninstall' name='action'>
                                    <input type='hidden' value='" . $baseUrl . "' name='domain'>
                                    <input type='hidden' value='" . $activationKey . "' name='activation_key'>
                                    <input type='hidden' value='" . $registeredVersion . "' name='registered_version'>
                                    <button type='submit'>If nothing happens click here !</button>
                                    <script language='javascript'>
                                            document.getElementById('license_uninstall').submit();
                                    </script>
                            </form>");
                        break;
                    default :
                        Mage::getSingleton("core/session")->addError(
                            "An error occurs while retrieving the license activation (500)"
                        );
                        Mage::getConfig()->saveConfig(
                            $post['namespace'] . "/license/activation_code", "", "default", "0"
                        );
                        Mage::helper("licensemanager")->log(
                            ucfirst($post['namespace']), 
                            $registeredVersion, $baseUrl, $activationKey, 'uninstall -> unknown'
                        );
                        Mage::getConfig()->cleanCache();
                        break;
                }
            } else {
                Mage::getSingleton("core/session")->addError(
                    "An error occurs while retrieving license activation (404)."
                );
            }
        } else {
            Mage::getSingleton("core/session")->addError("Invalid activation key.");
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}