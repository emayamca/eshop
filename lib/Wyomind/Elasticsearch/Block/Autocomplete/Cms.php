<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Block_Autocomplete_Cms extends Wyomind_Elasticsearch_Block_Autocomplete_Abstract
{
    /**
     * @var string
     */
    protected $_title = 'Pages';

    /**
     * @var string
     */
    protected $_template = 'wyomind/elasticsearch/autocomplete/cms.phtml';

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_config->getValue('base_url', '');
    }
}