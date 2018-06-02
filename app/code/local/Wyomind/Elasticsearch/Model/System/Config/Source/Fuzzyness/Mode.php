<?php

/**
 * Query default operator configuration
 *
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Model_System_Config_Source_Fuzzyness_Mode
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'AUTO', 'label' => __('AUTO')),
            array('value' => '0', 'label' => __('0')),
            array('value' => '1', 'label' => __('1')),
            array('value' => '2', 'label' => __('2'))
        );
    }

}
