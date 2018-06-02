<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
/**
 * @method  Mage_Cms_Model_Page getEntity()
 * @method  $this               setEntity(Mage_Cms_Model_Page $page)
 */
class Wyomind_Elasticsearch_Block_Catalogsearch_Autocomplete_Cms
    extends Wyomind_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var string
     */
    protected $_autocompleteTitle = 'Pages';

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('wyomind/elasticsearch/autocomplete/cms.phtml');
    }
}