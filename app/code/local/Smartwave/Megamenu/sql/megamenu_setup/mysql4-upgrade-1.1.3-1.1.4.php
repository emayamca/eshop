<?php
/**
 * @package		Eternal_Megamenu
 * @author		Eternal Friend
 * @copyright	Copyright 2014
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'sw_ocat_hide_this_item', array(
    'group'             => 'Onepage Category',
    'label'             => 'Hide This Category',
    'type'              => 'int',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_block_yesno',
    'visible'           => true,
    'required'          => false,
    'sort_order'        => 0,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));
$installer->addAttribute('catalog_category', 'sw_ocat_category_icon_image', array(
    'type'              => 'varchar',
    'label'             => 'Icon Image',
    'input'             => 'image',
    'backend'           => 'catalog/category_attribute_backend_image',
    'required'          => false,
    'sort_order'        => 10,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'group'             => 'Onepage Category'
));
$installer->addAttribute('catalog_category', 'sw_ocat_category_font_icon', array(
    'group'             => 'Onepage Category',
    'label'             => 'Font Icon Class',
    'note'              => 'If this category has no "Icon Image", font icon will be shown. example to input: icon-dollar',
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'sort_order'    => 20,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));
$installer->addAttribute('catalog_category', 'sw_ocat_category_hoverbgcolor', array(
    'group'             => 'Onepage Category',
    'label'             => 'Category Hover Background Color',
    'note'              => 'eg: #00d59d',
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'sort_order'    => 25,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));
$installer->addAttribute('catalog_category', 'sw_ocat_additional_content', array(
    'group'             => 'Onepage Category',
    'label'             => 'Additional Content',
    'type'              => 'text',
    'input'             => 'textarea',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'sort_order'        => 40,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));
$installer->endSetup();