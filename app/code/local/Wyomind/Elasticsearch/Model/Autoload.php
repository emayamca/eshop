<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Model_Autoload
{
    /**
     * Try autoloading namespace classes before regular Magento classes
     *
     * @param string $class
     */
    public static function load($class)
    {
        $classFile = BP . DS . 'lib' . DS . str_replace('\\', DS, $class, $count) . '.php';
        if ($count > 0 && is_file($classFile)) {
            include $classFile;
        }
    }
}