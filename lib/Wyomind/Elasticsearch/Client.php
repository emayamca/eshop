<?php

/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Client extends \Elastica\Client
{

    /**
     * @param string $name
     * @return Wyomind_Elasticsearch_Index
     */
    public function getIndex($name)
    {
        return new Wyomind_Elasticsearch_Index($this, $name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getIndexAlias($name)
    {
        $prefix = $this->getConfig('index_prefix');

        return $prefix . $name;
    }

    /**
     * @param string $name
     * @param bool $new
     * @return string
     */
    public function getIndexName($name,
            $new = false)
    {
        $alias = $this->getIndexAlias($name);
        $name = $alias . '_idx1'; // index name must be different than alias name
        foreach ($this->getStatus()->getIndicesWithAlias($alias) as $indice) {
            if ($new) {
                $name = $indice->getName() != $name ? $name : $alias . '_idx2';
            } else {
                $name = $indice->getName();
            }
        }

        return $name;
    }

    /**
     * Returns query operator
     *
     * @return string
     */
    public function getQueryOperator()
    {
        return $this->getConfig('query_operator');
    }

    /**
     * Checks if fuzzy query is enabled
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-flt-query.html
     * @return bool
     */
    public function isFuzzyQueryEnabled()
    {
        return (bool) $this->getConfig('enable_fuzzy_query');
    }

    public function getFuzzyQueryMode()
    {
        return (string) $this->getConfig('fuzzy_query_mode');
    }

    /**
     * Prepares query text for search
     *
     * @param string $text
     * @return string
     */
    public function prepareQueryText($text)
    {
        $words = explode(' ', $text);
        $words = array_filter($words, 'strlen');
        $text = implode(' ', $words);

        return $text;
    }

    /**
     * @param Wyomind_Elasticsearch_Type $type
     * @param string $q
     * @param array $params
     * @return \Elastica\Search
     */
    public function getSearch(Wyomind_Elasticsearch_Type $type,
            $q,
            $params = array(),
            $filter = null,
            $fuzzy = true)
    {
        if (empty($params)) {
            $params = array('limit' => 10000); // should be enough
        }

        $q = $this->prepareQueryText($q);


        // for product weight
        $functionQuery = new \Elastica\Query\FunctionScore();

        // standard search (on all searchable attributes)
        $bool = new \Elastica\Query\BoolQuery();
        $bool->setMinimumNumberShouldMatch(1);

        /**
         * Using cross-fields because it seems the best approach for entity search (products, categories, ...).
         * Cross-fields multi-match query has to work on fields that have the same analyzer.
         * So we build such a query for each configured analyzers.
         *
         * @link https://www.elastic.co/guide/en/elasticsearch/guide/current/_cross_fields_entity_search.html
         */
        foreach ($type->getAnalyzers() as $analyzer) {
            $fields = $type->getSearchFields($q, $analyzer);
            if (!empty($fields)) {
                $query = new \Elastica\Query\MultiMatch();
                $query->setQuery($q);
                $query->setType('cross_fields');
                $query->setFields($fields);
                $query->setOperator($this->getQueryOperator());
                $query->setTieBreaker(.1);
                $bool->addShould($query);
            }
        }

        if ($this->isFuzzyQueryEnabled() && $fuzzy) {
            $fields = $type->getSearchFields($q, 'std');
            if (!empty($fields)) {
                $query = new \Elastica\Query\Match('_all', array(
                    'query' => $q,
                    'operator' => 'AND',
                    'fuzziness' => $this->getFuzzyQueryMode(),
                ));
                $bool->addShould($query);
            }
        }

        // manage product weight
        $functionQuery->setQuery($bool);
        $functionQuery->setBoostMode("sum");
        $functionParams = array(
            "field" => "product_weight",
            "factor" => 10,
            "modifier" => "square",
            "missing" => 0
        );

        $version = $this->getVersion();
        if (version_compare($version, "2.0.0") < 0) {
            unset($functionParams["missing"]);
        }
        $functionQuery->addFunction("field_value_factor", $functionParams);

        if ($filter === null) {
            $filter = Mage::helper('elasticsearch/filter_' . $type->getName());
        }
        $filter->addAdditionalFilters($bool, $q);

        $search = $type->createSearch($functionQuery, $params);

        $additionalFields = $type->getAdditionalFields();
        if (!empty($additionalFields)) {
            // Return additional fields (entity id is already implicitly included)
            if (version_compare($version, "5.0.0") < 0) {
                $search->getQuery()->setParam("fields", $additionalFields); // ES 2.x
            } else {
                $search->getQuery()->setParam("stored_fields", $additionalFields); // ES 5.x                
            }
        }

        $suggest = new \Elastica\Suggest();
        $fields = $type->getSearchFields($q, 'std', false);
        $suggestedFields = new Varien_Object(array('name.std'));
        foreach ($fields as $field) {
            $suggestField = new \Elastica\Suggest\Phrase($field, $field);
            $suggestField->setText($q);
            $suggestField->setGramSize(1);
            $suggestField->setMaxErrors(.9);
            $candidate = new \Elastica\Suggest\CandidateGenerator\DirectGenerator($field);
            $candidate->setParam('min_word_length', 1);
            $suggestField->addCandidateGenerator($candidate);
            $suggest->addSuggestion($suggestField);
            $search->getQuery()->setSuggest($suggest);
        }


        return $search;
    }

}
