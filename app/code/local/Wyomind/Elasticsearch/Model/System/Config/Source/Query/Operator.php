<?php
/**
 * Query default operator configuration
 *
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Model_System_Config_Source_Query_Operator
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'OR', 'label' => Mage::helper('elasticsearch')->__('OR')),
            array('value' => 'AND', 'label' => Mage::helper('elasticsearch')->__('AND')),
        );
    }
}