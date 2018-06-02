<?php

/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
abstract class Wyomind_Elasticsearch_Block_Abstract
{

    /**
     * @var Wyomind_Elasticsearch_Config
     */
    protected $_config;

    /**
     * @var array
     */
    protected $_labels;

    /**
     * @var string
     */
    protected $_template = '';

    /**
     * @var string
     */
    protected $_currency = '';

    /**
     * Removes protocol to call URLs with current one (secure or not)
     *
     * @param string $url
     * @return string
     */
    public function cleanUrl($url)
    {
        return preg_replace('/^https?:/i', '', $url);
    }

    /**
     * @param $str
     * @return string
     */
    public function escapeHtml($str)
    {
        return htmlspecialchars($str, ENT_COMPAT, 'UTF-8', false);
    }

    /**
     * @param string $url
     * @return string
     */
    public function escapeUrl($url)
    {
        return htmlspecialchars($url);
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        if (null === $this->_labels) {
            $this->_labels = array();
            $config = @unserialize($this->_config->getConfig('autocomplete/labels'));
            if (is_array($config)) {
                foreach ($config as $data) {
                    $this->_labels[$data['label']] = $data['translation'];
                }
            }
        }

        return $this->_labels;
    }

    /**
     * @param string $label
     * @return string
     */
    public function getLabel($label)
    {
        $labels = $this->getLabels();
        if (isset($labels[$label]) && '' !== trim($labels[$label])) {
            $label = trim($labels[$label]);
        }

        return $label;
    }

    /**
     * @return Wyomind_Elasticsearch_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @param Wyomind_Elasticsearch_Config $config
     * @return $this
     */
    public function setConfig(Wyomind_Elasticsearch_Config $config)
    {
        $this->_config = $config;

        return $this;
    }

    /**
     * @return string
     */
    function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->_template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return 'app' . DIRECTORY_SEPARATOR . 'design' . DIRECTORY_SEPARATOR . 'frontend';
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $baseDir = $this->getBaseDir();
        $file = str_replace('/', DIRECTORY_SEPARATOR, $this->_template);

        $fallbacks = array(
            'base' => 'default',
            'default' => 'default',
        );

        if ($this->_config) {
            if ($package = $this->_config->getPackage()) {
                $theme = $this->_config->getTheme();
                if (empty($theme)) {
                    $theme = 'default';
                }
                $fallbacks[$package] = $theme;
            }
        }

        foreach (array_reverse($fallbacks) as $package => $theme) {
            $filePath = implode(DIRECTORY_SEPARATOR, array($baseDir, $package, $theme, 'template', $file));
            if (file_exists($filePath)) {
                $file = $filePath;
            }
        }

        return $file;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        ob_start();

        $file = $this->getTemplateFile();
        if (file_exists($file)) {
            include $file;
        }

        $html = ob_get_clean();

        return $html;
    }

}
