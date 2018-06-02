<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
abstract class Wyomind_Elasticsearch_Block_Autocomplete_Abstract extends Wyomind_Elasticsearch_Block_Abstract
{
    /**
     * @var string
     */
    protected $_title = '';

    /**
     * @var Varien_Object
     */
    protected $_entity;

    /**
     * @return Varien_Object
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @param Varien_Object $entity
     * @return $this
     */
    public function setEntity(Varien_Object $entity)
    {
        $this->_entity = $entity;

        return $this;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function validate($data)
    {
        return !empty($data);
    }

    /**
     * Should be overriden in child classes
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getLabel($this->_title);
    }
}