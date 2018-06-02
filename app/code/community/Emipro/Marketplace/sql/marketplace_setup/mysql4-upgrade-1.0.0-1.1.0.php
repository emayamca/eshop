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


$installer->run("ALTER TABLE  `" . $this->getTable('sales/creditmemo_item') . "` ADD  `shipping_charge` DECIMAL( 10, 2 )  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('sales/creditmemo_item') . "` ADD  `base_shipping_charge` DECIMAL( 10, 2 )  NULL");
$installer->run("ALTER TABLE  `" . $this->getTable('sales/creditmemo_item') . "` ADD  `credit_days_flag` TINYINT(1)  DEFAULT 0");
$installer->run("ALTER TABLE  `" . $this->getTable('marketplace/commission') . "` ADD  `base_shipping_charge` DECIMAL( 10, 2 )  NULL AFTER `product_id`");

$installer->endSetup(); 
