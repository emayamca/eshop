<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Index extends \Elastica\Index
{
    /**
     * @var array
     */
    protected $_analyzers = array();

    /**
     * @return array
     */
    public function getAnalyzers()
    {
        return $this->_analyzers;
    }

    /**
     * @param array $analyzers
     * @return $this
     */
    public function setAnalyzers(array $analyzers)
    {
        $this->_analyzers = $analyzers;

        return $this;
    }
}