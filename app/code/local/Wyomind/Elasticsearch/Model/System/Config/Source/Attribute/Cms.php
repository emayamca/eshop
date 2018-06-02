<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Model_System_Config_Source_Attribute_Cms
{
    /**
     * @var array
     */
    protected $_allowedTypes = array(
        'char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext'
    );

    /**
     * Return list of searchable attributes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $resource = Mage::getResourceModel('cms/page');
        $tableInfo = $resource->getReadConnection()->describeTable($resource->getMainTable());

        foreach ($tableInfo as $field => $info) {
            if (in_array($info['DATA_TYPE'], $this->_allowedTypes) &&
                $field != 'layout_update_xml' &&
                substr($field, 0, 7) !== 'custom_')
            {
                $options[$field] = array(
                    'value' => $field,
                    'label' => ucwords(strtr($field, '_-', '  ')),
                );
            }
        }

        Mage::dispatchEvent('wyomind_elasticsearch_cms_attributes', array(
            'attributes' => $options,
        ));

        ksort($options);

        return $options;
    }
}
