<?php

/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://www.wyomind.com/)
 */
require_once 'abstract.php';

class Wyomind_Elasticsearch_Shell extends Mage_Shell_Abstract
{

    public function run()
    {
        try {
            $this->_validate();
            if ($this->getArg('update-config')) {
                $this->_updateConfig();
            } elseif ($this->getArg('list')) {
                $this->_list();
            } else {
                $this->_echo($this->usageHelp());
            }
        } catch (Exception $e) {
            $this->_echo("");
            Mage::logException($e);
            $this->_fault($e->getMessage());
        }
    }


    protected function _updateConfig($ids = null)
    {
        $helper = Mage::helper('elasticsearch/autocomplete');
        $helper->saveConfig();
        $this->_echo("The config file has been updated");
    }

    protected function _fault($str)
    {
        $this->_echo($str);
        exit;
    }

    protected function _echo($str)
    {
        fwrite(STDOUT, $str . PHP_EOL);
        return $this;
    }

    protected function _validate()
    {
        if (!Mage::isInstalled()) {
            exit('Please install magento before running this script.');
        }

        if (!Mage::helper('core')->isDevAllowed()) {
            exit('You are not allowed to run this script.');
        }

        if (!Mage::helper('core')->isModuleEnabled('Wyomind_Elasticsearch')) {
            exit('Please enable Woymind_Elasticsearch module before running this script.');
        }

        return true;
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f shell/wyomind_simplegoogleshopping.php -- [options]

  -h                            Short alias for help
  --update-config               Update the autocomplete config file
  help                          This help

USAGE;
    }

}

$shell = new Wyomind_Elasticsearch_Shell();
$shell->run();
