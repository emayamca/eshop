<?php
/**
 * Defines list of available search engines
 *
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Model_System_Config_Source_Transportmode
{
    public function toOptionArray()
    {
        $transportModes = array(
            'Http'  => Mage::helper('adminhtml')->__('Http'),
            'AwsAuthV4' => Mage::helper('adminhtml')->__('AwsAuthV4'),
        );

        $options = array();
        foreach ($transportModes as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }

        return $options;
    }
}
