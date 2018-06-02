<?php

/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 *
 * @method      array           getAnalyzers()
 * @method      array           getClientConfig()
 * @method      array           getTypes()
 */
class Wyomind_Elasticsearch_Config extends Varien_Object
{

    protected $_object = null;

    /**
     * Get content of config.file
     * @return type
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * Set content of config.file
     * @param type $data
     */
    public function setObject($data)
    {
        $this->_object = $data;
    }

    /**
     * @param string $scope
     */
    public function __construct($scope, $synomym = false)
    {
        $configFile = self::getFile($synomym);

        if (file_exists($configFile) && is_file($configFile) && filesize($configFile)) {
            $data = @json_decode(file_get_contents($configFile), true);
           
            $this->setObject($data);
            if (isset($data[$scope]) && is_array($data[$scope])) {
                parent::__construct($data[$scope]);
            }
        }
    }

    /**
     * @param string $path
     * @param null $default
     * @return mixed
     */
    public function getConfig($path, $default = null)
    {
        $key = 'config/' . $path;

        return $this->getValue($key, $default);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        $data = $this->getData($key);

        return null != $data ? $data : $default;
    }

    /**
     * @return string
     */
    static public function getDir()
    {
        $dir = dirname(__FILE__) .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . 'var' .
                DIRECTORY_SEPARATOR . 'elasticsearch' .
                DIRECTORY_SEPARATOR;

        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }

        return $dir;
    }

    /**
     * @return string
     */
    static public function getFile($synonym = false)
    {
        if ($synonym) {
            return self::getDir() . 'synonym.json';
        }
        return self::getDir() . 'config.json';
    }

    /**
     * @param array $config
     * @return int
     */
    static public function save($config, $synonym = false)
    {
        // Pretty output is PHP 5.4+
        $options = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;

        return @file_put_contents(self::getFile($synonym), json_encode($config, $options));
    }

}
