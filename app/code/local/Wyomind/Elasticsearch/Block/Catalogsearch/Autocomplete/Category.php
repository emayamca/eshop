<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
/**
 * @method  Mage_Catalog_Model_Category getEntity()
 * @method  $this                       setEntity(Mage_Catalog_Model_Category $category)
 */
class Wyomind_Elasticsearch_Block_Catalogsearch_Autocomplete_Category
    extends Wyomind_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var string
     */
    protected $_autocompleteTitle = 'Categories';

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('wyomind/elasticsearch/autocomplete/category.phtml');
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl(Mage_Catalog_Model_Category $category)
    {
        return $category->getUrl();
    }
}