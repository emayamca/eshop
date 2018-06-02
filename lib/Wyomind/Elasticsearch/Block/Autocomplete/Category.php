<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Block_Autocomplete_Category extends Wyomind_Elasticsearch_Block_Autocomplete_Abstract
{
    /**
     * @var string
     */
    protected $_title = 'Categories';

    /**
     * @var string
     */
    protected $_template = 'wyomind/elasticsearch/autocomplete/category.phtml';

    /**
     * @param Varien_Object $category
     * @return string
     */
    public function getCategoryPathName(Varien_Object $category)
    {
        if ($this->_config->getConfig('category/show_path', true)) {
            return $category->getData('_path');
        }

        return $category->getName();
    }

    /**
     * @param Varien_Object $category
     * @return string
     */
    public function getCategoryUrl(Varien_Object $category)
    {
        return $this->cleanUrl($category->getData('_url'));
    }
}