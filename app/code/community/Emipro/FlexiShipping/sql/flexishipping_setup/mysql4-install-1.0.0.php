<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_FlexiShipping
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
$installer = $this;  
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('emipro_flexi_shipping_rules')};
CREATE TABLE {$this->getTable('emipro_flexi_shipping_rules')} (
`ship_id` int(11) unsigned NOT NULL auto_increment,
`seller_admin_id` int(11) NOT NULL,
`name` varchar(255) NOT NULL default '',
`description` text NOT NULL,
`from_date` date default NULL,
`to_date` date default NULL,
`store_ids` varchar(255) NOT NULL default '',
`website_id` varchar(255) NOT NULL default '',
`customer_group_id` varchar(255) NOT NULL default '',
`is_active` tinyint(1) NOT NULL default '0',
`conditions_serialized` text NOT NULL,
`actions_serialized` text NOT NULL,
`stop_rules_processing` tinyint(1) NOT NULL default '1',
`sort_order` int(10) unsigned NOT NULL default '0',
`fees` text default NULL,
`shipto` text  default NULL,
`all_countries` varchar(255)  default NULL,
`specific_countries_ids` varchar(255)  default NULL,
`free_shipping` varchar(255)  default NULL,
`cart_price` varchar(255)  default NULL,
PRIMARY KEY  (`ship_id`),
KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('emipro_flexi_shipping_rules_products')};
CREATE TABLE {$this->getTable('emipro_flexi_shipping_rules_products')} (
 `id` int(11) unsigned NOT NULL auto_increment,
`entity_id` int(11) NOT NULL,
`ship_id` int(11) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("ALTER TABLE {$this->getTable('emipro_flexi_shipping_rules_products')} ADD CONSTRAINT {$this->getTable('emipro_flexi_shipping_rules_products')}_FK_1
FOREIGN KEY (`ship_id`) REFERENCES {$this->getTable('emipro_flexi_shipping_rules')} (`ship_id`)
ON DELETE CASCADE ON UPDATE CASCADE
");

//$installer->run("ALTER TABLE  `".$this->getTable('sales/order_item')."` ADD  `shipping_charge` DECIMAL( 10, 2 )  NULL");

$installer->endSetup();
?>
