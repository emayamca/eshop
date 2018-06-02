<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Marketplace 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()->newTable($installer->getTable('seller_detail'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Seller Id')
        ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'First Name')
        ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Last Name')
        ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Email')
        ->addColumn('banner', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Banner Image')
        ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Store Name')
        ->addColumn('website_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Website URL')
        ->addColumn('store_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Store URL')
        ->addColumn('company_logo', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Company Logo')
        ->addColumn('fb_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Facebook ID')
        ->addColumn('twitter_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Twitter ID')
        ->addColumn('company_description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Company Description')
        ->addColumn('shipping_policies', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Shipping Policies')
        ->addColumn('return_policies', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Return Policies')
        ->addColumn('street1', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Street1')
        ->addColumn('street2', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Street2')
        ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Country')
        ->addColumn('region', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Region')
        ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Region ID')
        ->addColumn('city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'City')
        ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
            'nullable' => true,
                ), 'Postcode')
        ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
            'nullable' => true,
                ), 'Telephone')
        ->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Meta Keywords')
        ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Meta Description')
        ->addColumn('replacement_gaurantee', Varien_Db_Ddl_Table::TYPE_INTEGER, 5, array(
            'nullable' => true,
                ), 'Replacement Gaurantee')
        ->addColumn('delivered_in', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Delivered In')
        ->addColumn('default_product_approve', Varien_Db_Ddl_Table::TYPE_INTEGER, 2, array(
            'nullable' => true,
            'default' => 1,
                ), 'Default Product Approve')
        ->addColumn('seller_credit_days', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => true,
            'default' => 0,
                ), 'Seller Credit Days')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
        ), 'Created At');
$installer->getConnection()->createTable($table);

$sellercommtable = $installer->getConnection()->newTable($installer->getTable('seller_commission'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'auto_increment' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Seller Id')
        ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15, array(
            'nullable' => true,
                ), 'Order Increment Id')
        ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Order Item Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Product Id')
        ->addColumn('commission_amt', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
            'nullable' => true,
                ), 'Commission Amount')
        ->addColumn('seller_credit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,2', array(
            'nullable' => true,
                ), 'Seller Credit')
        ->addColumn('seller_debit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,2', array(
            'nullable' => true,
                ), 'Seller Debit')
        ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
                ), 'Date')
        ->addColumn('summary', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Summary')
        ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Qty')
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TINYINT, '2', array(
    'nullable' => false,
    'default' => 0,
        ), 'Type');
$installer->getConnection()->createTable($sellercommtable);

$sellertranstable = $installer->getConnection()->newTable($installer->getTable('seller_transaction'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'auto_increment' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Seller Id')
        ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
            'nullable' => true,
                ), 'Amount')
        ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
                ), 'Date')
        ->addColumn('summary', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Summary')
        ->addColumn('request_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
    'nullable' => true,
        ), 'Request Status');

$installer->getConnection()->createTable($sellertranstable);

$seller_table = $installer->getConnection()->newTable($installer->getTable('seller'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
                ), 'Status')
        ->addColumn('seller_admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Seller Admin Id')
        ->addColumn('product_vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Product Vendor Id')
        ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 5, array(
            'nullable' => true,
                ), 'Website Id')
        ->addColumn('email_verify_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
            'nullable' => true,
                ), 'Email Verify Status')
        ->addColumn('email_verify_key', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
    'nullable' => true,
        ), 'Email Verify Key');

$installer->getConnection()->createTable($seller_table);

$seller_category_table = $installer->getConnection()->newTable($installer->getTable('seller_category'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('attributeset_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Attributeset Id')
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable' => true,
        ), 'Category Id');
$installer->getConnection()->createTable($seller_category_table);

$sellerruletable = $installer->getConnection()->newTable($installer->getTable('seller_rule'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'auto_increment' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Seller Id')
        ->addColumn('commission_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
            'default' => 0,
                ), 'Commission Type')
        ->addColumn('commission', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
    'nullable' => true,
        ), 'Commission');

$installer->getConnection()->createTable($sellerruletable);

/* -----------Seller Review Table Start------------- */
$seller_review = $installer->getConnection()->newTable($installer->getTable('seller_review'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'auto_increment' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Seller Review Id')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Seller Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Customer Id')
        ->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Text')
        ->addColumn('rating', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Rating')
        ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
                ), 'Created Date')
        ->addColumn('approved', Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array(
    'nullable' => true,
    'default' => 1,
        ), 'Approved');
$installer->getConnection()->createTable($seller_review);

/* -----------Seller Review Table End------------- */

$seller_question = $installer->getConnection()->newTable($installer->getTable('seller_question'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'auto_increment' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Seller Question Id')
        ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
            'nullable' => true,
                ), 'Product Sku')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Seller Id')
        ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
            'nullable' => true,
                ), 'Customer Email')
        ->addColumn('subject', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
            'nullable' => true,
                ), 'Subject')
        ->addColumn('question', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Question')
        ->addColumn('answer', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
                ), 'Answer')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
            'nullable' => true,
                ), 'Status')
        ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
        ), 'Date');
$installer->getConnection()->createTable($seller_question);

$balance_table = $installer->getConnection()->newTable($installer->getTable('seller_balance_statement'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id')
        ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Parent customer Id')
        ->addColumn('transaction_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => true,
                ), 'Transaction type')
        ->addColumn('credit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,2', array(
            'nullable' => true,
                ), 'Transaction type')
        ->addColumn('debit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,2', array(
            'nullable' => true,
                ), 'Transaction type')
        ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
                ), 'Date')
        ->addColumn('summary', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => true,
                ), 'Summary')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => true,
                ), 'Store Name')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable' => true,
        ), 'Order Id');

$installer->getConnection()->createTable($balance_table);

$roleModel = Mage::getModel('admin/roles')
        ->getCollection()
        ->addFieldToFilter('role_name', "Seller/Vendor");

if ($roleModel->count() == 0) {
    $role = Mage::getModel('admin/roles')
            ->setName("Seller/Vendor")
            ->setRoleType("G")
            ->save();

    Mage::getModel('admin/rules')
            ->setRoleId($role->getId())
            ->setResources(
                    array('admin/sales',
                        'admin/sales/order',
                        'admin/sales/order/actions',
                        'admin/sales/order/actions/invoice',
                        'admin/sales/order/actions/creditmemo',
                        'admin/sales/order/actions/ship',
                        'admin/sales/order/actions/emails',
                        'admin/sales/order/actions/comment',
                        'admin/sales/order/actions/view',
                        'admin/sales/invoice',
                        'admin/sales/shipment',
                        'admin/sales/creditmemo', 
                        'admin/catalog',
                        'admin/catalog/products',
                        'admin/promo',
						'admin/promo/catalog', 
                        'admin/marketplace',
                        'admin/marketplacedashboard',
                        'admin/marketplace/seller_account',
                        'admin/marketplace/sellercommission',
                        'admin/marketplace/seller_withdraw',
                        'admin/marketplace/seller_report',
                        'admin/marketplace/seller_category',
                        'admin/marketplace/customer_question',
                        'admin/marketplace/seller_profile',
                        'admin/catalog/add_new_product',
                        'admin/catalog/multiple_image_upload',
                        'admin/catalog/bulkproductimport',
                        'admin/catalog/export',
                        'admin/marketplace/sellerhelpdesk',
                        'admin/marketplace/flexishipping',
                        'admin/cms',
                        'admin/cms/media_gallery'
            ))
            ->saveRel();
}
$attr = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'vendor_id');
if (null == $attr->getId()) {
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

    $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'vendor_id', array(
        'group' => 'General',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Seller',
        'input' => 'select',
        'class' => '',
        'source' => 'eav/entity_attribute_source_table',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 1,
        'filterable' => 1,
        'comparable' => 0,
        'visible_on_front' => 0,
        'unique' => 0,
        'apply_to' => 'simple,configurable,bundle,grouped,virtual,downloadable',
    ));
    $setup->updateAttribute('catalog_product', 'vendor_id', 'is_filterable', 1);
    $setup->updateAttribute('catalog_product', 'vendor_id', 'is_filterable_in_search', 1);
}
$attr = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'marketplace_product_approve');
if (null == $attr->getId()) {
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
    $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'marketplace_product_approve', array(
        'group' => 'General',
        'label' => 'Marketplace Approved',
        'type' => 'int',
        'input' => 'boolean',
        'source' => 'eav/entity_attribute_source_boolean',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 0,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 0,
        'visible_in_advanced_search' => 0,
        'unique' => 0,
        'default' => 1
    ));
}
$cmsBlockModel = Mage::getModel('cms/block')->load("marketplace-content");
if (!$cmsBlockModel->getId()) {
    $content = '<div style="text-align: center;">
<h2>Open Marketplace E-commerce Store</h2>
<p>Open your own marketplace store with register your self as seller. Your selling will be boost with our marketplace site. You will get online platform to sell your product globally.</p>
<p><a class="button" style="margin: 10px;" href="{{store url=""}}marketplace/seller/create/"><span><span>Open Your Own E-commerce Store</span></span></a></p>
</div>';
    $cmsBlock = array(
        'title' => Mage::helper('marketplace')->__('Marketplace'),
        'identifier' => 'marketplace-content',
        'content' => $content,
        'is_active' => 1,
        'sort_order' => 0,
        'stores' => array(0)
    );
    Mage::getModel('cms/block')->setData($cmsBlock)->save();
}
$cmsBlockBanner = Mage::getModel('cms/block')->load("seller_product_listing_banner");
if (!$cmsBlockBanner->getId()) {
    $bannercontent = '<div class="seller_banner"><img class="banner_image" alt="" src="{{var imgsrc}}" /></div>'; 
    $cmsBlockbanner = array(
        'title' => Mage::helper('marketplace')->__('Seller Product Listing Page Banner'),
        'identifier' => 'seller_product_listing_banner',
        'content' => $bannercontent,
        'is_active' => 1,
        'sort_order' => 0,
        'stores' => array(0)
    );
    Mage::getModel('cms/block')->setData($cmsBlockbanner)->save();
}
$attr = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'upc');
if (null == $attr->getId()) {
    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

    $entityTypeId = $setup->getEntityTypeId('catalog_product');
    $attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
    $attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

    $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'upc', array(
        'group' => 'General',
        'label' => 'UPC',
        'type' => 'varchar',
        'input' => 'text',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => 1,
        'class' => 'validate-digits',
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
$installer->run("ALTER TABLE  `" . $this->getTable('sales/order_item') . "` ADD  `shipping_charge` DECIMAL( 10, 2 )  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('sales/order_item') . "` ADD  `base_shipping_charge` DECIMAL( 10, 2 )  NULL");
$installer->run("ALTER TABLE  `".$this->getTable('sales/order_item')."` ADD  `seller_id` INTEGER  NULL");    
$installer->run("ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `seller_id` INTEGER  NULL"); 

$installer->endSetup(); 
