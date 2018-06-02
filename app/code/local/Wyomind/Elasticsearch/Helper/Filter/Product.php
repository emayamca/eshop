<?php
/**
 * Filter for the Product catalog search
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.2.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Helper_Filter_Product extends Wyomind_Elasticsearch_Helper_Filter_Abstract
{

    /**
     * Add any additional queries to the main query
     *
     * @param BoolQuery the query that has to be extended
     * @param string the query string
     */
    function addAdditionalFilters(\Elastica\Query\BoolQuery $query, $q) {
//        $query->addMust(new \Elastica\Query\Range('_prices.price', array('gte' => 0)));
//        $query->addMust(new \Elastica\Query\Range('visibility', array('gte' => 3)));
        return;
    }
}
