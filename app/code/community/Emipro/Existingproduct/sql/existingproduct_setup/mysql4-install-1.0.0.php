<?php
$installer = $this;
$installer->startSetup();
$installer->run("ALTER TABLE seller auto_increment=1000;");


$attr = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'esin');
if (null == $attr->getId()) {
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

    $entityTypeId = $setup->getEntityTypeId('catalog_product');
    $attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
    $attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

    $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'esin', array(
        'group' => 'General',
        'label' => 'ESIN',
        'type' => 'varchar',
        'input' => 'text',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => 1,
        'frontend_class' => 'validate-alphanum',
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 0,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 0,
        'visible_in_advanced_search' => 0,
        'unique' => 0,
    ));
}

$installer->endSetup();