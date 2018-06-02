<?php
$installer = $this;
$installer->startSetup();

/* @var $this Mage_Eav_Model_Entity_Setup */

// Add an extra column to the catalog_eav_attribute-table:
$this->getConnection()->addColumn(
    $this->getTable('catalog/eav_attribute'),
    'manage_by_seller',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'   => 'Manage By Seller',      
    )
);

$roleModel = Mage::getModel('admin/roles')->load("Seller/Vendor",'role_name');
if($roleModel && $roleModel->getRoleId()!=''){
         Mage::getModel('admin/rules')
            ->setRoleId($roleModel->getRoleId())
            ->setResources(array(
            			'admin/sales',
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
                        'admin/marketplace/seller_bankdetails',
                        'admin/catalog/add_new_product',
                        'admin/catalog/multiple_image_upload',
                        'admin/catalog/bulkproductimport',
                        'admin/catalog/export',
                        'admin/marketplace/sellerhelpdesk',
                        'admin/marketplace/flexishipping',
                        'admin/cms',
                        'admin/cms/media_gallery',
                        'admin/marketplace/stocknotification',
                        'admin/catalog/existingproduct',
                        'admin/sales/orderitem'
            ))->saveRel();
}

$installer->endSetup(); 
