<?php

class Wyomind_Notificationmanager_Model_Adminhtml_System_Config_Form_Field_Extensions
{
    public function XML2Array($xml) 
    {
        $newArray = array();
        $array = (array) $xml;
        foreach ($array as $key => $value) {
            $value = (array) $value;
            if (isset($value [0])) {
                $newArray [$key] = trim($value [0]);
            } else {
                $newArray [$key] = $this->XML2Array($value, true);
            }
        }
        return $newArray;
    }

    public function toOptionArray() 
    {
        $dir = "app/code/local/Wyomind/";
        $ret = array();
        if (is_dir($dir)) {
            if (($dh = opendir($dir)) != false) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($dir . $file) && $file != "." && $file != ".." && $file != "Notificationmanager") {
                        if (is_file($dir . $file . '/etc/system.xml')) {
                            $xml = simplexml_load_file($dir . $file . '/etc/system.xml');
                            $namespace = strtolower($file);
                            $label = $this->XML2Array($xml);
                            $label = $label['sections'][$namespace]['label'];

                            $enabled = Mage::getConfig()->getModuleConfig('Wyomind_' . ucfirst($namespace))
                                                        ->is('active', 'true');

                            if ($enabled) {
                                $ret[] = array('label' => $label, 'value' => $file);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }

        $dir = "app/code/community/Wyomind/";
        if (is_dir($dir)) {
            if (($dh = opendir($dir)) != false) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($dir . $file) && $file != "." && $file != "..") {
                        if (is_file($dir . $file . '/etc/system.xml')) {
                            $xml = simplexml_load_file($dir . $file . '/etc/system.xml');
                            $namespace = strtolower($file);
                            $label = $this->XML2Array($xml);
                            if (isset($label['sections'][$namespace])) {
                                $label = $label['sections'][$namespace]['label'];

                                $enabled = Mage::getConfig()->getModuleConfig('Wyomind_' . ucfirst($namespace))
                                                            ->is('active', 'true');
                                if ($label == null) {
                                    $label = ucfirst($namespace);
                                }
                                if ($enabled) {
                                    $ret[] = array('label' => $label, 'value' => $file);
                                }
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        return $ret;
    }
}