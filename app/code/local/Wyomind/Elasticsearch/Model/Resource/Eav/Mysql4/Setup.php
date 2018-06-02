<?php
class Wyomind_Elasticsearch_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {

    public function getDefaultEntities() {

        return array(
           
            'catalog_product' => array(
                'entity_model' => 'catalog/product',
                'attribute_model' => 'catalog/resource_eav_attribute',
                'table' => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes' => array(
                    'product_weight' => array(
                        'group' => "Elasticsearch",
                        'label' => 'Product weight',
                        'type' => 'int',
                        'input' => 'select',
                        'default' => '1',
                        'note' => '',
                        'backend' => '',
                        'apply_to' => array('simple','virtual','downloadable'),
                        'frontend' => '',
                        'source' => 'elasticsearch/attribute_source_weight',
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'searchable' => true,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'visible_in_advanced_search' => false,
                        'unique' => false
                    )
                )
            )
        );
    }

}
