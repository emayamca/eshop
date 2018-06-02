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

$installer->run("ALTER TABLE  `" . $this->getTable('sales/creditmemo') . "` ADD  `seller_id` INTEGER  DEFAULT NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('sales/invoice_item') . "` ADD  `seller_id` INTEGER  DEFAULT NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('sales/shipment_item') . "` ADD  `seller_id` INTEGER  DEFAULT NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('sales/creditmemo_item') . "` ADD  `seller_id` INTEGER  DEFAULT NULL");

$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `bank_account_name` VARCHAR(100)  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `bank_account_number` VARCHAR(100)  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `bank_name` VARCHAR(100)  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `bank_ifsc_code` VARCHAR(100) NULL ");
$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `bank_branch_name` VARCHAR(100)  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `bank_city` VARCHAR(100)  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `vat_number` VARCHAR(100)  NULL");

$installer->endSetup(); 
