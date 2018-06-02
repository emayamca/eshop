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

$selleradvancecommtable = $installer->getConnection()->newTable($installer->getTable('seller_advancecommission'))
        ->addColumn('advancecomm_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'auto_increment' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Advancecomm Id')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
                ), 'Id')
        ->addColumn('commission', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
            'nullable' => true,
                ), 'Commission')
        ->addColumn('commission_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
                ), 'Commission Type')
        ->addColumn('attributeset_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable' => false,
        ), 'Attributeset Id');

$installer->getConnection()->createTable($selleradvancecommtable);
$installer->endSetup();
?>
