<?php
/**
 * Defines list of available search engines
 *
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Model_System_Config_Source_Engine
{
    /**
     * Return list of search engines for config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $engines = array(
            'catalogsearch/fulltext_engine'  => Mage::helper('adminhtml')->__('MySQL'),
            'elasticsearch/engine' => Mage::helper('adminhtml')->__('Elasticsearch'),
        );

        $options = array();
        foreach ($engines as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }

        return $options;
    }
}
