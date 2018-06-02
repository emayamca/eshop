<?php

/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.2.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
abstract class Wyomind_Elasticsearch_Helper_Filter_Abstract
{

    /**
     * Add any additional queries to the main query
     *
     * @param BoolQuery the query that has to be extended
     * @param string the query string
     */
    abstract function addAdditionalFilters(\Elastica\Query\BoolQuery $query,
            $q);
}
