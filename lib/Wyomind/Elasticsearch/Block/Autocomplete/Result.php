<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Block_Autocomplete_Result extends Wyomind_Elasticsearch_Block_Abstract
{
    /**
     * @var string
     */
    protected $_q = '';

    /**
     * @var string
     */
    protected $_template = 'wyomind/elasticsearch/autocomplete.phtml';

    /**
     * @var array
     */
    protected $_entityResults = array();

    /**
     * @var array
     */
    protected $_entityResultsCount = array();

    /**
     * @var array
     */
    protected $_entityBlocks = array();
    
    protected $_suggests = array();
    
    public function setSuggests($suggests) {
        $this->_suggests = $suggests;
    }
    
    public function getSuggests() {
        return $this->_suggests;
    }

    /**
     * @param string $q
     * @param Wyomind_Elasticsearch_Config $config
     */
    public function __construct($q, Wyomind_Elasticsearch_Config $config)
    {
        $this->_q = $q;
        $this->_config = $config;
    }

    /**
     * @return string
     */
    public function getAllResultsLabel()
    {
        return $this->getLabel('All Results');
    }

    /**
     * @return string
     */
    public function getResultUrl()
    {
        $baseUrl = $this->_config->getValue('base_url', '');

        return $this->cleanUrl(sprintf('%scatalogsearch/result/?q=%s', $baseUrl, $this->_q));
    }

    /**
     * Returns entity associated block
     *
     * @param string $entity
     * @return Wyomind_Elasticsearch_Block_Autocomplete_Abstract
     * @throws Exception
     */
    public function getEntityBlock($entity)
    {
        if (!isset($this->_entityBlocks[$entity])) {
            throw new Exception('Cannot find block for entity ' . $entity);
        }

        return $this->_entityBlocks[$entity];
    }

    /**
     * Associates the entity corresponding block class
     *
     * @param $entity
     * @param Wyomind_Elasticsearch_Block_Autocomplete_Abstract $block
     * @return $this
     */
    public function setEntityBlock($entity, Wyomind_Elasticsearch_Block_Autocomplete_Abstract $block)
    {
        $this->_entityBlocks[$entity] = $block->setConfig($this->_config);

        return $this;
    }

    /**
     * Returns entity HTML
     *
     * @param string $entity
     * @param Varien_Object $data
     * @return string
     */
    public function getEntityHtml($entity, Varien_Object $data)
    {
        return $this->getEntityBlock($entity)->setEntity($data)->toHtml();
    }

    /**
     * @param string $entity
     * @return string
     */
    public function getEntityTitle($entity)
    {
        return $this->getEntityBlock($entity)->getTitle();
    }

    /**
     * @param string $entity
     * @return array
     */
    public function getEntityResults($entity)
    {
        return isset($this->_entityResults[$entity]) ? $this->_entityResults[$entity] : array();
    }

    /**
     * @return array
     */
    public function getAllResults()
    {
        return $this->_entityResults;
    }

    /**
     * @param string $entity
     * @param array $results
     * @return $this
     */
    public function setEntityResults($entity, array $results)
    {
        if (!empty($results)) {
            $this->_entityResults[$entity] = $results;
        }

        return $this;
    }

    /**
     * @param string $entity
     * @return array
     */
    public function getEntityResultsCount($entity)
    {
        return isset($this->_entityResultsCount[$entity]) ? $this->_entityResultsCount[$entity] : 0;
    }

    /**
     * @param string $entity
     * @param int $count
     * @return $this
     */
    public function setEntityResultsCount($entity, $count)
    {
        $this->_entityResultsCount[$entity] = (int) $count;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNoResult()
    {
        return empty($this->_entityResults);
    }
}